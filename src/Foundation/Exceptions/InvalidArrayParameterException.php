<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidArrayParameterException extends PaymentException
{

    public function construct( $message, $code = 400 )
    {
        parent::__construct($message, $code);
    }

    public function invalid_array_key()
    {
        return new static("Missing required array key/s. Please check your key/s.", 400);
    }

    public static function send_billing_info_parameters()
    {
        return new static("Missing required array key/s. Please check your parameters when using credit card payment mode.", 400);
    }
}