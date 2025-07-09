<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer; // Use your Customer model
use App\Models\Order; // Needed for show view
use App\Models\Payment; // Needed for show view
use App\Models\Invoice; // Needed for show view
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; // For statistics calculation
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        // Use the scope if you defined one to exclude admins/staff
        // If your Customer model is separate and *only* for customers, no scope needed.
        $query = Customer::query(); // Use Customer model directly

        // --- Filtering ---
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm); // Search phone too
            });
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('banned') && $request->banned != 'all') {
            $query->where('is_banned', filter_var($request->banned, FILTER_VALIDATE_BOOLEAN));
        }
        // Add email verified filter if you add that column to Customer model
        // if ($request->filled('verified') && $request->verified != 'all') { ... }


        $customers = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = Customer::distinct()->pluck('status')->mapWithKeys(fn($status) => [$status => ucfirst($status)])->sort()->toArray(); // Dynamic statuses

        return view('admin.customers.index', compact('customers', 'statuses'));
    }

    /**
     * Display the specified customer's details and related data.
     */
    public function show(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->load('orders'); // Load related orders
        $customer->load('payments'); // Load related payments
        $customer->load('invoices'); // Load related invoices
        // --- Load related data with pagination ---
        $orders = $customer->orders()
                           ->with('city:id,name') // Load city for orders
                           ->latest() // Show most recent first
                           ->paginate(10, ['*'], 'orders_page'); // Paginate orders

        $payments = Payment::whereHas('order', fn($q) => $q->where('customer_id', $customer->id))
                           ->with(['order:id', 'paymentMethod:id,name']) // Load order ID and payment method name
                           ->latest()
                           ->paginate(10, ['*'], 'payments_page'); // Paginate payments

        $invoices = Invoice::whereHas('order', fn($q) => $q->where('customer_id', $customer->id))
                            ->with('order:id,created_at') // Load order ID and date
                            ->latest('issue_date')
                            ->paginate(10, ['*'], 'invoices_page'); // Paginate invoices

        // --- Calculate Statistics ---
        $stats = DB::table('orders')
                   ->where('customer_id', $customer->id)
                   ->selectRaw("COUNT(*) as total_orders,
                                SUM(CASE WHEN status = 'completed' THEN total ELSE 0 END) as total_spent,
                                AVG(CASE WHEN status = 'completed' THEN total ELSE NULL END) as average_order_value")
                    // Add more stats like SUM(total) for total value regardless of status etc.
                   ->first();

        // Convert stats stdClass to array or use directly
        $statistics = [
            'total_orders' => $stats->total_orders ?? 0,
            'total_spent' => $stats->total_spent ?? 0.00,
            'average_order_value' => $stats->average_order_value ?? 0.00,
            // Add more calculated stats here
        ];


        return view('admin.customers.show', compact(
            'customer',
            'orders',
            'payments',
            'invoices',
            'statistics'
        ));
    }


    /**
     * Ban the specified customer.
     */
    public function ban(Request $request, Customer $customer)
    {
        // --- Authorization Check ---
        // if (auth()->user()->cannot('ban', $customer)) { abort(403); }

        $request->validate([ 'ban_reason' => 'required|string|max:1000', ]);

        try {
            $customer->update([
                'is_banned' => true,
                'status' => Customer::STATUS_BANNED, // Use constant if defined
                'banned_at' => now(),
                'ban_reason' => $request->input('ban_reason'),
            ]);
            // Invalidate sessions/tokens if needed
            return back()->with('success', "Customer '{$customer->name}' has been banned.");
        } catch (\Exception $e) {
            Log::error("Error banning customer ID {$customer->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to ban customer.');
        }
    }

     /**
     * Unban the specified customer.
     */
    public function unban(Customer $customer)
    {
        // --- Authorization Check ---
        // if (auth()->user()->cannot('unban', $customer)) { abort(403); }

        try {
            // Determine status to revert to (Active or Verification Required - adjust if needed)
             $newStatus = Customer::STATUS_ACTIVE; // Default to active for Customer model

            $customer->update([
                'is_banned' => false,
                'status' => $newStatus,
                'banned_at' => null,
                'ban_reason' => null,
            ]);
            return back()->with('success', "Customer '{$customer->name}' has been unbanned.");
        } catch (\Exception $e) {
            Log::error("Error unbanning customer ID {$customer->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to unban customer.');
        }
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        // --- Authorization Check ---
        // if (auth()->user()->cannot('delete', $customer)) { abort(403); }

        // --- Check for related data (e.g., non-cancelled orders) ---
        // if ($customer->orders()->where('status', '!=', 'cancelled')->exists()) {
        //     return back()->with('error', 'Cannot delete customer with active or completed orders. Consider banning instead.');
        // }

        try {
            // Consider Soft Deletes for customers
            // $customer->delete(); // Soft delete if using trait
            DB::transaction(function() use ($customer) {
                // Manually handle relations if not using cascade/soft deletes properly
                // $customer->orders()->update(['customer_id' => null]); // Anonymize orders? Risky.
                // $customer->paymentsViaOrders()->delete(); // Requires defining relation
                // etc.
                $customer->delete(); // Hard delete (Use with caution!)
            });

            return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
             Log::error("Error deleting customer ID {$customer->id}: " . $e->getMessage());
            return back()->with('error', 'Failed to delete customer.');
        }
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $statuses = Customer::statuses(); // Get statuses for dropdown
        return view('admin.customers.create', compact('statuses'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email',
            'phone' => 'required|string|max:50|unique:customers,phone',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()], // Strong password
            'status' => ['required', Rule::in(array_keys(Customer::statuses()))],
            'balance' => 'nullable|numeric|min:0',
            'is_banned' => 'nullable|boolean', // Should typically be managed by ban/unban actions
            'ban_reason' => 'nullable|string|max:1000|required_if:is_banned,true',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.customers.create')
                        ->withErrors($validator)
                        ->withInput();
        }
        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            $validatedData['password'] = Hash::make($validatedData['password']);
            $validatedData['is_banned'] = $request->has('is_banned'); // Convert checkbox to boolean
            if ($validatedData['is_banned']) {
                $validatedData['banned_at'] = now();
                $validatedData['status'] = Customer::STATUS_BANNED; // Override status if banned
            } else {
                $validatedData['banned_at'] = null;
                $validatedData['ban_reason'] = null;
            }
            // Ensure balance is set, default to 0 if not provided or empty
            $validatedData['balance'] = $request->filled('balance') ? $validatedData['balance'] : 0.00;


            Customer::create($validatedData);
            DB::commit();
            return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating customer: " . $e->getMessage());
            return redirect()->route('admin.customers.create')
                         ->with('error', 'Failed to create customer. ' . $e->getMessage())
                         ->withInput();
        }
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $statuses = Customer::statuses();
        return view('admin.customers.edit', compact('customer', 'statuses'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'phone' => ['required', 'string', 'max:50', Rule::unique('customers')->ignore($customer->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()], // Optional on update
            'status' => ['required', Rule::in(array_keys(Customer::statuses()))],
            'balance' => 'nullable|numeric|min:0',
            // is_banned and ban_reason are managed via ban/unban actions, not typically in general edit.
            // If you want to edit them here, add validation and logic.
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.customers.edit', $customer->id)
                        ->withErrors($validator)
                        ->withInput();
        }
        $validatedData = $validator->validated();

        DB::beginTransaction();
        try {
            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']); // Don't update password if field is empty
            }
            // Ensure balance is set, default to current balance if not provided or empty
            $validatedData['balance'] = $request->filled('balance') ? $validatedData['balance'] : $customer->balance;

            // If status is changed from 'banned' to something else, unban them.
            // And if changed to 'banned', ban them.
            // This could be more complex, ban/unban actions are cleaner.
            if ($validatedData['status'] !== Customer::STATUS_BANNED && $customer->is_banned) {
                $validatedData['is_banned'] = false;
                $validatedData['banned_at'] = null;
                $validatedData['ban_reason'] = null;
            } elseif ($validatedData['status'] === Customer::STATUS_BANNED && !$customer->is_banned) {
                 $validatedData['is_banned'] = true;
                 $validatedData['banned_at'] = now();
                 // $validatedData['ban_reason'] = 'Manually set to banned by admin.'; // Or get from a field if added
            }


            $customer->update($validatedData);
            DB::commit();
            return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating customer ID {$customer->id}: " . $e->getMessage());
            return redirect()->route('admin.customers.edit', $customer->id)
                         ->with('error', 'Failed to update customer. ' . $e->getMessage())
                         ->withInput();
        }
    }

    // Add edit/update methods if you allow editing customer details (name, email, password reset etc.)

} // End Controller