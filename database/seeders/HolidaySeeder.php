<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            ['date' => '2026-01-01', 'name' => 'Confraternização Universal', 'recurring' => true],
            ['date' => '2026-04-21', 'name' => 'Tiradentes', 'recurring' => true],
            ['date' => '2026-05-01', 'name' => 'Dia do Trabalho', 'recurring' => true],
            ['date' => '2026-09-07', 'name' => 'Independência do Brasil', 'recurring' => true],
            ['date' => '2026-10-12', 'name' => 'Nossa Senhora Aparecida', 'recurring' => true],
            ['date' => '2026-11-02', 'name' => 'Finados', 'recurring' => true],
            ['date' => '2026-11-15', 'name' => 'Proclamação da República', 'recurring' => true],
            ['date' => '2026-12-25', 'name' => 'Natal', 'recurring' => true],
        ];

        foreach ($holidays as $holiday) {
            Holiday::query()->firstOrCreate(
                ['date' => $holiday['date'], 'recurring' => $holiday['recurring']],
                $holiday
            );
        }
    }
}
