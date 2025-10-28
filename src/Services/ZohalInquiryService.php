<?php

namespace Inquiry\Services;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Inquiry\Models\InquiryLog;

class ZohalInquiryService
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = Config::get('zohal.base_url', 'https://service.zohal.io/api/v0/services');
        $this->token = Config::get('zohal.token');
    }

    /**
     * Call a dynamic inquiry method
     * @throws Exception
     */
    public function callInquiryMethod(string $method, array $parameters, array $requestContext = []): array
    {
        $url = $this->baseUrl . '/' . $method;
        $startTime = microtime(true);
        $requestId = $requestContext['request_id'] ?? uniqid('req_', true);
        
        try {
            $response = $this->getHttpClient()
                ->post($url, $parameters);

            $endTime = microtime(true);
            $responseTimeMs = round(($endTime - $startTime) * 1000);

            // Log the request and response
            $this->logInquiry($method, $url, $parameters, $response, $responseTimeMs, $requestContext, $requestId);

            // Check if the request was successful
            if (!$response->successful()) {
                throw new Exception($response->body(), $response->status());
            }

            return $response->json();

        } catch (Exception $e) {
            $endTime = microtime(true);
            $responseTimeMs = round(($endTime - $startTime) * 1000);
            
            // Log the failed request
            $this->logFailedInquiry($method, $url, $parameters, $e, $responseTimeMs, $requestContext, $requestId);
            
            throw $e;
        }
    }

    /**
     * Get configured HTTP client
     */
    protected function getHttpClient(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])
            ->timeout(30)->retry(3, 1000);
    }

    /**
     * Log successful inquiry request and response
     */
    protected function logInquiry(
        string $method,
        string $url,
        array $parameters,
        $response,
        int $responseTimeMs,
        array $requestContext,
        string $requestId
    ): void {
        try {
            InquiryLog::create([
                'method' => $method,
                'endpoint' => $url,
                'request_data' => $parameters,
                'response_data' => $response->json(),
                'status_code' => $response->status(),
                'response_status' => 'success',
                'error_message' => null,
                'response_time_ms' => $responseTimeMs,
                'ip_address' => $requestContext['ip_address'] ?? null,
                'user_agent' => $requestContext['user_agent'] ?? null,
                'request_id' => $requestId,
            ]);
        } catch (Exception $e) {
            // Log the logging error but don't break the main flow
            error_log('Failed to log inquiry: ' . $e->getMessage());
        }
    }

    /**
     * Log failed inquiry request
     */
    protected function logFailedInquiry(
        string $method,
        string $url,
        array $parameters,
        Exception $exception,
        int $responseTimeMs,
        array $requestContext,
        string $requestId
    ): void {
        try {
            InquiryLog::create([
                'method' => $method,
                'endpoint' => $url,
                'request_data' => $parameters,
                'response_data' => null,
                'status_code' => $exception->getCode() ?: 500,
                'response_status' => 'error',
                'error_message' => $exception->getMessage(),
                'response_time_ms' => $responseTimeMs,
                'ip_address' => $requestContext['ip_address'] ?? null,
                'user_agent' => $requestContext['user_agent'] ?? null,
                'request_id' => $requestId,
            ]);
        } catch (Exception $e) {
            // Log the logging error but don't break the main flow
            error_log('Failed to log failed inquiry: ' . $e->getMessage());
        }
    }


    /**
     * Get available inquiry methods
     * Based on Zohal services: Authentication, Banking Inquiries, and Service Inquiries
     */
    public function getAvailableMethods(): array
    {
        return [
            // Authentication & Identity Verification Services
            'shahkar' => 'Shahkar verification service',
            'national_identity_inquiry' => 'National identity verification',
            'mobile_verification' => 'Mobile number verification',
            'card_verification' => 'Bank card verification',
            'iban_verification' => 'IBAN verification',
            
            // Banking Inquiries and Conversions
            'bank_account_inquiry' => 'Bank account inquiry',
            'card_to_iban' => 'Convert card number to IBAN',
            'iban_to_card' => 'Convert IBAN to card number',
            'bank_balance_inquiry' => 'Bank balance inquiry',
            'transaction_inquiry' => 'Transaction inquiry',
            'bank_statement' => 'Bank statement inquiry',
            
            // Service Inquiries
            'postal_code_inquiry' => 'Postal code verification',
            'address_inquiry' => 'Address verification',
            'plate_inquiry' => 'Vehicle plate inquiry',
            'insurance_inquiry' => 'Insurance inquiry',
            'pension_inquiry' => 'Pension inquiry',
            'tax_inquiry' => 'Tax inquiry',
            'social_security_inquiry' => 'Social security inquiry',
            
            // Utility and Service Verification
            'utility_bill_inquiry' => 'Utility bill inquiry',
            'phone_bill_inquiry' => 'Phone bill inquiry',
            'internet_bill_inquiry' => 'Internet bill inquiry',
            'gas_bill_inquiry' => 'Gas bill inquiry',
            'electricity_bill_inquiry' => 'Electricity bill inquiry',
            
            // Business and Commercial Services
            'company_inquiry' => 'Company information inquiry',
            'commercial_registration' => 'Commercial registration inquiry',
            'tax_id_inquiry' => 'Tax ID verification',
            'business_license_inquiry' => 'Business license inquiry',
        ];
    }

    /**
     * Get available methods organized by category
     */
    public function getAvailableMethodsByCategory(): array
    {
        return [
            'authentication' => [
                'shahkar' => 'Shahkar verification service',
                'national_identity_inquiry' => 'National identity verification',
                'mobile_verification' => 'Mobile number verification',
                'card_verification' => 'Bank card verification',
                'iban_verification' => 'IBAN verification',
            ],
            'banking' => [
                'bank_account_inquiry' => 'Bank account inquiry',
                'card_to_iban' => 'Convert card number to IBAN',
                'iban_to_card' => 'Convert IBAN to card number',
                'bank_balance_inquiry' => 'Bank balance inquiry',
                'transaction_inquiry' => 'Transaction inquiry',
                'bank_statement' => 'Bank statement inquiry',
            ],
            'service_inquiries' => [
                'postal_code_inquiry' => 'Postal code verification',
                'address_inquiry' => 'Address verification',
                'plate_inquiry' => 'Vehicle plate inquiry',
                'insurance_inquiry' => 'Insurance inquiry',
                'pension_inquiry' => 'Pension inquiry',
                'tax_inquiry' => 'Tax inquiry',
                'social_security_inquiry' => 'Social security inquiry',
            ],
            'utility_verification' => [
                'utility_bill_inquiry' => 'Utility bill inquiry',
                'phone_bill_inquiry' => 'Phone bill inquiry',
                'internet_bill_inquiry' => 'Internet bill inquiry',
                'gas_bill_inquiry' => 'Gas bill inquiry',
                'electricity_bill_inquiry' => 'Electricity bill inquiry',
            ],
            'business_services' => [
                'company_inquiry' => 'Company information inquiry',
                'commercial_registration' => 'Commercial registration inquiry',
                'tax_id_inquiry' => 'Tax ID verification',
                'business_license_inquiry' => 'Business license inquiry',
            ],
        ];
    }
}
