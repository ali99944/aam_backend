<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Customer;
use App\Models\DeliveryFee;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
// Add other necessary models like Payment, DeliveryPersonnel, DeliveryCompany if editing those
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View; // For potentially rendering invoice view to string/PDF
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
// Use DomPDF or similar if generating PDF invoices
// use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    // --- LIST ORDERS ---
    public function index(Request $request)
    {
        $query = Order::with(['customer:id,name', 'city:id,name']); // Eager load essentials

        // Filtering
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', $searchTerm)
                  ->orWhere('track_code', 'like', $searchTerm)
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', $searchTerm)->orWhere('email', 'like', $searchTerm));
            });
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
             $query->whereDate('created_at', '<=', $request->end_date);
        }

        $orders = $query->latest()->paginate(20); // Order by newest first
        $statuses = Order::distinct()->pluck('status')->mapWithKeys(fn($status) => [$status => ucfirst($status)])->toArray(); // Dynamic statuses

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    // --- SHOW CREATE FORM ---
    public function create()
    {
        // Data for dropdowns
        $customers = Customer::orderBy('name')->select('id', 'name', 'email')->get(); // Get more info for display
        // Select only active, in-stock, public products? Adjust as needed
        $products = Product::where('is_public', true)->where('status', 'active')->orderBy('name')->select('id', 'name', 'sell_price', 'stock')->get();
        $cities = City::orderBy('name')->pluck('name', 'id'); // Assuming City model exists

        return view('admin.orders.create', compact('customers', 'products', 'cities'));
    }

    // --- STORE NEW ORDER (ADMIN CREATED) ---
    public function store(Request $request)
    {
        // --- Validation ---
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'phone_number' => 'required|string|max:50',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'nullable|string|max:20',
            'special_mark' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled', 'in-check'])], // Initial status admin sets

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ], [
            'items.required' => 'Please add at least one product to the order.',
            'items.*.product_id.required' => 'A product selection is missing for an item.',
            'items.*.product_id.exists' => 'An invalid product was selected.',
            'items.*.quantity.required' => 'Quantity is missing for an item.',
            'items.*.quantity.min' => 'Quantity must be at least 1 for all items.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.orders.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        // --- Start Transaction ---
        DB::beginTransaction();
        try {
            $subtotal = 0;
            $orderItemsData = [];
            $productStockUpdates = [];

            // --- Calculate Totals & Prepare Items ---
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) { // Double check product exists
                    throw new \Exception("Product with ID {$item['product_id']} not found.");
                }

                // --- Stock Check ---
                if ($product->stock < $item['quantity']) {
                     DB::rollBack(); // Rollback before redirecting
                     return redirect()->route('admin.orders.create')
                                ->with('error', "Insufficient stock for product '{$product->name}'. Available: {$product->stock}, Requested: {$item['quantity']}.")
                                ->withInput();
                }

                $itemPrice = $product->sell_price; // Use current selling price
                $itemTotal = $itemPrice * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItemsData[] = [
                    // order_id will be set after order creation
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $itemPrice,
                    'total' => $itemTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Prepare stock update (decrement)
                 $productStockUpdates[] = ['id' => $product->id, 'decrement' => $item['quantity']];
            }

            // --- Delivery Fee ---
            $deliveryFeeAmount = DeliveryFee::where('city_id', $validated['city_id'])->value('amount');
            // Fetch default fee from settings/config if city-specific fee not found
            if ($deliveryFeeAmount === null) {
                 $deliveryFeeAmount = config('settings.default_delivery_fee', 15.00); // Example: Default 15.00
            }

            // --- Discount (Basic Example - Needs Refinement) ---
            // This is a placeholder. Real discount logic can be complex (codes, auto-apply).
            $discountAmount = 0.00;
            // Example: if(isset($validated['discount_code'])) { $discountAmount = calculate_discount(...); }

            // --- Final Total ---
            $total = ($subtotal - $discountAmount) + $deliveryFeeAmount;

            // --- Create Order ---
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'status' => $validated['status'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'delivery_fee' => $deliveryFeeAmount,
                'total' => $total,
                'phone_number' => $validated['phone_number'],
                'address_line_1' => $validated['address_line_1'],
                'address_line_2' => $validated['address_line_2'],
                'city_id' => $validated['city_id'],
                'postal_code' => $validated['postal_code'],
                'special_mark' => $validated['special_mark'],
                'notes' => $validated['notes'],
                'track_code' => 'AAM-' . strtoupper(Str::random(8)), // Generate unique track code
                'payment_method_code' => $request->input('payment_method_code', 'manual_admin'), // Example
            ]);

            // --- Assign order_id to items and insert ---
            foreach ($orderItemsData as &$itemData) {
                $itemData['order_id'] = $order->id;
            }
            OrderItem::insert($orderItemsData); // Bulk insert for efficiency

             // --- Update Stock ---
            foreach($productStockUpdates as $update) {
                Product::where('id', $update['id'])->decrement('stock', $update['decrement']);
                // Optionally check if stock reached lower_stock_warn and trigger notification
            }

            // --- Create Invoice Record ---
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV-' . $order->id . '-' . date('Ymd'), // Example invoice number
                'issue_date' => now()->toDateString(),
                'total_amount' => $order->total,
                'subtotal' => $order->total,
                'invoice_date' => now(),
                'delivery_fee' => $deliveryFeeAmount
                // Add due date, status etc. if needed
            ]);

             // --- Optional: Create Payment Record if applicable ---
             // Example: If admin marked as paid via Cash
             // if ($request->input('payment_status') === 'completed' && $request->input('payment_method_code') === 'cash') {
             //     $order->payment()->create([ 'payment_method_id' => ID_OF_CASH_METHOD, 'invoice_id' => $invoice->id, 'amount' => $order->total, 'status' => 'completed', 'transaction_id' => 'ADMIN_MANUAL_'.date('YmdHis') ]);
             // }

            // --- Commit Transaction ---
            DB::commit();

            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating order via admin: " . $e->getMessage() . ' :: ' . $e->getTraceAsString());
             return redirect()->route('admin.orders.create')
                         ->with('error', 'Failed to create order: ' . $e->getMessage())
                         ->withInput();
        }
    }

    // --- SHOW SINGLE ORDER ---
    public function show(Order $order)
    {
        // Load all necessary data for detailed view
        $order->load([
            'customer',
            'items.product:id,name,sku_code', // Select specific product columns
            'city:id,name',
            'payment.paymentMethod:id,name', // Load payment method name
            // 'delivery.deliveryPersonnel:id,name',
            // 'delivery.deliveryCompany:id,name',
            // 'invoice'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    // --- SHOW EDIT FORM ---
    public function edit(Order $order)
    {
        // Check if order is in an editable state (e.g., pending, processing)
        if (in_array($order->status, ['completed', 'cancelled'])) {
             return redirect()->route('admin.orders.show', $order->id)->with('warning', 'Cannot edit a completed or cancelled order.');
        }

        $order->load(['customer', 'items.product', 'city']); // Load necessary data

        // Data for dropdowns
        $statuses = Order::distinct()->pluck('status')->mapWithKeys(fn($status) => [$status => ucfirst($status)])->toArray();
        // Add logic to fetch available delivery personnel/companies if needed for assignment

        return view('admin.orders.edit', compact('order', 'statuses'));
    }

    // --- UPDATE ORDER ---
    public function update(Request $request, Order $order)
    {
        // Check if editable
        if (in_array($order->status, ['completed', 'cancelled'])) {
            return redirect()->route('admin.orders.show', $order->id)->with('warning', 'Cannot edit a completed or cancelled order.');
        }

        // --- Validation (Focus on editable fields) ---
         $validated = $request->validate([
            // Allow updating address/contact only if not shipped? Add logic based on status.
            'phone_number' => 'required|string|max:50',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'nullable|string|max:20',
            'special_mark' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'status' => ['required', Rule::in(['pending', 'processing', 'completed', 'cancelled', 'in-check'])],
             // Add validation for delivery fields if implementing assignment here
             // 'delivery_personnel_id' => 'nullable|exists:delivery_personnel,id',
             // 'tracking_number' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $originalStatus = $order->status;
            $newStatus = $validated['status'];

            // --- Update Order Core Info ---
            $order->update($validated);

            // --- Handle Status Change Side Effects ---
            if ($originalStatus !== $newStatus) {
                // If order is Cancelled, restore stock
                if ($newStatus === 'cancelled' && $originalStatus !== 'cancelled') {
                    foreach ($order->items as $item) {
                        Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                    }
                }
                // If order is un-cancelled, decrement stock (if coming from cancelled)
                elseif ($originalStatus === 'cancelled' && $newStatus !== 'cancelled') {
                     foreach ($order->items as $item) {
                        // Check stock again before decrementing!
                        $product = Product::find($item->product_id);
                        if($product && $product->stock >= $item->quantity){
                            $product->decrement('stock', $item->quantity);
                        } else {
                            // This situation needs handling - maybe prevent un-cancelling?
                            throw new \Exception("Insufficient stock to un-cancel order for product ID {$item->product_id}");
                        }
                    }
                }
                 // Add logic for other status changes (e.g., trigger notifications)
            }

            // --- Handle Delivery Info Update (If included in form/validation) ---
            // if ($request->filled('delivery_personnel_id') || $request->filled('tracking_number')) {
            //     $order->delivery()->updateOrCreate(
            //          ['order_id' => $order->id], // Find by order_id
            //          [
            //              'delivery_personnel_id' => $request->input('delivery_personnel_id'),
            //              'delivery_company_id' => $request->input('delivery_company_id'), // Needs getting company from personnel or separate input
            //              'tracking_number' => $request->input('tracking_number'),
            //              'status' => 'pending', // Or derive based on order status
            //              // delivery_date, confirmation_image updated later
            //          ]
            //     );
            // }

            DB::commit();
            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating order ID {$order->id}: " . $e->getMessage());
            return redirect()->route('admin.orders.edit', $order->id)
                         ->with('error', 'Failed to update order: ' . $e->getMessage())
                         ->withInput();
        }
    }

    // --- DELETE ORDER ---
    public function destroy(Order $order)
    {
        // Consider soft deletes instead of hard deletes for orders
        // if ($order->trashed()) { ... restore logic ... } else { $order->delete(); }

        // --- Prevent deleting completed orders? ---
        if ($order->status === 'completed') {
            return back()->with('error', 'Cannot delete a completed order. Consider cancelling or archiving.');
        }

        DB::beginTransaction();
        try {
             // Restore stock only if order wasn't already cancelled
             if ($order->status !== 'cancelled') {
                 foreach ($order->items as $item) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                 }
             }

            // Delete related items (payments, delivery, invoice will cascade if set up, otherwise delete manually)
            $order->items()->delete();
            // $order->payment()->delete();
            // $order->delivery()->delete();
            // $order->invoice()->delete();

            $order->delete(); // Hard delete

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting order ID {$order->id}: " . $e->getMessage());
            return redirect()->route('admin.orders.index')->with('error', 'Failed to delete order.');
        }
    }

    // --- SHOW INVOICE VIEW ---
    public function invoice(Order $order)
    {
         $order->load([
            'customer',
            'items.product:id,name,sku_code',
            'city:id,name',
            'invoice' // Load the invoice details
        ]);

        if (!$order->invoice) {
             // Optionally generate invoice here if it doesn't exist
             return redirect()->route('admin.orders.show', $order->id)->with('warning', 'Invoice not generated for this order yet.');
        }

        // Prepare data for the invoice view
        $viewData = [
            'order' => $order,
            'invoice' => $order->invoice,
            // Add company details (from settings/config)
            'company' => [
                'name' => config('app.name', 'AAM Store'),
                'address' => config('settings.company_address', 'Default Address'),
                'phone' => config('settings.company_phone', 'Default Phone'),
                'email' => config('settings.company_email', 'Default Email'),
                'logo_url' => asset(config('settings.company_logo', 'images/logo.png')),
            ]
        ];

        // --- Option 1: Show HTML Invoice View ---
        return view('admin.orders.invoice', $viewData);

        // --- Option 2: Generate PDF Invoice ---
        // Requires laravel-dompdf or similar: composer require barryvdh/laravel-dompdf
        // $pdf = Pdf::loadView('admin.orders.invoice_pdf', $viewData); // Use a separate PDF template
        // return $pdf->stream('invoice-'.$order->invoice->invoice_number.'.pdf'); // Stream to browser
        // return $pdf->download('invoice-'.$order->invoice->invoice_number.'.pdf'); // Force download
    }
}