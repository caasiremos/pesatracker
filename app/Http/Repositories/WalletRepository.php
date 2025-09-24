<?php

namespace App\Http\Repositories;

use App\Exceptions\ExpectedException;
use App\Models\Customer;
use App\Models\TransactionLog;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Payment\AirtelMoneyGateWay;
use App\Payment\MtnMomoGateWay;
use App\Payment\Relworx\MobileMoney;
use App\Utils\Logger;
use App\Utils\Money;
use App\Utils\PhoneNumberUtil;
use App\Utils\SMS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Event\Code\Throwable;

class WalletRepository
{
    public function __construct(private MobileMoney $mobileMoney) {}

    public function walletTransfer(Request $request, Customer $customer)
    {
        // Get source wallet (current customer)
        $sourceWallet = Wallet::query()->where('customer_id', $customer->id)->first();
        
        // Get destination wallet using the wallet identifier from request
        $destinationWallet = Wallet::query()->where('wallet_identifier', $request->wallet_identifier)->first();
        
        // Check if both wallets exist
        if (!$sourceWallet || !$destinationWallet) {
            throw new \Exception('Source or destination wallet not found');
        }
        
        // Check if source wallet has sufficient balance
        if ($sourceWallet->balance < $request->amount) {
            throw new \Exception('Insufficient balance in source wallet');
        }
        
        // Perform the transfer
        $sourceWallet->balance -= $request->amount;
        $destinationWallet->balance += $request->amount;
        
        // Save both wallets
        $sourceWallet->save();
        $destinationWallet->save();
        
        return [
            'source_wallet' => $sourceWallet,
            'destination_wallet' => $destinationWallet,
            'amount_transferred' => $request->amount
        ];
    }

    /**
     * Get wallet details
     * 
     * @param Customer $customer
     * @return Wallet
     */
    public function getWalletDetails(Customer $customer)
    {
        return Wallet::query()
            ->select('id', 'customer_id', 'wallet_identifier', 'balance','deleted_at', 'created_at', 'updated_at')
            ->where('customer_id', $customer->id)
            ->first();
    }

    public function initiateWalletDeposit(Request $request, Customer $customer)
    {
        $walletTransactions = $this->initiateRelworxCollection($request, $customer);

        return $walletTransactions;
    }

    /**
     * Initiate telecom collection
     * 
     * @param Request $request
     * @param Customer $customer
     * @return WalletTransaction
     */
    private function initiateTelecomCollection(Request $request, Customer $customer)
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

    /**
     * Get customer wallets
     * 
     * @param Request $request
     * @return array
     */
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
     * Get wallet transactions
     * 
     * @param Request $request
     * @return array
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
     * @param Request $request
     * @param Customer $customer
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
     * Save transaction logs
     * 
     * @param Request $request
     * @param string $transaction_id
     * @param Customer $customer
     * @param string $provider
     * @param string $telecom_product
     * @return TransactionLog
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

    /**
     * Mtn callback
     * 
     * @param Request $request
     */
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
     * Airtel callback
     * 
     * @param Request $request
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

    /**
     * Update wallet balance
     * 
     * @param TransactionLog $transactionLog
     */
    private function updateWalletBalance(TransactionLog $transactionLog)
    {
        $wallet = Wallet::query()->where('customer_id', $transactionLog->customer_id)->first();
        $wallet->balance += $transactionLog->amount;
        $wallet->save();
    }

    /**
     * Update transaction log
     * 
     * @param TransactionLog $transaction_log
     * @return bool
     */
    private function updateTransactionLog($transaction_log): bool
    {
        $transaction_log->status = TransactionLog::STATUS_SUCCESS;
        $transaction_log->save();
        return true;
    }

    /**
     * Initiate relworx collection
     * 
     * @param Request $request
     * @param Customer $customer
     * @return WalletTransaction
     */
    private function initiateRelworxCollection(Request $request, Customer $customer)
    {
        $reference = Str::uuid();
        $response = $this->mobileMoney->initiateCollection($reference, $request->phone_number, $request->amount);
        Logger::info('Relworx collection response', $response);
        if ($response['success'] === true) {
            $this->saveTransactionLogs($request, $response['internal_reference'], $customer, 'relworx', 'relworx collection');
            //create wallet transaction
            WalletTransaction::query()->create([
                'customer_id' => $customer->id,
                'wallet_id' => $customer->wallet->id,
                'amount' => $request->amount,
                'provider' => 'relworx',
                'transaction_phone_number' => $request->phone_number,
                'external_reference' => $response['internal_reference'],
                'transaction_reference' => $reference,
                'transaction_status' => WalletTransaction::STATUS_PENDING,
                'telecom_product' => 'relworx collection',
            ]);
        } else {
            throw new ExpectedException('Failed initiating relworx collection');
        }
    }

    /**
     * Relworx collection callback
     * 
     * @param Request $request
     */
    public function relworxCollectionCallback(Request $request)
    {
        Logger::info('Relworx collection callback', $request->all());
        if ($request->status === 'success') {
            DB::beginTransaction();
            try {
                $walletTransaction = WalletTransaction::where('transaction_reference', $request->customer_reference)->first();
                if ($walletTransaction) {
                    $walletTransaction->transaction_status = $request->status;
                    $walletTransaction->save();

                    $wallet = Wallet::where('customer_id', $walletTransaction->customer_id)->first();
                
                    if ($wallet) {
                        $wallet->balance += $request->amount;
                        $wallet->save();
                        $wallet->refresh();
                        DB::commit();
                        $formattedAmount = Money::formatAmount($request->amount);
                        SMS::send($walletTransaction->customer->phone_number, "Your PesaTrack Wallet Topup of {$formattedAmount} was successful.");
                    } else {
                        throw new ExpectedException('Wallet not found for customer');
                    }
                } else {
                    throw new ExpectedException('Transaction not found');
                }
            } catch (Throwable $throwable) {
                DB::rollBack();
                throw $throwable;
            }
        } else {
            throw new ExpectedException('Failed initiating relworx collection');
        }
    }

    /**
     * Relworx disbursement callback
     * 
     * @param Request $request
     */
    public function relworxDisbursementCallback(Request $request)
    {
        Logger::info('Relworx disbursement callback', $request->all());
    }

    /**
     * Relworx product callback
     * 
     * @param Request $request
     */
    public function relworxProductCallback(Request $request)
    {
        Logger::info('Relworx product callback', $request->all());
    }
}
