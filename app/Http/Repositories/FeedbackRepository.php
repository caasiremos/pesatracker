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


     /**
     * customer feedbacks
     */
    public function getFeedbacks(Request $request)
    {
        $search = $request->input('search');

        $feedbacks = Feedback::query()
            ->with('customer')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->latest()
            ->paginate(7)
            ->withQueryString();

        return [
            'feedbacks' => $feedbacks,
            'filters' => ['search' => $search]
        ];
    }
}
