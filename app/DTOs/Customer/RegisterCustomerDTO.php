<?php

namespace App\DTOs\Customer;

/**
 * Encapsula os dados de entrada para cadastro de cliente.
 * Construído a partir de um FormRequest validado — nunca recebe
 * dados brutos não validados.
 */
final class RegisterCustomerDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $cpf,
        public readonly string $birthDate,
        public readonly string $email,
        public readonly string $phone,
        public readonly ?string $whatsapp,
        public readonly ?string $notes,
        public readonly string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            cpf: $data['cpf'],
            birthDate: $data['birth_date'],
            email: $data['email'],
            phone: $data['phone'],
            whatsapp: $data['whatsapp'] ?? null,
            notes: $data['notes'] ?? null,
            password: $data['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'name'       => $this->name,
            'cpf'        => $this->cpf,
            'birth_date' => $this->birthDate,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'whatsapp'   => $this->whatsapp,
            'notes'      => $this->notes,
            'password'   => $this->password,
        ];
    }
}
