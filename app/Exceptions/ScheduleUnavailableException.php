<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ScheduleUnavailableException extends BuziosException
{
    public function __construct(string $message = 'Este horário não está disponível para agendamento.')
    {
        parent::__construct($message, 'SCHEDULE_UNAVAILABLE', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
