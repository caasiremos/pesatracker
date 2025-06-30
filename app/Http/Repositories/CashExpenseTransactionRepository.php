<?php

namespace App\Http\Repositories;

use App\Models\CashExpense;
use App\Models\Customer;
use Illuminate\Http\Request;

/**
 * Cash expense transaction repository
 */
class CashExpenseTransactionRepository
{
    /**
     * Get all cash expense transactions for a customer
     *
     * @param  Customer  $customer - customer
     * @return Collection
     */
    public function getCustomerCashExpenseTransactions(Customer $customer)
    {
        return CashExpense::query()
            ->with('category:id,name',)
            ->select(['id', 'amount', 'category_id'])
            ->where('customer_id', $customer->id)
            ->get();
    }

    /**
     * Create a new cash expense transaction
     *
     * @param  Request  $request - params
     * @param  Customer  $customer - customer
     * @return CashExpense
     */
    public function createCashExpenseTransaction(Request $request, Customer $customer)
    {
        return CashExpense::query()
            ->create([
                'category_id' => $request->category_id,
                'customer_id' => $customer->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'note' => $request->note,
                'attachment' => $this->saveDocumentFile($request, 'attachment', $customer),
            ]);
    }

    /**
     * Process document and save to storage
     *
     * @param  Request  $request - params
     * @param  string  $document_name - name to save document
     * @param  Customer  $customer - customer
     * @return void
     */
    private function saveDocumentFile(Request $request, $document_name, Customer $customer)
    {
        if ($request->hasFile($document_name)) {
            $path = rand(1111111, 99999999) . '-' . date('m.d.y') . '.' . $request->file($document_name)
                ->getClientOriginalExtension();
            $request->file($document_name)->move(public_path('attachments'), $path);
            $customer->$document_name = '/attachments/' . $path;
        }
    }

    /**
     * Get cash expense transactions
     *
     * @param  Request  $request - params
     * @return array
     */
    public function getCashExpenseTransactions(Request $request)
    {
        $search = $request->input('search');

        $transactions = CashExpense::query()
            ->with('customer', 'category')
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
