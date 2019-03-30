<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidPostbackInvokerException extends PaymentException
{

    public function __construct()
    {
        parent::__construct("Danger!!! Postback must be invoke by Dragonpay but was invoked by unknown Payment Switch!");
    }
}