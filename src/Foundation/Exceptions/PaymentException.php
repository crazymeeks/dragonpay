<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Foundation\Exceptions;

use Exception;

class PaymentException extends Exception
{
    public function __construct($message, $code = 500, Exception $previous = null)
    {   
        parent::__construct($message, $code, $previous);
    }
}