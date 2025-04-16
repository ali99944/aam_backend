<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentMethod; // For filtering
use App\Models\Order; // For filtering
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request) {
        $query = Payment::with(['order.customer', 'paymentMethod', 'invoice']); // Eager load relationships

        // --- Filtering ---
        if ($request->filled('search')) { // Search by Order ID, Transaction ID, Customer Name/Email
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm){
                $q->where('transaction_id', 'like', "%{$searchTerm}%")
                  ->orWhereHas('order', fn($oq) => $oq->where('id', $searchTerm)) // Exact Order ID
                  ->orWhereHas('order.customer', function($cq) use ($searchTerm){
                      $cq->where('name', 'like', "%{$searchTerm}%")
                         ->orWhere('email', 'like', "%{$searchTerm}%");
                  });
            });
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(25);
        $statuses = Payment::statuses();
        $paymentMethods = PaymentMethod::where('is_enabled', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.payments.index', compact('payments', 'statuses', 'paymentMethods'));
    }

    // No create, store, edit, update, destroy needed for just viewing
}