<?php
namespace App\Services;

use App\Models\ActionRequest;
use App\Models\CartItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Order;
use App\Models\User; // Or Customer
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Invoice; // Assuming invoice generation logic
use App\Models\Payment; // Assuming payment creation logic
use App\Notifications\OrderPlaced; // Example Notification
use App\Notifications\OrderCancelled; // Example Notification
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification; // If sending notifications
use Illuminate\Support\Str;


class ActionRequestService
{

    public function executeAction(ActionRequest $requestModel): bool
    {
        $data = $requestModel->data; // Decoded JSON data

        switch ($requestModel->action_type) {
            // --- Add Order Create Handler ---
            case ActionRequest::TYPE_ORDER_CREATE_REQUEST:
                return $this->handleOrderCreateRequest($data, $requestModel);

            // --- Rename Order Cancel Handler ---
            case ActionRequest::TYPE_ORDER_CANCEL:
                return $this->handleOrderCancelRequest($data); // Pass $requestModel if needed

            default:
                throw new \Exception("Unknown action type: '{$requestModel->action_type}' for request #{$requestModel->id}.");
        }
    }


    // --- New Handler: Order Create Request ---
    private function handleOrderCreateRequest(array $data, ActionRequest $actionRequest): bool
    {
        Log::debug("Handling order_create_request", $data);

        // --- Basic Data Validation ---
        if (empty($data['items']) || empty($data['shipping_address']) || empty($data['order_summary'])) {
            Log::error("Missing required data sections (items, shipping_address, order_summary) in order_create_request.", $data);
            throw new \InvalidArgumentException("Incomplete order creation data in request #{$actionRequest->id}.");
        }

        // --- Re-Validate Stock just before creation (critical) ---
        foreach ($data['items'] as $itemData) {
             $product = Product::find($itemData['product_id']);
             if (!$product || $product->stock < $itemData['quantity']) {
                  throw new \Exception("Stock level changed for product SKU {$itemData['product_sku']} - cannot create order.");
             }
        }

        // --- Find or Create Customer (if guest) ---
        $customer = null;
        if (!empty($data['customer_info']['id'])) {
             $customer = Customer::find($data['customer_info']['id']);
        } elseif (!empty($data['customer_info']['email'])) {
            // Find existing guest by email or create new one? Decide strategy.
            // For now, let's assume guest info is just stored on the order directly if customer_id is null
            // OR create a basic customer record
            $customer = Customer::firstOrCreate(
                 ['email' => $data['customer_info']['email']],
                 ['name' => $data['customer_info']['name'] ?? 'Guest Customer', /* maybe generate temp password? */]
            );
             Log::info("Created or found customer ID {$customer->id} for guest order.");
        }
        if(!$customer) {
             // This shouldn't happen if validation passed, but safety check
              throw new \Exception("Could not determine customer for order request #{$actionRequest->id}.");
        }


        // --- Create the Actual Order ---
        $order = Order::create([
            'customer_id' => $customer->id,
            'phone_number' => $data['customer_info']['phone'],
            'address_line_1' => $data['shipping_address']['address_line_1'],
            'address_line_2' => $data['shipping_address']['address_line_2'] ?? null,
            'city_id' => $data['shipping_address']['city_id'],
            'postal_code' => $data['shipping_address']['postal_code'] ?? null,
            'special_mark' => $data['shipping_address']['special_mark'] ?? null,
            'notes' => $data['order_summary']['notes'] ?? null,
            'status' => Order::STATUS_PROCESSING, // Set initial status after approval
            'subtotal' => $data['order_summary']['subtotal'],
            'discount_amount' => $data['order_summary']['discount_amount'],
            'delivery_fee' => $data['order_summary']['delivery_fee'],
            'total' => $data['order_summary']['total'],
            'payment_method_code' => $data['order_summary']['payment_method_code'],
            'track_code' => Order::generateTrackCode(), // Generate track code now
        ]);

        // --- Create Order Items & Deduct Stock ---
        foreach ($data['items'] as $itemData) {
             $order->items()->create([
                 'product_id' => $itemData['product_id'],
                 'quantity' => $itemData['quantity'],
                 'price' => $itemData['price'],
                 'total' => $itemData['total'],
             ]);
             // Deduct stock
             Product::where('id', $itemData['product_id'])->decrement('stock', $itemData['quantity']);
             Log::debug("Decremented stock for product {$itemData['product_id']} by {$itemData['quantity']}");
        }

        // --- Create Invoice ---
         // Add logic here to generate invoice number, dates etc.
        $invoice = $order->invoice()->create([
             'invoice_number' => 'INV-' . date('Y') . '-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
             'invoice_date' => now()->toDateString(),
             'subtotal' => $order->subtotal,
             'tax_amount' => 0.00, // Calculate tax if needed
             'discount_amount' => $order->discount_amount,
             'delivery_fee' => $order->delivery_fee,
             'total_amount' => $order->total,
             'status' => Invoice::STATUS_SENT, // Or 'paid' if payment instant?
             'notes' => $data['order_summary']['notes'],
        ]);
         Log::info("Created invoice #{$invoice->invoice_number} for Order ID {$order->id}");

        // --- Create Initial Pending Payment Record ---
        $paymentMethod = PaymentMethod::where('code', $order->payment_method_code)->first();
        if ($paymentMethod) {
             $order->payments()->create([
                 'invoice_id' => $invoice->id, // Link payment to invoice
                 'payment_method_id' => $paymentMethod->id,
                 'amount' => $order->total,
                 'status' => Payment::STATUS_PENDING, // Payment is pending initially
                 'transaction_id' => null,
             ]);
             Log::info("Created pending payment record for Order ID {$order->id}");
        } else {
             Log::warning("Could not find payment method for code {$order->payment_method_code} for Order ID {$order->id}");
        }


        // --- Clear User's Cart (or guest cart) ---
        $guestToken = $data['customer_info']['guest_cart_token'] ?? null;
        CartItem::forUserOrGuest($customer, $guestToken)->delete();
        Log::info("Cleared cart for Customer ID {$customer->id} / Guest Token {$guestToken}");


        // --- Optional: Send Order Confirmation Notification ---
         // Notification::send($customer, new OrderPlaced($order));

        Log::info("Order ID {$order->id} created successfully from Action Request #{$actionRequest->id}.");
        return true;
    }


