<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Força o header Accept: application/json em todas as requisições da API.
 * Isso garante que o Laravel retorne JSON mesmo em erros de autenticação,
 * validação e exceções não tratadas — nunca HTML.
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
