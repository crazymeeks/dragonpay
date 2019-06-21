<?php

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action;

use Ixudra\Curl\CurlService;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\Adapter\SoapClientAdapter;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\BaseAction;

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