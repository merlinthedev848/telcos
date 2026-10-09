<?php

namespace App\Modules\Email\Services;

use App\Modules\Core\Services\CriticalOperation;
use App\Modules\Customers\Models\Customer;
use App\Modules\Email\Models\EmailThread;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmailThreadingService
{
    public function __construct(private readonly CriticalOperation $operations)
    {
    }

    /**
     * Ingest and thread an inbound or outbound email message.
     *
     * @param Customer $customer
     * @param array<string, mixed> $message
     */
    public function recordInbound(Customer $customer, array $message): EmailThread
    {
        return DB::transaction(function () use ($customer, $message): EmailThread {
            $messageId = $message['message_id'] ?? null;
            $inReplyTo = $message['headers']['In-Reply-To'] ?? $message['in_reply_to'] ?? null;
            $references = $message['headers']['References'] ?? $message['references'] ?? null;

            $thread = null;

            // 1. Try matching by direct message_id or reply header
            if (! empty($inReplyTo)) {
                $thread = EmailThread::query()
                    ->where('customer_id', $customer->id)
                    ->where('message_id', $inReplyTo)
                    ->first();
            }

            if (! $thread && ! empty($references)) {
                $refArray = is_array($references) ? $references : explode(' ', (string) $references);
                $thread = EmailThread::query()
                    ->where('customer_id', $customer->id)
                    ->whereIn('message_id', $refArray)
                    ->first();
            }

            if (! $thread && ! empty($messageId)) {
                $thread = EmailThread::query()
                    ->where('customer_id', $customer->id)
                    ->where('message_id', $messageId)
                    ->first();
            }

            // 2. If no thread exists, create a new one
            if (! $thread) {
                $thread = EmailThread::query()->create([
                    'customer_id' => $customer->id,
                    'subject' => $message['subject'],
                    'status' => 'open',
                    'message_id' => $messageId ?? '<'.Str::uuid().'@telecomos.local>',
                    'last_message_at' => now(),
                ]);
            } else {
                $thread->update([
                    'status' => 'open',
                    'last_message_at' => now(),
                ]);
            }

            $thread->messages()->create([
                'direction' => $message['direction'] ?? 'inbound',
                'from' => $message['from'],
                'to' => $message['to'] ?? 'support@telecomos.local',
                'subject' => $message['subject'],
                'body' => $message['body'],
                'headers' => $message['headers'] ?? [],
            ]);

            $this->operations->record('email', 'email.threaded', $thread, [
                'thread_id' => $thread->id,
                'customer_id' => $customer->id,
                'subject' => $thread->subject,
            ]);

            return $thread;
        });
    }
}
