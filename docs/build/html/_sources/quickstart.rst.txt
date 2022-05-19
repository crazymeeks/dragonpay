==========
Quickstart
==========

This page provide a quick introduction on how to use Dragonpay library. 
If you have not already installed Dragonpay, go check the :ref:`installation` page

Making your first payment
=========================

.. code-block:: php

    namespace YourNameSpace;

    use Crazymeeks\Foundation\PaymentGateway\Dragonpay;

    class ExampleClass
    {

        public function postCheckout()
        {
            $parameters = [
                'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
                'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
                'ccy' => 'PHP', # Char(3) The currency of the amount
                'description' => 'Test', # Varchar(128) A brief description of what the payment is for
                'email' => 'some@merchant.ph', # Varchar(40) email address of customer
                'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
                'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

            ];


            $merchant_account = [
                'merchantid' => 'MERCHANTID',
                'password'   => 'MERCHANT_KEY'
            ];
            // Initialize Dragonpay
            $dragonpay = new Dragonpay($merchant_account);
            // Set parameters, then redirect to dragonpay
            $dragonpay->setParameters($parameters)->away();

        }
    }

SOAP/XML Web Service Model(Recommended)
=======================================
For **GREATER SECURITY**, you can use the API using XML Web Service Model. Under this model, the parameters are not passed through browser redirect which are visible to end-users. Instead parameters are exchanged directly between the Merchant site and Payment Switch servers through SOAP calls. The PS will return a token which you will be used to redirect to PS.  
Just make sure you have ``SoapClient`` enabled/installed on your system and call ``getToken()`` method.

.. code-block:: php

    namespace YourNameSpace;

    use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
    use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token;

    class ExampleClass
    {

        public function postCheckout()
        {
            $parameters = [
                'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
                'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
                'ccy' => 'PHP', # Char(3) The currency of the amount
                'description' => 'Test', # Varchar(128) A brief description of what the payment is for
                'email' => 'some@merchant.ph', # Varchar(40) email address of customer
                'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
                'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

            ];

            $merchant_account = [
                'merchantid' => 'MERCHANTID',
                'password'   => 'MERCHANT_KEY'
            ];
            // Initialize Dragonpay
            $dragonpay = new Dragonpay($merchant_account);
            // Get token from Dragonpay
            $token = $dragonpay->getToken($parameters);
            // If $token instance of Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token, then proceed
            if ( $token instanceof Token ) {
                $dragonpay->away();
            }


        }
    }

Using Credit Card
=================
To use credit card payment, please make sure you have SoapClient installed/enabled on your system and make call to ``useCreditCard($parameters)`` method. This method will throw ``Crazymeeks\Foundation\Exceptions\SendBillingInfoException`` when error occurred.
**Note:** credit card is only available in production.

