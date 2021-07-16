<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Contracts;

interface DigestInterface
{


    /**
     * Create digest
     *
     * @param array $data The data needs to be encrypted
     * 
     * @return mixed
     */
    public function make(array $data);
}