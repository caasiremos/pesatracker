<?php

namespace App\Http\Services;

use App\Http\Respositories\DashboardRespository;

class DashboardService
{
    public function __construct(private DashboardRespository $dashboardRespository) {}

    public function getLatestCustomerRegistration() 
    {
        return $this->dashboardRespository->getLatestCustomerRegistration();
    }

    public function getLatestScheduledTransaction()
    {
        return $this->dashboardRespository->getScheduledTransaction();
    }

    public function dashboardStatistics()
    {
        $customerCount = $this->dashboardRespository->customerCount();
        $userCount = $this->dashboardRespository->userCount();
        $feedbackCount = $this->dashboardRespository->feedbackCount();
        $failedTransactionCount = $this->dashboardRespository->failedScheduledTransactionCount();
        $successfulTransactionCount = $this->dashboardRespository->successfulScheduledTransactionCount();
        $totalTransactionSum = $this->dashboardRespository->getTotalScheduledTransactionSum();

        return [
            'customerCount' => $customerCount,
            'userCount' => $userCount,
            'feedbackCount' => $feedbackCount,
            'failedTransactionCount' => $failedTransactionCount,
            'successfulTransactionCount' => $successfulTransactionCount,
            'failedTransactionCount' => $failedTransactionCount,
            'totalTransactionSum' => $totalTransactionSum
        ];
    }
}
