<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // For logging

class ContactMessageController extends Controller
{
    /**
     * Store a newly created contact message in storage.
     * This is typically a public endpoint.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $message = ContactMessage::create($validator->validated());

            // --- Optional: Send Notification Email ---
            // Mail::to(config('mail.admin_address'))->send(new NewContactMessage($message));

            return response()->json([
                'message' => 'Your message has been sent successfully.'
                // Optionally return message ID: 'message_id' => $message->id
                ], 201); // 201 Created

        } catch (\Exception $e) {
            Log::error("Error storing contact message: " . $e->getMessage());
            return response()->json(['message' => 'An error occurred while sending your message. Please try again later.'], 500);
        }
    }

     // index, show, destroy methods are usually admin-only, so keep them in the Admin controller
}