<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class InvalidAppointmentDateException extends BuziosException
{
    public function __construct(string $message = 'Data ou horário inválido para agendamento.')
    {
        parent::__construct($message, 'INVALID_APPOINTMENT_DATE', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
