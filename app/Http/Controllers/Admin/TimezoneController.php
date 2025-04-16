<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timezone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TimezoneController extends Controller
{
    public function index(Request $request)
    {
        $query = Timezone::query();
        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(fn($q) => $q->where('name', 'like', $searchTerm)
                                     ->orWhere('offset', 'like', $searchTerm)
                                     ->orWhere('abbreviation', 'like', $searchTerm));
        }
         if ($request->filled('is_active') && $request->is_active !== 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $timezones = $query->orderBy('name')->paginate(30);
        return view('admin.timezones.index', compact('timezones'));
    }

    public function create()
    {
        return view('admin.timezones.create');
    }

    public function store(Request $request)
    {
        $validator = $this->validateTimezone($request);
        if ($validator->fails()) {
            return redirect()->route('admin.locations.timezones.create')->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        Timezone::create($validated);
        return redirect()->route('admin.locations.timezones.index')->with('success', 'Timezone created successfully.');
    }

    public function edit(Timezone $timezone)
    {
        return view('admin.timezones.edit', compact('timezone'));
    }

    public function update(Request $request, Timezone $timezone)
    {
        $validator = $this->validateTimezone($request, $timezone->id);
        if ($validator->fails()) {
            return redirect()->route('admin.locations.timezones.edit', $timezone->id)->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $timezone->update($validated);
        return redirect()->route('admin.locations.timezones.index')->with('success', 'Timezone updated successfully.');
    }

    public function destroy(Timezone $timezone)
    {
        // Check relations? (Unlikely for timezone unless linked to users/settings)
        $timezone->delete();
        return redirect()->route('admin.locations.timezones.index')->with('success', 'Timezone deleted successfully.');
    }

    private function validateTimezone(Request $request, ?int $timezoneId = null): \Illuminate\Validation\Validator
    {
        $rules = [
            'name' => ['required','string','max:100', Rule::unique('timezones')->ignore($timezoneId)],
            'offset' => 'nullable|string|max:15', // e.g., UTC-05:00
            'gmt_offset' => 'nullable|integer', // e.g., -18000
            'abbreviation' => 'nullable|string|max:10', // e.g., EST
            'is_active' => 'nullable|boolean',
        ];
        return Validator::make($request->all(), $rules);
    }
}