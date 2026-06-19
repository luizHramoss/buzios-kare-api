<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'name'       => $this->name,
            'cpf'        => $this->cpf,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'email'      => $this->email,
            'phone'      => $this->phone,
            'whatsapp'   => $this->whatsapp,
            'notes'      => $this->notes,
            'status'     => $this->status->value,
            'status_label' => $this->status->label(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
