<?php

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action;

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
    protected $name = 'GetTxnStatus';
    
    protected $txnid;
    
    public function __construct($txnid)
    {
        $this->txnid = $txnid;
    }

    /**
     * @inheritDoc
     */
    public function doAction(Dragonpay $dragonpay, SoapClientAdapter $soap_adapater = null)
    {
        $result = parent::doAction($dragonpay, $soap_adapater);
        return Dragonpay::STATUS[$result->GetTxnStatusResult];
    }
}