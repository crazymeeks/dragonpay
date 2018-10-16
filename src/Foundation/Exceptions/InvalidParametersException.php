<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidParametersException extends PaymentException
{

    public function construct( $message, $code = 111 )
    {
        parent::__construct($message, $code);
    }
}