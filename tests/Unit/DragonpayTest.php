<?php

namespace Tests\Unit;

use Tests\TestCase;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\Exceptions\PaymentException;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token;
use Crazymeeks\Foundation\PaymentGateway\Options\Processor;

use Crazymeeks\Foundation\Adapter\SoapClientAdapter;
use Crazymeeks\Foundation\PaymentGateway\BillingInfoVerifier;

class DragonpayTest extends TestCase
{

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_should_create_request_parameters( $parameters )
    {
        $dragonpay = new Dragonpay();

        $dragonpay->setParameters(
            $parameters
        );

        $this->assertSame($dragonpay->parameters->get(), [
            'merchantid' => 'MERCHANTID', # Varchar(20) A unique code assigned to Merchant
            'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
            'amount' => number_format(1, 2, '.', ''), # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
            'ccy' => 'PHP', # Char(3) The currency of the amount
            'description' => 'Test', # Varchar(128) A brief description of what the payment is for
            'email' => 'some@merchant.ph', # Varchar(40) email address of customer
            'digest' => sha1('MERCHANTID:TXNID:1.00:PHP:Test:some@merchant.ph:PASSWORD'), # This will be use to generate a digest key
            'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
            'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
        ]);

    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_should_create_query_string_from_parameters( $parameters )
    {

        $dragonpay = new Dragonpay();

        $dragonpay->setParameters(
            $parameters
        );

        $expected =  http_build_query([
            'merchantid' => 'MERCHANTID', # Varchar(20) A unique code assigned to Merchant
            'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
            'amount' => number_format(1, 2, '.', ''), # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
            'ccy' => 'PHP', # Char(3) The currency of the amount
            'description' => 'Test', # Varchar(128) A brief description of what the payment is for
            'email' => 'some@merchant.ph', # Varchar(40) email address of customer
            'digest' => sha1('MERCHANTID:TXNID:1.00:PHP:Test:some@merchant.ph:PASSWORD'), # This will be use to generate a digest key
            'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
            'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed, '', '&'
        ], '', '&');
        
        $this->assertEquals($expected, $dragonpay->parameters->query());

    }

    /**
     * When using SOAP/XML web service.
     * 
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_should_set_request_token_parameters( $parameters )
    {
        $dragonpay = new Dragonpay();

        $parameters['merchantid'] = getenv('MERCHANT_ID');
        $parameters['password'] = getenv('MERCHANT_KEY');
        $parameters['txnid'] = 'TXNID-' . rand();
        
        $token = $dragonpay->getToken(
            $parameters
        );
        
        $this->assertInstanceof(Token::class, $token);
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_should_throw_payment_exception_if_dragonpay_returns_error( $parameters )
    {
        $this->expectException( PaymentException::class );

        $dragonpay = new Dragonpay();

        $token = $dragonpay->getToken(
            $parameters
        );

    }

   /**
    * @test
    * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
    */
    public function it_should_see_error( $parameters )
    {

        $dragonpay = new Dragonpay();
        try{
            $token = $dragonpay->getToken(
                $parameters
            );
        }catch( PaymentException $e ){
            $this->assertEquals( $e->getMessage(), $dragonpay->seeError() );
        }
        
    }
    
    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_should_set_payment_channel( $parameters )
    {
        $dragonpay = new Dragonpay();

        $dragonpay->filterPaymentChannel( Dragonpay::CREDIT_CARD );

        $this->assertEquals(64, $dragonpay->getPaymentChannel());
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_should_redirect_to_dragonpay_portal_when_parameters_set_are_valid( $parameters )
    {
        $dragonpay = new Dragonpay();

        $dragonpay->setParameters(
            $parameters
        );
        $dragonpay->filterPaymentChannel( Dragonpay::ONLINE_BANK );
        $url = $dragonpay->away( true );
        
        $url = parse_url($url);
        
        $query_params = explode('=', $url['query']);
        
        $this->assertEquals('merchantid', $query_params[0]);

        $this->assertEquals('test.dragonpay.ph', $url['host']);

    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_should_redirect_to_dragonpay_when_request_token_is_valid( $parameters )
    {
        $dragonpay = new Dragonpay();

        $parameters['merchantid'] = getenv('MERCHANT_ID');
        $parameters['password'] = getenv('MERCHANT_KEY');
        $parameters['txnid'] = 'TXNID-' . rand();
        
        $token = $dragonpay->getToken(
            $parameters
        );
        $dragonpay->filterPaymentChannel( Dragonpay::CREDIT_CARD );
        $url = $dragonpay->away( true );
        
        $url = parse_url($url);
        $query_params = explode('=', $url['query']);
        $this->assertEquals('tokenid', $query_params[0]);
        $this->assertEquals(64, $query_params[2]);
        
    }

    /**
     * @test
     */
    public function it_should_set_payment_url()
    {
        $dragonpay = new Dragonpay();

        // $dragonpay->setPaymentUrl('http://test.example.com/test.aspx' , 'sandbox');

        // $this->assertEquals( 'http://test.example.com/test.aspx', $dragonpay->getWebserviceUrl() );
        
        // $this->assertEquals('sandbox', $dragonpay->getPaymentMode());

        $url = $dragonpay->setPaymentUrl('http://test.example.com/test.aspx')->getPaymentUrl();

        $this->assertEquals('http://test.example.com/test.aspx', $url);

    }

    /**
     * @test
     */
    public function it_should_set_send_billing_info_url()
    {
        $dragonpay = new Dragonpay();
        $new_billing_info_url = $dragonpay->setBillingInfoUrl('http://test.dragonpay.billinginfo.aspx')
                                          ->getBillingInfoUrl();
        $this->assertEquals('http://test.dragonpay.billinginfo.aspx', $new_billing_info_url);
    }

    /**
     * @test
     */
    public function it_should_set_web_service_url()
    {
        $dragonpay = new Dragonpay();
        $url = $dragonpay->setWebServiceUrl('http://test.dragonpay.webservice.aspx')
                         ->getWebServiceUrl();
        $this->assertEquals('http://test.dragonpay.webservice.aspx', $url);
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::billing_info()
     */
    public function it_should_pay_using_credit_card_with_using_query_parameters( $parameters )
    {
        $dragonpay = new Dragonpay();

        $verifier = \Mockery::mock(BillingInfoVerifier::class);
        $soap = \Mockery::mock(SoapClientAdapter::class);

        $verifier->shouldReceive('setParameterObject')
                 ->with($dragonpay->parameters);
        $verifier->shouldReceive('send')
                 ->with($soap, $dragonpay->getBillingInfoUrl())
                 ->andReturn(true);

        $soap->shouldReceive('setParameters')
             ->with($dragonpay->parameters->billing_info());
        $soap->shouldReceive('execute')
             ->with($dragonpay->getBillingInfoUrl() . '?wsdl', array(
                'location' => $dragonpay->getBillingInfoUrl(),
                'trace' => 1,
            ));


        $parameters['merchantid'] = getenv('MERCHANT_ID');
        $parameters['password'] = getenv('MERCHANT_KEY');
        $parameters['txnid'] = 'TXNID-' . rand();

        $dragonpay->useCreditCard($parameters, $verifier, $soap);
        
        $url = $dragonpay->away( true );
        
        $url = parse_url($url);
        $query_params = explode('=', $url['query']);
        
        
        $this->assertEquals('merchantid', $query_params[0]);
        $this->assertEquals(64, $dragonpay->getPaymentChannel());
        
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::billing_info()
     */
    public function it_should_pay_using_credit_card_with_requested_token( $parameters )
    {
        $dragonpay = new Dragonpay();

        $verifier = \Mockery::mock(BillingInfoVerifier::class);
        $soap = \Mockery::mock(SoapClientAdapter::class);

        $verifier->shouldReceive('setParameterObject')
                 ->with($dragonpay->parameters);
        $verifier->shouldReceive('send')
                 ->with($soap, $dragonpay->getBillingInfoUrl())
                 ->andReturn(true);

        $soap->shouldReceive('setParameters')
             ->with($dragonpay->parameters->billing_info());
        $soap->shouldReceive('execute')
             ->with($dragonpay->getBillingInfoUrl() . '?wsdl', array(
                'location' => $dragonpay->getBillingInfoUrl(),
                'trace' => 1,
            ));


        $parameters['merchantid'] = getenv('MERCHANT_ID') ? getenv('MERCHANT_ID') : 'MERCHANT_ID';
        $parameters['password'] = getenv('MERCHANT_KEY') ? getenv('MERCHANT_KEY') : 'MERCHANT_KEY';
        $parameters['txnid'] = 'TXNID-' . rand();

        
        $getTokenReturn = new \stdClass();
        $getTokenReturn->GetTxnTokenResult = 'dp-returned-token';

        $soap_adapter = \Mockery::mock(\Crazymeeks\Foundation\Adapter\SoapClientAdapter::class);
        $soap_client = \Mockery::mock(\SoapClient::class);
        

        $soap_adapter->shouldReceive('initialize')
                    ->with($dragonpay->getWebserviceUrl())
                    ->andReturn($soap_client);

        $soap_adapter->shouldReceive('GetTxnToken')
                     ->with($dragonpay->parameters->prepareRequestTokenParameters($parameters))
                     ->andReturn($getTokenReturn);


        $token = $dragonpay->useCreditCard($parameters, $verifier, $soap)
                  ->getToken($parameters, $soap_adapter);
        
        $url = $dragonpay->away( true );
        $url = parse_url($url);
        $query_params = explode('=', $url['query']);
        $soap_url = $url['scheme'] . '://' . $url['host'] . $url['path'];
        $this->assertEquals('tokenid', $query_params[0]);
        $this->assertEquals($soap_url, $dragonpay->getPaymentUrl());
        $this->assertInstanceof(Token::class, $token);
        
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_should_set_procid_in_the_parameters( $parameters )
    {
        
        $dragonpay = new Dragonpay();
        $dragonpay->setParameters($parameters)
                  ->withProcid(Processor::CREDIT_CARD);
        $this->assertArrayHasKey('procid', $dragonpay->parameters->get());
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     * @expectedException Crazymeeks\Foundation\Exceptions\InvalidProcessIdException
     */
    public function it_should_throw_exception_if_process_id_is_not_in_the_list( $parameters )
    {
        $dragonpay = new Dragonpay();
        $dragonpay->setParameters($parameters)
                  ->withProcid('dfd');
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::postback()
     */
    public function it_should_handle_postback_with_closure_as_parameter($parameters)
    {
        $_POST = $parameters;

        $dragonpay = new Dragonpay();

        $dragonpay->handlePostback(function($data){
            $this->assertArrayHasKey('txnid', $data);
            $this->assertArrayHasKey('refno', $data);
            $this->assertArrayHasKey('status', $data);
            $this->assertArrayHasKey('message', $data);
            $this->assertArrayHasKey('digest', $data);
            $this->assertArrayHasKey('description', $data);
        });
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::postback()
     */
    public function it_should_handle_postback_where_parameter_class_implements_postback_handler_interface($parameters)
    {
        $_POST = $parameters;

        $dragonpay = new Dragonpay();
        $my_post_backhandler_class = new \Tests\Classes\PostbackHandler();
        $response = $dragonpay->handlePostback($my_post_backhandler_class);
        $this->assertArrayHasKey('txnid', $response);
        $this->assertArrayHasKey('refno', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('digest', $response);
        $this->assertArrayHasKey('description', $response);
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::getAllPaymentChannels()
     */
    public function it_should_get_all_available_payment_channels($response)
    {
        $dragonpay = new Dragonpay();
        
        $parameters['merchantId'] = getenv('MERCHANT_ID') ? getenv('MERCHANT_ID') : 'MERCHANT_ID';
        $parameters['password'] = getenv('MERCHANT_KEY') ? getenv('MERCHANT_KEY') : 'MERCHANT_KEY';
        $parameters['amount'] = Dragonpay::ALL_PROCESSORS; // this is optional
        $soap_adapter = \Mockery::mock(SoapClientAdapter::class);
        $soap_adapter->shouldReceive('GetAvailableProcessors')
                     ->with($parameters)
                     ->andReturn($response);

        $processors = $dragonpay->getPaymentChannels($parameters, $soap_adapter);

        $this->assertObjectHasAttribute('procId', $processors[0]);
        $this->assertObjectHasAttribute('shortName', $processors[0]);
        $this->assertObjectHasAttribute('longName', $processors[0]);
        $this->assertObjectHasAttribute('logo', $processors[0]);
        $this->assertObjectHasAttribute('currencies', $processors[0]);
        $this->assertObjectHasAttribute('url', $processors[0]);
        $this->assertObjectHasAttribute('realTime', $processors[0]);
        $this->assertObjectHasAttribute('pwd', $processors[0]);
        $this->assertObjectHasAttribute('defaultBillerId', $processors[0]);
        $this->assertObjectHasAttribute('hasTxnPwd', $processors[0]);
        $this->assertObjectHasAttribute('defaultBillerId', $processors[0]);
        $this->assertObjectHasAttribute('hasManualEnrollment', $processors[0]);
        $this->assertObjectHasAttribute('type', $processors[0]);
        $this->assertObjectHasAttribute('status', $processors[0]);
        $this->assertObjectHasAttribute('remarks', $processors[0]);
        $this->assertObjectHasAttribute('dayOfWeek', $processors[0]);
        $this->assertObjectHasAttribute('startTime', $processors[0]);
        $this->assertObjectHasAttribute('endTime', $processors[0]);
        $this->assertObjectHasAttribute('minAmount', $processors[0]);
        $this->assertObjectHasAttribute('maxAmount', $processors[0]);
        $this->assertObjectHasAttribute('mustRedirect', $processors[0]);
        $this->assertObjectHasAttribute('surcharge', $processors[0]);
        $this->assertObjectHasAttribute('hasAltRefNo', $processors[0]);
        $this->assertObjectHasAttribute('cost', $processors[0]);
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::getAllPaymentChannels()
     */
    public function it_should_check_if_payment_channel_is_available_on_days($response)
    {
        $dragonpay = new Dragonpay();
        
        $parameters['merchantId'] = getenv('MERCHANT_ID') ? getenv('MERCHANT_ID') : 'MERCHANT_ID';
        $parameters['password'] = getenv('MERCHANT_KEY') ? getenv('MERCHANT_KEY') : 'MERCHANT_KEY';
        $parameters['amount'] = Dragonpay::ALL_PROCESSORS; // this is optional
        $soap_adapter = \Mockery::mock(SoapClientAdapter::class);
        $soap_adapter->shouldReceive('GetAvailableProcessors')
                     ->with($parameters)
                     ->andReturn($response);

        $processors = $dragonpay->getPaymentChannels($parameters, $soap_adapter);

        $available_everyday = $dragonpay->channels->everyDay($processors[0]->dayOfWeek);
        $available_weekdays = $dragonpay->channels->weekDays('0XXXXX0');
        $available_weekends = $dragonpay->channels->weekEnds('X00000X');
        $available_sunday_to_friday = $dragonpay->channels->sundayToFriday('XXXXXX0');
        $available_monday_to_saturday = $dragonpay->channels->mondayToSaturday('0XXXXXX');
        
        $this->assertTrue($available_everyday);
        $this->assertTrue($available_weekdays);
        $this->assertTrue($available_weekends);
        $this->assertTrue($available_sunday_to_friday);
        $this->assertTrue($available_monday_to_saturday);
    }

    /**
     * @test
     * @dataProvider Tests\DataProviders\DragonpayDataProvider::request_parameters()
     */
    public function it_redirect_to_specific_payment_using_procid($parameters)
    {
        $dragonpay = new Dragonpay();

        $parameters['merchantid'] = getenv('MERCHANT_ID');
        $parameters['password'] = getenv('MERCHANT_KEY');
        $parameters['txnid'] = 'TXNID-' . rand();
        
        $token = $dragonpay->getToken(
            $parameters
        );
        
        $url = $dragonpay->withProcid(Processor::BAYADCENTER)->away( true );

        $url_query = parse_url($url, PHP_URL_QUERY);

        $this->assertStringStartsWith('tokenid', $url_query);
        
    }
}