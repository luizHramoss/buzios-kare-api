<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'                 => $this->uuid,
            'service'              => $this->service,
            'date'                 => $this->date?->format('Y-m-d'),
            'start_time'           => $this->start_time,
            'end_time'             => $this->end_time,
            'notes'                => $this->notes,
            'status'               => $this->status->value,
            'status_label'         => $this->status->label(),
            'value'                => (float) $this->value,
            'payment_method'       => $this->payment_method?->value,
            'cancellation_reason'  => $this->cancellation_reason,
            'rescheduled_from'     => $this->rescheduled_from_id
                ? ['uuid' => $this->rescheduledFrom?->uuid]
                : null,
            // Dados do cliente só aparecem na visão do admin (rota carrega 'customer')
            'customer' => $this->when(
                $this->relationLoaded('customer'),
                fn () => [
                    'uuid'  => $this->customer->uuid,
                    'name'  => $this->customer->name,
                    'phone' => $this->customer->phone,
                ]
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
