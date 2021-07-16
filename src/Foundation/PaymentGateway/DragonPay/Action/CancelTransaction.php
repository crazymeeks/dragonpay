<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Foundation\PaymentGateway\DragonPay\Action;

use Ixudra\Curl\CurlService;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\PaymentGateway\DragonPay\Action\BaseAction;
use Crazymeeks\Foundation\Exceptions\Action\CancelTransactionException;

class CancelTransaction extends BaseAction
{

    /**
     * @inheritDoc
     */
    public function doAction(Dragonpay $dragonpay, CurlService $curl = null)
    {
        $result = parent::doAction($dragonpay, $curl);

        if ($result == 0) {
            return true;
        }
        throw new CancelTransactionException();

    }


     /**
     * @inheritDoc
     */
    protected function getOp()
    {
        return 'VOID';
    }
}