.. code-block:: php

    namespace YourNameSpace;

    use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
    use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token;

    class ExampleClass
    {

        public function postCheckout()
        {
            $parameters = [
                'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
                'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
                'ccy' => 'PHP', # Char(3) The currency of the amount
                'description' => 'Test', # Varchar(128) A brief description of what the payment is for
                'email' => 'some@merchant.ph', # Varchar(40) email address of customer
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

            $merchant_account = [
                'merchantid' => 'MERCHANTID',
                'password'   => 'MERCHANT_KEY'
            ];

            $testing = false; # Set Payment mode to production
            // Initialize Dragonpay
            $dragonpay = new Dragonpay($merchant_account, $testing);
            $dragonpay->useCreditCard($parameters)->away();
            
            # If you want to use SOAP, just chain call
            # getToken($parameters) method like below
            # $dragonpay->useCreditCard($parameters)->getToken($parameters)->away();

        }
    }

If you want to use token(recommended), you can do it using below code:

.. code-block:: php

    $dragonpay->useCreditCard($parameters)->getToken($parameters)->away();


==========================
Filtering Payment Channels
==========================


**Available payment channels**

    ``Dragonpay::ONLINE_BANK``
    ``Dragonpay::OTC_BANK``
    ``Dragonpay::OTC_NON_BANK``
    ``Dragonpay::PAYPAL``
    ``Dragonpay::GCASH``
    ``Dragonpay::INTL_OTC``

Payment Channels are grouped together by type. E.g ``Online Banking``, ``Over-the-Counter/ATM``, etc.
You can set payment channel by calling ``filterPaymentChannel()`` method and pass one of the available payment channels above.

.. code-block:: php

    namespace YourNameSpace;

    use Crazymeeks\Foundation\PaymentGateway\Dragonpay;

    class ExampleClass
    {

        public function postCheckout()
        {
            $parameters = [
                'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
                'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
                'ccy' => 'PHP', # Char(3) The currency of the amount
                'description' => 'Test', # Varchar(128) A brief description of what the payment is for
                'email' => 'some@merchant.ph', # Varchar(40) email address of customer
                'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
                'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

            ];

            $merchant_account = [
                'merchantid' => 'MERCHANTID',
                'password'   => 'MERCHANT_KEY'
            ];
            // Initialize Dragonpay
            $dragonpay = new Dragonpay($merchant_account);
            // Filter payment channel
            $dragonpay->filterPaymentChannel( Dragonpay::ONLINE_BANK );
            // Set parameters, then redirect to dragonpay
            $dragonpay->setParameters($parameters)->away();

        }
    }

Pre-selecting Payment Channels
==============================
If you want to go directly to a payment channel without having to select from the dropdown list and without stopping by the Dragonpay selection page, you can chain call the ``withProcid($procid)`` method. This method will throw ``Crazymeeks\Foundation\Exceptions\InvalidProcessIdException`` when processor id is not supported.

**Available Processors:**

``Processor::CREDIT_CARD``
``Processor::GCASH``
``Processor::PAYPAL``  
``Processor::BAYADCENTER``
``Processor::BITCOIN``
``Processor::CEBUANA_LHUILLIER``
``Processor::CHINA_UNIONPAY``
``Processor::DRAGONPAY_PREPARED_CREDITS``
``Processor::ECPAY``
``Processor::LBC``
``Processor::MLHUILLIER``
``Processor::ROBINSONS_DEPT_STORE``
``Processor::SM_PAYMENT_COUNTERS``

.. code-block:: php

    namespace YourNameSpace;

    use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
    use Crazymeeks\Foundation\PaymentGateway\Options\Processor;

    class ExampleClass
    {

        public function postCheckout()
        {
            $parameters = [
                'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
                'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
                'ccy' => 'PHP', # Char(3) The currency of the amount
                'description' => 'Test', # Varchar(128) A brief description of what the payment is for
                'email' => 'some@merchant.ph', # Varchar(40) email address of customer
                'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
                'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

            ];

            $merchant_account = [
                'merchantid' => 'MERCHANTID',
                'password'   => 'MERCHANT_KEY'
            ];
            // Initialize Dragonpay
            $dragonpay = new Dragonpay($merchant_account);
            // Set parameters, then redirect to dragonpay
            $dragonpay->setParameters($parameters)
                    ->withProcid(Processor::CREDIT_CARD)
                    ->away();
        }
    }

Or if you prefer using SOAP/XML web service

.. code-block:: php

    $token = $dragonpay->getToken($parameters);
    if ( $token instanceof \Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token ) {
        // use procid
        $dragonpay->withProcid(Processor::CREDIT_CARD)->away();
    }

Payment Mode
============
By default, the payment mode of this library is sandbox. To change this to production, just pass boolean ``false`` to second parameter of Constructor of ``Crazymeeks\Foundation\PaymentGateway\Dragonpay``.

.. code-block:: php

    $merchant_account = [
        'merchantid' => 'MERCHANTID',
        'password'   => 'MERCHANT_KEY'
    ];
    $testing = false;
    // Initialize Dragonpay
    $dragonpay = new Dragonpay($merchant_account, $testing);

Exceptions
==========
You can wrap your code in a ``try{}catch(){}`` and use ``Crazymeeks\Foundation\Exceptions\PaymentException`` so you can catch error and see error message safely when something went wrong.

.. code-block:: php

    namespace YourNameSpace;

    use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
    use Crazymeeks\Foundation\Exceptions\PaymentException;

    class ExampleClass
    {

        public function postCheckout()
        {
            $parameters = [
                'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
                'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
                'ccy' => 'PHP', # Char(3) The currency of the amount
                'description' => 'Test', # Varchar(128) A brief description of what the payment is for
                'email' => 'some@merchant.ph', # Varchar(40) email address of customer
                'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
                'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

            ];

            $merchant_account = [
                'merchantid' => 'MERCHANTID',
                'password'   => 'MERCHANT_KEY'
            ];

            $dragonpay = new Dragonpay($merchant_account);
            // Set parameters, then redirect to dragonpay
            try {
                $dragonpay->setParameters($parameters)->away();
            } catch(PaymentException $e){
                echo $e->getMessage();
            } catch(\Exception $e){
                echo $e->getMessage();
            }

        }
    }

Postback handler
================
According to DP's official documentation, **postback URL** is invoked directly by the PS and does not expect any return value. PS will invoke the **postback URL** first before the browser redirect to the **return URL**. Thus, the ideal process flow is: upon receiving the
postback URL call, the merchant’s system performs the necessary database updates
and initiate whatever back-end process is required. Then when it receives the return
URL call, it counter-checks the status in the database and provides the visual
response. If merchant does not provide both callback URL’s, PS will only invoke the
one provided. **Please keep in mind the HTTP method of your postback URL should be POST($_POST) not GET($_GET).**

.. image:: postbackURL.png

This library provides simple feature for this out of the box so you can handle data when PS invoked your _postback URL._ Just call `handlePostback()` method. `handlePostback()` will return the following array so you can do whatever you want to this returned data:

.. code-block:: php

    array(
        'txnid' => '109019',
        'refno' => '0398739',
        'status' => 'S',
        'message' => 'loioeiu8398!)()39483',
        'digest'  => '0oi30430aoi!)04490',
        'description' => 'Success'
    )

Usage
=====
Using closure/anonymous function:

.. code-block:: php

    $merchant_account = [
        'merchantid' => 'MERCHANTID',
        'password'   => 'MERCHANT_KEY'
    ];
    $dragonpay = new Dragonpay($merchant_account);
    $dragonpay->handlePostback(function($data){
        // do your stuff here like save data to your database.
        $insert = "Insert INTO mytable(`txnid`, `refno`, `status`) VALUES ($data['txnid'], $data['refno'])";
        mysql_query($insert);

        # or if you are in Laravel, you can use Model or DB Facade...
        // DB::table('mytable')->insert($data);
        
    }, $_POST);

Or if you are using Laravel framework, use ``$request->all()`` or ``$request->toArray()`` instead of $_POST.

.. code-block:: php

    $dragonpay->handlePostback(function($data){
        // do your stuff here like save data to your database.
        $insert = "Insert INTO mytable(`txnid`, `refno`, `status`) VALUES ($data['txnid'], $data['refno'])";
        mysql_query($insert);

        # or if you are in Laravel, you can use Model or DB Facade...
        // DB::table('mytable')->insert($data);
        
    }, $request->all());

Or you can also create your own class that implements ``Crazymeeks\Foundation\PaymentGateway\Handler\PostbackHandlerInterface``

.. code-block:: php

    namespace YourNameSpace;

    use Crazymeeks\Foundation\PaymentGateway\Handler\PostbackHandlerInterface;

    class MyPostBackHandler implements PostbackHandlerInterface
    {
        public function handle(array $data)
        {
            // do your stuff here like save data to your database.
            $insert = "Insert INTO mytable(`txnid`, `refno`, `status`) VALUES ($data['txnid'], $data['refno'])";
            mysql_query($insert);

            # or if you are in Laravel, you can use Model or DB Facade...
            // DB::table('mytable')->insert($data);
        }
    }
    $merchant_account = [
    'merchantid' => 'MERCHANTID',
    'password'   => 'MERCHANT_KEY'
    ];
    $dragonpay = new Dragonpay($merchant_account);
    $dragonpay->handlePostback(new MyPostBackHandler(), $_POST);
    # If you are in Laravel, use $request->all() or $request->toArray() instead of $_POST.
    # $dragonpay->handlePostback(new MyPostBackHandler(), $request->all());

Cancellation of Transaction
===========================
To cancel a transaction, just call ``action()`` method and pass object of ``Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\CancelTransaction`` with transaction id as constructor parameter. ``action()`` method will throw ``Crazymeeks\Foundation\Exceptions\Action\CancelTransactionException`` when error occured.

.. code-block:: php

    $merchant_account = [
       'merchantid' => 'MERCHANTID',
       'password'   => 'MERCHANT_KEY'
    ];
    $txnid = 'SAMPLE-TXNID-10910';
    $dragonpay = new Dragonpay($merchant_account);
    try{
        $dragonpay->action(new \Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\CancelTransaction($txnid));
    }catch(\Crazymeeks\Foundation\Exceptions\Action\CancelTransactionException $e){
        // Error transaction cancellation
    }

Transaction Status Inquiry
==========================
If you want to check transaction status, just call ``action()`` method of pass object of ``Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\CheckTransactionStatus``. You may pass either ``txnid`` or ``refno`` in the constructor of this class.

.. code-block:: php

    $merchant_account = [
        'merchantid' => 'MERCHANTID',
        'password'   => 'MERCHANT_KEY'
    ];
    $txnid = 'SAMPLE-TXNID-10910';
    
    $dragonpay = new Dragonpay($merchant_account);
    $status = $dragonpay->action(new \Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\CheckTransactionStatus($txnid));

Transaction Status Inquiry Response
===================================

.. code-block:: php
   
    stdClass Object
    (
        [RefNo] => XMNUQ7M9W5
        [MerchantId] => MERCHANTID
        [TxnId] => TXNID-145076875
        [RefDate] => 2022-05-19T16:37:11.915
        [Amount] => 1
        [Currency] => PHP
        [Description] => Test Description
        [Status] => S
        [Email] => some@merchant.ph
        [MobileNo] => 
        [ProcId] => BOG
        [ProcMsg] => [000] BOG Reference No: 20220519163731
        [SettleDate] => 2022-05-19T16:37:31.76
        [Param1] => param1
        [Param2] => param2
        [Fee] => 0
    )

Advanced Control
================
Please read Dragonpay_ documentation then read through 5.4.2 Advanced Control

.. _Dragonpay: https://www.dragonpay.ph/wp-content/uploads/Dragonpay-PS-API-v2-latest.pdf

.. code-block:: php

    $merchant_account = [
        'merchantid' => 'MERCHANTID',
        'password'   => 'MERCHANT_KEY'
    ];
    $dragonpay = new Dragonpay($merchant_account);
    $amount = Dragonpay::ALL_PROCESSORS;
    $processors = $dragonpay->getPaymentChannels($amount);

**Response**

.. code-block:: php

    Array
    (
        [0] => stdClass Object
            (
                [procId] => BDO
                [shortName] => BDO
                [longName] => BDO Internet Banking
                [logo] => ~/images/bdologo.jpg
                [currencies] => PHP
                [url] => 
                [realTime] => 1
                [pwd] => 
                [defaultBillerId] => 
                [hasTxnPwd] => 
                [hasManualEnrollment] => 1
                [type] => 1
                [status] => A
                [remarks] => Use your BDO Retail Internet Banking (RIB) account to make a payment. Read our <a href='http://www.dragonpay.ph/bdorib-how-to' target='_blank'>BDO RIB guide</a> for more details.
                [dayOfWeek] => XXXXXXX
                [startTime] => 06:00
                [endTime] => 21:30
                [minAmount] => 1
                [maxAmount] => 1000000
                [mustRedirect] => 
                [surcharge] => 0
                [hasAltRefNo] => 
                [cost] => 0
            )

        [1] => stdClass Object
            (
                [procId] => BDOA
                [shortName] => BDO ATM
                [longName] => Banco de Oro ATM
                [logo] => ~/images/bdologo.jpg
                [currencies] => PHP
                [url] => 
                [realTime] => 
                [pwd] => 
                [defaultBillerId] => 
                [hasTxnPwd] => 
                [hasManualEnrollment] => 
                [type] => 2
                [status] => A
                [remarks] => Pay at any BDO ATM nationwide. Payments are processed next day. <a href='http://www.dragonpay.ph/bdo-atm-how-to/' target='_blank'>Click here for details</a>. Payments are processed next day.
                [dayOfWeek] => XXXXXXX
                [startTime] => 00:00
                [endTime] => 00:00
                [minAmount] => 200
                [maxAmount] => 1000000
                [mustRedirect] => 
                [surcharge] => 0
                [hasAltRefNo] => 1
                [cost] => 0
            )

    )

Note: If an amount value greater than zero is passed, it will return
a list of channels available for that amount. But if you want to retrieve the full list
regardless of the amount so you can cache it locally and avoid having to calling the
web method for each transaction, you can set amount to ``Dragonpay::ALL_PROCESSORS``.

Updating payment url and web service url
========================================
If for some intance Dragonpay updated their payment and web service url(most likely will not happen).

**Payment URL** is the url where customer will be redirected to process and complete payment.  
**Web Service URL** is the url where we request token.  
**Send Billing Info URL** sending billing info for billing info for credit card payment

.. code-block:: php

    $merchant_account = [
        'merchantid' => 'MERCHANTID',
        'password'   => 'MERCHANT_KEY'
    ];
    $dragonpay = new Dragonpay($merchant_account);

    // Payment Url
    $newPaymentUrl = "https://test.dp.com/Pay.aspx";
    // Web Service Url
    $newWebSrvcUrl = "https://test.dp.com/WebService.aspx";
    $newBillingInfoUrl = "https://test.dp.com/WebServiceBilling.aspx";
    $dragonpay->setPaymentUrl($newPaymentUrl)
            ->setBillingInfoUrl($newBillingInfoUrl)
            ->setWebServiceUrl($newWebSrvcUrl);

**Note:** The code above will change the api urls of the sandbox. You just need to pass `boolean false`  
as 2nd parameter of `Dragonpay` class.  
It should look like this:

.. code-block:: php

    $is_sandbox = false;
    $dragonpay = new Dragonpay($merchant_account, $is_sandbox);

Tips
====
Do not use email domain ``@example.com``. It seems the Payment switch does not accept it.

Miscellaneous
=============
If you found any security issues or bugs, it will be a big help if you raise an issue or email the author directly and will address it right away.

Author
======
Jeff Claud