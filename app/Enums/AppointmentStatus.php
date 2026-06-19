<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case AGENDADO        = 'agendado';
    case CONFIRMADO      = 'confirmado';
    case EM_ATENDIMENTO  = 'em_atendimento';
    case FINALIZADO      = 'finalizado';
    case CANCELADO       = 'cancelado';
    case REMARCADO       = 'remarcado';
    case NAO_COMPARECEU  = 'nao_compareceu';

    public function label(): string
    {
        return match ($this) {
            self::AGENDADO       => 'Agendado',
            self::CONFIRMADO     => 'Confirmado',
            self::EM_ATENDIMENTO => 'Em atendimento',
            self::FINALIZADO     => 'Finalizado',
            self::CANCELADO      => 'Cancelado',
            self::REMARCADO      => 'Remarcado',
            self::NAO_COMPARECEU => 'Não compareceu',
        };
    }

    /**
     * Status que ainda ocupam um slot de horário (bloqueiam conflitos).
     */
    public static function activeStatuses(): array
    {
        return [
            self::AGENDADO,
            self::CONFIRMADO,
            self::EM_ATENDIMENTO,
        ];
    }

    /**
     * Status finais — não podem mais ser alterados.
     */
    public static function finalStatuses(): array
    {
        return [
            self::FINALIZADO,
            self::CANCELADO,
            self::REMARCADO,
            self::NAO_COMPARECEU,
        ];
    }

    public function isFinal(): bool
    {
        return in_array($this, self::finalStatuses(), true);
    }

    /**
     * Transições permitidas a partir deste status.
     */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::AGENDADO       => in_array($target, [self::CONFIRMADO, self::CANCELADO, self::REMARCADO, self::NAO_COMPARECEU], true),
            self::CONFIRMADO      => in_array($target, [self::EM_ATENDIMENTO, self::CANCELADO, self::REMARCADO, self::NAO_COMPARECEU], true),
            self::EM_ATENDIMENTO  => in_array($target, [self::FINALIZADO], true),
            default               => false,
        };
    }
}
