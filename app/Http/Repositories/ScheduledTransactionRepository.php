<?php

namespace App\Http\Repositories;

use App\Models\CashExpense;
use App\Models\Customer;
use App\Models\ScheduledTransaction;
use Illuminate\Http\Request;

class ScheduledTransactionRepository
{
    public function getScheduledTransactions(Customer $customer)
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
}
