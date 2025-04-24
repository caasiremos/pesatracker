<?php

namespace App\Http\Repositories;

use App\Models\CashExpense;
use App\Models\Customer;
use App\Models\ScheduledTransaction;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Http\Request;

class ScheduledTransactionRepository
{
    public function getCustomerScheduledTransactions(Customer $customer)
    {
        return ScheduledTransaction::query()
            ->with('category:id,name', 'merchant:id,name')
            ->select(['id', 'amount', 'category_id', 'merchant_id'])
            ->where('customer_id', $customer->id)
            ->get();
    }

    public function createScheduledTransaction(Request $request, Customer $customer)
    {
        return ScheduledTransaction::query()
            ->create([
                'category_id' => $request->category_id,
                'merchant_id' => $request->merchant_id,
                'customer_id' => $customer->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'frequency' => $request->frequency,
                'reference' => $request->frequency,
                'note' => $request->note,
            ]);
    }

    /**
     * Scheduled transactions like customer
     */
    public function getScheduledTransactions(Request $request)
    {
        $search = $request->input('search');

        $transactions = ScheduledTransaction::query()
            ->with('customer', 'merchant', 'category')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(7)
            ->withQueryString();


        return [
            'transactions' => $transactions,
            'filters' => ['search' => $search]
        ];
    }
}
