<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedAppointmentException extends BuziosException
{
    public function __construct(string $message = 'Você não tem permissão para acessar este agendamento.')
    {
        parent::__construct($message, 'UNAUTHORIZED_APPOINTMENT', Response::HTTP_FORBIDDEN);
    }
}
