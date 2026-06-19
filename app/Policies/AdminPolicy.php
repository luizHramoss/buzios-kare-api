<?php

namespace App\Policies;

use App\Models\Admin;

class AdminPolicy
{
    /**
     * Apenas super_admin pode criar, editar ou excluir outros administradores.
     */
    public function manage(Admin $actingAdmin): bool
    {
        return $actingAdmin->isSuperAdmin();
    }

    /**
     * Um admin não pode excluir a própria conta, e o último super_admin
     * não pode ser removido (validação adicional fica no Service).
     */
    public function delete(Admin $actingAdmin, Admin $target): bool
    {
        if ($actingAdmin->id === $target->id) {
            return false;
        }

        return $actingAdmin->isSuperAdmin();
    }
}
