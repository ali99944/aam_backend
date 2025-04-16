<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionRequest;
use App\Services\ActionRequestService; // Import the service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ActionRequestController extends Controller
{
    protected $actionRequestService;

    public function __construct(ActionRequestService $actionRequestService)
    {
        $this->actionRequestService = $actionRequestService;
    }

    public function index(Request $request) {
        $query = ActionRequest::with(['requestor', 'processor']); // Eager load users

        // Filtering
        if ($request->filled('search')) { // Search by ID or type
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm){
                 $q->where('id', $searchTerm)
                   ->orWhere('action_type', 'like', "%{$searchTerm}%");
            });
        }
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('action_type') && $request->action_type != 'all') {
            $query->where('action_type', $request->action_type);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = ActionRequest::statuses();
        $actionTypes = ActionRequest::availableActionTypes();

        return view('admin.action_requests.index', compact('requests', 'statuses', 'actionTypes'));
    }

    // View to create a manual request
    public function create() {
         $actionTypes = ActionRequest::availableActionTypes();
        return view('admin.action_requests.create', compact('actionTypes'));
    }

    // Store a manually created request
    public function store(Request $request) {
        $availableTypes = array_keys(ActionRequest::availableActionTypes());

         $validator = Validator::make($request->all(), [
             'action_type' => ['required', Rule::in($availableTypes)],
             'data' => ['required', 'json'], // Validate incoming data is JSON
         ], [
             'action_type.in' => 'Invalid action type selected.',
             'data.required' => 'Data payload cannot be empty.',
             'data.json' => 'The provided data is not valid JSON.',
         ]);

         if ($validator->fails()) {
             return redirect()->route('admin.action-requests.create')->withErrors($validator)->withInput();
         }
         $validated = $validator->validated();

         // Decode the JSON *before* saving because the model expects an array
         $dataArray = json_decode($validated['data'], true);
         if (json_last_error() !== JSON_ERROR_NONE) {
             // This should be caught by 'json' rule, but double-check
             return redirect()->route('admin.action-requests.create')
                             ->withErrors(['data' => 'Invalid JSON format.'])
                             ->withInput();
         }

         try {
            ActionRequest::create([
                'action_type' => $validated['action_type'],
                'data' => $dataArray, // Save the decoded array
                'status' => ActionRequest::STATUS_PENDING, // Manual requests start pending
                'requested_by_user_id' => Auth::id(), // Logged-in admin requested it
            ]);
            return redirect()->route('admin.action-requests.index')->with('success', 'Action Request created successfully.');
         } catch (\Exception $e) {
            Log::error("Error creating manual action request: " . $e->getMessage());
            return redirect()->route('admin.action-requests.create')
                           ->with('error', 'Failed to create request: ' . $e->getMessage())
                           ->withInput();
         }
    }

    /**
     * Process (Approve/Reject) a pending request.
     */
    public function process(Request $request, ActionRequest $actionRequest) // Use Route Model Binding
    {
         // Basic validation for the process action itself
         $validator = Validator::make($request->all(), [
             'process_action' => ['required', Rule::in(['approve', 'reject'])],
             'rejection_reason' => ['required_if:process_action,reject', 'nullable', 'string', 'max:1000'],
         ], [
             'rejection_reason.required_if' => 'Please provide a reason for rejecting the request.',
         ]);

         if ($validator->fails()) {
             // Return errors back to the index page, perhaps using a modal for processing?
             // For simplicity, redirecting back with errors. Consider AJAX/Modals for better UX.
             return redirect()->route('admin.action-requests.index')
                             ->withErrors($validator, 'process_' . $actionRequest->id); // Scope errors to specific request
         }

         $approve = $request->input('process_action') === 'approve';
         $reason = $request->input('rejection_reason');

         try {
             $this->actionRequestService->executeAction($actionRequest);
             $action = $approve ? 'approved' : 'rejected';
             return redirect()->route('admin.action-requests.index')
                             ->with('success', "Request #{$actionRequest->id} has been {$action}.");
         } catch (\Exception $e) {
             // Catch errors from the service
             return redirect()->route('admin.action-requests.index')
                              ->with('error', "Failed to process request #{$actionRequest->id}: " . $e->getMessage());
         }
    }

    // No update/edit view needed as per requirement
    // Destroy is usually not needed either, keep record of actions
    // public function destroy(ActionRequest $actionRequest) { ... }

}