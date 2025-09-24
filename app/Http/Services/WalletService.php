<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\WalletRepository;
use App\Models\Customer;
use App\Utils\Logger;
use Illuminate\Http\Request;
use Throwable;

class WalletService
{
    public function __construct(private WalletRepository $walletRepository) {}

    public function getWalletDetails(Customer $customer)
    {
        return $this->walletRepository->getWalletDetails($customer);
    }

    public function initiateWalletDeposit(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'amount' => 'required|integer',
                'phone_number' => 'required',
            ]);
            
            return $this->walletRepository->initiateWalletDeposit($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function getCustomerWallets(Request $request)
    {
        return $this->walletRepository->getCustomerWallets($request);
    }

    public function getWalletTransactions(Request $request)
    {
        return $this->walletRepository->getWalletTransactions($request);
    }

    public function airtelCallback(Request $request)
    {
        try {
            Logger::info($request->all());

            if ($request->transaction['status_code'] != 'TS') {
                throw new ExpectedException('Airtel transaction callback failed');
            }

            return $this->walletRepository->airtelCallback($request);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function mtnCallback(Request $request)
    {
        try {
            Logger::info($request->all());
            if ($request['status'] === 'FAILED') {
                throw new ExpectedException('MTN callback transaction failed');
            }
            return $this->walletRepository->mtnCallback($request);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function relworxCollectionCallback(Request $request)
    {
        $this->walletRepository->relworxCollectionCallback($request);
    }

    public function relworxDisbursementCallback(Request $request)
    {
        $this->walletRepository->relworxDisbursementCallback($request);
    }

    public function relworxProductCallback(Request $request)
    {
        $this->walletRepository->relworxProductCallback($request);
    }

    public function walletTransfer(Request $request, Customer $customer)
    {
        return $this->walletRepository->walletTransfer($request, $customer);
    }
}
