<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // If allowing password reset maybe
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        // Use the scope if you defined one to exclude admins/staff
        $query = Customer::query();

        // --- Filtering ---
        if ($request->filled('search')) {
            $searchTerm = "%{$request->search}%";
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('banned') && $request->banned != 'all') {
            $query->where('is_banned', filter_var($request->banned, FILTER_VALIDATE_BOOLEAN));
        }
        if ($request->filled('verified') && $request->verified != 'all') {
            if(filter_var($request->verified, FILTER_VALIDATE_BOOLEAN)) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }


        $customers = $query->orderBy('createdAt', 'desc')->paginate(20);
        $statuses = Customer::statuses(); // Get statuses for filter dropdown

        return view('admin.customers.index', compact('customers', 'statuses'));
    }

    /**
     * Ban the specified customer.
     */
    public function ban(Request $request, Customer $customer) // Route model binding
    {
        // --- Validation ---
        $request->validate([
            'ban_reason' => 'required|string|max:1000',
        ]);

        // --- Perform Ban ---
        try {
            $customer->update([
                'is_banned' => true,
                'status' => Customer::STATUS_BANNED,
                'banned_at' => now(),
                'ban_reason' => $request->input('ban_reason'),
            ]);

            // --- Optional: Invalidate sessions/tokens ---
            // If using Sanctum: $customer->tokens()->delete();
            // If using sessions: // More complex, might need custom logic or package

            return back()->with('success', "Customer '{$customer->name}' has been banned.");

        } catch (\Exception $e) {
            Log::error("Error banning customer ID {$customer->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to ban customer. Please try again.');
        }
    }

     /**
     * Unban the specified customer.
     */
    public function unban(Customer $customer)
    {
         // --- Authorization Check (optional, might be same as ban) ---

        // --- Perform Unban ---
        try {
             // Determine the status to revert to (Active or Verification Required)
            $newStatus = $customer->is_email_verified ? Customer::STATUS_ACTIVE : Customer::STATUS_VERIFICATION_REQUIRED;

            $customer->update([
                'is_banned' => false,
                'status' => $newStatus,
                'banned_at' => null,
                'ban_reason' => null,
            ]);

            return back()->with('success', "Customer '{$customer->name}' has been unbanned.");

        } catch (\Exception $e) {
            Log::error("Error unbanning customer ID {$customer->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to unban customer. Please try again.');
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {

        try {
            // --- Handle Related Data? ---
            // Decide if orders etc. should be deleted or anonymized.
            // For now, we just delete the Customer. Soft deletes might be better.
            // $customer->orders()->update(['Customer_id' => null]); // Example anonymization

            $customer->delete();
            return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');

        } catch (\Exception $e) {
             Log::error("Error deleting customer ID {$customer->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to delete customer.');
        }
    }

    // Optional: Show method for detailed view
    // public function show(Customer $customer) {
    //     // Load orders, addresses etc.
    //     // return view('admin.customers.show', compact('customer'));
    // }
}