<?php


namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action;

use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use  Crazymeeks\Foundation\Adapter\SoapClientAdapter;

interface ActionInterface
{
    public function doAction(Dragonpay $dragonpay, SoapClientAdapter $soap_adapter = null);
}