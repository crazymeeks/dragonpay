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
use Crazymeeks\Foundation\Adapter\SoapClientAdapter;
use Crazymeeks\Foundation\PaymentGateway\DragonPay\Action\BaseAction;

class CheckTransactionStatus extends BaseAction
{
 


    /**
     * Dragonpay Web service name
     *
     * @var string
     */
    protected $name = 'MerchantRequest.aspx';
    
    protected $txnid;
    
    public function __construct($txnid)
    {
        $this->txnid = $txnid;
    }

    /**
     * @inheritDoc
     */
    public function doAction(Dragonpay $dragonpay, CurlService $curl = null)
    {
        $status = parent::doAction($dragonpay, $curl);

        return Dragonpay::STATUS[$status];
    }


    /**
     * @inheritDoc
     */
    protected function getOp()
    {
        return 'GETSTATUS';
    }
}