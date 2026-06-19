<?php

namespace App\Traits;

use App\Support\AuditLogger;

/**
 * Fornece método auxiliar para registrar eventos de auditoria.
 *
 * Uso nos Services/Actions:
 *   $this->auditLog('appointment.created', $appointment, old: [], new: $data);
 */
trait HasAuditLog
{
    protected function auditLog(
        string $event,
        mixed $auditable = null,
        array $oldValues = [],
        array $newValues = [],
    ): void {
        app(AuditLogger::class)->log(
            event: $event,
            auditable: $auditable,
            oldValues: $oldValues,
            newValues: $newValues,
        );
    }
}
