<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidMerchantIdException extends PaymentException
{

    public function construct($message, $code = 201)
    {
        parent::__construct($message, $code);
    }
}