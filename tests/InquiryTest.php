<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Inquiry\Services\ZohalInquiryService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class InquiryTest extends BaseTestCase
{
    private ZohalInquiryService $service;
    private array $validShahkarParams;
    private array $validNationalIdentityParams;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new ZohalInquiryService();
        
        // Set up test data
        $this->validShahkarParams = [
            'national_code' => '1234567890',
            'mobile' => '09123456789'
        ];
        
        $this->validNationalIdentityParams = [
            'national_code' => '1234567890',
            'mobile' => '09123456789',
            'birth_date' => '1390/01/01'
        ];
        
        // Mock configuration
        Config::set('zohal.base_url', 'https://service.zohal.io/api/v0/services');
        Config::set('zohal.token', 'test-token');
    }

    /**
     * @test
     * @group service
     */
    public function it_calls_shahkar_inquiry_api_correctly()
    {
        $this->mockSuccessfulApiResponse('shahkar', [
            'data' => ['matched' => true]
        ]);

        $result = $this->service->callInquiryMethod('shahkar', $this->validShahkarParams);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['result']);
        $this->assertTrue($result['response_body']['data']['matched']);
    }

    /**
     * @test
     * @group service
     */
    public function it_calls_national_identity_inquiry_api_correctly()
    {
        $this->mockSuccessfulApiResponse('national_identity_inquiry', [
            'data' => [
                'matched' => true,
                'alive' => true,
                'first_name' => 'John',
                'last_name' => 'Doe'
            ]
        ]);

        $result = $this->service->callInquiryMethod('national_identity_inquiry', $this->validNationalIdentityParams);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['result']);
        $this->assertTrue($result['response_body']['data']['matched']);
        $this->assertTrue($result['response_body']['data']['alive']);
        $this->assertEquals('John', $result['response_body']['data']['first_name']);
        $this->assertEquals('Doe', $result['response_body']['data']['last_name']);
    }

    /**
     * @test
     * @group service
     */
    public function it_throws_exception_on_api_failure()
    {
        Http::fake([
            'service.zohal.io/api/v0/services/*' => Http::response('API Error', 500)
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('API Error');
        $this->expectExceptionCode(500);

        $this->service->callInquiryMethod('shahkar', $this->validShahkarParams);
    }

    /**
     * @test
     * @group routes
     */
    public function it_handles_shahkar_inquiry_route_successfully()
    {
        $this->mockSuccessfulApiResponse('shahkar', [
            'data' => ['matched' => true]
        ]);

        $response = $this->postJson('/api/v1/inquiry/shahkar', $this->validShahkarParams);

        $response->assertStatus(200)
                ->assertJson([
                    'result' => true,
                    'response_body' => [
                        'result' => 1,
                        'response_body' => [
                            'data' => ['matched' => true]
                        ]
                    ]
                ]);
    }

    /**
     * @test
     * @group routes
     */
    public function it_handles_national_identity_inquiry_route_successfully()
    {
        $this->mockSuccessfulApiResponse('national_identity_inquiry', [
            'data' => [
                'matched' => true,
                'alive' => true,
                'first_name' => 'John',
                'last_name' => 'Doe'
            ]
        ]);

        $response = $this->postJson('/api/v1/inquiry/national_identity_inquiry', $this->validNationalIdentityParams);

        $response->assertStatus(200)
                ->assertJson([
                    'result' => true,
                    'response_body' => [
                        'result' => 1,
                        'response_body' => [
                            'data' => [
                                'matched' => true,
                                'alive' => true,
                                'first_name' => 'John',
                                'last_name' => 'Doe'
                            ]
                        ]
                    ]
                ]);
    }

    /**
     * @test
     * @group routes
     */
    public function it_returns_error_response_when_api_fails()
    {
        Http::fake([
            'service.zohal.io/api/v0/services/*' => Http::response('API Error', 500)
        ]);

        $response = $this->postJson('/api/v1/inquiry/shahkar', $this->validShahkarParams);

        $response->assertStatus(500)
                ->assertJson([
                    'result' => false,
                    'response_body' => 'API Error'
                ]);
    }


    /**
     * Mock a successful API response for the given method
     */
    private function mockSuccessfulApiResponse(string $method, array $data): void
    {
        Http::fake([
            "service.zohal.io/api/v0/services/{$method}" => Http::response([
                'result' => 1,
                'response_body' => $data
            ], 200)
        ]);
    }
}
