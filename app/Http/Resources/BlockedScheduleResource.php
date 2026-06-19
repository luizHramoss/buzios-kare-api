<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlockedScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'date'       => $this->date?->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time'   => $this->end_time,
            'type'       => $this->type->value,
            'type_label' => $this->type->label(),
            'reason'     => $this->reason,
            'created_by' => $this->whenLoaded('createdBy', fn () => $this->createdBy->name),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
