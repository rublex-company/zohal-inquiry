<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Inquiry\Http\Controllers\InquiryController;

/*
 * This file is part of the Laravel Zohal Inquiry package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$route = Route::post('api/v1/inquiry/{method}', [InquiryController::class, 'handleInquiry'])
    ->where('method', '[a-zA-Z0-9_-]+')->name('inquiry.method');

// Apply authentication middleware if enabled
if (Config::get('zohal.auth.enabled', true)) {
    $middleware = Config::get('zohal.auth.middleware', 'auth:api');
    $route->middleware($middleware);
}