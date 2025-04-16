<?php
namespace App\Http\Controllers\DeliveryPortal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\DeliveryPersonnel; // Driver model
use App\Models\OrderDelivery; // Delivery assignment model

class OrderController extends Controller
{
    /**
     * Display a listing of orders relevant to the delivery company.
     */
    public function index(Request $request)
    {
        $deliveryCompany = Auth::guard('delivery_company')->user();

        // Fetch orders assigned to this company's drivers OR needing assignment
        // This logic needs refinement based on how orders are initially linked to companies
        $query = Order::with(['customer', 'city', 'delivery.deliveryPersonnel'])
                    // ->where(function($q) use ($deliveryCompany) {
                    //     // Orders assigned to this company's drivers
                    //     $q->whereHas('delivery.deliveryPersonnel', fn($dpq) => $dpq->where('delivery_company_id', $deliveryCompany->id))
                    //     // Or orders needing assignment (assuming they are somehow linked to the company, maybe via city/region?)
                    //     ->orWhere(function($q2) {
                    //          $q2->whereIn('status', [Order::STATUS_PROCESSING]) // Needs driver
                    //             ->whereDoesntHave('delivery'); // Not assigned yet
                    //           // Add filter to only show orders this company CAN deliver (e.g., city)
                    //     });
                    // });
                    // ^^ Complex Logic: Simplify for now, show all processing/assigned
                    ->whereIn('status', [Order::STATUS_PROCESSING, 'out_for_delivery', 'assigned']); // Example relevant statuses


         // Add filtering from request (status, search etc.)

         $orders = $query->orderBy('created_at', 'desc')->paginate(20);
         $statuses = Order::statuses();

         return view('delivery_portal.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order)
    {
         $deliveryCompany = Auth::guard('delivery_company')->user();

         // --- Authorization Check ---
         // Ensure this order is relevant to the logged-in delivery company
         // (e.g., assigned to one of their drivers or deliverable by them)
         // Implement appropriate logic here - Placeholder:
         // if (! $this->isOrderRelevant($order, $deliveryCompany)) {
         //     abort(403, 'Unauthorized to view this order.');
         // }

         $order->load(['customer', 'items.product', 'city', 'delivery.deliveryPersonnel']);

          // Get drivers belonging ONLY to this company who are active
          $availableDrivers = $deliveryCompany->deliveryPersonnel()
                                             ->where('is_active', true)
                                             ->orderBy('name')
                                             ->pluck('name', 'id');


          return view('delivery_portal.orders.show', compact('order', 'availableDrivers'));
    }

     /**
     * Assign a delivery person to an order.
     */
    public function assignDriver(Request $request, Order $order)
    {
        $deliveryCompany = Auth::guard('delivery_company')->user();

        // --- Authorization Check ---
        // if (! $this->isOrderRelevant($order, $deliveryCompany) || $order->delivery) { // Prevent re-assigning?
        //     abort(403);
        // }

        $validated = $request->validate([
            'delivery_personnel_id' => [
                'required',
                 Rule::exists('delivery_personnel', 'id')->where(function ($query) use ($deliveryCompany) {
                     // Ensure assigned driver belongs to the logged-in company
                     $query->where('delivery_company_id', $deliveryCompany->id)->where('is_active', true);
                 }),
            ],
            // Optional: Add tracking number if generated here
             'tracking_number' => 'nullable|string|max:100|unique:order_deliveries,tracking_number',
             'delivery_date' => 'nullable|date', // Estimated delivery date maybe?
        ]);

        try {
            // Create or Update the delivery record
            OrderDelivery::updateOrCreate(
                ['order_id' => $order->id], // Find by order_id
                [
                    'delivery_personnel_id' => $validated['delivery_personnel_id'],
                    'status' => 'assigned', // Or use a constant
                    'tracking_number' => $validated['tracking_number'] ?? 'AAM-DP-' . $order->id . '-' . $validated['delivery_personnel_id'], // Example generation
                    'delivery_date' => $validated['delivery_date'] ?? null, // Handle date
                ]
            );

             // Update order status if needed (e.g., to 'assigned')
             if($order->status === Order::STATUS_PROCESSING) {
                 $order->update(['status' => 'assigned']); // Example status update
             }


             return redirect()->route('delivery-portal.orders.show', $order->id)->with('success', 'Driver assigned successfully.');

        } catch (\Exception $e) {
             Log::error("Error assigning driver to order {$order->id} by company {$deliveryCompany->id}: " . $e->getMessage());
             return back()->with('error', 'Failed to assign driver.');
        }
    }

     // Helper function for authorization check (example)
     // private function isOrderRelevant(Order $order, $deliveryCompany): bool
     // {
     //     // Check if already assigned to this company's driver
     //     if ($order->delivery && $order->delivery->deliveryPersonnel?->delivery_company_id === $deliveryCompany->id) {
     //         return true;
     //     }
     //     // Check if unassigned and in a city this company serves (needs more logic)
     //     if (!$order->delivery && $order->status === Order::STATUS_PROCESSING /* && companyServesCity($deliveryCompany, $order->city_id) */) {
     //         return true;
     //     }
     //     return false;
     // }
}