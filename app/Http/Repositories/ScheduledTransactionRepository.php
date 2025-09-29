<?php

namespace App\Http\Repositories;

use App\Models\CashExpense;
use App\Models\Customer;
use App\Models\ScheduledTransaction;
use App\Models\ScheduledTransactionLog;
use App\Payment\Relworx\Products;
use App\Utils\Logger;
use App\Utils\Money;
use App\Utils\SMS;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduledTransactionRepository
{
    /**
     * Get the business account number from the config
     *
     * @return string
     */
    private static function accountNo()
    {
        return config('services.relworx.account_id');
    }

    /**
     * Get a transaction reference
     *
     * @return string
     */
    private static function transactionReference()
    {
        return Str::uuid()->toString();
    }

    /**
     * Get customer scheduled transactions
     *
     * @param Customer $customer
     * @return array
     */
    public function getCustomerScheduledTransactions(Customer $customer)
    {
        return ScheduledTransaction::query()
            ->with('category:id,name', 'product:id,name,code')
            ->select(['id', 'amount', 'category_id', 'product_id', 'code'])
            ->where('customer_id', $customer->id)
            ->get();
    }

    /**
     * Get upcoming scheduled transactions count by date
     *
     * @param Customer $customer
     * @return array
     */
    public function getUpcomingScheduledTransactionsCountByDate(Customer $customer)
    {
        return ScheduledTransaction::query()
            ->selectRaw('payment_date, COUNT(*) as count')
            ->where('customer_id', $customer->id)
            ->groupBy('payment_date')
            ->orderBy('payment_date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->payment_date,
                    'count' => $item->count,
                ];
            })
            ->values();
    }

    /**
     * Get upcoming scheduled transactions today
     *
     * @param Customer $customer
     * @return array
     */
    public function getUpcomingScheduledTransactionsToday(Customer $customer)
    {
        return ScheduledTransaction::query()
            ->where('customer_id', $customer->id)
            ->where('payment_date', now()->toDateString())
            ->sum('amount');
    }

    /**
     * Get upcoming scheduled transactions this week
     *
     * @param Customer $customer
     * @return array
     */
    public function getUpcomingScheduledTransactionsThisWeek(Customer $customer)
    {
        return ScheduledTransaction::query()
            ->where('customer_id', $customer->id)
            ->whereBetween('payment_date', [
                now()->startOfWeek()->toDateString(),
                now()->endOfWeek()->toDateString()
            ])
            ->sum('amount');
    }

    /**
     * Get upcoming scheduled transactions this month
     *
     * @param Customer $customer
     * @return array
     */
    public function getUpcomingScheduledTransactionsThisMonth(Customer $customer)
    {
        return ScheduledTransaction::query()
            ->where('customer_id', $customer->id)
            ->whereBetween('payment_date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString()
            ])
            ->sum('amount');
    }

    /**
     * Get upcoming transactions balances
     *
     * @param Customer $customer
     * @return array
     */
    public function getUpcomingTransactionsBalances(Customer $customer)
    {
        return [
            'today' => $this->getUpcomingScheduledTransactionsToday($customer),
            'this_week' => $this->getUpcomingScheduledTransactionsThisWeek($customer),
            'this_month' => $this->getUpcomingScheduledTransactionsThisMonth($customer),
        ];
    }

    /**
     * Get upcoming transactions by date
     *
     * @param Request $request
     * @param Customer $customer
     * @return array
     */
    public function getUpcomingTransactionsByDate(Request $request, Customer $customer)
    {
        $date = Carbon::createFromFormat('d/m/Y', $request->payment_date)->format('Y-m-d');
        return ScheduledTransaction::query()
            ->where('customer_id', $customer->id)
            ->where('payment_date', $date)
            ->get();
    }

    /**
     * Create a scheduled transaction
     *
     * @param Request $request
     * @param Customer $customer
     * @return ScheduledTransaction
     */
    public function createScheduledTransaction(Request $request, Customer $customer)
    {
        return ScheduledTransaction::query()
            ->create([
                'category_id' => $request->category_id,
                'product_id' => $request->product_id,
                'code' => $request->code,
                'customer_id' => $customer->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'frequency' => $request->frequency,
                'note' => $request->note,
                'transaction_phone_number' => $customer->phone_number,
            ]);
    }

    /**
     * Scheduled transactions like customer
     *
     * @param Request $request
     * @return array
     */
    public function getScheduledTransactions(Request $request)
    {
        $search = $request->input('search');

        $transactions = ScheduledTransaction::query()
            ->with('customer', 'product', 'category')
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

    /**
     * Run scheduled transactions that are due today
     */
    public function runScheduledTransactions()
    {
        $scheduledTransactions = ScheduledTransaction::where('payment_date', now()->toDateString())->get();

        foreach ($scheduledTransactions as $transaction) {
            if ($transaction->product->code == 'UMEME_PRE_PAID') {
                if ($this->checkCustomerBalance($transaction)) {
                    $this->lightPayment($transaction);
                } else {
                    $this->sendInsufficientBalanceSms($transaction);
                }
            }
            if ($transaction->product->code == 'NATIONAL_WATER') {
                if ($this->checkCustomerBalance($transaction)) {
                    $this->waterPayment($transaction);
                } else {
                    $this->sendInsufficientBalanceSms($transaction);
                }
            }
            if ($transaction->product->code == 'MTN_UG_VOICE_BUNDLES' || $transaction->product->code == 'AIRTEL_UG_VOICE_BUNDLES') {
                if ($this->checkCustomerBalance($transaction)) {
                    $this->voiceBundlesPayment($transaction);
                } else {
                    $this->sendInsufficientBalanceSms($transaction);
                }
            }
            if (
                $transaction->product->code == 'MTN_UG_AIRTIME' ||
                $transaction->product->code == 'AIRTEL_UG_AIRTIME' ||
                $transaction->product->code == 'UTL_AIRTIME'
            ) {
                if ($this->checkCustomerBalance($transaction)) {
                    $this->airtimePayment($transaction);
                } else {
                    $this->sendInsufficientBalanceSms($transaction);
                }
            }

            if (
                $transaction->product->code == 'MTN_UG_INTERNET' ||
                $transaction->product->code == 'AIRTEL_UG_INTERNET' ||
                $transaction->product->code == 'ROKE_TELECOM_UG_INTERNET'
            ) {
                if ($this->checkCustomerBalance($transaction)) {
                    $this->internetPayment($transaction);
                } else {
                    $this->sendInsufficientBalanceSms($transaction);
                }
            }
        }
    }

    /**
     * Light payment
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function lightPayment(ScheduledTransaction $transaction)
    {
        $reference = static::transactionReference();

        $params = [
            'account_no' => static::accountNo(),
            'reference' => $reference,
            'msisdn' => $transaction->code,
            'amount' => $transaction->amount,
            'product_code' => $transaction->product->code,
            'contact_phone' => $transaction->transaction_phone_number,
            'location_id' => null
        ];

        Logger::info($params);

        // Create a transaction log
        echo "Creating transaction log\n";
        $this->createTransactionLog($transaction, $reference, ScheduledTransaction::STATUS_PENDING);

        // Validate the product
        echo "Validating product light payment\n";
        $validateProduct = (new Products())->validateProduct($params);

        Logger::info($validateProduct);

        if ($validateProduct['success']) {
            $purchaseProductParams = [
                "account_no" => static::accountNo(),
                "validation_reference" => $validateProduct['validation_reference'],
            ];
            // Purchase the product
            echo "Purchasing product\n";
            $purchase = (new Products())->purchaseProduct($purchaseProductParams);

            if ($purchase['success']) {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_SUCCESS, $purchase['internal_refence']);
                // Deduct the amount from the customer's balance
                echo "Deducting amount from customer balance\n";
                $this->deductAmountFromCustomerBalance($transaction);
                // Send an SMS
                echo "Sending SMS\n";
                $this->sendSms($transaction);
                // Update the payment date
                echo "Updating payment date\n";
                $this->updatePaymentDate($transaction);
            } else {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_FAILED, null);
            }
        }
    }

    /**
     * Water payment
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function waterPayment(ScheduledTransaction $transaction)
    {
        $reference = static::transactionReference();
        $params = [
            'account_no' => static::accountNo(),
            'reference' => $reference,
            'msisdn' => $transaction->code,
            'amount' => $transaction->amount,
            'product_code' => $transaction->product->code,
            'contact_phone' => $transaction->transaction_phone_number,
            "location_id" => 22632, //Location ID for Other National Water Areas
        ];

        Logger::info($params);

        // Create a transaction log
        echo "Creating transaction log\n";
        $this->createTransactionLog($transaction, $reference, ScheduledTransaction::STATUS_PENDING);

        // Validate the product
        echo "Validating product\n";
        $validateProduct = (new Products())->validateProduct($params);

        Logger::info($validateProduct);

        if ($validateProduct['success']) {
            $purchaseProductParams = [
                "account_no" => static::accountNo(),
                "validation_reference" => $validateProduct['validation_reference'],
            ];
            // Purchase the product
            echo "Purchasing product\n";
            $purchase = (new Products())->purchaseProduct($purchaseProductParams);

            if ($purchase['success']) {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_SUCCESS, $purchase['internal_refence']);
                // Deduct the amount from the customer's balance
                echo "Deducting amount from customer balance\n";
                $this->deductAmountFromCustomerBalance($transaction);
                // Send an SMS
                echo "Sending SMS\n";
                $this->sendSms($transaction);
                // Update the payment date
                echo "Updating payment date\n";
                $this->updatePaymentDate($transaction);
            } else {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_FAILED, null);
            }
        }
    }

    /**
     * Voice bundles payment
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function voiceBundlesPayment(ScheduledTransaction $transaction)
    {
        $reference = static::transactionReference();
        $params = [
            'account_no' => static::accountNo(),
            'reference' => $reference,
            'msisdn' => $transaction->transaction_phone_number,
            'amount' => $transaction->amount,
            'product_code' => $transaction->product->code,
            'contact_phone' => $transaction->transaction_phone_number,
            'location_id' => "",
        ];

        Logger::info($params);

        // Create a transaction log
        echo "Creating transaction log\n";
        $this->createTransactionLog($transaction, $reference, ScheduledTransaction::STATUS_PENDING);

        // Validate the product
        echo "Validating product\n";
        $validateProduct = (new Products())->validateProduct($params);
        Logger::info($validateProduct);

        if ($validateProduct['success']) {
            $purchaseProductParams = [
                "account_no" => static::accountNo(),
                "validation_reference" => $validateProduct['validation_reference'],
            ];
            // Purchase the product
            echo "Purchasing product\n";
            $purchase = (new Products())->purchaseProduct($purchaseProductParams);

            if ($purchase['success']) {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_SUCCESS, $purchase['internal_refence']);
                // Deduct the amount from the customer's balance
                echo "Deducting amount from customer balance\n";
                $this->deductAmountFromCustomerBalance($transaction);
                // Send an SMS
                echo "Sending SMS\n";
                $this->sendSms($transaction);
                // Update the payment date
                echo "Updating payment date\n";
                $this->updatePaymentDate($transaction);
            } else {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_FAILED, null);
            }
        }
    }

    /**
     * Airtime payment
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function airtimePayment(ScheduledTransaction $transaction)
    {
        $reference = static::transactionReference();
        $params = [
            'account_no' => static::accountNo(),
            'reference' => $reference,
            'msisdn' => $transaction->transaction_phone_number,
            'amount' => $transaction->amount,
            'product_code' => $transaction->product->code,
            'contact_phone' => $transaction->transaction_phone_number,
            'location_id' => "",
        ];

        Logger::info($params);

        // Create a transaction log
        echo "Creating transaction log\n";
        $this->createTransactionLog($transaction, $reference, ScheduledTransaction::STATUS_PENDING);

        // Validate the product
        echo "Validating product\n";
        $validateProduct = (new Products())->validateProduct($params);

        Logger::info($validateProduct);

        if ($validateProduct['success']) {
            $purchaseProductParams = [
                "account_no" => static::accountNo(),
                "validation_reference" => $validateProduct['validation_reference'],
            ];
            // Purchase the product
            echo "Purchasing product\n";
            $purchase = (new Products())->purchaseProduct($purchaseProductParams);

            if ($purchase['success']) {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_SUCCESS, $purchase['internal_refence']);
                // Deduct the amount from the customer's balance
                echo "Deducting amount from customer balance\n";
                $this->deductAmountFromCustomerBalance($transaction);
                // Send an SMS
                echo "Sending SMS\n";
                $this->sendSms($transaction);
                // Update the payment date
                echo "Updating payment date\n";
                $this->updatePaymentDate($transaction);
            } else {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_FAILED, null);
            }
        }
    }

    /**
     * Internet payment
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function internetPayment(ScheduledTransaction $transaction)
    {
        $reference = static::transactionReference();
        $params = [
            'account_no' => static::accountNo(),
            'reference' => $reference,
            'msisdn' => $transaction->transaction_phone_number,
            'amount' => $transaction->amount,
            'product_code' => $transaction->code,
            'contact_phone' => $transaction->transaction_phone_number,
            'location_id' => "",
        ];

        Logger::info($params);

        // Create a transaction log
        echo "Creating transaction log\n";
        $this->createTransactionLog($transaction, $reference, ScheduledTransaction::STATUS_PENDING);

        // Validate the product
        echo "Validating product\n";
        $validateProduct = (new Products())->validateProduct($params);

        Logger::info($validateProduct);

        if ($validateProduct['success']) {
            $purchaseProductParams = [
                "account_no" => static::accountNo(),
                "validation_reference" => $validateProduct['validation_reference'],
            ];
            // Purchase the product
            echo "Purchasing product\n";
            $purchase = (new Products())->purchaseProduct($purchaseProductParams);

            if ($purchase['success']) {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_SUCCESS, $purchase['internal_refence']);
                // Deduct the amount from the customer's balance
                echo "Deducting amount from customer balance\n";
                $this->deductAmountFromCustomerBalance($transaction);
                // Send an SMS
                echo "Sending SMS\n";
                $this->sendSms($transaction);
                // Update the payment date
                echo "Updating payment date\n";
                $this->updatePaymentDate($transaction);
            } else {
                // Update the transaction log
                echo "Updating transaction log\n";
                $this->updateTransactionLog($reference, ScheduledTransaction::STATUS_FAILED, null);
            }
        }
    }

    /**
     * Update the payment date
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function updatePaymentDate(ScheduledTransaction $transaction)
    {
        $paymentDate = $transaction->payment_date;
        if ($transaction->frequency == 'Monthly') {
            $paymentDate = now()->addMonth()->toDateString();
        } else if ($transaction->frequency == 'Weekly') {
            $paymentDate = now()->addWeek()->toDateString();
        } else if ($transaction->frequency == 'Daily') {
            $paymentDate = now()->addDay()->toDateString();
        } else if ($transaction->frequency == 'Yearly') {
            $paymentDate = now()->addYear()->toDateString();
        } else if ($transaction->frequency == 'Quarterly') {
            $paymentDate = now()->addQuarter()->toDateString();
        } else {
            $paymentDate = now()->addMonth()->toDateString();
        }

        $transaction->payment_date = $paymentDate;
        $transaction->save();
    }

    /**
     * Create a transaction log
     *
     * @param ScheduledTransaction $transaction
     * @param string $reference
     * @param string $status
     * @return void
     */
    private function createTransactionLog(ScheduledTransaction $transaction, $reference, $status)
    {
        ScheduledTransactionLog::create([
            'scheduled_transaction_id' => $transaction->id,
            'status' => $status,
            'amount' => $transaction->amount,
            'scheduled_date' => Carbon::createFromFormat('d/m/Y', $transaction->payment_date)->format('Y-m-d'),
            'internal_transaction_reference' => $reference,
        ]);
    }

    /**
     * Update the transaction log
     *
     * @param string $reference
     * @param string $status
     * @param string $externalTransactionReference
     * @return void
     */
    private function updateTransactionLog($reference, $status, $externalTransactionReference)
    {
        $transactionLog = ScheduledTransactionLog::where('internal_transaction_reference', $reference)->first();
        $transactionLog->update([
            'status' => $status,
            'external_transaction_reference' => $externalTransactionReference,
        ]);
    }

    /**
     * Deduct the amount from the customer's balance
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function deductAmountFromCustomerBalance(ScheduledTransaction $transaction)
    {
        $wallet = $transaction->customer->wallet;
        $wallet->balance -= $transaction->amount;
        $wallet->save();
    }

    /**
     * Send an SMS
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function sendSms(ScheduledTransaction $transaction)
    {
        $product = $transaction->product->name;
        $amount = Money::formatAmount($transaction->amount);
        $phone = $transaction->customer->phone_number;
        $customer = $transaction->customer->name;
        $message = "Dear {$customer}, your PesaTrack scheduled payment of {$amount} for {$product} has been settled from your wallet on {$transaction->payment_date}. Thank you for using PesaTrack.";
        SMS::send($phone, $message);
    }

    /**
     * Check the customer's balance
     *
     * @param ScheduledTransaction $transaction
     * @return bool
     */
    private function checkCustomerBalance(ScheduledTransaction $transaction)
    {
        $wallet = $transaction->customer->wallet;
        return $wallet->balance >= $transaction->amount;
    }

    /**
     * Send an SMS
     *
     * @param ScheduledTransaction $transaction
     * @return void
     */
    private function sendInsufficientBalanceSms(ScheduledTransaction $transaction)
    {
        $product = $transaction->product->name;
        $amount = Money::formatAmount($transaction->amount);
        $phone = $transaction->customer->phone_number;
        $customer = $transaction->customer->name;
        $message = "Dear {$customer}, your PesaTrack scheduled payment of {$amount} for {$product} has failed because you have insufficient balance. Please recharge your account to continue using PesaTrack.";
        SMS::send($phone, $message);
    }
}
