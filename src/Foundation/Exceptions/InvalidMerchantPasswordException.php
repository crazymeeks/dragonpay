<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidMerchantPasswordException extends PaymentException
{

    public function construct( $message, $code = 202 )
    {
        parent::__construct($message, $code);
    }
}