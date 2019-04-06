<?php

namespace Crazymeeks\Foundation\Exceptions\Action;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class CancelTransactionException extends PaymentException
{
    public function __construct()
    {
        parent::__construct("Cancellation unsuccessful. Please contact Dragonpay.");
    }
}