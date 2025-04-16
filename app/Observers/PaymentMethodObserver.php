<?php
namespace App\Observers;

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB; // Use DB facade for efficiency

class PaymentMethodObserver
{
    /**
     * Handle the PaymentMethod "saving" event.
     * Ensure only one payment method can be the default.
     *
     * @param  \App\Models\PaymentMethod  $paymentMethod
     * @return void
     */
    public function saving(PaymentMethod $paymentMethod): void
    {
        // Check if is_default is being set to true and has changed
        if ($paymentMethod->isDirty('is_default') && $paymentMethod->is_default === true) {
            // Use DB update for efficiency - set all others to false
            DB::table('payment_methods')
              ->where('id', '!=', $paymentMethod->id) // Exclude the current model
              ->where('is_default', true) // Only update those currently default
              ->update(['is_default' => false]);
        }

        // Optional: Prevent setting is_default to false if it's the *only* enabled method?
        // if ($paymentMethod->isDirty('is_default') && $paymentMethod->is_default === false) {
        //     $otherDefaultsExist = PaymentMethod::where('id', '!=', $paymentMethod->id)->where('is_default', true)->exists();
        //     if (!$otherDefaultsExist) {
        //          // Optionally throw exception or revert change? Depends on requirements.
        //         // $paymentMethod->is_default = true; // Revert back
        //     }
        // }
    }
}