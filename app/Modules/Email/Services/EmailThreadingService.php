<?php

namespace App\Modules\Email\Services;

use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Email\Models\EmailThread;

class EmailThreadingService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    public function recordInbound(Customer $customer, array $message): EmailThread
    {
        $thread = EmailThread::query()->firstOrCreate(
            ['message_id' => $message['message_id'] ?? null],
            [
                'customer_id' => $customer->id,
                'subject' => $message['subject'],
                'status' => 'open',
                'last_message_at' => now(),
            ],
        );

        $thread->messages()->create([
            'direction' => 'inbound',
            'from' => $message['from'],
            'to' => $message['to'] ?? 'support@telecomos.local',
            'subject' => $message['subject'],
            'body' => $message['body'],
            'headers' => $message['headers'] ?? [],
        ]);

        $this->operations->record('email', 'email.threaded', $thread);

        return $thread;
    }
}
