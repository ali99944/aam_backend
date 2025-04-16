<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Models\CartItem;
use App\Models\Customer; // Your Customer model
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; // For random generation

class CartController extends Controller
{
    // Helper to get authenticated customer or guest token
    private function getUserOrGuestIdentifier(Request $request): array
    {
        $guestToken = $request->header('X-Cart-Token');

        // Check for authenticated customer first (assuming Sanctum)
        $customer = $request->bearerToken() ? $request->user('customer') : null; // Use your Sanctum guard name for customers

        return ['customer' => $customer, 'guestToken' => $guestToken];
    }

    /**
     * Display the user's or guest's cart items.
     */
    public function index(Request $request)
    {
        ['customer' => $customer, 'guestToken' => $guestToken] = $this->getUserOrGuestIdentifier($request);

        if (!$customer && !$guestToken) {
            // Return empty cart if no identifier provided
            return response()->json(['data' => [], 'guest_cart_token' => null, 'total' => 0.00]);
            // Or return 401/403 if identification is always required to view any cart
            // return response()->json(['message' => 'Cart identifier missing.'], 401);
        }

        $cartItems = CartItem::with('product')
                            ->forUserOrGuest($customer, $guestToken)
                            ->get();

        $total = $cartItems->sum(function ($item) {
             return round(($item->product->sell_price ?? 0) * $item->quantity, 2);
        });

        return response()->json([
             'data' => CartItemResource::collection($cartItems),
             'guest_cart_token' => $guestToken, // Return token so frontend can store it
             'total' => round($total, 2)
        ]);
    }

    /**
     * Add an item to the cart.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            // 'addons_data' => 'nullable|array', // If using addons
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        $product = Product::find($productId);

        // --- Check Product Status & Stock ---
        if (!$product || !$product->is_public || $product->status !== Product::STATUS_ACTIVE) {
            return response()->json(['message' => 'Product is not available.'], 404);
        }
        if ($product->stock < $quantity) {
            return response()->json(['message' => 'Insufficient stock available.', 'available_stock' => $product->stock], 400);
        }

        ['customer' => $customer, 'guestToken' => $guestToken] = $this->getUserOrGuestIdentifier($request);

        $newGuestToken = null;
        if (!$customer && !$guestToken) {
            // Generate a new token for a new guest cart
            $guestToken = (string) Str::random();
            $newGuestToken = $guestToken; // Flag to return the new token
        }

        // --- Find or Create Cart Item ---
        $cartItem = CartItem::query()
            ->forUserOrGuest($customer, $guestToken)
            ->where('product_id', $productId)
            // Add addon checks here if necessary to treat items with different addons as distinct
            ->first();

        if ($cartItem) {
            // Item exists, update quantity
            $newQuantity = $cartItem->quantity + $quantity;
             if ($product->stock < $newQuantity) {
                return response()->json(['message' => 'Insufficient stock to add requested quantity.', 'available_stock' => $product->stock - $cartItem->quantity], 400);
            }
            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {
            // Item doesn't exist, create new entry
            $cartItemData = [
                'product_id' => $productId,
                'quantity' => $quantity,
                // 'addons_data' => $validated['addons_data'] ?? null,
                // 'price_at_add' => $product->sell_price, // Store price if needed
            ];
            if ($customer) {
                $cartItemData['customer_id'] = $customer->id;
            } else {
                $cartItemData['guest_cart_token'] = $guestToken;
            }
            $cartItem = CartItem::create($cartItemData);
        }

        // --- Return Response ---
        // Optionally reload the entire cart or just return the added/updated item
        // Reloading cart provides updated total
        return $this->index($request)->setStatusCode(201); // Use 201 for successful creation/addition
         // Or return just the item:
        // return response()->json([
        //     'message' => 'Item added to cart.',
        //     'item' => new CartItemResource($cartItem->load('product')),
        //     'guest_cart_token' => $newGuestToken // Only send if newly generated
        // ], 201);
    }

    /**
     * Update the quantity of a specific cart item.
     */
    public function update(Request $request, $cartItemId) // Pass Cart Item ID
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $quantity = $request->input('quantity');
        ['customer' => $customer, 'guestToken' => $guestToken] = $this->getUserOrGuestIdentifier($request);

