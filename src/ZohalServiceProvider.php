<?php

namespace Inquiry;

use Illuminate\Support\ServiceProvider;
use Inquiry\Services\ZohalInquiryService;

class ZohalServiceProvider extends ServiceProvider
{
    /**
     * Package version
     */
    public const VERSION = '1.0.0';

    /*
    * Indicates if loading of the provider is deferred.
    *
    * @var bool
    */
    protected bool $defer = false;

    /**
     * Publishes all the config file this package needs to function
     */
    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__ . '/config/zohal.php' => config_path('zohal.php'),
        ], 'zohal-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'zohal-migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton(ZohalInquiryService::class, function ($app) {
            return new ZohalInquiryService();
        });

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/config/zohal.php', 'zohal'
        );
    }

    /**
     * Get the services provided by the provider
     * @return array
     */
    public function provides(): array
    {
        return [ZohalInquiryService::class];
    }
}