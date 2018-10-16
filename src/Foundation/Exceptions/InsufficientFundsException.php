<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InsufficientFundsException extends PaymentException
{

    public function construct( $message, $code = 108 )
    {
        parent::__construct($message, $code);
    }
}