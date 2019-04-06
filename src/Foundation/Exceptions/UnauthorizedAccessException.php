<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class UnauthorizedAccessException extends PaymentException
{

    public function construct($message, $code = 104)
    {
        parent::__construct($message, $code);
    }
}