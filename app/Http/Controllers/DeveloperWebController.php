<?php
// app/Http/Controllers/DeveloperWebController.php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use App\Models\ThirdPartyApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DeveloperWebController extends Controller
{
    /**
     * Display the developer tools dashboard.
     */
    public function index(Request $request)
    {
        $keys = ApiKey::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $thirdPartyKeys = ThirdPartyApiKey::where('user_id', $request->user()->id)
            ->orderBy('service_name', 'asc')
            ->get();

        $logs = DB::table('api_logs')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $teamMembers = \App\Models\TeamMember::orderBy('name')->take(15)->get();

        return view('developer.index', compact('keys', 'thirdPartyKeys', 'logs', 'teamMembers'));
    }

    /**
     * Generate a new API Key.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
        ]);

        $plainKey = 'mgr_live_' . Str::random(40);
        $hash = hash('sha256', $plainKey);
        $prefix = substr($plainKey, 0, 17); // e.g., mgr_live_abcdefgh

        ApiKey::create([
            'user_id' => $request->user()->id,
            'label' => $request->label,
            'key_hash' => $hash,
            'key_prefix' => $prefix,
            'is_active' => true,
        ]);

        return redirect()->back()->with([
            'success' => 'API Key generated successfully.',
            'new_api_key' => $plainKey,
        ]);
    }

    /**
     * Soft delete an API Key.
     */
    public function destroy(Request $request, $id)
    {
        $key = ApiKey::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $key->delete();

        return redirect()->back()->with('success', 'API Key deleted successfully.');
    }

    /**
     * Store a new third-party API Key.
     */
    public function storeThirdParty(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'api_key' => 'nullable|string',
            'api_url' => 'nullable|string|max:255',
            'model_name' => 'nullable|string|max:255',
        ]);

        ThirdPartyApiKey::create([
            'user_id' => $request->user()->id,
            'service_name' => strtolower($request->service_name),
            'api_key' => $request->api_key,
            'api_url' => $request->api_url,
            'model_name' => $request->model_name,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Third-party API key stored successfully.');
    }

    /**
     * Update an existing third-party API Key.
     */
    public function updateThirdParty(Request $request, $id)
    {
        $key = ThirdPartyApiKey::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'service_name' => 'required|string|max:255',
            'api_key' => 'nullable|string',
            'api_url' => 'nullable|string|max:255',
            'model_name' => 'nullable|string|max:255',
        ]);

        $data = [
            'service_name' => strtolower($request->service_name),
            'api_url' => $request->api_url,
            'model_name' => $request->model_name,
        ];

        if ($request->filled('api_key')) {
            $data['api_key'] = $request->api_key;
        }

        $key->update($data);

        return redirect()->back()->with('success', 'Third-party API key updated successfully.');
    }

    /**
     * Toggle active status of a third-party API Key.
     */
    public function toggleThirdParty(Request $request, $id)
    {
        $key = ThirdPartyApiKey::where('user_id', $request->user()->id)->findOrFail($id);
        $key->update([
            'is_active' => !$key->is_active
        ]);

        return redirect()->back()->with('success', 'Third-party API key status toggled successfully.');
    }

    /**
     * Delete a third-party API Key.
     */
    public function destroyThirdParty(Request $request, $id)
    {
        $key = ThirdPartyApiKey::where('user_id', $request->user()->id)->findOrFail($id);
        $key->delete();

        return redirect()->back()->with('success', 'Third-party API key removed successfully.');
    }
}

