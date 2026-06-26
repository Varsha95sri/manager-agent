<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\TeamMember;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $rangeType = $request->input('range_type', 'all_time');
        $baseDate = \Carbon\Carbon::parse($request->input('date', \Carbon\Carbon::today()->toDateString()));
        $today = \Carbon\Carbon::today();
        
        $startDate = $baseDate->copy()->toDateString();
        $endDate = $baseDate->copy()->toDateString();
        
        if ($rangeType === 'date_wise') {
            $startDate = $baseDate->toDateString();
            $endDate = $startDate;
        } elseif ($rangeType === 'week_wise') {
            $startDate = $baseDate->copy()->startOfWeek()->toDateString();
            $endDate = $baseDate->copy()->endOfWeek()->toDateString();
        } elseif ($rangeType === 'month_wise') {
            $startDate = $baseDate->copy()->startOfMonth()->toDateString();
            $endDate = $baseDate->copy()->endOfMonth()->toDateString();
        } elseif ($rangeType === 'year_wise') {
            $startDate = $baseDate->copy()->startOfYear()->toDateString();
            $endDate = $baseDate->copy()->endOfYear()->toDateString();
        } elseif ($rangeType === 'custom_range') {
            $startDate = $request->input('start_date', $today->toDateString());
            $endDate = $request->input('end_date', $today->toDateString());
        }

        $query = LeaveRequest::with('employee')->orderBy('created_at', 'desc');
        
        if ($rangeType !== 'all_time') {
             $query->where(function($q) use ($startDate, $endDate) {
                 $q->whereBetween('start_date', [$startDate, $endDate])
                   ->orWhereBetween('end_date', [$startDate, $endDate])
                   ->orWhere(function($q2) use ($startDate, $endDate) {
                       $q2->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                   });
             });
        }

        $leaves = $query->paginate(15)->withQueryString();
        $employees = TeamMember::orderBy('name')->get();
        return view('manager.leaves', compact('leaves', 'employees', 'rangeType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:team_members,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        LeaveRequest::create($validated);
        return back()->with('success', 'Leave request added successfully.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $leave = LeaveRequest::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $leave->update($validated);
        return back()->with('success', 'Leave status updated.');
    }

    public function destroy($id): RedirectResponse
    {
        LeaveRequest::findOrFail($id)->delete();
        return back()->with('success', 'Leave request deleted.');
    }
}
