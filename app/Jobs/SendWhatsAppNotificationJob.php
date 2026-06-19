<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job de envio de WhatsApp. Estrutura preparada para integração futura
 * com a WhatsApp Business API ou serviços como Twilio/Z-API.
 */
class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public readonly int $customerId,
        public readonly string $message,
    ) {}

    public function handle(): void
    {
        $customer = Customer::find($this->customerId);

        if (! $customer || ! $customer->whatsapp) {
            return;
        }

        // TODO: integrar com provedor real de WhatsApp.
        Log::info('WhatsApp notification dispatched', [
            'to'      => $customer->whatsapp,
            'message' => $this->message,
        ]);
    }
}
