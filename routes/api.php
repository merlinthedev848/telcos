<?php

use App\Modules\Billing\Http\Controllers\Api\InvoiceApiController;
use App\Modules\Billing\Http\Controllers\Api\PaymentApiController;
use App\Modules\Customers\Http\Controllers\Api\CustomerApiController;
use App\Modules\Email\Http\Controllers\Api\EmailInboundApiController;
use App\Modules\Paywall\Http\Controllers\Api\PaywallApiController;
use App\Modules\Provisioning\Http\Controllers\Api\ProvisioningApiController;
use App\Modules\Support\Http\Controllers\Api\SupportTicketApiController;
use App\Modules\Switching\Http\Controllers\Api\SwitchingApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    // Customers
    Route::apiResource('customers', CustomerApiController::class);

    // Invoices & Billing
    Route::apiResource('invoices', InvoiceApiController::class)->only(['index', 'show']);
    Route::post('invoices/{invoice}/dunning', [InvoiceApiController::class, 'runDunning']);

    // Payments
    Route::get('payments', [PaymentApiController::class, 'index']);
    Route::post('invoices/{invoice}/payments', [PaymentApiController::class, 'storeForInvoice']);
    Route::post('customers/{customer}/payments', [PaymentApiController::class, 'storeForCustomer']);

    // DNS Paywall
    Route::post('paywall/{customer}/apply', [PaywallApiController::class, 'apply']);
    Route::post('paywall/{customer}/restore', [PaywallApiController::class, 'restore']);

    // Provisioning
    Route::get('provisioning/health', [ProvisioningApiController::class, 'health']);
    Route::post('provisioning/{customer}/activate', [ProvisioningApiController::class, 'activate']);
    Route::post('provisioning/{customer}/suspend', [ProvisioningApiController::class, 'suspend']);
    Route::post('provisioning/{customer}/restore', [ProvisioningApiController::class, 'restore']);

    // Support
    Route::apiResource('support/tickets', SupportTicketApiController::class)->only(['index', 'show', 'store']);
    Route::post('support/tickets/{ticket}/reply', [SupportTicketApiController::class, 'reply']);

    // Email Inbound Webhook
    Route::post('email/inbound', [EmailInboundApiController::class, 'handle']);

    // Switching (One Touch Switching)
    Route::apiResource('switching/workflows', SwitchingApiController::class)->only(['index', 'show', 'store']);
    Route::post('switching/workflows/{workflow}/advance', [SwitchingApiController::class, 'advance']);
});
