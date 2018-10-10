<?php

namespace Tests\Unit;

use Tests\TestCase;

use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\Exceptions\PaymentException;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token;

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
            'email' => 'test@example.com', # Varchar(40) email address of customer
            'digest' => sha1('MERCHANTID:TXNID:1:PHP:Test:test@example.com:PASSWORD'), # This will be use to generate a digest key
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
            'email' => 'test@example.com', # Varchar(40) email address of customer
            'digest' => sha1('MERCHANTID:TXNID:1:PHP:Test:test@example.com:PASSWORD'), # This will be use to generate a digest key
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
        
    }

}