<?php

namespace App\Http\Services;

use App\Exceptions\ExpectedException;
use App\Http\Repositories\FeedbackRepository;
use App\Models\Customer;
use Illuminate\Http\Request;
use Throwable;

class FeedbackService
{
    public function __construct(private FeedbackRepository $feedbackRepository) {}

    public function getCustomerFeedback(Customer $customer)
    {
        return $this->feedbackRepository->getCustomerFeedbacks($customer);
    }

    public function createFeedback(Request $request, Customer $customer)
    {
        try {
            $request->validate([
                'message' => 'required',
            ]);

            return $this->feedbackRepository->createFeedback($request, $customer);
        } catch (ExpectedException $expectedException) {
            throw $expectedException;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }
}
