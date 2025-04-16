<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request) {
        $query = ContactMessage::query();

        if($request->input('filter') === 'unread') {
            $query->where('is_read', false);
        } elseif ($request->input('filter') === 'read') {
             $query->where('is_read', true);
        }
        // Add search by name/email/subject if needed

        $messages = $query->orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = ContactMessage::unread()->count(); // For badge

        return view('admin.contact_messages.index', compact('messages', 'unreadCount'));
    }

    public function show(ContactMessage $contactMessage) {
        // Mark as read when viewed
        if (!$contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }
        return view('admin.contact_messages.show', compact('contactMessage'));
    }

    // Optional: Explicitly mark as unread
    // public function markUnread(ContactMessage $contactMessage) {
    //     $contactMessage->update(['is_read' => false]);
    //     return redirect()->route('admin.contact-messages.index')->with('success', 'Message marked as unread.');
    // }

    public function destroy(ContactMessage $contactMessage) {
        $contactMessage->delete();
        return redirect()->route('admin.contact-messages.index')->with('success', 'Message deleted successfully.');
    }
}