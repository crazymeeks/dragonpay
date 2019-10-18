<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Tests\Classes;

use Crazymeeks\Foundation\PaymentGateway\Handler\PostbackHandlerInterface;

class PostbackHandler implements PostbackHandlerInterface
{


    /**
     * @inheritDoc
     */
    public function handle(array $data)
    {
        // Developer can do everything here...
        // like save/update data to database, etc
        return $data;
    }
}