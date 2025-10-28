<?php

namespace Inquiry\Facades;

use Illuminate\Support\Facades\Facade;
use Inquiry\Services\ZohalInquiryService;

/*
 * This file is part of the Laravel Zohal Inquiry package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Zohal extends Facade
{
    final public const VERSION = '1.0.0';

    /**
     * Get the registered name of the component
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ZohalInquiryService::class;
    }
    
    /**
     * Call a dynamic inquiry method
     */
    public static function inquiry(string $method, array $parameters): array
    {
        return static::getFacadeRoot()->callInquiryMethod($method, $parameters);
    }

    /**
     * Get available inquiry methods
     */
    public static function getAvailableMethods(): array
    {
        return static::getFacadeRoot()->getAvailableMethods();
    }

    /**
     * Get available methods organized by category
     */
    public static function getAvailableMethodsByCategory(): array
    {
        return static::getFacadeRoot()->getAvailableMethodsByCategory();
    }

    /**
     * Get the package version
     */
    public static function version(): string
    {
        return static::VERSION;
    }
}