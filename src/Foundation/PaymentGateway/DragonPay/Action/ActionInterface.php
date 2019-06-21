<?php


namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action;

use Ixudra\Curl\CurlService;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use  Crazymeeks\Foundation\Adapter\SoapClientAdapter;

interface ActionInterface
{
    public function doAction(Dragonpay $dragonpay, CurlService $curl = null);
}