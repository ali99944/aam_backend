<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    /**
     * Mark a payment as completed (e.g., cash received).
     */
    public function confirm(Request $request, Payment $payment)
    {
        // Add authorization checks to ensure only an admin can do this.

        if ($payment->status === 'completed') {
            return response()->json(['message' => 'Payment is already marked as completed.'], 422);
        }

        $payment->update([
            'status' => 'completed',
            'transaction_id' => 'CASH-' . $payment->id . '-' . now()->timestamp, // A reference for the cash collection
        ]);

        // Update related records
        $payment->invoice()->update(['status' => 'paid']);
        // Optional: Update order status if needed
        // $payment->order()->update(['order_status' => 'completed']);

        return response()->json(['message' => 'Payment confirmed successfully.']);
    }
}