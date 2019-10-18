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

class InvalidPostbackInvokerException extends PaymentException
{

    public function __construct()
    {
        parent::__construct("Danger!!! Postback must be invoke by Dragonpay but was invoked by unknown Payment Switch!");
    }
}