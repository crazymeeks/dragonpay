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

interface ActionInterface
{

    /**
     * Do action
     *
     * @param \Crazymeeks\Foundation\PaymentGateway\Dragonpay $dragonpay
     * @param \Ixudra\Curl\CurlService|null $curl
     * 
     * @return mixed
     */
    public function doAction(Dragonpay $dragonpay, CurlService $curl = null);
}