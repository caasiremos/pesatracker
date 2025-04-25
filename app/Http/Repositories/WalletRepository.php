<?php

namespace App\Http\Repositories;

use App\Exceptions\ExpectedException;
use App\Models\Customer;
use App\Models\TransactionLog;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Payment\AirtelMoneyGateWay;
use App\Payment\MtnMomoGateWay;
use App\Utils\Logger;
use App\Utils\PhoneNumberUtil;
use App\Utils\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Throw_;
use PHPUnit\Event\Code\Throwable;

class WalletRepository
{
    public function getWalletDetails(Customer $customer)
    {
        return Wallet::query()
            ->select('customer_id', 'wallet_identifier', 'balance')
            ->where('customer_id', $customer->id)
            ->first();
    }

    public function initiateWalletDeposit(Request $request, Customer $customer)
    {
        $walletTransactions = WalletTransaction::query()
            ->create([
                'customer_id' => $customer->id,
                'wallet_id' => $customer->wallet->id,
                'amount' => $request->amount,
                'provider' => PhoneNumberUtil::provider($request->phone_number),
                'transaction_phone_number' => $request->phone_number,
            ]);

        $provider = PhoneNumberUtil::provider($request->phone_number);
        if ($provider === 'mtn') {
            $this->initiateMtnCollection($request, $customer);
        } else {
            $this->initiateAirtelCollection($request, $customer);
        }

        return $walletTransactions;
    }

    public function getCustomerWallets(Request $request)
    {
        $search = $request->input('search');

        $wallets = Wallet::query()
            ->with('customer')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('wallet_identifier', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(7)
            ->withQueryString();

        return [
            'wallets' => $wallets,
            'filters' => ['search' => $search]
        ];
    }

    /**
     * Wallet transactions like customer
     */
    public function getWalletTransactions(Request $request)
    {
        $search = $request->input('search');

        $transactions = WalletTransaction::query()
            ->with('customer', 'wallet')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('wallet', function ($q) use ($search) {
                            $q->where('wallet_identifier', 'like', "%{$search}%");
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
     * Initiates customer payment, sends a pin prompt for
     * customer to complete transaction
     *
     * @throws Exception
     */
    public function initiateAirtelCollection(Request $request, Customer $customer)
    {
        $payments = AirtelMoneyGateWay::initiatePayments($request->amount, $request->phone_number);
        if (array_key_exists('id', $payments['data']['transaction'])) {
            $transaction_log = $this->saveTransactionLogs($request, $payments['data']['transaction']['id'], $customer, 'airtel', 'airtel collection');
            if (! $transaction_log) {
                throw new \Exception('Failed initiating wallet deposit');
            }
        }
    }

    /**
     * Initiate mtn mobile money collection
     *
     * @throws Exception
     */
    public function initiateMtnCollection(Request $request, Customer $customer)
    {
        $payment = (new MtnMomoGateWay($request))->initiateCustomerCollection($request->phone_number, $request->amount);
        [$external_id, $status_code] = $payment;
        if ($status_code !== 202) {
            throw new ExpectedException('Wallet deposit initiation failed, FAILED CODE => ' . $status_code);
        }
        $transaction_log = $this->saveTransactionLogs($request, $external_id, $customer, 'mtn', 'mtn collection');
        if (! $transaction_log) {
            throw new ExpectedException('Failed saving transaction log');
        }
    }

    /**
     * Log transaction before sending to mtn or airtel
     *
     * @throws Exception
     */
    private function saveTransactionLogs(
        Request $request,
        string $transaction_id,
        Customer $customer,
        string $provider,
        string $telecom_product
    ) {
        return TransactionLog::query()->create(array_merge($request->only('amount', 'phone_number'), [
            'telecom_transaction_id' => $transaction_id,
            'provider' => $provider,
            'customer_id' => $customer->id,
            'telecom_product' => $telecom_product,
        ]));
    }

    public function mtnCallback(Request $request)
    {
        DB::beginTransaction();
        try {
            $transactionId = $request['externalId'];
            $transactionLog = TransactionLog::where('telecom_transaction_id', $transactionId)->first();
            if ($this->updateTransactionLog($transactionLog)) {
                $this->updateWalletBalance($transactionLog);
                DB::commit();
                SMS::send($transactionLog->customer->phone_number, "Your Pesatrack app wallet deposit of {$transactionLog->amount} was successful.");
            }
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    /**
     * Airtel Money callback
     *
     */
    public function airtelCallback(Request $request)
    {
        DB::beginTransaction();
        try {
            $transactionId = $request->transaction['id'];
            $transactionLog = TransactionLog::where('telecom_transaction_id', $transactionId)->first();

            if ($this->updateTransactionLog($transactionLog)) {
                $this->updateWalletBalance($transactionLog);
                DB::commit();
                SMS::send($transactionLog->customer->phone_number, "Your Pesatrack app wallet deposit of {$transactionLog->amount} was successful.");
            }
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }
    }

    private function updateWalletBalance(TransactionLog $transactionLog)
    {
        $wallet = Wallet::query()->where('customer_id', $transactionLog->customer_id)->first();
        $wallet->balance += $transactionLog->amount;
        $wallet->save();
    }

    private function updateTransactionLog($transaction_log): bool
    {
        $transaction_log->status = TransactionLog::STATUS_SUCCESS;
        $transaction_log->save();
        return true;
    }
}
