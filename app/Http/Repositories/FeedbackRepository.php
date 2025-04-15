<?php

namespace App\Http\Repositories;

use App\Models\Customer;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackRepository
{
    public function getCustomerFeedbacks(Customer $customer)
    {
        return Feedback::query()
            ->select('message')
            ->where('customer_id', $customer->id)->get();
    }

    public function createFeedback(Request $request, Customer $customer)
    {
        return Feedback::query()
            ->create([
                'message' => $request->message,
                'customer_id' => $customer->id
            ]);
    }
}
