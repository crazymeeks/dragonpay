<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class SendBillingInfoException extends PaymentException
{

    public function construct( $message, $code = 500 )
    {
        parent::__construct($message, $code);
    }
}