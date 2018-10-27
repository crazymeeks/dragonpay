<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidPaymentGatewayIdException extends PaymentException
{

    public function construct( $message, $code = 101 )
    {
        parent::__construct($message, $code);
    }
}