<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Customer;

class CustomerPolicy
{
    /**
     * Qualquer admin ativo pode gerenciar clientes.
     * Cliente nunca acessa este policy — endpoints de perfil próprio
     * usam autorização implícita (sempre o usuário autenticado).
     */
    public function manage(Admin $admin): bool
    {
        return $admin->isActive();
    }
}
