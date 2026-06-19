<?php

namespace App\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Classe base para todas as exceções de domínio da aplicação.
 *
 * Todas as exceções customizadas devem estender esta classe.
 * O handler global em bootstrap/app.php as captura e retorna JSON padronizado.
 */
abstract class BuziosException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
