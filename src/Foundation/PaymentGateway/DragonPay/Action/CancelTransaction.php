<?php

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action;

use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\Adapter\SoapClientAdapter;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\BaseAction;
use Crazymeeks\Foundation\Exceptions\Action\CancelTransactionException;

class CancelTransaction extends BaseAction
{

    /**
     * Dragonpay transaction id
     *
     * @var string
     */
    protected $txnid;

    /**
     * Dragonpay Web service name
     *
     * @var string
     */
    protected $name = 'CancelTransaction';


    /**
     * Constructor
     * 
     * @param string $txnid  Dragonpay transaction id
     * 
     */
    public function __construct($txnid)
    {
        $this->txnid = $txnid;
    }

    /**
     * Cancel transaction
     *
     * @param Crazymeeks\Foundation\PaymentGateway\Dragonpay $dragonpay
     * @param null|Crazymeeks\Foundation\Adapter\SoapClientAdapter $soap_adapter
     * 
     * @return void
     */
    public function doAction(Dragonpay $dragonpay, SoapClientAdapter $soap_adapater = null)
    {
        $result = parent::doAction($dragonpay, $soap_adapater);

        if ($result->CancelTransactionResult == 0) {
            return true;
        }
        throw new CancelTransactionException();

    }
}