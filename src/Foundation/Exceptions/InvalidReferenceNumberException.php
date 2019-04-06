<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidReferenceNumberException extends PaymentException
{

    public function construct($message, $code = 103)
    {
        parent::__construct($message, $code);
    }
}