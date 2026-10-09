<?php

namespace Tests\Feature;

use App\Modules\Customers\Models\Customer;
use App\Modules\Email\Models\EmailThread;
use App\Modules\Email\Services\EmailThreadingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailThreadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_threads_associate_correctly_by_headers(): void
    {
        $customer = Customer::query()->create([
            'account_number' => 'TEL-EMAIL1',
            'contact_name' => 'Dan Email',
            'email' => 'dan@email.test',
            'status' => 'active',
        ]);

        $service = app(EmailThreadingService::class);

        // Initial email without message_id
        $thread1 = $service->recordInbound($customer, [
            'from' => 'dan@email.test',
            'subject' => 'Service Inquiry',
            'body' => 'I would like to know if full fibre is available.',
        ]);

        $this->assertNotNull($thread1->message_id);
        $this->assertSame(1, $thread1->messages()->count());

        // Reply email referencing thread1 message_id
        $thread2 = $service->recordInbound($customer, [
            'from' => 'dan@email.test',
            'subject' => 'Re: Service Inquiry',
            'body' => 'Following up on this.',
            'headers' => [
                'In-Reply-To' => $thread1->message_id,
            ],
        ]);

        // Must attach to the exact same thread
        $this->assertSame($thread1->id, $thread2->id);
        $this->assertSame(2, $thread1->messages()->count());
    }
}
