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
use Crazymeeks\Foundation\PaymentGateway\DragonPay\Action\ActionInterface;

abstract class BaseAction implements ActionInterface
{

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

        $url = str_replace('/Pay.aspx', '', rtrim($dragonpay->getBaseUrlOf($dragonpay->getPaymentMode()), '/') . '/' . $this->getActionName() . '?op=' . $this->getOp() . '&');
        
        $parameters = [
            'merchantid' => $merchant_account['merchantid'],
            'merchantpwd' => $merchant_account['password'],
            'txnid' => $this->getTransactionId(),
        ];

        $url = $url . http_build_query($parameters);
        
        $result = $curl->to($url)
                       ->get();
                       
        return $result;

    }

    /**
     * Get transaction id
     *
     * @return string
     */
    protected function getTransactionId()
    {
        return $this->txnid;
    }

    /**
     * Get Dragonpay web service name
     */
    protected function getActionName()
    {
        return 'MerchantRequest.aspx';
    }
}