        $cartItem = CartItem::query()
            ->forUserOrGuest($customer, $guestToken)
            ->find($cartItemId);

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found.'], 404);
        }

        $product = $cartItem->product; // Get related product
        if (!$product) {
             $cartItem->delete(); // Clean up orphan cart item
             return response()->json(['message' => 'Associated product not found.'], 404);
        }

        // Check stock
        if ($product->stock < $quantity) {
             return response()->json(['message' => 'Insufficient stock available.', 'available_stock' => $product->stock], 400);
        }

        $cartItem->quantity = $quantity;
        $cartItem->save();

         // Return updated cart
        return $this->index($request);
         // Or return just the item
         // return new CartItemResource($cartItem->load('product'));
    }

    /**
     * Remove a specific item from the cart.
     */
    public function destroy(Request $request, $cartItemId)
    {
        ['customer' => $customer, 'guestToken' => $guestToken] = $this->getUserOrGuestIdentifier($request);

        $cartItem = CartItem::query()
            ->forUserOrGuest($customer, $guestToken)
            ->find($cartItemId);

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found.'], 404);
        }

        $cartItem->delete();

         // Return updated cart or just success
        return $this->index($request);
        // return response()->json(null, 204); // No Content
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(Request $request)
    {
         ['customer' => $customer, 'guestToken' => $guestToken] = $this->getUserOrGuestIdentifier($request);

         if (!$customer && !$guestToken) {
             return response()->json(['message' => 'Cart identifier missing.'], 401);
         }

         CartItem::query()
            ->forUserOrGuest($customer, $guestToken)
            ->delete();

        return response()->json(['data' => [], 'guest_cart_token' => $guestToken, 'total' => 0.00]);
        // return response()->json(null, 204);
    }

     /**
      * Associate a guest cart with a logged-in customer.
      * This should ideally be called *after* successful login.
      */
     public function mergeGuestCart(Request $request)
     {
         // Customer MUST be authenticated here
         $customer = $request->user('customer');
         if (!$customer) {
             return response()->json(['message' => 'Unauthenticated.'], 401);
         }

         $guestToken = $request->header('X-Cart-Token');
         if (!$guestToken) {
             return response()->json(['message' => 'Guest cart token missing.'], 400);
         }

         // Find guest cart items
         $guestItems = CartItem::where('guest_cart_token', $guestToken)
                               ->whereNull('customer_id')
                               ->get();

         if ($guestItems->isEmpty()) {
             return response()->json(['message' => 'No guest cart found or cart is empty.'], 200);
         }

         foreach ($guestItems as $guestItem) {
             // Check if the same product already exists in the customer's cart
             $customerItem = CartItem::where('customer_id', $customer->id)
                                     ->where('product_id', $guestItem->product_id)
                                     // Add addon checks if needed
                                     ->first();

             if ($customerItem) {
                 // Merge quantities (check stock)
                 $product = $guestItem->product; // Assuming product exists
                 $newQuantity = $customerItem->quantity + $guestItem->quantity;
                 if ($product && $product->stock >= $newQuantity) {
                     $customerItem->quantity = $newQuantity;
                     $customerItem->save();
                 }
                 // Delete the guest item after merging (or attempting to)
                 $guestItem->delete();
             } else {
                 // Just associate the guest item with the customer
                 $guestItem->update([
                     'customer_id' => $customer->id,
                     'guest_cart_token' => null // Remove guest token association
                 ]);
             }
         }

         return $this->index($request)->setStatusCode(200); // Return the merged cart
     }
}