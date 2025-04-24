<?php

namespace App\Http\Controllers;

use App\Exceptions\ExpectedException;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Http\Services\FeedbackService;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Throwable;

class FeedbackController extends Controller
{
public function __construct(private FeedbackService $feedbackService) {}

    public function index(Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->feedbackService->getCustomerFeedback($customer);
            return new ApiSuccessResponse($customer, "Success");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function store(Request $request, Customer $customer): ApiSuccessResponse|ApiErrorResponse
    {
        try {
            $customer = $this->feedbackService->createFeedback($request, $customer);
            return new ApiSuccessResponse($customer, "Feedback Submitted Successful");
        } catch (ExpectedException $expectedException) {
            return new ApiErrorResponse($expectedException->getMessage(), $expectedException);
        } catch (Throwable $throwable) {
            return new ApiErrorResponse($throwable->getMessage(), $throwable);
        }
    }

    public function feedbacks(Request $request)
    {
        $data = $this->feedbackService->getFeedbacks($request);
        return Inertia::render('Feedbacks', $data);
    }
}

