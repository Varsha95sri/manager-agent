<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request): View
    {
        $query = AuditLog::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('model_type', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('changes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter_action')) {
            $query->where('action', $request->input('filter_action'));
        }
        
        if ($request->filled('filter_model')) {
            $query->where('model_type', $request->input('filter_model'));
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        return view('manager.audit', compact('logs'));
    }
}
