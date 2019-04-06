<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class ErrorInOperationException extends PaymentException
{

    public function construct($message, $code = 110)
    {
        parent::__construct($message, $code);
    }
}