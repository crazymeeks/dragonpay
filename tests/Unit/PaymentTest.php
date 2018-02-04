<?php

namespace Tests\Unit;

use Tests\TestCase;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Dragonpay;
class PaymentTest extends TestCase
{

	/**
	 * 
	 * @test
	 */
	public function it_can_use_web_service_to_get_token()
	{


		$token_parameters = $this->webServiceGetTokenRequestParameters();

		$dragonpay = new Dragonpay;

		/**
		 |-------------------------------------
		 | [Optional] Specifying Payment mode
		 |-------------------------------------
		 */
		//$dragonpay->setPaymentMode('sandbox');

		 /**
		 |---------------------------------------
		 | [Optional] Filtering Payment Channel
		 |---------------------------------------
		 */
		//$dragonpay->filterPaymentChannel(2);

		 /**
		 |---------------------------------------------------
		 | [Optional] Specifying WebService Url for Sandbox
		 |---------------------------------------------------
		 */
		//$dragonpay->setSandboxWebServiceUrl('http://test.dragonpay.ph/DragonPayWebService/MerchantService.asmx');

		 /**
		 |------------------------------------------------------
		 | [Optional] Specifying WebService Url for Production
		 |------------------------------------------------------
		 */
		//$dragonpay->setProductionWebServiceUrl('http://test.dragonpay.ph/DragonPayWebService/MerchantService.asmx');

		$token = $dragonpay->requestToken($token_parameters);
		
		/**
		 |---------------------------------------------------
		 | [Required] Redirect to dragonpay
		 |---------------------------------------------------
		 */
		/*if ($token instanceof Token) {
			$dragonpay->useToken($token)->away();exit;
		}*/

		$this->assertInstanceOf(Token::class, $token);

	}

	/**
	 * Get the request parameters required
	 * by DragonPay's web service model
	 * 
	 * @return array
	 */
	private function webServiceGetTokenRequestParameters()
	{
		return [
			'merchantId' => 'SOMEMERCHANTID',
			'password' => 'MERCHANTPASSWORD',
			'merchantTxnId' => 'TRANS-02-' , rand(0,100),
			'amount' => 10,
			'ccy' => 'PHP',
			'description' => 'Testing using web service',
		];
	}
}