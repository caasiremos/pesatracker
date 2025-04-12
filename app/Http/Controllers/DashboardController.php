<?php

namespace App\Http\Controllers;

use App\Http\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    /**
     * Returns dashboard statistics
     */
    public function index()
    {
        $dashboardStatistics = $this->dashboardService->dashboardStatistics();
        $latestCustomers =  $this->dashboardService->getLatestCustomerRegistration();
        $latestTransactions = $this->dashboardService->getLatestScheduledTransaction();

        $data = [
            'stats' => $dashboardStatistics,
            'transactions' => $latestTransactions,
            'customers' => $latestCustomers
        ];
        return Inertia::render('Dashboard', $data);
    }
}
