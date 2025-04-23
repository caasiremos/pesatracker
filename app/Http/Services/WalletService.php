<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\WalletRepository;
use App\Models\Customer;
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
                'phone_number' => 'required'
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
}
