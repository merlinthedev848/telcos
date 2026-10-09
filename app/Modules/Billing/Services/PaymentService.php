<?php

namespace App\Modules\Billing\Services;

use App\Enums\InvoiceStatus;
use App\Enums\PaywallStatus;
use App\Modules\Billing\Models\Invoice;
use App\Modules\Billing\Models\Payment;
use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Paywall\Services\DnsPaywallService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function __construct(
        private readonly CriticalOperation $operations,
        private readonly DnsPaywallService $paywall,
    ) {
    }

    /**
     * Process and record a payment against an invoice or customer account.
     *
     * @param Customer $customer
     * @param float $amount
     * @param Invoice|null $invoice
     * @param string $provider
     * @param string|null $reference
     * @param array<string, mixed> $payload
     */
    public function recordPayment(
        Customer $customer,
        float $amount,
        ?Invoice $invoice = null,
        string $provider = 'manual',
        ?string $reference = null,
        array $payload = [],
    ): Payment {
        return DB::transaction(function () use ($customer, $amount, $invoice, $provider, $reference, $payload): Payment {
            $paymentRef = $reference ?? 'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));

            $payment = Payment::query()->create([
                'customer_id' => $customer->id,
                'invoice_id' => $invoice?->id,
                'provider' => $provider,
                'reference' => $paymentRef,
                'amount' => $amount,
                'status' => 'received',
                'received_at' => now(),
                'payload' => $payload,
            ]);

            if ($invoice) {
                $newBalance = max(0.0, round(((float) $invoice->balance_due) - $amount, 2));
                $newStatus = $newBalance <= 0.0 ? InvoiceStatus::Paid->value : $invoice->status;

                $invoice->update([
                    'balance_due' => $newBalance,
                    'status' => $newStatus,
                ]);
            }

            // Check if customer total overdue balance is now 0 and paywall should be lifted
            $totalRemainingDue = (float) $customer->invoices()
                ->whereIn('status', [InvoiceStatus::Issued->value, InvoiceStatus::Overdue->value])
                ->sum('balance_due');

            if ($totalRemainingDue <= 0.0 && $customer->paywallState?->status === PaywallStatus::Restricted->value) {
                $this->paywall->restore($customer);
            }

            $this->operations->record('billing', 'payment.received', $payment, [
                'payment_reference' => $payment->reference,
                'customer_id' => $customer->id,
                'invoice_id' => $invoice?->id,
                'amount' => $amount,
                'provider' => $provider,
            ]);

            return $payment;
        });
    }
}
