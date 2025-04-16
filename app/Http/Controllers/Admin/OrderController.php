<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Customer; // If filtering by customer
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request) {
        $query = Order::with(['customer']); // Eager load customer

        // --- Filtering ---
        if ($request->filled('search')) { // Search by Order ID or Customer Name/Email
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm){
                $q->where('id', $searchTerm) // Search by exact ID
                  ->orWhereHas('customer', function($cq) use ($searchTerm){
                      $cq->where('name', 'like', "%{$searchTerm}%")
                         ->orWhere('email', 'like', "%{$searchTerm}%");
                  });
            });
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        // Add filter by Payment Method if needed

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = Order::statuses();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Display the specified order with all its details.
     */
    public function show(Order $order) {
        // Eager load all relevant relationships
        $order->load([
            'customer',
            'items.product', // Load product info for each item
            'payments.paymentMethod', // Load payment method details
            'delivery.deliveryPersonnel', // Load assigned delivery person
            'invoice' // Load invoice details
        ]);

        $statuses = Order::statuses(); // For status update dropdown

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, Order $order) {
        $statuses = Order::statuses();
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys($statuses))],
            'notify_customer' => 'nullable|boolean', // Optional notification flag
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        // --- Add Business Logic Here ---
        // - Can the status be changed (e.g., can't change completed to pending)?
        // - Trigger notifications?
        // - Update related models (e.g., invoice status if order completed/cancelled)?
        // - Create delivery record if status changed to 'shipped'?
        // -----------------------------

        DB::beginTransaction();
        try {
            $order->status = $newStatus;
            $order->save();

            // Example: Update invoice status if order completed/cancelled
            if ($order->invoice) {
                 if ($newStatus === Order::STATUS_COMPLETED && $order->invoice->status !== Invoice::STATUS_PAID) {
                     // Should check payment status first ideally before marking invoice paid
                     // $order->invoice->update(['status' => Invoice::STATUS_PAID]);
                 } elseif ($newStatus === Order::STATUS_CANCELLED && $order->invoice->status !== Invoice::STATUS_VOID) {
                      $order->invoice->update(['status' => Invoice::STATUS_VOID]);
                 }
            }

            // Example: Trigger notification (using Laravel Notifications)
            // if ($request->has('notify_customer')) {
            //     $order->customer->notify(new OrderStatusUpdated($order, $oldStatus));
            // }

            DB::commit();
            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating status for order {$order->id}: " . $e->getMessage());
             return redirect()->route('admin.orders.show', $order->id)->with('error', 'Failed to update order status.');
        }
    }

    /**
     * Remove the specified order from storage. (Use with extreme caution!)
     */
    public function destroy(Order $order) {
        // --- WARNING ---
        // Deleting orders is generally discouraged in e-commerce due to reporting, history, etc.
        // Consider adding a 'deleted_at' (soft delete) or just using the 'cancelled' status.
        // If you MUST delete, ensure all related data (items, payments, delivery, invoice) is handled correctly (cascade or manual delete).

        // Prevent deleting processed/completed orders?
        if (in_array($order->status, [Order::STATUS_PROCESSING, Order::STATUS_COMPLETED])) {
            return back()->with('error', 'Cannot delete orders that have been processed or completed.');
        }

        try {
             // Cascade delete should handle related items if set up correctly.
             // If not, delete manually:
             // $order->items()->delete();
             // $order->payments()->delete();
             // $order->delivery()->delete();
             // $order->invoice()->delete();
             $order->delete();
            return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
             Log::error("Error deleting order {$order->id}: " . $e->getMessage());
             return back()->with('error', 'Failed to delete order.');
        }
    }
}