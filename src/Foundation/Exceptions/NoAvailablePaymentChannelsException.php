<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class NoAvailablePaymentChannelsException extends PaymentException
{

    public function __construct()
    {
        parent::__construct("No available payment channel.");
    }
}