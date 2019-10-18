<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Foundation\PaymentGateway\Options;

use Crazymeeks\Foundation\Exceptions\InvalidProcessIdException;

/**
 * For preselecting payment channels, dragonpay has a
 * basic support to allow merchant to go directly to a payment
 * channel without having to select it from the dropdown list.
 * This feature is currently supported only for the following processor id's:
 * Globe Gcash
 * Credit Cards
 * PayPal
 */

class Processor
{

    const GCASH = 'GCSH';
    const CREDIT_CARD = 'CC';
    const PAYPAL = 'PYPL';
    const BAYADCENTER = 'BAYD';
    const BITCOIN = 'BITC';
    const CEBUANA_LHUILLIER = 'CEBL';
    const CHINA_UNIONPAY = 'CUP';
    const DRAGONPAY_PREPARED_CREDITS = 'DPAY';
    const ECPAY = 'ECPY';
    const LBC = 'LBC';
    const MLHUILLIER = 'MLH';
    const ROBINSONS_DEPT_STORE = 'RDS';
    const SM_PAYMENT_COUNTERS = 'SMR';





    static $valid_proc_ids = [
        self::GCASH,
        self::CREDIT_CARD,
        self::PAYPAL,
        self::BAYADCENTER,
        self::BITCOIN,
        self::CEBUANA_LHUILLIER,
        self::CHINA_UNIONPAY,
        self::DRAGONPAY_PREPARED_CREDITS,
        self::ECPAY,
        self::LBC,
        self::MLHUILLIER,
        self::ROBINSONS_DEPT_STORE,
        self::SM_PAYMENT_COUNTERS,
    ];


    /**
     * Check process id against valid procids
     *
     * @param string $procid
     * 
     * @return void
     * 
     * @throws InvalidProcessIdException
     */
    public static function allowedProcId($procid)
    {
        if (!in_array($procid, static::$valid_proc_ids)) {
            //throw new InvalidProcessIdException();
        }
    }

}