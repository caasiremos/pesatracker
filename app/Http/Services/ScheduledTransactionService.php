<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\ScheduledTransactionRepository;
use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;

class ScheduledTransactionService
{
    public function __construct(private ScheduledTransactionRepository $scheduledTransaction)
    {
        
    }

    public function getCustomerScheduledTransaction(Customer $customer)
    {
        return $this->scheduledTransaction->getCustomerScheduledTransactions($customer);
    }

    public function getScheduledTransactions(Request $request)
    {
        return $this->scheduledTransaction->getScheduledTransactions($request);
    }

    public function createScheduledTransaction(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'amount' => 'required|integer',
                'category_id' => [
                    'required',
                    'exists:categories,id',
                    // Add a custom rule to verify the category belongs to the customer
                    function ($attribute, $value, $fail) use ($customer) {
                        $categoryExists = $customer->categories()->where('id', $value)->exists();
                        if (!$categoryExists) {
                            $fail('The selected category does not belong to this customer.');
                        }
                    },
                ],
                 'merchant_id' => [
                    'required',
                    'exists:merchants,id',
                    // Add a custom rule to verify the category belongs to the customer
                    function ($attribute, $value, $fail) use ($customer) {
                        $merchantExists = $customer->merchants()->where('id', $value)->exists();
                        if (!$merchantExists) {
                            $fail('The selected merchant does not belong to this customer.');
                        }
                    },
                ]
            ]);

            return $this->scheduledTransaction->createScheduledTransaction($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Get upcoming scheduled transactions count
     * 
     * @param Customer $customer
     * @return array
     */
    public function upcomingScheduledTransactionsCountByDate(Customer $customer)
    {
        try {
            return $this->scheduledTransaction->getUpcomingScheduledTransactionsCountByDate($customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Get upcoming scheduled transactions by date
     * 
     * @param Request $request
     * @param Customer $customer
     * @return array
     */
    public function upcomingScheduledTransactionsByDate(Request $request, Customer $customer)
    {
        return $this->scheduledTransaction->getUpcomingTransactionsByDate($request, $customer);
    }
}