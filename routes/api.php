<?php

use App\Modules\Billing\Http\Controllers\Api\InvoiceApiController;
use App\Modules\Customers\Http\Controllers\Api\CustomerApiController;
use App\Modules\Paywall\Http\Controllers\Api\PaywallApiController;
use App\Modules\Provisioning\Http\Controllers\Api\ProvisioningApiController;
use App\Modules\Support\Http\Controllers\Api\SupportTicketApiController;
use App\Modules\Switching\Http\Controllers\Api\SwitchingApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::apiResource('customers', CustomerApiController::class);
    Route::apiResource('invoices', InvoiceApiController::class)->only(['index', 'show']);
    Route::post('invoices/{invoice}/dunning', [InvoiceApiController::class, 'runDunning']);
    Route::post('paywall/{customer}/apply', [PaywallApiController::class, 'apply']);
    Route::post('paywall/{customer}/restore', [PaywallApiController::class, 'restore']);
    Route::post('provisioning/{customer}/activate', [ProvisioningApiController::class, 'activate']);
    Route::apiResource('support/tickets', SupportTicketApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('switching/workflows', SwitchingApiController::class)->only(['index', 'show', 'store']);
});
