<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class IncorrectSecretKeyException extends PaymentException
{

    public function construct( $message, $code = 102 )
    {
        parent::__construct($message, $code);
    }
}