     // --- Renamed Handler: Order Cancel Request ---
     private function handleOrderCancelRequest(array $data): bool
     {
         Log::debug("Handling order_cancel_request", $data);
         if (!isset($data['order_id'])) {
             Log::error("Missing order_id in order_cancel_request data.");
             return false;
         }

         $order = Order::find($data['order_id']);
         if (!$order) {
              Log::error("Order not found for ID: {$data['order_id']} in order_cancel_request.");
             return false;
         }

         // Check if order can be cancelled (Admin approval overrides previous checks maybe?)
         if (in_array($order->status, [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])) {
             Log::warning("Order ID {$order->id} cannot be cancelled by request, status is {$order->status}.");
             return $order->status === Order::STATUS_CANCELLED; // Return true if already cancelled
         }

         // --- Add Cancellation Logic (Admin Approved) ---
         // - Update order status
         // - Reinstate stock? (Careful!)
         // - Void invoice
         // - Trigger refund process? (Needs separate handling)
         // -----------------------------
         $order->status = Order::STATUS_CANCELLED;
         // Add cancellation reason if provided in $data['reason']? Maybe store on order?
         // $order->cancellation_reason = $data['reason'] ?? 'Approved cancellation request';
         $order->save();

         // --- Reinstate Stock ---
          foreach($order->items as $item) {
              Product::where('id', $item->product_id)->increment('stock', $item->quantity);
               Log::debug("Reinstated stock for product {$item->product_id} by {$item->quantity}");
          }

          // --- Void Invoice ---
          if ($order->invoice) {
              $order->invoice->update(['status' => Invoice::STATUS_VOID]);
          }

          // --- Trigger refund process notification? ---
          // Send notification to finance team?

          Log::info("Order ID {$order->id} cancelled via approved Action Request.");
          return true;
     }

    // ... existing handleProductUpdate, handleUserVerify ...
}