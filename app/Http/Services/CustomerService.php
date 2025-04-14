<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Respositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\Customer;
use App\Models\Otp;
use App\Utils\SMS;
use Illuminate\Support\Facades\Hash;
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
                throw new ExpectedException("Customer with this email does not exist");
            }

            if (!Hash::check($request->password, $customer->password)) {
                throw new ExpectedException("Incorrect Password");
            }

             $token = $this->customerRepository->login($customer);

            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'agent' => $customer->only('name', 'email', 'phone_number', 'status'),
            ];
            return $data;
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    public function logout(Request $request)
    {
        return $this->customerRepository->logout($request);
    }

      /**
     * Send OTP to customer to verify phone number
     */
    public function sendOtp(): int
    {
        $code =  $this->customerRepository->sendOtp();
        SMS::send(request()->phone_number, "Pesatrack OTP is {$code}. Don't share this code with anyone.");
        return $code;
    }

    /**
     * Verify that the OTP is valid
     */
    public function verifyOtp(Request $request): bool
    {
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
}