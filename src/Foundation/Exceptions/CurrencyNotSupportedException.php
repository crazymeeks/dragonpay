<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class CurrencyNotSupportedException extends PaymentException
{

    public function construct( $message, $code = 106 )
    {
        parent::__construct($message, $code);
    }
}