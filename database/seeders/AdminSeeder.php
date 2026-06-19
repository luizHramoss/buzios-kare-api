<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Enums\AdminStatus;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::query()->firstOrCreate(
            ['email' => 'admin@buzios.app'],
            [
                'name'     => 'Administrador Principal',
                'phone'    => '11999999999',
                'password' => Hash::make('Admin@123'),
                'role'     => AdminRole::SUPER_ADMIN->value,
                'status'   => AdminStatus::ACTIVE->value,
            ]
        );
    }
}
