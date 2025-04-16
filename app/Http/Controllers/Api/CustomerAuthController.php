<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer; // Your Customer model
use App\Models\Otp;
use App\Notifications\SendOtpNotification; // Create this notification
use App\Notifications\PasswordResetSuccessNotification; // Create this notification
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class CustomerAuthController extends Controller
{
    protected $otpExpiryMinutes = 10; // How long OTPs are valid

    /**
     * Register a new customer.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email', // Ensure unique in customers table
            'phone' => 'required|string|max:50|unique:customers,phone',   // Ensure unique in customers table
            'password' => ['required', 'confirmed', Password::min(8)], // Use default strong password rules
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'status' => Customer::STATUS_ACTIVE, // Or STATUS_VERIFICATION_REQUIRED if email verification needed
            ]);

            // --- Optional: Send Email Verification ---
            // if ($customer->status === Customer::STATUS_VERIFICATION_REQUIRED) {
            //    $this->sendVerificationOtp($customer->email); // Or send a verification link
            // }

            DB::commit();

            return response()->json([
                'message' => 'Registration successful.',
                'customer' => $customer->only('id', 'name', 'email', 'phone'), // Return basic info
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Customer Registration Failed: " . $e->getMessage());
            return response()->json(['message' => 'Registration failed. Please try again later.'], 500);
        }
    }

    /**
     * Authenticate a customer and return a Sanctum token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // // Use the customer guard
        // $customer = Customer::where('email', $request->email)->first();

        // if (!$customer) {
        //      return response()->json(['message' => 'Invalid credentials.'], 401);
        // }


        if (!Auth::guard('customer')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => 401
            ], 401);
        }


        $customer = Auth::guard('customer')->user();



        // --- Check if customer is banned or inactive ---
        if ($customer->status === Customer::STATUS_BANNED || $customer->is_banned) {
            return response()->json(['message' => 'Your account has been suspended.', 'reason' => $customer->ban_reason], 403);
        }
         if ($customer->status !== Customer::STATUS_ACTIVE) {
             // Optional: Differentiate other inactive statuses if needed
             // return response()->json(['message' => 'Account requires verification.'], 403);
         }


        // --- Revoke old tokens (optional, good practice) ---
        // $customer->tokens()->delete();

        // --- Issue new token ---
        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'customer' => $customer->only('id', 'name', 'email', 'phone'), // Basic info
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Request a password reset OTP.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:customers,email', // Ensure email exists
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;

        try {
            $otpCode = $this->generateAndStoreOtp($email, 'password_reset');

            // --- Send OTP via Notification ---
            // Use ShouldQueue if sending emails takes time
            Notification::route('mail', $email)
                        ->notify(new SendOtpNotification($otpCode));

            return response()->json(['message' => 'Password reset OTP sent to your email.']);

        } catch (\Exception $e) {
            Log::error("Forgot Password OTP Send Failed for {$email}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to send OTP. Please try again later.'], 500);
        }
    }

    /**
     * Verify the provided OTP.
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email', // Or use another identifier if needed
            'otp' => 'required|string|digits:6', // Assuming 6-digit OTP
            'purpose' => 'required|string|in:password_reset,email_verification', // Define expected purposes
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $otpCode = $request->otp;
        $purpose = $request->purpose;

        $otpRecord = Otp::where('identifier', $email)
                        ->where('purpose', $purpose)
                        ->whereNull('verified_at') // Only non-verified OTPs
                        ->latest() // Get the most recent OTP for this purpose
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'OTP not found or already used.'], 404);
        }

        if (!$otpRecord->isValid()) {
            return response()->json(['message' => 'OTP has expired.'], 400);
        }

        if ($otpRecord->code !== $otpCode) {
             return response()->json(['message' => 'Invalid OTP code.'], 400);
        }

        // --- Mark OTP as verified ---
        $otpRecord->update(['verified_at' => now()]);

        // --- Generate a temporary token/flag for the next step (reset password) ---
        // Store this securely, maybe associated with the OTP record ID or email, with short expiry
        // For simplicity here, we'll just return success. The resetPassword endpoint
        // should re-verify that *a recent* OTP for this purpose was verified if needed.
        // Or better: Issue a signed URL or a proper reset token now.
        // Let's return a simple success for now.

        return response()->json(['message' => 'OTP verified successfully.']);
    }


    /**
     * Reset the customer's password after OTP verification.
     *
     * Security Note: This endpoint assumes `verifyOtp` was called *recently*.
     * A more secure flow involves `verifyOtp` returning a secure, single-use token
     * which is then required by *this* endpoint.
     */
    public function resetPassword(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:customers,email',
            'otp' => 'required|string|digits:6', // Re-validate OTP or use the secure token from verifyOtp
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

         if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $otpCode = $request->otp;

        // --- Re-verify a *RECENTLY VERIFIED* OTP ---
        // This is crucial if not using a dedicated reset token.
        $verifiedOtp = Otp::where('identifier', $email)
                          ->where('purpose', 'password_reset')
                          ->where('code', $otpCode) // Check code again
                          ->whereNotNull('verified_at')
                          // Check if verified within the last N minutes (e.g., 15 mins)
                          ->where('verified_at', '>=', Carbon::now()->subMinutes(15))
                          ->latest('verified_at')
                          ->first();

        if (!$verifiedOtp) {
             return response()->json(['message' => 'Invalid or expired OTP verification. Please request a new OTP.'], 400);
        }

        // --- Find Customer and Update Password ---
        $customer = Customer::where('email', $email)->first();
        if (!$customer) {
            // Should not happen due to initial validation, but check anyway
             return response()->json(['message' => 'Customer not found.'], 404);
        }

        DB::beginTransaction();
        try {
            $customer->update([
                'password' => bcrypt($request->password)
            ]);

            // --- Invalidate the OTP record completely (optional, prevents reuse within expiry window) ---
            // $verifiedOtp->delete(); // Or update it further

            // --- Send Password Reset Confirmation ---
             Notification::send($customer, new PasswordResetSuccessNotification());

             DB::commit();

             return response()->json(['message' => 'Password reset successfully.']);

        } catch (\Exception $e) {
             DB::rollBack();
             Log::error("Password Reset Failed for {$email}: " . $e->getMessage());
             return response()->json(['message' => 'Failed to reset password. Please try again later.'], 500);
        }
    }


    /**
     * Log the customer out (revoke the current token).
     */
    public function logout(Request $request)
    {
        // Use the customer guard
        $customer = $request->user('sanctum_customer');

        if ($customer) {
             try {
                 // Revoke the token that was used to authenticate the current request
                 $customer->currentAccessToken()->delete();
                 return response()->json(['message' => 'Logout successful.']);
             } catch (\Exception $e) {
                 Log::error("Customer Logout Failed: " . $e->getMessage());
                  return response()->json(['message' => 'Logout failed.'], 500);
             }
        }

        return response()->json(['message' => 'No authenticated user to logout.'], 401);

    }

    /**
     * Get the authenticated customer's details.
     */
    public function user(Request $request)
    {
        $customer = $request->user('sanctum_customer'); // Use the correct guard

         if ($customer) {
             // Return relevant customer details (avoid sensitive info like password hash)
             return response()->json($customer->only('id', 'name', 'email', 'phone', 'status', 'is_email_verified'));
         }

        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    // --- Helper Methods ---

    /**
     * Generate a 6-digit OTP.
     */
    private function generateOtpCode(): string
    {
        // Ensure it's always 6 digits, padding if necessary (though random_int should be fine)
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Store the OTP in the database.
     */
    private function generateAndStoreOtp(string $identifier, string $purpose): string
    {
        $otpCode = $this->generateOtpCode();

        // Invalidate previous OTPs for the same purpose (optional but good practice)
        Otp::where('identifier', $identifier)->where('purpose', $purpose)->delete();

        Otp::create([
            'identifier' => $identifier,
            'code' => $otpCode, // Store plaintext temporarily - consider hashing if required by policy
            'purpose' => $purpose,
            'expires_at' => Carbon::now()->addMinutes($this->otpExpiryMinutes),
        ]);

        return $otpCode;
    }

     // --- Optional: Method to send email verification OTP ---
     // private function sendVerificationOtp(string $email) {
     //     try {
     //         $otpCode = $this->generateAndStoreOtp($email, 'email_verification');
     //         Notification::route('mail', $email)->notify(new SendOtpNotification($otpCode, 'Email Verification'));
     //         Log::info("Verification OTP sent to {$email}");
     //     } catch (\Exception $e) {
     //          Log::error("Email Verification OTP Send Failed for {$email}: " . $e->getMessage());
     //          // Handle failure - maybe log or retry?
     //     }
     // }

} // End Controller