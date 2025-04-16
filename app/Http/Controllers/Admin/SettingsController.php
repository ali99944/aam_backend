<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingsService; // Import your service
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // If needed for specific dropdowns

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Show the form for editing the application settings.
     */
    public function index()
    {
        $settings = $this->settingsService->getAllSettings();

        // Prepare data for dropdowns if needed (e.g., available locales, timezones)
        // Example: Fetch timezones for a dropdown
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL); // PHP built-in
        $availableLocales = config('app.available_locales', ['en' => 'English', 'ar' => 'العربية']); // Example locales from config

        return view('admin.settings.index', compact('settings', 'timezones', 'availableLocales'));
    }

    /**
     * Update the application settings in storage.
     */
    public function update(Request $request)
    {
        // --- Validation ---
        // Validate the known/critical settings. Add more as needed.
        $validator = Validator::make($request->all(), [
            // General Settings
            'general.app_name' => 'nullable|string|max:100',
            'general.app_url' => 'nullable|url|max:255',
            'general.admin_email' => 'nullable|email|max:255',
            'general.logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:1024', // Max 1MB for logo
            'general.favicon' => 'nullable|file|mimes:ico,png,svg|max:256', // Max 256KB for favicon

            // Contact Settings
            'contact.phone_primary' => 'nullable|string|max:30',
            'contact.phone_secondary' => 'nullable|string|max:30',
            'contact.email_support' => 'nullable|email|max:255',
            'contact.address.street' => 'nullable|string|max:255',
            'contact.address.city' => 'nullable|string|max:100',
            'contact.address.state' => 'nullable|string|max:100',
            'contact.address.zip' => 'nullable|string|max:20',
            'contact.address.country' => 'nullable|string|max:100',
            'contact.map_url' => 'nullable|url|max:500',

            // Social Links (Validate URLs if present)
            'social.links.facebook' => 'nullable|url|max:255',
            'social.links.twitter' => 'nullable|url|max:255',
            'social.links.instagram' => 'nullable|url|max:255',
            'social.links.linkedin' => 'nullable|url|max:255',
            'social.links.youtube' => 'nullable|url|max:255',
            // Add more social platforms as needed

            // Localization
            'localization.default_locale' => ['nullable', Rule::in(array_keys(config('app.available_locales', ['en', 'ar'])))],
            'localization.default_timezone' => ['nullable', Rule::in(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL))],
            'localization.enable_switcher' => 'nullable|boolean',

            // Payment Settings
            'payment.cod.enabled' => 'nullable|boolean',
            'payment.paytabs.enabled' => 'nullable|boolean',
            'payment.paytabs.profile_id' => 'required_if:payment.paytabs.enabled,1|nullable|string|max:100',
            'payment.paytabs.server_key' => 'required_if:payment.paytabs.enabled,1|nullable|string|max:100',
            // Add other payment gateways

            // Delivery Settings
            'delivery.default_fee' => 'nullable|numeric|min:0',
            'delivery.free_shipping_threshold' => 'nullable|numeric|min:0',
            // Add specific city fees later if managed here, or link to dedicated section

        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.settings.index')->withErrors($validator)->withInput();
        }

        // Prepare data, handling checkboxes and potential file uploads
        $dataToSave = $request->except(['_token', '_method', 'remove_logo', 'remove_favicon']); // Exclude helper fields

        // --- Handle File Uploads / Removals ---
        $currentSettings = $this->settingsService->getAllSettings();

        // Logo
        if ($request->hasFile('general.logo')) {
            if (!empty($currentSettings['general']['logo'] ?? null)) Storage::disk('public')->delete($currentSettings['general']['logo']);
            $dataToSave['general.logo'] = $request->file('general.logo')->store('settings/general', 'public');
        } elseif ($request->has('remove_logo')) {
            if (!empty($currentSettings['general']['logo'] ?? null)) Storage::disk('public')->delete($currentSettings['general']['logo']);
            $dataToSave['general.logo'] = null; // Set path to null
        }

        // Favicon
        if ($request->hasFile('general.favicon')) {
             if (!empty($currentSettings['general']['favicon'] ?? null)) Storage::disk('public')->delete($currentSettings['general']['favicon']);
            $dataToSave['general.favicon'] = $request->file('general.favicon')->store('settings/general', 'public');
        } elseif ($request->has('remove_favicon')) {
             if (!empty($currentSettings['general']['favicon'] ?? null)) Storage::disk('public')->delete($currentSettings['general']['favicon']);
            $dataToSave['general.favicon'] = null;
        }
        // --- End File Handling ---


        // Handle Checkboxes (convert presence to boolean 1/0 or true/false for JSON)
        // The service needs to handle saving these correctly, or we format here.
        // Assuming service handles array format from form names:
        $dataToSave['localization.enable_switcher'] = $request->has('localization.enable_switcher');
        $dataToSave['payment.cod.enabled'] = $request->has('payment.cod.enabled');
        $dataToSave['payment.paytabs.enabled'] = $request->has('payment.paytabs.enabled');
        // Add others...


        // Save settings using the service
        $success = $this->settingsService->saveSettings($dataToSave);

        if ($success) {
            return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
        } else {
            Log::error("Failed to save one or more settings files.");
            return redirect()->route('admin.settings.index')->with('error', 'Failed to save some settings. Please check logs.');
        }
    }
}