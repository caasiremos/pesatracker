<?php

namespace App\Http\Respositories;

use App\Models\Customer;
use App\Models\Feedback;
use App\Models\ScheduledTransaction;
use App\Models\User;
use App\Models\WalletTransaction;

class DashboardRespository
{
    public function getLatestCustomerRegistration()
    {
        return Customer::query()
            ->orderByDesc('created_at')
            ->get()
            ->limit(5);
    }

    public function getScheduledTransaction()
    {
        return ScheduledTransaction::query()
            ->orderByDesc('created_at')
            ->get()
            ->limit(5);
    }

    public function getLatestWalletTransaction()
    {
        return WalletTransaction::query()
            ->orderByDesc('created_at')
            ->get()
            ->limit(5);
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

    public function failedScheduledTransaction()
    {
        return ScheduledTransaction::query()
            ->where('stutus', 'failed')
            ->get()
            ->count();
    }

    public function failedWalletTransaction()
    {
        return WalletTransaction::query()
            ->where('stutus', 'failed')
            ->get()
            ->count();
    }

    public function successfulScheduledTransaction()
    {
        return ScheduledTransaction::query()
            ->where('stutus', 'completed')
            ->get()
            ->count();
    }

    public function successfulWalletTransaction()
    {
        return WalletTransaction::query()
            ->where('stutus', 'completed')
            ->get()
            ->count();
    }
}
