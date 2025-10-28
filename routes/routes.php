<?php

use Illuminate\Support\Facades\Route;
use Inquiry\Http\Controllers\InquiryController;

/*
 * This file is part of the Laravel Zohal Inquiry package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Route::post('api/v1/inquiry/{method}', [InquiryController::class, 'handleInquiry'])
    ->where('method', '[a-zA-Z0-9_-]+')->name('inquiry.method');