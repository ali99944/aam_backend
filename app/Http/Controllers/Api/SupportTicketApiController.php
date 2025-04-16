<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Customer; // Use your Customer model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Resources\SupportTicketResource; // Create this
use App\Http\Resources\SupportTicketReplyResource; // Create this
use Illuminate\Support\Facades\Notification;
// use App\Notifications\AdminNewSupportTicket; // Create notification for admins

class SupportTicketApiController extends Controller
{
    /**
     * List tickets belonging to the authenticated customer.
     */
    public function index(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->user('sanctum_customer');

        $query = $customer->supportTickets()->with('replies.admin:id,name'); // Eager load basic reply info

        // Filter by status
         if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $tickets = $query->orderBy('last_reply_at', 'desc')->orderBy('created_at', 'desc')->paginate(10);

        return response()->json($tickets);
    }

    /**
     * Store a new support ticket from the authenticated customer.
     */
    public function store(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->user('sanctum_customer');

        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => ['nullable', Rule::in(array_keys(SupportTicket::priorities()))],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $validated = $validator->validated();

        try {
            $ticket = $customer->supportTickets()->create([
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'priority' => $validated['priority'] ?? SupportTicket::PRIORITY_MEDIUM,
                'status' => SupportTicket::STATUS_OPEN, // Initial status
                'last_reply_at' => now(), // Set initial reply time
            ]);

            // --- Notify Admins ---
            // $adminUsers = User::whereRole('admin')->get(); // Get relevant admins
            // Notification::send($adminUsers, new AdminNewSupportTicket($ticket));

            return response()->json($ticket, 201);

        } catch (\Exception $e) {
            Log::error("API: Failed to create support ticket for customer {$customer->id}: ".$e->getMessage());
            return response()->json(['message' => 'Could not submit your request. Please try again.'], 500);
        }
    }

    /**
     * Display a specific ticket belonging to the authenticated customer.
     */
    public function show(Request $request, SupportTicket $supportTicket) // Route model binding
    {
         /** @var Customer $customer */
        $customer = $request->user('sanctum_customer');

        // Authorization: Check if the customer owns this ticket
        if ($supportTicket->customer_id !== $customer->id) {
             return response()->json(['message' => 'Not Found.'], 404); // Or 403 Forbidden
        }

        $supportTicket->load(['replies.admin:id,name', 'replies.customer:id,name']); // Load replies with replier names

        return response()->json($supportTicket);
    }

     /**
     * Store a reply from the authenticated customer to their ticket.
     */
    public function storeReply(Request $request, SupportTicket $supportTicket)
    {
        /** @var Customer $customer */
        $customer = $request->user('sanctum_customer');

         // Authorization: Check ownership
        if ($supportTicket->customer_id !== $customer->id) {
             return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Validation
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        // Check if ticket is closed/resolved
        if(in_array($supportTicket->status, [SupportTicket::STATUS_RESOLVED, SupportTicket::STATUS_CLOSED])) {
             return response()->json(['message' => 'Cannot reply to a resolved or closed ticket.'], 400);
        }

        try {
            $reply = $supportTicket->replies()->create([
                'message' => $validated['message'],
                'customer_id' => $customer->id, // Reply is from customer
                'admin_id' => null,
            ]);

            // Update ticket status and last reply time
            $supportTicket->update([
                'last_reply_at' => now(),
                'status' => SupportTicket::STATUS_CUSTOMER_REPLY, // Mark as customer reply
            ]);

             // --- Notify Admins ---
             // $adminUsers = User::whereRole('admin')->get();
             // Notification::send($adminUsers, new CustomerRepliedToTicket($reply));

            return response()->json($reply, 201);

        } catch (\Exception $e) {
             Log::error("API: Failed to store customer reply for ticket #{$supportTicket->id}: ".$e->getMessage());
            return response()->json(['message' => 'Could not submit your reply. Please try again.'], 500);
        }
    }
}