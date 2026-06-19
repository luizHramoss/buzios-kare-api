<?php

namespace App\Http\Resources;

use App\Http\Resources\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total'        => $this->resource['total'],
            'appointments' => AppointmentResource::collection(
                $this->resource['appointments']->map(fn ($a) => $a->loadMissing('customer'))
            ),
        ];
    }
}
