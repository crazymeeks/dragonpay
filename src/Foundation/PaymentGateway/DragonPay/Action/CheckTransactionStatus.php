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
use Crazymeeks\Foundation\PaymentGateway\DragonPay\Action\BaseAction;

class CheckTransactionStatus extends BaseAction
{

    /**
     * @inheritDoc
     */
    public function doAction(Dragonpay $dragonpay, CurlService $curl = null)
    {
        // $URL='https://test.dragonpay.ph/api/collect/v1/refno/XMNUQ7M9W5


        $callback = $this->getClosure($dragonpay, $curl);

        $response = $callback('txnid');

        if ($response->status === 200) {
            return json_decode($response->content);
        }
        // retry using refno
        $response = $callback('refno');
        
        if ($response->status === 200) {
            return json_decode($response->content);
        }

        return null;
        
    }

    /**
     * Get closure
     *
     * @param \Crazymeeks\Foundation\PaymentGateway\Dragonpay $dragonpay
     * @param \Ixudra\Curl\CurlService|null $curl
     * 
     * @return \Closure
     */
    protected function getClosure(Dragonpay $dragonpay, CurlService $curl = null)
    {
        $curl = is_null($curl) ? new CurlService() : $curl;

        $merchant_account = $dragonpay->getMerchantAccount();

        $url = str_replace('/Pay.aspx', '', rtrim($dragonpay->getBaseUrlOf($dragonpay->getPaymentMode()), '/'));
        
        $parameters = [
            'merchantid' => $merchant_account['merchantid'],
            'merchantpwd' => $merchant_account['password'],
            'txnid' => $this->getTransactionId(),
        ];

        return function($endpoint) use ($curl, $url, $parameters) {
            $response = $curl->to($url . "/api/collect/v1/$endpoint/" . $parameters['txnid'])
                       ->withHeader('Authorization: Basic ' . base64_encode($parameters['merchantid'] . ':' . $parameters['merchantpwd']))
                       ->returnResponseObject()
                       ->get();
            return $response;
        };

        
    }


    /**
     * @inheritDoc
     */
    protected function getOp()
    {
        return 'GETSTATUS';
    }
}