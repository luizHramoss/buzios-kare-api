<?php

namespace Database\Factories;

use App\Enums\AdminRole;
use App\Enums\AdminStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AdminFactory extends Factory
{
    protected $model = \App\Models\Admin::class;

    public function definition(): array
    {
        return [
            'name'     => $this->faker->name(),
            'email'    => $this->faker->unique()->safeEmail(),
            'phone'    => $this->faker->numerify('1199########'),
            'password' => Hash::make('password'),
            'role'     => AdminRole::ADMIN->value,
            'status'   => AdminStatus::ACTIVE->value,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(['role' => AdminRole::SUPER_ADMIN->value]);
    }
}
