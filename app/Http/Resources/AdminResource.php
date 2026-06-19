<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'         => $this->uuid,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'role'         => $this->role->value,
            'role_label'   => $this->role->label(),
            'status'       => $this->status->value,
            'status_label' => $this->status->label(),
            'created_at'   => $this->created_at?->toIso8601String(),
        ];
    }
}
