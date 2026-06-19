<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InsufficientPermissionException extends BuziosException
{
    public function __construct(string $message = 'Você não tem permissão para realizar esta ação.')
    {
        parent::__construct($message, 'INSUFFICIENT_PERMISSION', Response::HTTP_FORBIDDEN);
    }
}
