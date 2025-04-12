<?php

namespace App\Http\Controllers;

use App\Http\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService){}
    
    /**
     * Returns dashboard statistics
     */
    public function index()
    {
        $dashboardStatistics = $this->dashboardService->dashboardStatistics();
        $latestTransactions =  $this->dashboardService->getLatestCustomerRegistration();
        $latestUserRegistration = $this->dashboardService->getLatestScheduledTransaction();

        $data = [
            $dashboardStatistics, $latestTransactions, $latestUserRegistration
        ];
        dd($data);
        return Inertia::render('Dashboard', compact($data));
    }
}
