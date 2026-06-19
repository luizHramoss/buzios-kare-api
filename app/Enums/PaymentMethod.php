<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case DINHEIRO   = 'dinheiro';
    case PIX        = 'pix';
    case CARTAO_CREDITO = 'cartao_credito';
    case CARTAO_DEBITO  = 'cartao_debito';
    case TRANSFERENCIA  = 'transferencia';

    public function label(): string
    {
        return match ($this) {
            self::DINHEIRO       => 'Dinheiro',
            self::PIX            => 'PIX',
            self::CARTAO_CREDITO => 'Cartão de Crédito',
            self::CARTAO_DEBITO  => 'Cartão de Débito',
            self::TRANSFERENCIA  => 'Transferência',
        };
    }
}
