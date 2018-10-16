<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidTokenException extends PaymentException
{

    public function construct( $message, $code = 105 )
    {
        parent::__construct($message, $code);
    }
}