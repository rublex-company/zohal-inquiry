# Laravel Zohal Inquiry Package Usage

**Version:** 1.0.0

## Installation

1. Install the package via Composer:
```bash
composer require rublex/laravel-zohal-inquiry
```

2. Publish the configuration file:
```bash
php artisan vendor:publish --provider="Inquiry\ZohalServiceProvider" --tag="zohal-config"
```

3. Publish the database migrations (optional):
```bash
php artisan vendor:publish --provider="Inquiry\ZohalServiceProvider" --tag="zohal-migrations"
php artisan migrate
```

4. Add your Zohal API credentials to your `.env` file:
```env
ZOHAL_BASE_URL=https://service.zohal.io/api/v0/services
ZOHAL_TOKEN=your_api_token_here
ZOHAL_TIMEOUT=30
ZOHAL_RETRY_ATTEMPTS=3
ZOHAL_RETRY_DELAY=1000

# Authentication settings (optional)
ZOHAL_AUTH_ENABLED=true
ZOHAL_AUTH_GUARD=api
ZOHAL_AUTH_MIDDLEWARE=auth:api
```

## Usage

### API Routes

The package automatically registers a dynamic route for inquiry methods:

#### Dynamic Inquiry Route
```
POST /api/v1/inquiry/{method}
```

Where `{method}` can be any valid inquiry method (e.g., `shahkar`).

**Authentication:** By default, all API routes require authentication. You can configure this behavior in the `zohal.php` config file or using environment variables.

#### Authentication Configuration

The package supports configurable authentication:

- **Enable/Disable:** Set `ZOHAL_AUTH_ENABLED` to `false` to disable authentication
- **Guard:** Configure which authentication guard to use with `ZOHAL_AUTH_GUARD` (default: `api`)
- **Middleware:** Customize the middleware with `ZOHAL_AUTH_MIDDLEWARE` (default: `auth:api`)

Example to disable authentication:
```env
ZOHAL_AUTH_ENABLED=false
```

Example to use a different guard:
```env
ZOHAL_AUTH_GUARD=web
ZOHAL_AUTH_MIDDLEWARE=auth:web
```


#### Example API Request

**Dynamic Inquiry Request (with authentication):**
```bash
curl -X POST http://your-app.com/api/v1/inquiry/shahkar \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer your-api-token" \
  -d '{
    "national_code": "1234567890",
    "mobile": "09123456789"
  }'
```

**Note:** Include the appropriate authentication header based on your configured guard (Bearer token for API guard, session cookie for web guard, etc.).

#### Example API Response

```json
{
  "result": true,
  "response_body": {
    "matched": true,
    "data": {
      "matched": true
    }
  }
}
```


### Using the Service Directly

You can also use the service directly in your code:

```php
use Inquiry\Services\ZohalInquiryService;

$service = app(ZohalInquiryService::class);

$result = $service->callInquiryMethod('shahkar', [
    'national_code' => '1234567890',
    'mobile' => '09123456789'
]);

// Handle the result based on the API response
if ($result['matched']) {
    // Handle matched case
}
```

### Using the Facade

You can also use the facade for convenience:

```php
use Inquiry\Facades\Zohal;

// Dynamic inquiry
$result = Zohal::inquiry('shahkar', [
    'national_code' => '1234567890',
    'mobile' => '09123456789'
]);

```

## Database Migrations

The package includes optional database migrations for logging inquiry requests. These migrations create an `inquiry_logs` table to track:

- **Request Details**: Method, endpoint, request data, response data
- **Response Information**: Status code, response status, error messages
- **Performance Metrics**: Response time in milliseconds
- **Request Tracking**: IP address, user agent, request ID for correlation
- **Timestamps**: Created and updated timestamps for audit trails

### Migration Features

- **Comprehensive Logging**: Captures all inquiry requests and responses
- **Performance Monitoring**: Tracks response times for performance analysis
- **Error Tracking**: Logs error messages and status codes for debugging
- **Indexed Queries**: Optimized database indexes for efficient querying
- **Request Correlation**: Unique request IDs for tracking related requests

