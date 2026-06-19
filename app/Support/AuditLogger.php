<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Serviço centralizado de auditoria.
 *
 * Registra na tabela audit_logs:
 *   - Quem realizou a ação (user_type + user_id)
 *   - Qual ação (event)
 *   - Sobre qual recurso (auditable_type + auditable_id)
 *   - Valores anteriores e novos (old_values, new_values)
 *   - IP e User Agent
 *
 * Injetado via IoC container — nunca instanciar com `new`.
 */
class AuditLogger
{
    public function __construct(
        private readonly Request $request,
    ) {}

    public function log(
        string $event,
        mixed $auditable = null,
        array $oldValues = [],
        array $newValues = [],
        ?int $userId = null,
        ?string $userType = null,
    ): void {
        try {
            $user = $this->resolveUser($userId, $userType);

            AuditLog::create([
                'user_type'       => $user['type'],
                'user_id'         => $user['id'],
                'event'           => $event,
                'auditable_type'  => $auditable instanceof Model ? get_class($auditable) : null,
                'auditable_id'    => $auditable instanceof Model ? $auditable->getKey() : null,
                'old_values'      => empty($oldValues) ? null : $oldValues,
                'new_values'      => empty($newValues) ? null : $newValues,
                'ip_address'      => $this->request->ip(),
                'user_agent'      => $this->request->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Falha na auditoria nunca deve interromper a operação principal
            \Log::error('AuditLogger failed', [
                'event'   => $event,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function resolveUser(?int $userId, ?string $userType): array
    {
        if ($userId !== null && $userType !== null) {
            return ['id' => $userId, 'type' => $userType];
        }

        // Tenta detectar o usuário autenticado nos guards disponíveis
        foreach (['admin', 'customer'] as $guard) {
            $user = Auth::guard($guard)->user();
            if ($user !== null) {
                return [
                    'id'   => $user->getKey(),
                    'type' => class_basename($user),
                ];
            }
        }

        return ['id' => null, 'type' => 'System'];
    }
}
