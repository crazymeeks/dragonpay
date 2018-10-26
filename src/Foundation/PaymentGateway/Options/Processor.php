<?php

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

    static $valid_proc_ids = [
        self::GCASH,
        self::CREDIT_CARD,
        self::PAYPAL
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
    public static function allowedProcId( $procid )
    {
        if ( ! in_array($procid, static::$valid_proc_ids) ) {
            throw new InvalidProcessIdException();
        }
    }

}