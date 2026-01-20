<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\Customer;
use App\Models\Otp;
use App\Utils\Logger;
use App\Utils\SMS;
use Illuminate\Support\Facades\Hash;
use Throwable;

class CustomerService
{
    public function __construct(private CustomerRepository $customerRepository) {}

    /**
     * Register a new customer
     * @param Request $request
     * @return Customer
     * @throws ExpectedException
     * @throws Throwable
     */
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

    /**
     * Update customer details
     * @param Request $request
     * @param Customer $customer
     * @return Customer
     * @throws ExpectedException
     * @throws Throwable
     */
    public function updatedCustomerDetails(Request $request, Customer $customer)
    {
        try {
            return $this->customerRepository->updateCustomerDetails($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Login a customer
     * @param Request $request
     * @return array
     * @throws ExpectedException
     * @throws Throwable
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $customer = Customer::query()
                ->where('email', $request->email)
                ->where('status', 'active')
                ->first();

            if (!$customer) {
                throw new ExpectedException("Customer with this email does not exist or account is not active");
            }

            if (!Hash::check($request->password, $customer->password)) {
                throw new ExpectedException("Incorrect Password");
            }

             $token = $this->customerRepository->login($customer);

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'customer' => $customer->only('id', 'name', 'email', 'phone_number', 'status'),
            ];
            return $data;
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Logout a customer
     * @param Request $request
     * @return bool
     * @throws ExpectedException
     * @throws Throwable
     */
    public function logout(Request $request)
    {
        return $this->customerRepository->logout($request);
    }

    /**
     * Send OTP to customer to verify phone number
     * @return int
     * @throws ExpectedException
     * @throws Throwable
     */
    public function sendOtp(): int
    {
        $code =  $this->customerRepository->sendOtp();
        SMS::send(request()->phone_number, "Pesatrack OTP is {$code}. Don't share this code with anyone.");
        return $code;
    }

    /**
     * Verify OTP submitted
     * @param Request $request
     * @return bool
     * @throws ExpectedException
     * @throws Throwable
     */
    public function verifyOtp(Request $request): bool
    {
        Logger::info("verifyOtp", $request->all());
        try {
            $otp = Otp::query()
                ->where('phone_number', $request->phone_number)
                ->where('code', $request->code)
                ->where('type', $request->type)
                ->where('matched', false)
                ->first();

            if (!$otp) {
                throw new ExpectedException("Invalid OTP");
            }

            return $this->customerRepository->verifyOtp();
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Get all customers
     * @param Request $request
     * @return Collection
     * @throws ExpectedException
     * @throws Throwable
     */
    public function getCustomers(Request $request)
    {
        return $this->customerRepository->getCustomers($request);
    }
}