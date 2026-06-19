<?php

namespace Database\Seeders;

use App\Models\ScheduleConfig;
use Illuminate\Database\Seeder;

class ScheduleConfigSeeder extends Seeder
{
    public function run(): void
    {
        ScheduleConfig::query()->firstOrCreate([], [
            'work_start'                  => '08:00:00',
            'work_end'                    => '18:00:00',
            'break_start'                 => '12:00:00',
            'break_end'                   => '13:00:00',
            'slot_duration'               => 60,
            'min_advance_hours'           => 2,
            'max_advance_days'            => 60,
            'max_future_appointments'     => 3,
            'cancellation_advance_hours'  => 24,
            'allowed_days'                => [1, 2, 3, 4, 5], // segunda a sexta
        ]);
    }
}
