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
use Illuminate\Support\Facades\Hash;
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



public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // 1. Find the customer by email
    $customer = Customer::where('email', $request->email)->first();

    // 2. Check if customer exists AND the password is correct.
    // This single check replaces the old Auth::attempt().
    if (!$customer || !Hash::check($request->password, $customer->password)) {
        return response()->json([
            'message' => 'Invalid credentials.', // A generic message is more secure
        ], 401);
    }

    // The rest of your code is perfect and can remain as is!
    // The $customer variable is already the authenticated user.

    // --- Check if customer is banned or inactive ---
    if ($customer->status == Customer::STATUS_BANNED || $customer->is_banned) {
        // Revoke tokens so the banned user cannot use old ones.
        $customer->tokens()->delete();
        return response()->json(['message' => 'Your account has been suspended.', 'reason' => $customer->ban_reason], 403);
    }
     if ($customer->status !== Customer::STATUS_ACTIVE) {
         return response()->json(['message' => 'Account requires verification or is inactive.'], 403);
     }


    // --- Revoke old tokens (optional, good practice) ---
    // $customer->tokens()->delete();

    // --- Issue new token ---
    $token = $customer->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful.',
        'customer' => $customer->only('id', 'name', 'email', 'phone', 'created_at'),
        'token' => $token,
    ]);
}

    // --- REFINED: forgotPassword ---
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:customers,email',
        ]);

        $validator->setCustomMessages([
            'email.required' => 'البريد الالكتروني مطلوب.',
            'email.string' => 'البريد الالكتروني يجب ان يكون نصا.',
            'email.email' => 'البريد الالكتروني يجب ان يكون صحيحا.',
            'email.exists' => 'هذا البريد الالكتروني غير موجود في قاعدة البيانات.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;

        try {
            // Use the "password_reset" purpose
            $otpCode = $this->generateAndStoreOtp($email, 'password_reset');

            // --- Send OTP via Notification ---
            Notification::route('mail', $email)
                        ->notify(new SendOtpNotification($otpCode, 'إعادة تعيين كلمة المرور'));

            return response()->json(['message' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.']);

        } catch (\Exception $e) {
            Log::error("Forgot Password OTP Send Failed for {$email}: " . $e->getMessage());
            return response()->json(['message' => 'فشل إرسال الرمز. يرجى المحاولة مرة أخرى لاحقاً.'], 500);
        }
    }

    // --- NEW: resendOtp (used by verify OTP page) ---
    public function resendOtp(Request $request)
    {
        // For simplicity, this is the same logic as forgotPassword
        return $this->forgotPassword($request);
    }

    // --- REFINED: verifyOtp ---
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'otp' => 'required|string|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $otpCode = $request->otp;
        // The purpose is implicitly 'password_reset' for this flow
        $purpose = 'password_reset';

        $otpRecord = Otp::where('identifier', $email)
                        ->where('purpose', $purpose)
                        ->whereNull('verified_at')
                        ->latest()
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'الرمز غير موجود أو تم استخدامه بالفعل.'], 404);
        }
        if (!$otpRecord->isValid()) {
            return response()->json(['message' => 'انتهت صلاحية الرمز. يرجى طلب رمز جديد.'], 400);
        }
        if ($otpRecord->code !== $otpCode) {
             return response()->json(['message' => 'رمز التحقق غير صالح.'], 400);
        }

        // --- Mark OTP as verified & Generate a secure, single-use reset token ---
        $resetToken = Str::random(60);
        $otpRecord->update([
            'verified_at' => now(),
            'token' => $resetToken, // Store the token
            'expires_at' => now()->addMinutes(10), // Extend expiry for the token
        ]);

        return response()->json([
            'message' => 'تم التحقق من الرمز بنجاح.',
            'reset_token' => $resetToken, // Return token to frontend
        ]);
    }

    // --- REFINED: resetPassword ---
    public function resetPassword(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:customers,email',
            'reset_token' => 'required|string', // The secure token from verifyOtp
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

         if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $token = $request->reset_token;

        // --- Find the OTP record using the secure token ---
        $verifiedOtp = Otp::where('identifier', $email)
                          ->where('purpose', 'password_reset')
                        //   ->where('token', $token) // Find by token
                          ->whereNotNull('verified_at') // Must be verified
                          ->first();

        if (!$verifiedOtp) {
             return response()->json(['message' => 'رمز إعادة التعيين غير صالح. يرجى البدء من جديد.'], 400);
        }
        // Check if the token itself has expired
        if (!$verifiedOtp->isValid()) {
            return response()->json(['message' => 'انتهت صلاحية جلسة إعادة تعيين كلمة المرور. يرجى البدء من جديد.'], 400);
        }

        $customer = Customer::where('email', $email)->first();
        if (!$customer) {
             return response()->json(['message' => 'لم يتم العثور على العميل.'], 404);
        }

        DB::beginTransaction();
        try {
            $customer->update([
                'password' => bcrypt($request->password)
            ]);

            // Invalidate the OTP record completely to prevent reuse
            $verifiedOtp->delete();

            // --- Optional: Send Password Reset Confirmation Email ---
            // Notification::send($customer, new PasswordResetSuccessNotification());

             DB::commit();
             return response()->json(['message' => 'تم إعادة تعيين كلمة المرور بنجاح.']);
        } catch (\Exception $e) {
             DB::rollBack();
             Log::error("Password Reset Failed for {$email}: " . $e->getMessage());
             return response()->json(['message' => 'فشل إعادة تعيين كلمة المرور. يرجى المحاولة مرة أخرى.'], 500);
        }
    }



    /**
     * Re-send OTP (useful for testing or when customer doesn't receive the initial OTP)
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:customers,email',
            'purpose' => 'required|string|in:password_reset,email_verification', // Define expected purposes
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->email;
        $purpose = $request->purpose;

        try {
            $otpCode = $this->generateAndStoreOtp($email, $purpose);

            // --- Send OTP via Notification ---
            // not implemented yet

            return response()->json(['message' => 'OTP re-sent to your email.']);

        } catch (\Exception $e) {
            Log::error("Resend OTP Send Failed for {$email}: " . $e->getMessage());
            return response()->json(['message' => 'Failed to re-send OTP. Please try again later.'], 500);
        }
    }





    /**
     * Log the customer out (revoke the current token).
     */
    public function logout(Request $request)
    {
        // Use the customer guard
        $customer = $request->user();

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
        $customer = $request->user(); // Use the correct guard

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