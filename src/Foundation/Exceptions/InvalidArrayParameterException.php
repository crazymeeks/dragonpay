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

use Crazymeeks\Foundation\Exceptions\PaymentException;

class InvalidArrayParameterException extends PaymentException
{

    public function construct($message, $code = 400)
    {
        parent::__construct($message, $code);
    }

    public static function invalid_array_key()
    {
        return new static("Missing required array key/s. Please check your key/s.", 400);
    }

    public static function send_billing_info_parameters()
    {
        return new static("Missing required array key/s. Please check your parameters when using credit card payment mode.", 400);
    }
}