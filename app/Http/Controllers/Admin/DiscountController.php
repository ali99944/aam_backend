<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // For Enum validation
use Illuminate\Support\Str; // For generating random codes

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $query = Discount::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status') && $request->status != 'all') {
             $query->where('status', $request->status);
        }
        if ($request->filled('type') && $request->type != 'all') {
             $query->where('type', $request->type);
        }

        $discounts = $query->orderBy('created_at', 'desc')->paginate(15);

        // Data for filter dropdowns
        $statuses = [
            Discount::STATUS_ACTIVE => ucfirst(Discount::STATUS_ACTIVE),
            Discount::STATUS_INACTIVE => ucfirst(Discount::STATUS_INACTIVE),
            Discount::STATUS_EXPIRED => ucfirst(Discount::STATUS_EXPIRED),
        ];
        $types = [
            Discount::TYPE_FIXED => ucfirst(Discount::TYPE_FIXED),
            Discount::TYPE_PERCENTAGE => ucfirst(Discount::TYPE_PERCENTAGE),
        ];


        return view('admin.discounts.index', compact('discounts', 'statuses', 'types'));
    }

    public function create()
    {
        // Pass constants or arrays to the view for dropdowns
        $discountTypes = [Discount::TYPE_FIXED => 'Fixed Amount', Discount::TYPE_PERCENTAGE => 'Percentage'];
        $statuses = [Discount::STATUS_ACTIVE => 'Active', Discount::STATUS_INACTIVE => 'Inactive'];
        $expirationTypes = [
            Discount::EXPIRATION_NONE => 'No Expiration',
            Discount::EXPIRATION_DURATION => 'Duration (Days after activation)',
            Discount::EXPIRATION_PERIOD => 'Specific Period (Start/End Dates)'
        ];
        return view('admin.discounts.create', compact('discountTypes', 'statuses', 'expirationTypes'));
    }

    public function store(Request $request)
    {
        $validator = $this->validateDiscount($request);

        if ($validator->fails()) {
            return redirect()->route('admin.discounts.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();

        // Clean up fields based on expiration type
        $this->cleanupExpirationFields($validatedData);

        // Generate code if empty
        if (empty($validatedData['code'])) {
            $validatedData['code'] = $this->generateUniqueCode();
        }

        Discount::create($validatedData);

        return redirect()->route('admin.discounts.index')
                         ->with('success', 'Discount created successfully.');
    }

    // Show usually redirects to edit
    public function show(Discount $discount) { return redirect()->route('admin.discounts.edit', $discount); }

    public function edit(Discount $discount)
    {
        $discountTypes = [Discount::TYPE_FIXED => 'Fixed Amount', Discount::TYPE_PERCENTAGE => 'Percentage'];
        $statuses = [Discount::STATUS_ACTIVE => 'Active', Discount::STATUS_INACTIVE => 'Inactive', Discount::STATUS_EXPIRED => 'Expired']; // Allow setting to expired
        $expirationTypes = [
            Discount::EXPIRATION_NONE => 'No Expiration',
            Discount::EXPIRATION_DURATION => 'Duration (Days after activation)',
            Discount::EXPIRATION_PERIOD => 'Specific Period (Start/End Dates)'
        ];
        return view('admin.discounts.edit', compact('discount', 'discountTypes', 'statuses', 'expirationTypes'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validator = $this->validateDiscount($request, $discount->id);

        if ($validator->fails()) {
            return redirect()->route('admin.discounts.edit', $discount->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();

        // Clean up fields based on expiration type
        $this->cleanupExpirationFields($validatedData);

        // Prevent changing code if it exists? Or ensure uniqueness if changed.
        if (empty($validatedData['code']) && !empty($discount->code)) {
             $validatedData['code'] = $discount->code; // Keep old code if submitted empty
        } elseif (empty($validatedData['code'])) {
            $validatedData['code'] = $this->generateUniqueCode(); // Generate if was null and still empty
        }


        $discount->update($validatedData);

        return redirect()->route('admin.discounts.index')
                         ->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        // Add checks if discount is applied to products/orders before deleting?
        // e.g., if ($discount->products()->exists()) { ... }

        $discount->delete();

        return redirect()->route('admin.discounts.index')
                         ->with('success', 'Discount deleted successfully.');
    }

    // --- Helper Methods ---

    /**
     * Validate discount request data.
     */
    private function validateDiscount(Request $request, ?int $discountId = null): \Illuminate\Validation\Validator
    {
        $types = [Discount::TYPE_FIXED, Discount::TYPE_PERCENTAGE];
        $statuses = [Discount::STATUS_ACTIVE, Discount::STATUS_INACTIVE, Discount::STATUS_EXPIRED];
        $expirationTypes = [Discount::EXPIRATION_NONE, Discount::EXPIRATION_DURATION, Discount::EXPIRATION_PERIOD];

        $rules = [
            'name' => 'required|string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('discounts', 'code')->ignore($discountId), // Ignore current ID on update
                'regex:/^[a-zA-Z0-9_-]+$/' // Allow letters, numbers, underscore, hyphen
            ],
            'type' => ['required', Rule::in($types)],
            'value' => [
                'required',
                'numeric',
                'min:0',
                // Max value validation based on type
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('type') == Discount::TYPE_PERCENTAGE && $value > 100) {
                        $fail('The percentage value cannot exceed 100.');
                    }
                     // Add max fixed value if needed, e.g., < 10000
                },
            ],
            'status' => ['required', Rule::in($statuses)],
            'expiration_type' => ['required', Rule::in($expirationTypes)],
            'duration_days' => [
                'nullable',
                'required_if:expiration_type,' . Discount::EXPIRATION_DURATION,
                'integer',
                'min:1'
            ],
            'start_date' => [
                'nullable',
                'required_if:expiration_type,' . Discount::EXPIRATION_PERIOD,
                'date'
            ],
             'end_date' => [
                'nullable',
                'required_if:expiration_type,' . Discount::EXPIRATION_PERIOD,
                'date',
                'after_or_equal:start_date' // End date must be after or same as start date
            ],
            'description' => 'nullable|string|max:1000',
        ];

        $messages = [
            'code.regex' => 'The code can only contain letters, numbers, underscores, and hyphens.',
            'duration_days.required_if' => 'Duration is required when expiration type is Duration.',
            'start_date.required_if' => 'Start Date is required when expiration type is Period.',
            'end_date.required_if' => 'End Date is required when expiration type is Period.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Nullify irrelevant expiration fields based on the selected type.
     */
    private function cleanupExpirationFields(array &$data): void
    {
        $type = $data['expiration_type'] ?? Discount::EXPIRATION_NONE;

        if ($type !== Discount::EXPIRATION_DURATION) {
            $data['duration_days'] = null;
        }
        if ($type !== Discount::EXPIRATION_PERIOD) {
            $data['start_date'] = null;
            $data['end_date'] = null;
        }
    }

    /**
     * Generate a unique discount code.
     */
    private function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (Discount::where('code', $code)->exists());
        return $code;
    }
}