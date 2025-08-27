<?php

namespace App\Console\Commands;

use App\Http\Controllers\Webhooks\WebhookController;
use App\Services\PayPal\WebhookService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class TestWebhook extends Command
{
    protected $signature = 'webhook:test {event_type=PAYMENT.CAPTURE.COMPLETED}';

    protected $description = 'Test webhook locally';

    public function handle()
    {
        $eventType = $this->argument('event_type');

        $testData = [
            'event_type' => $eventType,
            'resource' => [
                'id' => 'TEST_'. 1234,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => '10.00',
                ],
                'status' => 'COMPLETED',
            ],
        ];

        $request = new Request($testData);
        $controller = new WebhookController(app(WebhookService::class));

        $response = $controller->handleWebhook($request);

        $this->info('Webhook test completed:');
        $this->info("Event Type: {$eventType}");
        $this->info('Response: '.$response);
    }
}