### Publishing Migrations

To enable inquiry logging, publish and run the migrations:

```bash
php artisan vendor:publish --provider="Inquiry\ZohalServiceProvider" --tag="zohal-migrations"
php artisan migrate
```

This will create the `inquiry_logs` table with the following structure:

- `id` - Primary key
- `method` - Inquiry method name (indexed)
- `endpoint` - API endpoint URL (indexed)
- `request_data` - JSON data of the request payload
- `response_data` - JSON data of the response payload
- `status_code` - HTTP status code
- `response_status` - Response status (success, error, timeout, etc.)
- `error_message` - Error message if any
- `response_time_ms` - Response time in milliseconds
- `ip_address` - Client IP address (IPv6 support)
- `user_agent` - Client user agent string
- `request_id` - Unique request identifier (indexed)
- `created_at` - Request timestamp
- `updated_at` - Last update timestamp

## Configuration

The package configuration is located in `config/zohal.php`:

```php
return [
    'base_url' => env('ZOHAL_BASE_URL', 'https://service.zohal.io/api/v0/services/inquiry'),
    'token' => env('ZOHAL_TOKEN'),
    'timeout' => env('ZOHAL_TIMEOUT', 30),
    'retry_attempts' => env('ZOHAL_RETRY_ATTEMPTS', 3),
    'retry_delay' => env('ZOHAL_RETRY_DELAY', 1000),
];
```

## Available Methods

The service supports a comprehensive range of inquiry methods based on Zohal's three main service categories:

### Authentication & Identity Verification Services
- `shahkar` - Shahkar verification service
- `national_identity_inquiry` - National identity verification
- `mobile_verification` - Mobile number verification
- `card_verification` - Bank card verification
- `iban_verification` - IBAN verification

### Banking Inquiries and Conversions
- `bank_account_inquiry` - Bank account inquiry
- `card_to_iban` - Convert card number to IBAN
- `iban_to_card` - Convert IBAN to card number
- `bank_balance_inquiry` - Bank balance inquiry
- `transaction_inquiry` - Transaction inquiry
- `bank_statement` - Bank statement inquiry

### Service Inquiries
- `postal_code_inquiry` - Postal code verification
- `address_inquiry` - Address verification
- `plate_inquiry` - Vehicle plate inquiry
- `insurance_inquiry` - Insurance inquiry
- `pension_inquiry` - Pension inquiry
- `tax_inquiry` - Tax inquiry
- `social_security_inquiry` - Social security inquiry

### Utility and Service Verification
- `utility_bill_inquiry` - Utility bill inquiry
- `phone_bill_inquiry` - Phone bill inquiry
- `internet_bill_inquiry` - Internet bill inquiry
- `gas_bill_inquiry` - Gas bill inquiry
- `electricity_bill_inquiry` - Electricity bill inquiry

### Business and Commercial Services
- `company_inquiry` - Company information inquiry
- `commercial_registration` - Commercial registration inquiry
- `tax_id_inquiry` - Tax ID verification
- `business_license_inquiry` - Business license inquiry

You can get the complete list of available methods programmatically:

```php
// Get all methods as a flat array
$methods = Zohal::getAvailableMethods();
// Returns an associative array with method names as keys and descriptions as values

// Get methods organized by category
$methodsByCategory = Zohal::getAvailableMethodsByCategory();
// Returns a nested array organized by service categories
```

## Error Handling

The service includes comprehensive error handling:

- **API Errors**: When the external API call fails, exceptions are thrown with the error message and status code
- **Timeout Handling**: Automatic retry with configurable attempts and delay
- **HTTP Client Configuration**: Built-in timeout and retry logic using Laravel's HTTP client

## Version Information

You can get the current package version programmatically:

```php
use Inquiry\Facades\Zohal;

$version = Zohal::version();
echo "Package version: " . $version; // Output: Package version: 1.0.0
```

## Testing

Run the included tests:

```bash
php artisan test tests/InquiryTest.php
```

The tests include:
- Service method testing with mocked HTTP responses
- Route validation testing
- Error handling testing
