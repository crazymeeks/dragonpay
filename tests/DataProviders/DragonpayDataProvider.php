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
            'email' => 'some@merchant.ph', # Varchar(40) email address of customer
            'password' => 'PASSWORD', # This will be use to generate a digest key
            'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
            'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

        ];

        return [
            array($parameters)
        ];
    }

    /**
     * When using credit card, these info is required
     *
     * @return void
     */
    public function billing_info()
    {
        $parameters = [

            'merchantid' => 'MERCHANTID', # Varchar(20) A unique code assigned to Merchant
            'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
            'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
            'ccy' => 'PHP', # Char(3) The currency of the amount
            'description' => 'Test', # Varchar(128) A brief description of what the payment is for
            'email' => 'some@merchant.ph', # Varchar(40) email address of customer
            'password' => 'PASSWORD', # This will be use to generate a digest key
            'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
            'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

            'firstName' => 'John',
            'lastName'  => 'Doe',
            'address1'  => '#123 Chocolate Hills',
            'address2'  => 'Sweet Mountain',
            'city'      => 'Hillside',
            'state'     => 'Bohol',
            'country'   => 'PH',
            'zipCode'   => '1201',
            'telNo'     => '63 2029',
        ];

        return [
            array($parameters)
        ];
    }

    /**
     * Data pass by Dragonpay when the application's postback api
     * invoked by Dragonpay
     *
     * @return array
     */
    public function postback()
    {
        $_POST = [
            'txnid' => 'SOMERANDOMID',
            'refno' => 'SOMERANDOMREFNO',
            'status' => 'S',
            'message' => 'SOMERANDOMMESSAGE',
            'digest'  => 'THEENCRYPTEDDIGEST',
        ];

        return [
            array($_POST)
        ];
    }

    /**
     * Available payment channels
     *
     * @return array
     */
    public function getAllPaymentChannels()
    {

        $parameters = [

            'merchantid' => 'MERCHANTID', # Varchar(20) A unique code assigned to Merchant
            'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
            'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
            'ccy' => 'PHP', # Char(3) The currency of the amount
            'description' => 'Test', # Varchar(128) A brief description of what the payment is for
            'email' => 'some@merchant.ph', # Varchar(40) email address of customer
            'password' => 'PASSWORD', # This will be use to generate a digest key
            'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
            'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

        ];

        $payment_channels = json_decode(file_get_contents(__DIR__ . '/_files/payment_channels.json'));

        return [
            array($payment_channels, $parameters)
        ];
        
    }

}