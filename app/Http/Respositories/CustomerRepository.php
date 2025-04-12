<?php

namespace App\Http\Respositories;

use App\Models\Customer;
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

    public function logout(Customer $customer) 
    {
        return  $customer->createToken('auth_token')->plainTextToken;
    }

    public function deactivate() {}
}
