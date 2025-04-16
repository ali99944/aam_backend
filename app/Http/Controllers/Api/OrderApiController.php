<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ActionRequest;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Customer;
use App\Models\DeliveryFee;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\ActionRequestService; // To potentially process cancellation immediately? No, use ActionRequest.
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Resources\OrderResource; // Create this resource

class OrderApiController extends Controller
{
    // Helper to get user/guest identifier (from CartController)
    private function getUserOrGuestIdentifier(Request $request): array
    {
        $guestToken = $request->header('X-Cart-Token');

        // Check for authenticated customer first (assuming Sanctum)
        $customer = $request->bearerToken() ? $request->user('customer') : null; // Use your Sanctum guard name for customers

        return ['customer' => $customer, 'guestToken' => $guestToken];
    }

    /**
     * Submit an order creation request.
     * Creates an ActionRequest for admin approval.
     */
    public function store(Request $request)
    {
        ['customer' => $customer, 'guestToken' => $guestToken] = $this->getUserOrGuestIdentifier($request);

        // --- Validation ---
        $validator = Validator::make($request->all(), [
            // Address Details
            'phone_number' => 'required|string|max:50',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city_id' => 'required|integer|exists:cities,id', // Validate city exists
            'postal_code' => 'nullable|string|max:20',
            'special_mark' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            // Payment
            'payment_method_code' => ['required', 'string', Rule::exists('payment_methods', 'code')->where('is_enabled', true)], // Validate enabled payment method
            // Guest Info (Required if no authenticated customer)
            'guest_name' => ['required_without:customer', 'nullable', 'string', 'max:255'],
            'guest_email' => ['required_without:customer', 'nullable', 'email', 'max:255'],
        ], [
            'payment_method_code.exists' => 'Selected payment method is invalid or disabled.',
            'guest_name.required_without' => 'Name is required for guest checkout.',
            'guest_email.required_without' => 'Email is required for guest checkout.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $validated = $validator->validated();

        // --- Identify Customer or Guest ---
        $customerId = $customer?->id;
        $customerName = $customer?->name ?? $validated['guest_name']; // Use guest name if provided
        $customerEmail = $customer?->email ?? $validated['guest_email']; // Use guest email if provided

        // --- Fetch Cart & Validate Items ---
        $cartItems = CartItem::with('product')
                            ->forUserOrGuest($customer, $guestToken)
                            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 400);
        }

        $orderItemsData = [];
        $subtotal = 0;
        $errors = [];

        foreach ($cartItems as $item) {
            if (!$item->product || !$item->product->is_public || $item->product->status !== Product::STATUS_ACTIVE) {
                // $errors[] = "Product '{$item->product->name ?? 'ID:'.$item->product_id}' is no longer available.";
                continue; // Skip unavailable items
            }
            if ($item->product->stock < $item->quantity) {
                 $errors[] = "Insufficient stock for '{$item->product->name}'. Available: {$item->product->stock}.";
                 continue; // Skip items with insufficient stock
            }
            $itemPrice = $item->product->sell_price; // Apply discount logic here later if needed
            $lineTotal = round($itemPrice * $item->quantity, 2);
            $subtotal += $lineTotal;

            $orderItemsData[] = [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $itemPrice, // Price per unit at time of order
                'total' => $lineTotal,
                'product_name' => $item->product->name, // Store name for reference
                'product_sku' => $item->product->sku_code, // Store SKU
            ];
        }

        if (!empty($errors)) {
             return response()->json(['message' => 'Some items in your cart are unavailable or have insufficient stock.', 'errors' => $errors], 400);
        }

        // --- Calculate Delivery Fee ---
        $city = City::find($validated['city_id']);
        $deliveryFee = DeliveryFee::where('city_id', $city->id)->where('is_active', true)->value('amount');
        if ($deliveryFee === null) {
            // Fetch default fee from config/settings
            $deliveryFee = config('app.default_delivery_fee', 5.00); // Example fallback
        }
        $deliveryFee = round($deliveryFee, 2);

        // --- Calculate Totals (Basic - Add discount/tax logic later) ---
        $discountAmount = 0.00; // Placeholder
        $total = round($subtotal + $deliveryFee - $discountAmount, 2);

        // --- Prepare Data for Action Request ---
        $actionRequestData = [
            'customer_info' => [
                'id' => $customerId, // Can be null for guests
                'name' => $customerName,
                'email' => $customerEmail,
                'phone' => $validated['phone_number'],
                'guest_cart_token' => $guestToken // Include guest token if applicable
            ],
            'shipping_address' => [
                'address_line_1' => $validated['address_line_1'],
                'address_line_2' => $validated['address_line_2'] ?? null,
                'city_id' => $validated['city_id'],
                'city_name' => $city->name ?? 'Unknown City', // Store city name too
                'postal_code' => $validated['postal_code'] ?? null,
                'special_mark' => $validated['special_mark'] ?? null,
            ],
            'order_summary' => [
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'payment_method_code' => $validated['payment_method_code'],
                'notes' => $validated['notes'] ?? null,
            ],
            'items' => $orderItemsData,
        ];

        // --- Create Action Request ---
         DB::beginTransaction(); // Wrap in transaction
        try {
            $actionRequest = ActionRequest::create([
                'action_type' => ActionRequest::TYPE_ORDER_CREATE_REQUEST,
                'data' => $actionRequestData, // Store the prepared data array
                'status' => ActionRequest::STATUS_PENDING,
                'requested_by_user_id' => $customerId, // Link to customer if exists
            ]);

             // IMPORTANT: Do NOT clear the cart here. Clear it only after the request is APPROVED by admin.

             DB::commit();
             // Maybe return a temporary tracking ID or the action request ID
            return response()->json([
                'message' => 'Your order request has been submitted and is pending review.',
                'request_id' => $actionRequest->id, // Send back request ID
                'estimated_delivery_time' => $city->deliveryFee?->estimated_delivery_time ?? config('app.default_delivery_time', '2-4 days') // Provide estimate
                ], 202); // 202 Accepted

        } catch (\Exception $e) {
             DB::rollBack();
             Log::error("API Order submission error: " . $e->getMessage(), ['data' => $validated]);
            return response()->json(['message' => 'Failed to submit order request. Please try again later.'], 500);
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
         $customer = request()->user('customer');
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
        $customer = $request->user('customer');

        // Authorization: Ensure the authenticated customer owns this order
        if (!$customer || $order->customer_id !== $customer->id) {
            return response()->json(['message' => 'Unauthorized to cancel this order.'], 403);
        }

        // Check if order is eligible for cancellation request
        // Allow cancelling 'pending' or 'in-check', maybe 'processing'? Define rules.
        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_IN_CHECK, Order::STATUS_PROCESSING])) {
             return response()->json(['message' => "Order cannot be cancelled in its current status ('{$order->status}')."], 400);
        }

        // Check if a cancellation request already exists and is pending
        $existingRequest = ActionRequest::where('action_type', ActionRequest::TYPE_ORDER_CANCEL)
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
                'action_type' => ActionRequest::TYPE_ORDER_CANCEL,
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