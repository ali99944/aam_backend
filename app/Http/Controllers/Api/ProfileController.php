<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Customer; // Your Customer model

class ProfileController extends Controller
{
    // Image handling helper (copy from previous controllers if needed)
    private function handleImageUpload(Request $request, string $fieldName, string $directory, ?string $oldPath = null): ?string {
        if ($request->hasFile($fieldName)) {
           if ($oldPath) Storage::disk('public')->delete($oldPath);
           return $request->file($fieldName)->store($directory, 'public');
        }
        return $oldPath;
    }
    private function deleteImage(?string $path): void { if ($path) Storage::disk('public')->delete($path); }

    /**
     * Get the currently authenticated customer's profile data.
     */
    public function show(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->user('customer'); // Use your customer guard

        if (!$customer) {
             return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Return relevant, non-sensitive profile details
        return response()->json($customer->only(
            'id', 'name', 'email', 'phone', 'avatar_url', // Include avatar_url accessor result
             // Add other fields safe to return: 'address', 'city', etc. if stored on customer
             'email_verified_at' // Useful for frontend checks
        ));
    }

    /**
     * Update the currently authenticated customer's profile data.
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();


        if (!$customer) {
             return response()->json(['message' => 'Unauthenticated.'], 401);
        }


        $validator = Validator::make($request->all(), [
            // Validate only the fields allowed for update
            'name' => 'sometimes|required|string|max:255',
            // Email update might require re-verification, handle carefully
            // 'email' => ['sometimes','required','string','email','max:255', Rule::unique('customers')->ignore($customer->id)],
            'phone' => ['sometimes','required','string','max:50', Rule::unique('customers')->ignore($customer->id)],
            // 'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024', // Avatar update
             // Add validation for other updatable fields (address, etc.)
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $updateData = [];

        // Build update array only with submitted fields
        if ($request->has('name')) $updateData['name'] = $validated['name'];
        if ($request->has('phone')) $updateData['phone'] = $validated['phone'];
        // Add other validated fields here...

        // Handle avatar upload
        // if ($request->hasFile('avatar')) {
        //     $updateData['avatar'] = $this->handleImageUpload($request, 'avatar', 'customers/avatars', $customer->avatar);
        // } elseif ($request->input('remove_avatar') == '1') { // Check for a flag to remove avatar
        //      $this->deleteImage($customer->avatar);
        //      $updateData['avatar'] = null;
        // }

        // Handle Email Change - Requires Re-verification Flow (More Complex)
        // if ($request->has('email') && $validated['email'] !== $customer->email) {
        //     $updateData['email'] = $validated['email'];
        //     $updateData['email_verified_at'] = null; // Mark as unverified
        //     // Trigger sending a new verification link/OTP here
        //     // $this->sendVerificationOtp($validated['email']);
        //     Log::info("Email changed for customer {$customer->id}. Verification required.");
        // }


        if (empty($updateData)) {
            return response()->json(['message' => 'No changes provided.'], 200); // Or 304 Not Modified
        }

        try {
            $customer->update($updateData);

             // Return the updated profile data (excluding sensitive info)
             return response()->json([
                 'message' => 'Profile updated successfully.',
                 'customer' => $customer->fresh()->only('id', 'name', 'email', 'phone', 'email_verified_at')
             ]);

        } catch (\Exception $e) {
            Log::error("Profile update failed for customer {$customer->id}: " . $e->getMessage());
             return response()->json(['message' => 'Failed to update profile. Please try again.'], 500);
        }
    }


    /**
     * Change the authenticated customer's password.
     */
    public function changePassword(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->user('customer');

        if (!$customer) {
             return response()->json(['message' => 'Unauthenticated.'], 401);
        }

         $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($customer) {
                if (!Hash::check($value, $customer->password)) {
                    $fail('The current password does not match our records.');
                }
            }],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            // 'password.different' => 'The new password must be different from the current password.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $customer->update([
                'password' => bcrypt($request->input('password'))
            ]);

             // Optional: Logout user from other devices by revoking other tokens
             // $customer->tokens()->where('id', '!=', $customer->currentAccessToken()->id)->delete();

             return response()->json(['message' => 'Password changed successfully.']);

        } catch (\Exception $e) {
             Log::error("Password change failed for customer {$customer->id}: " . $e->getMessage());
             return response()->json(['message' => 'Failed to change password. Please try again.'], 500);
        }
    }

}