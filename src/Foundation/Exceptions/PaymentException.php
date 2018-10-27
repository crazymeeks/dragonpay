<?php

namespace Crazymeeks\Foundation\Exceptions;

use Exception;

class PaymentException extends Exception
{
    public function __construct( $message, $code = 500, Exception $previous = null )
    {   
        parent::__construct( $message, $code, $previous );
    }
}