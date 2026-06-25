<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\PredictiveAnalyticsService;

class PredictiveAnalyticsController extends Controller
{
    public function index(Request $request, PredictiveAnalyticsService $analyticsService): View
    {
        $timeFilter = $request->input('time_filter', 'all_time');
        
        $employeeRisks = $analyticsService->getFlightRiskEmployees();
        $highFlightRisks = $analyticsService->getHighFlightRiskEmployeesPaginated(4);
        $flightRiskChart = $analyticsService->getFlightRiskChartData();
        $highRiskProjects = $analyticsService->getHighRiskProjectsPaginated(2);
        
        $workloadMetrics = $analyticsService->getWorkloadMetrics($timeFilter);
        $deliveryChart = $analyticsService->getDeliveryForecastChartData($timeFilter);
        $capacityChart = $analyticsService->getCapacityPlanningChartData($timeFilter);

        return view('manager.analytics', compact(
            'employeeRisks', 
            'highFlightRisks',
            'flightRiskChart',
            'highRiskProjects', 
            'workloadMetrics', 
            'deliveryChart', 
            'capacityChart',
            'timeFilter'
        ));
    }
}
