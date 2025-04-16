<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PolicyController extends Controller
{
    /**
     * Display a listing of the policies.
     */
    public function index(Request $request)
    {
        $query = Policy::query();

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name', 'like', $searchTerm)
                                    ->orWhere('key', 'like', $searchTerm));
        }

        $policies = $query->orderBy('name')->paginate(20);
        return view('admin.policies.index', compact('policies'));
    }

     /**
     * Display the specified policy content (Live Preview simulation).
     * This shows how the HTML content might render.
     */
    public function show(Policy $policy)
    {
        // You might want a simple layout for preview, different from admin layout
        return view('admin.policies.show', compact('policy'));
    }

    /**
     * Show the form for editing the specified policy.
     */
    public function edit(Policy $policy)
    {
        return view('admin.policies.edit', compact('policy'));
    }

    /**
     * Update the specified policy in storage.
     */
    public function update(Request $request, Policy $policy)
    {
        $validator = Validator::make($request->all(), [
            // Key is usually not editable once set, but validate if it is
             'key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('policies', 'key')->ignore($policy->id),
                'regex:/^[a-z0-9._-]+$/' // Allow lowercase, numbers, dot, underscore, hyphen
            ],
            'name' => 'required|string|max:255',
            'content' => 'required|string', // Basic validation, WYSIWYG handles HTML
        ], [
            'key.regex' => 'The key can only contain lowercase letters, numbers, dots, underscores, and hyphens.',
            'key.unique' => 'This policy key is already in use.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.policies.edit', $policy->id)
                        ->withErrors($validator)
                        ->withInput();
        }

        $validated = $validator->validated();

        // Optional: Sanitize HTML content before saving using a library like DOMPurifier
        // $validated['content'] = purify($validated['content']); // Example usage

        $policy->update($validated);

        return redirect()->route('admin.policies.index')
                         ->with('success', "Policy '{$policy->name}' updated successfully.");
    }

    /**
     * Remove the specified policy from storage.
     * Note: Consider implications - are these policies linked elsewhere?
     */
    public function destroy(Policy $policy)
    {
        $policyName = $policy->name;
        $policy->delete();

        return redirect()->route('admin.policies.index')
                         ->with('success', "Policy '{$policyName}' deleted successfully.");
    }


    public function create()
    {
        // Pass an empty policy object or null to the form partial
        $policy = new Policy(); // Or just pass null if your form handles it
        return view('admin.policies.create', compact('policy'));
    }

     /**
     * Store a newly created policy in storage.
      * ADDED THIS METHOD
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
             // Key must be unique on creation
             'key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('policies', 'key'), // No ignore here
                'regex:/^[a-z0-9._-]+$/'
            ],
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ], [
             'key.regex' => 'The key can only contain lowercase letters, numbers, dots, underscores, and hyphens.',
             'key.unique' => 'This policy key is already in use.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.policies.create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $validated = $validator->validated();

        // Optional: Sanitize HTML content
        // $validated['content'] = purify($validated['content']);

        Policy::create($validated);

        return redirect()->route('admin.policies.index')
                         ->with('success', "Policy '{$validated['name']}' created successfully.");
    }
}