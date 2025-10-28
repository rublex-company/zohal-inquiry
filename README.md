# Laravel Zohal Inquiry Package

[![Latest Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://packagist.org/packages/rublex/laravel-zohal-inquiry)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

A Laravel package for integrating with Zohal inquiry services API. Zohal provides comprehensive services in three main areas: Authentication & Identity Verification, Banking Inquiries & Conversions, and Service Inquiries.

## Features

- **Comprehensive Service Coverage**: Support for 25+ inquiry methods across multiple service categories
- **Dynamic inquiry method calls**: Flexible API for calling any supported Zohal service
- **Configurable API settings**: Easy configuration through environment variables
- **HTTP client with retry logic**: Built-in resilience with automatic retries
- **Laravel facade support**: Clean, easy-to-use facade interface
- **Easy integration**: Simple setup and integration with Laravel applications

## Service Categories

- **Authentication & Identity Verification**: Shahkar, national ID, mobile, card, and IBAN verification
- **Banking Inquiries & Conversions**: Account inquiries, card-to-IBAN conversions, balance checks
- **Service Inquiries**: Postal codes, addresses, vehicle plates, insurance, pension, tax inquiries
- **Utility & Service Verification**: Bill inquiries for phone, internet, gas, electricity
- **Business & Commercial Services**: Company information, commercial registration, business licenses

## Installation

```bash
composer require rublex/laravel-zohal-inquiry
```

## Quick Start

1. Publish the configuration file:
```bash
php artisan vendor:publish --provider="Inquiry\ZohalServiceProvider" --tag="zohal-config"
```

2. Publish the database migrations (optional):
```bash
php artisan vendor:publish --provider="Inquiry\ZohalServiceProvider" --tag="zohal-migrations"
php artisan migrate
```

3. Add your Zohal API credentials to your `.env` file:
```env
ZOHAL_BASE_URL=https://service.zohal.io/api/v0/services
ZOHAL_TOKEN=your_api_token_here
```

4. Use the package in your code:
```php
use Inquiry\Facades\Zohal;

$result = Zohal::inquiry('shahkar', [
    'national_code' => '1234567890',
    'mobile' => '09123456789'
]);

// Get package version
$version = Zohal::version(); // Returns '1.0.0'
```

## Documentation

For detailed usage instructions, see [USAGE.md](USAGE.md).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).