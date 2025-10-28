<?php

namespace Inquiry\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Inquiry\Services\ZohalInquiryService;

class InquiryController
{
    protected ZohalInquiryService $zohalService;

    public function __construct(ZohalInquiryService $zohalService)
    {
        $this->zohalService = $zohalService;
    }

    /**
     * Handle dynamic inquiry requests
     */
    public function handleInquiry(Request $request, string $method): JsonResponse
    {
        try {
            // Get all parameters from request body
            $parameters = $request->all();

            // Prepare request context for logging
            $requestContext = [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'request_id' => $request->header('X-Request-ID') ?? uniqid('req_', true),
            ];

            // Call the dynamic method on the service
            $result = $this->zohalService->callInquiryMethod($method, $parameters, $requestContext);

            return Response::json([
                'result' => true,
                'response_body' => $result,
            ]);

        } catch (\Exception $e) {
            return Response::json([
                'result' => false,
                'response_body' => $e->getMessage()
            ], $e->getCode());
        }
    }


}
