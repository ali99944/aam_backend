<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ActionRequest;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Customer;
use App\Models\DeliveryFee;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderApiController extends Controller
{
    // Helper to get user/guest identifier (from CartController)
    private function getUserOrGuestIdentifier(Request $request): array
    {
        $guestToken = $request->header('X-Cart-Token');

        // Check for authenticated customer first (assuming Sanctum)
        $customer = $request->bearerToken() ? $request->user() : null; // Use your Sanctum guard name for customers

        return ['customer' => $customer, 'guestToken' => $guestToken];
    }

    /**
     * Get a list of orders for the authenticated customer.
     */
    public function index(Request $request)
    {
        // Use the 'customer' guard to get the authenticated customer
        $customer = $request->user();

        // Fetch orders for the authenticated customer
        $orders = Order::with('items.product')
                       ->where('customer_id', $customer->id)
                       ->get();

        // Return orders as a resource collection
        return response()->json($orders);
    }

    /**
     * Get a list of orders for the authenticated customer.
     */
    public function get(Request $request, $id)
    {
        // Use the 'customer' guard to get the authenticated customer
        $customer = $request->user();

        // Fetch orders for the authenticated customer
        $order = Order::with('items.product')
                       ->where('customer_id', $customer->id)
                       ->where('id', $id)
                       ->get()
                       ->first();
        if(!$order) {
            return response()->json([
                'message' => 'لم يتم العثور علي طلب بهذا المعرف'
            ], 404);
        }

        // Return orders as a resource collection
        return response()->json($order);
    }


    /**
     * Creates an order with 'in-check' status and submits an ActionRequest for approval.
     */
    public function store(Request $request)
    {
        ['customer' => $customer, 'guestToken' => $guestToken] = $this->getUserOrGuestIdentifier($request);

        // --- Step 1: Validation (remains mostly the same) ---
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:50',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city_id' => 'required|integer|exists:cities,id',
            'payment_method_code' => ['required', 'string'], // Example codes
            'guest_name' => ['required_if:customer_id,null', 'nullable', 'string', 'max:255'],
            'guest_email' => ['required_if:customer_id,null', 'nullable', 'email', 'max:255'],
            // Remove 'notes', 'special_mark', 'postal_code' from required validation if optional
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $validated = $validator->validated();

        // --- Step 2: Fetch and Validate Cart ---
        $cartItems = CartItem::with('product')
                            ->forUserOrGuest($customer, $guestToken)
                            ->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }
        // ... (Stock check logic remains the same from previous controller) ...
        $subtotal = 0;
        $orderItemsData = [];
        foreach ($cartItems as $item) { /* ... same calculation logic ... */
            if ($item->product->stock < $item->quantity) {
                 return response()->json(['message' => "Insufficient stock for '{$item->product->name}'. Available: {$item->product->stock}."], 400);
            }
             $itemPrice = $item->product->sell_price;
             $lineTotal = round($itemPrice * $item->quantity, 2);
             $subtotal += $lineTotal;
             $orderItemsData[] = new OrderItem([ // Prepare models, don't insert yet
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $itemPrice,
                'total' => $lineTotal,
             ]);
        }

        // --- Step 3: Calculate Totals ---
        $deliveryFee = DeliveryFee::where('city_id', $validated['city_id'])->where('is_active', true)->value('amount')
                       ?? config('settings.default_delivery_fee', 15.00);
        $discountAmount = 0.00; // Placeholder for future logic
        $total = round($subtotal + $deliveryFee - $discountAmount, 2);


        // --- Step 4: Create Order & Action Request in a Transaction ---
        DB::beginTransaction();
        try {
            // A. Create the Order with 'in-check' status
            $order = Order::create([
                'customer_id' => $customer?->id,
                'guest_name' => $customer ? null : $validated['guest_name'],
                'guest_email' => $customer ? null : $validated['guest_email'],
                'order_status' => Order::ORDER_STATUS_IN_CHECK, // <<< IMPORTANT: New initial status
                'delivery_status' => Order::DELIVERY_STATUS_PENDING,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method_code' => $validated['payment_method_code'],
                'track_code' => Order::generateTrackCode(),
                'phone_number' => $validated['phone_number'],
                'address_line_1' => $validated['address_line_1'],
                'address_line_2' => $request->input('address_line_2'),
                'city_id' => $validated['city_id'],
                'postal_code' => $request->input('postal_code'),
                'special_mark' => $request->input('special_mark'),
                'notes' => $request->input('notes'),
            ]);

            // B. Save the order items associated with the new order
            $order->items()->saveMany($orderItemsData);

            // C. Create the ActionRequest referencing the new Order
            // Using the polymorphic relationship
            $order->creationRequest()->create([
                'action_type' => ActionRequest::TYPE_ORDER_CREATE_REQUEST,
                'status' => ActionRequest::STATUS_PENDING,
                'requested_by_user_id' => $customer?->id, // If it's a customer
                'data' => null // Data is now in the order itself, not needed here
            ]);

            // D. Decrement Stock
            foreach ($cartItems as $item) {
                Product::where('id', $item->product_id)->decrement('stock', $item->quantity);
            }

            // E. Clear the user's/guest's cart
             CartItem::query()->forUserOrGuest($customer, $guestToken)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Your order has been placed successfully and is pending review.',
                'track_code' => $order->track_code,
                ], 201); // 201 Created

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("API Order creation error: " . $e->getMessage(), ['data' => $validated]);
            return response()->json(['message' => 'An error occurred while placing your order. Please try again.'], 500);
        }
    }


    /**
     * Get order details using the unique track code.
     * Accessible by anyone with the code (guest or customer).
     */
    public function showByTrackCode(string $trackCode)
    {
        $order = Order::with([
                        'customer', // Include basic customer info
                        'items.product' => fn($q) => $q->select('id', 'name', 'main_image'), // Select only needed product fields
                        'delivery', // Include delivery info if available
                        'paymentMethod', // Include payment method name
                        'city', // Include city name
                        // Exclude sensitive details like full payment records maybe
                        ])
                      ->where('track_code', $trackCode)
                      ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

         // Check if user owns the order if they are authenticated
         $customer = request()->user();
         if ($customer && $order->customer_id !== $customer->id && $order->customer_id !== null) {
             // Allow viewing guest orders? Maybe not.
             // return response()->json(['message' => 'Forbidden'], 403);
         }


        return response()->json($order); // Create OrderResource to format output
    }

    /**
     * Request cancellation of an order (by authenticated customer).
     */
    public function requestCancellation(Request $request, Order $order) // Route model binding
    {
        /** @var Customer $customer */
        $customer = $request->user();

        // Authorization: Ensure the authenticated customer owns this order
        if (!$customer || $order->customer_id !== $customer->id) {
            return response()->json(['message' => 'Unauthorized to cancel this order.'], 403);
        }

        // Check if order is eligible for cancellation request
        // Allow cancelling 'pending' or 'in-check', maybe 'processing'? Define rules.
        if (!in_array($order->status, [Order::ORDER_STATUS_PENDING, Order::ORDER_STATUS_IN_CHECK, Order::ORDER_STATUS_PROCESSING])) {
             return response()->json(['message' => "Order cannot be cancelled in its current status ('{$order->status}')."], 400);
        }

        // Check if a cancellation request already exists and is pending
        $existingRequest = ActionRequest::where('action_type', ActionRequest::TYPE_ORDER_CANCEL_REQUEST)
                                        ->where('status', ActionRequest::STATUS_PENDING)
                                        ->whereJsonContains('data->order_id', $order->id) // Check inside JSON data
                                        ->exists();
        if ($existingRequest) {
             return response()->json(['message' => 'A cancellation request for this order is already pending.'], 409); // Conflict
        }

        // --- Prepare Data for Action Request ---
        $actionRequestData = [
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'reason' => $request->input('reason', 'Customer request'), // Optional reason from request body
        ];

        // --- Create Action Request ---
        try {
             $actionRequest = ActionRequest::create([
                'action_type' => ActionRequest::TYPE_ORDER_CANCEL_REQUEST,
                'data' => $actionRequestData,
                'status' => ActionRequest::STATUS_PENDING,
                'requested_by_user_id' => $customer->id,
            ]);

            return response()->json([
                'message' => 'Your order cancellation request has been submitted and is pending review.',
                'request_id' => $actionRequest->id
                ], 202); // 202 Accepted

        } catch (\Exception $e) {
             Log::error("API Order cancellation request error for order {$order->id}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to submit cancellation request. Please try again later.'], 500);
        }
    }
}