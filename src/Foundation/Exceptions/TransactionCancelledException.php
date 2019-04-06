<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class TransactionCancelledException extends PaymentException
{

    public function construct($message, $code = 107)
    {
        parent::__construct($message, $code);
    }
}