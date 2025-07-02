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
    public function getLatestCustomerRegistration()
    {
        return Customer::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function getScheduledTransaction()
    {
        return ScheduledTransaction::query()
            ->with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function getLatestWalletTransaction()
    {
        return WalletTransaction::query()
            ->with('customer')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function userCount()
    {
        return User::count();
    }

    public function customerCount()
    {
        return Customer::count();
    }

    public function feedbackCount()
    {
        return Feedback::count();
    }

    public function failedScheduledTransactionCount()
    {
        return ScheduledTransactionLog::query()
            ->where('status', ScheduledTransactionLog::STATUS_FAILED)
            ->count();
    }

    public function failedWalletTransactionCount()
    {
        return WalletTransaction::query()
            ->with('customer')
            ->where('transaction_status', 'failed')
            ->count();
    }

    public function successfulScheduledTransactionCount()
    {
        return ScheduledTransactionLog::query()
            ->where('status', ScheduledTransactionLog::STATUS_SUCCESS)
            ->count();
    }

    public function successfulWalletTransactionCount()
    {
        return WalletTransaction::query()
            ->with('customer')
            ->where('transaction_status', 'completed')
            ->count();
    }

    public function getTotalScheduledTransactionSum()
    {
        return ScheduledTransaction::query()
            ->sum('amount');
    }
}
