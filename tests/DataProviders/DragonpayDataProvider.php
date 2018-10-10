<?php

namespace Tests\DataProviders;


class DragonpayDataProvider
{

    /**
     * PS REQUEST PARAMETERS
     *
     * @return array
     */
    public function request_parameters()
    {
        $parameters = [

            'merchantid' => 'MERCHANTID', # Varchar(20) A unique code assigned to Merchant
            'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
            'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
            'ccy' => 'PHP', # Char(3) The currency of the amount
            'description' => 'Test', # Varchar(128) A brief description of what the payment is for
            'email' => 'test@example.com', # Varchar(40) email address of customer
            'password' => 'PASSWORD', # This will be use to generate a digest key
            'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
            'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

        ];

        return [
            array($parameters)
        ];
    }

}