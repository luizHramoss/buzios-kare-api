<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class AppointmentAlreadyExistsException extends BuziosException
{
    public function __construct(string $message = 'Já existe um agendamento neste horário.')
    {
        parent::__construct($message, 'APPOINTMENT_ALREADY_EXISTS', Response::HTTP_CONFLICT);
    }
}
