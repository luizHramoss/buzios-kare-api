<?php

namespace App\DTOs\Customer;

final class UpdateCustomerProfileDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $phone,
        public readonly ?string $whatsapp,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            phone: $data['phone'] ?? null,
            whatsapp: $data['whatsapp'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * Retorna apenas os campos efetivamente enviados, para um UPDATE parcial.
     */
    public function toArrayFiltered(): array
    {
        return array_filter(
            [
                'name'     => $this->name,
                'phone'    => $this->phone,
                'whatsapp' => $this->whatsapp,
                'notes'    => $this->notes,
            ],
            fn ($value) => $value !== null
        );
    }
}
