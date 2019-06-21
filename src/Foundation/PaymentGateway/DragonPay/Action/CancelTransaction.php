<?php

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action;

use Ixudra\Curl\CurlService;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\Adapter\SoapClientAdapter;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\BaseAction;
use Crazymeeks\Foundation\Exceptions\Action\CancelTransactionException;

class CancelTransaction extends BaseAction
{

    /**
     * Dragonpay Web service name
     *
     * @var string
     */
    protected $name = 'MerchantRequest.aspx';


    /**
     * Dragonpay transaction id
     *
     * @var string
     */
    protected $txnid;

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
     * @inheritDoc
     */
    public function doAction(Dragonpay $dragonpay, CurlService $curl = null)
    {
        $result = parent::doAction($dragonpay, $curl);

        if ($result == 0) {
            return true;
        }
        throw new CancelTransactionException();

    }


     /**
     * @inheritDoc
     */
    protected function getOp()
    {
        return 'VOID';
    }
}