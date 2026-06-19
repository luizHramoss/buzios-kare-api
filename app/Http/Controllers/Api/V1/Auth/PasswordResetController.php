<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\Customer;
use App\Support\AuditLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Recuperação de senha apenas para Clientes.
 * Administradores não possuem fluxo de "esqueci minha senha" via API
 * pública — reset de senha de admin é feito por outro admin via
 * AdminController::update().
 */
class PasswordResetController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {}

    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        // Resposta genérica sempre, para não revelar se o e-mail existe (segurança).
        return response()->json([
            'message' => 'Se o e-mail existir em nossa base, um link de recuperação foi enviado.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Customer $customer, string $password) {
                $customer->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Revoga todos os tokens existentes por segurança
                $customer->tokens()->delete();

                event(new PasswordReset($customer));

                $this->auditLogger->log(
                    event: 'customer.password_reset',
                    auditable: $customer,
                    userId: $customer->id,
                    userType: 'customer',
                );
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Não foi possível redefinir a senha. Token inválido ou expirado.',
            ], 422);
        }

        return response()->json([
            'message' => 'Senha redefinida com sucesso.',
        ]);
    }
}
