<?php

namespace App\Http\Respositories;

use App\Models\Customer;
use App\Models\Otp;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerRepository
{
    public function login(Customer $customer) 
    {
        return  $customer->createToken('auth_token')->plainTextToken;
    }

    /**
     * creates new customer account
     */
    public function register(Request $request)
    {
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'status' => "Pending"
        ]);

        event(new Registered($customer));
        return $customer->makeHidden(['created_at', 'updated_at']);
    }

    public function logout(Request $request) 
    {
        return $request->user()->currentAccessToken()->delete();
    }

    public function deactivate(Customer $customer) 
    {
        $customer->status = 'inactive';
        $customer->save();
        return $customer->refresh();
    }

    /**
     * Send OTP to customer to verify phone number
     * being used to register or to access the app
     */
    public function sendOtp(): int
    {
        $code = mt_rand(100000, 999999);
        $otp = new Otp(request()->all());
        $otp->code = $code;
        $otp->save();
        return $code;
    }

    /**
     * Verify that the OTP is valid
     */
    public function verifyOtp(): bool
    {
        $otp = Otp::query()
            ->where('phone_number', request()->phone_number)
            ->where('code', request()->code)
            ->where('type', request()->type)
            ->where('matched', false)
            ->first();

        $otp->matched = true;

        return $otp->save();
    }
}
