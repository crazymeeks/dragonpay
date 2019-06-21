<?php

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action;

use Ixudra\Curl\CurlService;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\Adapter\SoapClientAdapter;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\ActionInterface;

abstract class BaseAction implements ActionInterface
{


    /**
     * Transaction operation
     *
     * @return void
     */
    protected function getOp()
    {
        throw new \Exception('Class {' . get_class($this) . '} does not implement getOp() method.');
    }

    /**
     * @inheritDoc
     */
    public function doAction(Dragonpay $dragonpay, CurlService $curl = null)
    {
        $curl = is_null($curl) ? new CurlService() : $curl;

        $merchant_account = $dragonpay->getMerchantAccount();

        
        $url = rtrim($dragonpay->getBaseUrlOf($dragonpay->getPaymentMode()), '/') . '/' . $this->name . '?op=' . $this->getOp() . '&';

        $parameters = [
            'merchantid' => $merchant_account['merchantid'],
            'merchantpwd' => $merchant_account['password'],
            'txnid' => $this->txnid,
        ];

        $url = $url . http_build_query($parameters);
        
        $result = $curl->to($url)
                       ->get();

        
        // $soap = $curl->initialize($dragonpay->getWebserviceUrl());

        // $result = $soap->{$this->name}([
        //     'merchantId' => $merchant_account['merchantid'],
        //     'merchantPwd' => $merchant_account['password'],
        //     'txnId'       => $this->txnid,
        // ]);
        
        return $result;

    }
}