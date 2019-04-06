<?php

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action;

use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\Adapter\SoapClientAdapter;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\ActionInterface;

abstract class BaseAction implements ActionInterface
{


    /**
     * @inheritDoc
     */
    public function doAction(Dragonpay $dragonpay, SoapClientAdapter $soap_adapater = null)
    {
        $soap_adapater = is_null($soap_adapater) ? new SoapClientAdapter() : $soap_adapater;

        $merchant_account = $dragonpay->getMerchantAccount();
        
        $soap = $soap_adapater->initialize($dragonpay->getWebserviceUrl());

        $result = $soap->{$this->name}([
            'merchantId' => $merchant_account['merchantid'],
            'merchantPwd' => $merchant_account['password'],
            'txnId'       => $this->txnid,
        ]);

        return $result;

    }
}