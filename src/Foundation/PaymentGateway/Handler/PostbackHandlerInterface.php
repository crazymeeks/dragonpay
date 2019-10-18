<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Foundation\PaymentGateway\Handler;

interface PostbackHandlerInterface
{


    /**
     * Handle postback invoked by Dragonpay
     *
     * @param array $data
     * 
     * @return mixed
     */
    public function handle(array $data);

}