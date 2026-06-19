<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        // Os bindings de Repositories serão registrados na Etapa 3
        // via RepositoryServiceProvider.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Configura os rate limiters da aplicação.
     *
     * - api:  60 req/min por IP (rotas gerais)
     * - auth: 10 req/min por IP (login, register, forgot-password)
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $limit = (int) env('RATE_LIMIT_API', 60);

            return Limit::perMinute($limit)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Muitas requisições. Aguarde um momento e tente novamente.',
                    ], 429);
                });
        });

        RateLimiter::for('auth', function (Request $request) {
            $limit = (int) env('RATE_LIMIT_AUTH', 10);

            return Limit::perMinute($limit)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Muitas tentativas de autenticação. Aguarde 1 minuto.',
                    ], 429);
                });
        });
    }
}
