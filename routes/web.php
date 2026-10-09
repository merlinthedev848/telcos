<?php

use App\Modules\Billing\Http\Controllers\InvoiceController;
use App\Modules\Core\Http\Controllers\DashboardController;
use App\Modules\Customers\Http\Controllers\CustomerController;
use App\Modules\Integrations\Http\Controllers\IntegrationsController;
use App\Modules\Reports\Http\Controllers\ReportsController;
use App\Modules\Support\Http\Controllers\SupportInboxController;
use App\Modules\Switching\Http\Controllers\SwitchingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['web'])->group(function (): void {
    Route::view('/login', 'auth.login')->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'The provided credentials could not be verified.']);
    })->name('login.attempt');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');
});

Route::middleware(['web', 'auth'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/360', [CustomerController::class, 'show360'])->name('customers.360');

    // Billing & Invoices
    Route::get('billing/invoices', [InvoiceController::class, 'index'])->name('billing.invoices.index');
    Route::post('billing/invoices/{invoice}/mark-overdue', [InvoiceController::class, 'markOverdue'])->name('billing.invoices.mark-overdue');

    // Support
    Route::get('support/inbox', SupportInboxController::class)->name('support.inbox');

    // Switching (OTS)
    Route::get('switching/workflows', SwitchingController::class)->name('switching.workflows');

    // Reports & Analytics
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/export/invoices', [ReportsController::class, 'exportInvoicesCsv'])->name('reports.export.invoices');

    // Integrations
    Route::get('integrations', [IntegrationsController::class, 'index'])->name('integrations.index');
    Route::post('integrations/{key}/test', [IntegrationsController::class, 'testConnection'])->name('integrations.test');
});
