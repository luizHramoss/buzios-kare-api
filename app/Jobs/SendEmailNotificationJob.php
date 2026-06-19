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
 * Job genérico de envio de e-mail transacional relacionado a agendamentos.
 * No MVP, o "envio" é simulado via Log; a integração real com um
 * provedor de e-mail (Mailgun, SES, etc.) é conectada aqui depois.
 */
class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public readonly int $customerId,
        public readonly string $template,
        public readonly array $data = [],
    ) {}

    public function handle(): void
    {
        $customer = Customer::find($this->customerId);

        if (! $customer) {
            Log::warning('SendEmailNotificationJob: customer not found', ['customer_id' => $this->customerId]);
            return;
        }

        // TODO: integrar com Mailable real. Por ora, registra a intenção de envio.
        Log::info('Email notification dispatched', [
            'to'       => $customer->email,
            'template' => $this->template,
            'data'     => $this->data,
        ]);
    }
}
