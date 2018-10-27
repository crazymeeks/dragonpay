<?php

namespace Crazymeeks\Foundation\Exceptions;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidProcessIdException extends PaymentException
{
    public function __construct()
    {
        parent::__construct("Invalid Process ID", 500);
    }
}