<?php

namespace Database\Factories;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    protected $model = \App\Models\Customer::class;

    public function definition(): array
    {
        return [
            'name'       => $this->faker->name(),
            'cpf'        => $this->generateFakeCpf(),
            'birth_date' => $this->faker->date('Y-m-d', '-18 years'),
            'email'      => $this->faker->unique()->safeEmail(),
            'phone'      => $this->faker->numerify('1199########'),
            'whatsapp'   => $this->faker->numerify('1199########'),
            'notes'      => null,
            'password'   => Hash::make('password'),
            'status'     => CustomerStatus::ACTIVE->value,
        ];
    }

    public function blocked(): static
    {
        return $this->state(['status' => CustomerStatus::BLOCKED->value]);
    }

    private function generateFakeCpf(): string
    {
        // CPF sintético apenas para testes (não passa validação de dígito verificador real)
        return $this->faker->unique()->numerify('###.###.###-##');
    }
}
