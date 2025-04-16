<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User; // For admin assignment
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Notification; // If sending notifications
// use App\Notifications\SupportTicketReplied; // Create this notification

class SupportTicketController extends Controller
{
    public function index(Request $request) {
        $query = SupportTicket::with(['customer', 'assignedAdmin']);

        // Filtering
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
             $query->where(function($q) use ($term) {
                $q->where('subject', 'like', $term)
                  ->orWhere('id', $request->search) // Search by ID
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', $term)->orWhere('email', 'like', $term));
            });
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
         if ($request->filled('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }
         if ($request->filled('assigned_admin_id')) {
            if($request->assigned_admin_id === 'unassigned') $query->whereNull('assigned_admin_id');
            else $query->where('assigned_admin_id', $request->assigned_admin_id);
        }

        $tickets = $query->orderBy('last_reply_at', 'desc')->orderBy('created_at', 'desc')->paginate(20);
        $statuses = SupportTicket::statuses();
        $priorities = SupportTicket::priorities();
        $admins = User::/*whereRole('admin')->orWhereRole('support')*/orderBy('name')->pluck('name', 'id'); // Fetch potential assignees

        return view('admin.support_tickets.index', compact('tickets', 'statuses', 'priorities', 'admins'));
    }

    public function show(SupportTicket $supportTicket) {
        $supportTicket->load(['customer', 'assignedAdmin', 'replies.customer', 'replies.admin']); // Load all needed relations
        $statuses = SupportTicket::statuses();
        $priorities = SupportTicket::priorities();
        $admins = User::orderBy('name')->pluck('name', 'id');

        // Optional: Mark as 'in_progress' if opened by admin and status is 'open'
        // if ($supportTicket->status === SupportTicket::STATUS_OPEN) {
        //    $supportTicket->update(['status' => SupportTicket::STATUS_IN_PROGRESS]);
        // }

        return view('admin.support_tickets.show', compact('supportTicket', 'statuses', 'priorities', 'admins'));
    }

    // Store Reply from Admin
    public function storeReply(Request $request, SupportTicket $supportTicket) {
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            // Optional: Allow changing status when replying
            'status' => ['nullable', Rule::in(array_keys(SupportTicket::statuses()))],
        ]);

        $admin = Auth::user(); // Currently logged-in admin

        $reply = $supportTicket->replies()->create([
            'message' => $validated['message'],
            'admin_id' => $admin->id,
            'customer_id' => null,
        ]);

        // Update ticket status and last reply time
        $newStatus = $request->input('status', SupportTicket::STATUS_IN_PROGRESS); // Default to 'in_progress' or keep current?
        // Prevent setting back to 'open' or 'customer_reply' via reply
        if(in_array($newStatus, [SupportTicket::STATUS_OPEN, SupportTicket::STATUS_CUSTOMER_REPLY]) && $supportTicket->status != $newStatus) {
            $newStatus = $supportTicket->status === SupportTicket::STATUS_OPEN ? SupportTicket::STATUS_IN_PROGRESS : $supportTicket->status;
        }

        $supportTicket->update([
            'last_reply_at' => now(),
            'status' => $newStatus,
            'assigned_admin_id' => $supportTicket->assigned_admin_id ?? $admin->id // Assign if unassigned
        ]);

        // --- Send Notification to Customer ---
        // try {
        //     Notification::send($supportTicket->customer, new SupportTicketReplied($reply));
        // } catch (\Exception $e) {
        //     Log::error("Failed to send reply notification for ticket #{$supportTicket->id}: ".$e->getMessage());
        // }

        return redirect()->route('admin.support-tickets.show', $supportTicket->id)->with('success', 'Reply sent successfully.');
    }

    // Update status, priority, assigned admin separately
    public function updateDetails(Request $request, SupportTicket $supportTicket) {
         $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(SupportTicket::statuses()))],
            'priority' => ['required', Rule::in(array_keys(SupportTicket::priorities()))],
             'assigned_admin_id' => 'nullable|exists:users,id', // Validate admin exists
        ]);

        $supportTicket->update($validated);
        return redirect()->route('admin.support-tickets.show', $supportTicket->id)->with('success', 'Ticket details updated.');
    }

    public function destroy(SupportTicket $supportTicket) {
        // Use soft deletes if preferred
        $supportTicket->replies()->delete(); // Delete replies first if not using cascade
        $supportTicket->delete();
        return redirect()->route('admin.support-tickets.index')->with('success', 'Support ticket deleted.');
    }
}