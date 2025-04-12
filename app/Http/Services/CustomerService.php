<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Respositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\Customer;
use Throwable;

class CustomerService
{
    public function __construct(private CustomerRepository $customerRepository) {}

    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|min:12|max:12|unique:' . Customer::class,
                'email' => 'required|string|lowercase|email|max:255|unique:' . Customer::class,
                'password' => ['required', Rules\Password::defaults()],
            ]);

            return $this->customerRepository->register($request);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
