<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que o usuário autenticado pertence ao guard/role correto.
 *
 * Uso nas rotas:
 *   ->middleware('auth.role:admin')
 *   ->middleware('auth.role:customer')
 *
 * Impede que um token de cliente acesse rotas de administrador e vice-versa.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        foreach ($roles as $role) {
            if ($request->user($role) !== null) {
                // Força o usuário autenticado na request para o guard correto
                auth()->shouldUse($role);

                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Não autorizado.',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
