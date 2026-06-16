<?php
// app/Http/Controllers/DeveloperController.php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    /**
     * Display a listing of the developer API keys for the authenticated user.
     */
    public function index(Request $request)
    {
        $keys = ApiKey::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($keys);
    }

    /**
     * Generate a new API key for the authenticated user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
        ]);

        // Generate a new secure random API key with "mgr_live_" prefix
        $plainKey = 'mgr_live_' . Str::random(40);
        $hash = hash('sha256', $plainKey);

        // Store the prefix to help the user identify which key is which
        $prefix = substr($plainKey, 0, 17); // e.g., mgr_live_abcdefgh

        $apiKey = ApiKey::create([
            'user_id' => $request->user()->id,
            'label' => $request->label,
            'key_hash' => $hash,
            'key_prefix' => $prefix,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'API Key created successfully. Make sure to copy it now as it will not be shown again.',
            'key' => $plainKey,
            'api_key' => $apiKey,
        ], 201);
    }

    /**
     * Soft delete the specified API key.
     */
    public function destroy(Request $request, $id)
    {
        $apiKey = ApiKey::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $apiKey->delete();

        return response()->json([
            'message' => 'API Key deleted successfully.',
        ]);
    }

    /**
     * Retrieve the last 50 API logs for the authenticated user.
     */
    public function logs(Request $request)
    {
        $logs = \Illuminate\Support\Facades\DB::table('api_logs')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($logs);
    }
}
