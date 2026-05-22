<?php

use App\Modules\Billing\Http\Controllers\InvoiceController;
use App\Modules\Core\Http\Controllers\DashboardController;
use App\Modules\Customers\Http\Controllers\CustomerController;
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
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials could not be verified.']);
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

    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/360', [CustomerController::class, 'show360'])->name('customers.360');

    Route::get('billing/invoices', [InvoiceController::class, 'index'])->name('billing.invoices.index');
    Route::post('billing/invoices/{invoice}/mark-overdue', [InvoiceController::class, 'markOverdue'])->name('billing.invoices.mark-overdue');

    Route::get('support/inbox', SupportInboxController::class)->name('support.inbox');
    Route::get('switching/workflows', SwitchingController::class)->name('switching.workflows');
});
