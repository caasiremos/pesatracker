<?php

namespace App\Http\Repositories;

use App\Models\Customer;
use App\Models\Feedback;
use App\Models\ScheduledTransaction;
use App\Models\ScheduledTransactionLog;
use App\Models\User;
use App\Models\WalletTransaction;

class DashboardRespository
{
    /**
     * Get latest customer registration
     * @return Collection
     */
    public function getLatestCustomerRegistration()
    {
        return Customer::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * Get latest scheduled transaction
     * @return Collection
     */
    public function getScheduledTransaction()
    {
        return ScheduledTransaction::query()
            ->with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * Get latest wallet transaction
     * @return Collection
     */
    public function getLatestWalletTransaction()
    {
        return WalletTransaction::query()
            ->with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * Get total number of users
     * @return int
     */
    public function userCount()
    {
        return User::count();
    }

    /**
     * Get total number of customers
     * @return int
     */
    public function customerCount()
    {
        return Customer::count();
    }

    /**
     * Get total number of feedbacks
     * @return int
     */
    public function feedbackCount()
    {
        return Feedback::count();
    }

    /**
     * Get total number of failed scheduled transactions
     * @return int
     */
    public function failedScheduledTransactionCount()
    {
        return ScheduledTransactionLog::query()
            ->where('status', ScheduledTransactionLog::STATUS_FAILED)
            ->count();
    }

    /**
     * Get total number of failed wallet transactions
     * @return int
     */
    public function failedWalletTransactionCount()
    {
        return WalletTransaction::query()
            ->with('customer')
            ->where('transaction_status', 'failed')
            ->count();
    }

    /**
     * Get total number of successful scheduled transactions
     * @return int
     */
    public function successfulScheduledTransactionCount()
    {
        return ScheduledTransactionLog::query()
            ->where('status', ScheduledTransactionLog::STATUS_SUCCESS)
            ->count();
    }

    /**
     * Get total number of successful wallet transactions
     * @return int
     */
    public function successfulWalletTransactionCount()
    {
        return WalletTransaction::query()
            ->with('customer')
            ->where('transaction_status', 'completed')
            ->count();
    }

    /**
     * Get total sum of scheduled transactions
     * @return int
     */
    public function getTotalScheduledTransactionSum()
    {
        return ScheduledTransaction::query()
            ->sum('amount');
    }
}
