<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class CustomerBlockedException extends BuziosException
{
    public function __construct(string $message = 'Sua conta está bloqueada. Entre em contato com o suporte.')
    {
        parent::__construct($message, 'CUSTOMER_BLOCKED', Response::HTTP_FORBIDDEN);
    }
}
