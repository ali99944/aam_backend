<?php // app/Http/Controllers/Admin/CurrencyController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller {
    public function index(Request $request) { /* Standard index logic with search/filter */
        $query = Currency::query();
        if ($request->filled('search')) {
             $searchTerm = '%' . $request->search . '%';
             $query->where(fn($q) => $q->where('name', 'like', $searchTerm)->orWhere('code', 'like', $searchTerm));
        }
         if ($request->filled('is_active') && $request->is_active !== 'all') {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }
        $currencies = $query->orderBy('name')->paginate(20);
        return view('admin.currencies.index', compact('currencies'));
    }
    public function create() { return view('admin.currencies.create'); }
    public function store(Request $request) { /* Standard store logic */
        $validator = $this->validateCurrency($request);
        if ($validator->fails()) return redirect()->route('admin.locations.currencies.create')->withErrors($validator)->withInput();
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        Currency::create($validated);
        return redirect()->route('admin.locations.currencies.index')->with('success', 'Currency created.');
    }
    public function edit(Currency $currency) { return view('admin.currencies.edit', compact('currency')); }
    public function update(Request $request, Currency $currency) { /* Standard update logic */
        $validator = $this->validateCurrency($request, $currency->id);
        if ($validator->fails()) return redirect()->route('admin.locations.currencies.edit', $currency->id)->withErrors($validator)->withInput();
        $validated = $validator->validated();
        $validated['is_active'] = $request->has('is_active');
        $currency->update($validated);
        return redirect()->route('admin.locations.currencies.index')->with('success', 'Currency updated.');
    }
    public function destroy(Currency $currency) { /* Standard destroy logic */
        // Check if used by countries?
         if ($currency->countries()->count() > 0) {
            return redirect()->route('admin.locations.currencies.index')->with('error', 'Cannot delete currency assigned to countries.');
        }
        $currency->delete();
        return redirect()->route('admin.locations.currencies.index')->with('success', 'Currency deleted.');
    }
    private function validateCurrency(Request $request, ?int $currencyId = null): \Illuminate\Validation\Validator {
        $rules = [
            'name' => 'required|string|max:50',
            'code' => ['required','string','max:5', Rule::unique('currencies')->ignore($currencyId)],
            'symbol' => 'nullable|string|max:5',
            'exchange_rate' => 'required|numeric|min:0.000001', // Min slightly above 0
            'is_active' => 'nullable|boolean',
        ];
        return Validator::make($request->all(), $rules);
    }
}