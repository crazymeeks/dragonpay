<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Foundation\Exceptions\Action;

use Crazymeeks\Foundation\Exceptions\PaymentException;

class CancelTransactionException extends PaymentException
{
    public function __construct()
    {
        parent::__construct("Cancellation unsuccessful. Please contact Dragonpay.");
    }
}