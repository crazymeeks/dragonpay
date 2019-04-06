<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class TransactionLimitExceededException extends PaymentException
{

    public function construct($message, $code = 109)
    {
        parent::__construct($message, $code);
    }
}