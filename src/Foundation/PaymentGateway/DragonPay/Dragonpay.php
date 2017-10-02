<?php

/**
 * Dragonpay core library. 
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * @author Jefferson Claud
 */

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay;

use SoapClient;
use Crazymeeks\Foundation\PaymentGateway\Digest;
use Crazymeeks\Foundation\PaymentGateway\RequestBag;
use Crazymeeks\Contracts\Foundation\PaymentGateway\PaymentGatewayInterface;
class Dragonpay implements PaymentGatewayInterface
{

	const REQUEST_PARAM_MERCHANT_ID = 'merchantid';
	const REQUEST_PARAM_TXNID       = 'txnid';
	const REQUEST_PARAM_AMOUNT      = 'amount';
	const REQUEST_PARAM_CCY         = 'ccy';
	const REQUEST_PARAM_DESCRIPTION = 'description';
	const REQUEST_PARAM_EMAIL       = 'email';
	const REQUEST_PARAM_DIGEST      = 'digest';
	const REQUEST_PARAM_PARAM1      = 'param1';
	const REQUEST_PARAM_PARAM2      = 'param2';

	// we are unsetting this key later for security
	const REQUEST_PARAM_SECRET_KEY = 'key';


	/**
	 * Dragonpay credit card required params
	 */
	const BILLINGINFO_MERCHANT_ID    = 'merchantId';
	const BILLINGINFO_MERCHANT_TXNID = 'merchantTxnId';
	const BILLINGINFO_FIRSTNAME      = 'firstName';
	const BILLINGINFO_LASTNAME       = 'lastName';
	const BILLINGINFO_ADDRESS1       = 'address1';
	const BILLINGINFO_ADDRESS2       = 'address2';
	const BILLINGINFO_CITY           = 'city';
	const BILLINGINFO_STATE          = 'state';
	const BILLINGINFO_COUNTRY        = 'country';
	const BILLINGINFO_ZIPCODE        = 'zipCode';
	const BILLINGINFO_TELNO          = 'telNo';
	const BILLINGINFO_EMAIL          = 'email';


	/**
	 * DragonPay sandbox url
	 *
	 * @var string
	 */
	const SANDBOX_URL = 'http://test.dragonpay.ph/Pay.aspx?';

	/**
	 * DragonPay production url
	 *
	 * @var string
	 */
	const PRODUCTION_URL = 'https://gw.dragonpay.ph/Pay.aspx?';

	/**
	 * Dragon pay send billing info url. As of the development of this
	 * library, unfortunately dragonpay has no sandbox url for the
	 * SendBillingInfo() or credit card
	 *
	 * If you wish to change the url of SendBillingInfo(), you can call
	 * the setBillingInfoUrl($full_url) method
	 *
	 */
	protected $sendbillinginfo_url = 'https://gw.dragonpay.ph/DragonPayWebService/MerchantService.asmx';

	static $required_request_parameters = array(
		self::REQUEST_PARAM_MERCHANT_ID,
		self::REQUEST_PARAM_TXNID,
		self::REQUEST_PARAM_AMOUNT,
		self::REQUEST_PARAM_CCY,
		self::REQUEST_PARAM_DESCRIPTION,
		self::REQUEST_PARAM_EMAIL,		
		self::REQUEST_PARAM_SECRET_KEY,
	);

	/**
	 * Dragonpay credit card required params
	 */
	static $required_sendbillinginfo_parameters = array(
		self::BILLINGINFO_MERCHANT_ID,
		self::BILLINGINFO_MERCHANT_TXNID,
		self::BILLINGINFO_FIRSTNAME,
		self::BILLINGINFO_LASTNAME,
		self::BILLINGINFO_ADDRESS1,
		self::BILLINGINFO_ADDRESS2,
		self::BILLINGINFO_CITY,
		self::BILLINGINFO_STATE,
		self::BILLINGINFO_COUNTRY,
		self::BILLINGINFO_ZIPCODE,
		self::BILLINGINFO_TELNO,
		self::BILLINGINFO_EMAIL,
	);

	static $optional_request_parameters = array(
		self::REQUEST_PARAM_PARAM1,
		self::REQUEST_PARAM_PARAM2,
	);


	/**
	 * Our digest type
	 * 
	 * For Dragon Pay, it is sha1
	 *
	 * @var string
	 */
	protected $digest_type = 'sha1';

	/**
	 * DragonPay digest code
	 *
	 * @var string
	 */
	protected $digest;


	/**
	 * Request Body parameters ($_POST)
	 *
	 * @var \Crazymeeks\Core\PaymentGateway\RequestBag
	 */
	protected $requestbag;

	/**
	 * DragonPay gateway url. Sandbox mode by default
	 *
	 * @see $sandbox_url or $production_url
	 *
	 * @var string
	 */
	protected $gateway_url = self::SANDBOX_URL;

	/**
	 * Flag if sandbox mode
	 *
	 * @var bool
	 */
	protected $is_sandbox = true;

	/**
	 * DragonPay secret key
	 */
	protected $secret_key;

	/**
	 * Dragonpay's payment channel. e.g if $payment_channel = 64 user will pay using credit card
	 *
	 * @see Dragonpay's documentation for Payment Channels
	 * @link https://www.dragonpay.ph/wp-content/uploads/2014/05/Dragonpay-PS-API
	 */
	protected $payment_channel = null;

	/**
	 * Constructor
	 *
	 * @param array $request_params        Dragonpay Request parameters($_POST)
	 * @param string $url                  The Dragonpay payment url
	 * @param bool $mode                   true = sandbox, false = production
	 * 
	 */
	public function __construct(array $request_params = array(), $url = null, $mode = true)
	{
		$this->init($request_params, $url, $mode);
	}

	/**
	 * Init
	 *
	 * @param array $request_params        Dragonpay Request parameters($_POST)
	 * @param string $url                  The Dragonpay payment url
	 * @param bool $mode                   true = sandbox, false = production
	 *
	 * @return void
	 * 
	 */
	private function init($request_params, $url, $mode)
	{
		if(count($request_params) > 0){

			self::checkParameters($request_params);

			// prepare the digest code
			$request_params = $this->setDigest($request_params);

			$request_params['digest'] = $this->getDigest();
			
			$request_params = $this->sanitizeParameters($request_params);
			
			$this->requestbag = new RequestBag($request_params);

			// sandbox?
			if(!is_null($url)){
				$this->setGatewayUrl((rtrim($url, '?')) . '?');
			}elseif($mode){
				$this->setPaymentMode('sandbox');
				$this->setGatewayUrl(self::SANDBOX_URL);
			}else{
				$this->setPaymentMode('production');
				$this->setGatewayUrl(self::PRODUCTION_URL);
			}
		}
	}

	/**
	 * Send Billing info. If you wish to pay using credit card,
	 * Dragonpay requires fraud checking checking by sending
	 * customer's billing address
	 *
	 * @param array $parameters     The array of parameters
	 * @see Dragonpay's docs of SendBillingInfo usage
	 * @link https://www.dragonpay.ph/wp-content/uploads/2014/05/Dragonpay-PS-API
	 *
	 * @return bool    true if success, otherwise false
	 *
	 * @api
	 */
	public function sendBillingInfo(array $parameters)
	{
	
		foreach($parameters as $key => $param){
			$i = (string) $key;
			if(!in_array($i, self::$required_sendbillinginfo_parameters)){
				throw new \LogicException('Invalid Dragonpay SendBillingInfo() Parameters. Please check your parameters');
			}
			// make sure we have no empty values
			if(empty($param) || $param == ''){
				throw new \LogicException('SendBillingInfo() should not have an empty value. Please check your parameters');
			}
		}
		// Set payment channel to 64(credit card)
		$this->setPaymentChannel(64);

		$url = $this->getBillingInfoUrl();
		
		$wsdl = new SoapClient($url . '?wsdl',  array(
		   'location' => $url,
		   'trace' => 1,
		));

		$result = $wsdl->SendBillingInfo($parameters)->SendBillingInfoResult;

		return $result == 0 ? true : false;
	}

	/**
	 * Setter: SendBillingInfo. This is for fraud checking if you wish
	 * user to pay via credit card
	 *
	 * @param string $api_url           The full url of Dragonpay's api_url. This is not the same as sandbox/production url
	 *
	 * @return bool
	 *
	 * @throws \LogicException
	 */
	public function setBillingInfoUrl($api_url)
	{
		$this->sendbillinginfo_url = $api_url;
	}

	/**
	 * Getter: SendBillingInfoUrl
	 *
	 * @return string
	 */
	public function getBillingInfoUrl()
	{
		return $this->sendbillinginfo_url;
	}

	/**
	 * Setter: Payment Channel. e.g Online banking, OTC, Paypal, Credit Card
	 *
	 * @param int $channel
	 * @see Dragonpay's payment channel
	 * @link https://www.dragonpay.ph/wp-content/uploads/2014/05/Dragonpay-PS-API
	 *
	 * @return void
	 * @throws \LogicException
	 *
	 * @api
	 */
	public function setPaymentChannel($channel)
	{
		if(!is_numeric($channel)){
			throw new \LogicException('Invalid parameter. integer expected but ' . gettype($channel) . ' given');
		}
		$this->payment_channel = $channel;
	}

	/**
	 * Getter: Payment Channel
	 *
	 * @return mixed
	 */
	public function getPaymentChannel()
	{
		return $this->payment_channel;
	}

	/**
	 * Setter: Payment Channel. e.g Online banking, OTC, Paypal, Credit Card
	 *
	 * @param int $channel
	 * @see Dragonpay's payment channel
	 * @link https://www.dragonpay.ph/wp-content/uploads/2014/05/Dragonpay-PS-API
	 *
	 * @return void
	 * @throws \LogicException
	 *
	 * @api
	 */
	public function filterPaymentChannel($channel)
	{
		$this->setPaymentChannel($channel);
	}	

	/**
	 * Setter: DragonPay required digest parameter
	 *
	 * @param array $request_params              Dragonpay request param. Please @see https://www.dragonpay.ph/wp-content/uploads/2014/05/Dragonpay-PS-API
	 *
	 * @return array
	 */
	public function setDigest(&$request_params)
	{
		$request_params['description'] = urlencode($request_params['description']);
		$request_params['amount'] = number_format($request_params['amount'], 2, '.', '');

		$this->digest = implode(':', $request_params);
		unset($request_params['key']);
		return $request_params;
	}

	/**
	 * Getter: DragonPay digest code. Not encrypted
	 *
	 * @return string
	 */
	public function getDigest(){
		return $this->digest;
	}

	/**
	 * Set Dragonpay expected parameters
	 *
	 * @param array $params
	 * @param string $url                  The Dragonpay payment url
	 * @param bool $mode                   true = sandbox, false = production
	 *
	 * @return $this
	 * 
	 * @api
	 */
	public function setRequestParameters(array $params, $url = null, $mode = true)
	{	
		return new static($params);
	}

	protected function sanitizeParameters(&$request_params)
	{
		
		foreach($request_params as $key => $value){
			
			if($key != 'digest'){
				if(($key == 'email' || $key == 'ccy')){
					$request_params[$key] = $value;
				}elseif(urlencode(urldecode($value)) === $value){
					$request_params[$key] = urldecode(urlencode(($value)));
				}
			}
			
		}
		return $request_params;
	}

	/**
	 * Getter: DragonPay required Request Parameters
	 *
	 * @return string      http_build_query format
	 */
	public function getRequestParameters()
	{	

		$digest_type = $this->getDigestType();
		$digest = new Digest($digest_type, $this->requestbag->getRequestParams()['digest']);
		$query_params = array_merge($this->requestbag->getRequestParams(), ['digest' => (string) $digest]);

		// check if Payment Channel filtering is enabled
		if(!is_null($this->getPaymentChannel())){
			$query_params = array_merge($query_params, ['mode' => (int) $this->getPaymentChannel()]);
		}

		return http_build_query($query_params, '', '&');
	}

	/**
	 * Create request parameter
	 *
	 * @param array $params          The array of request parameters
	 *
	 * @return \Crazymeeks\Foundation\PaymentGateway\RequestBag
	 */
	private static function createRequestParameters(array $params)
	{

		self::checkParameters($params);

		return new static($params);
	}

	/**
	 * Make sure Dragonpay's expected Request Parameters are met.
	 *
	 * @param array $params     The array of parameters.
	 * @see Dragonpay Request parameters
	 * @link https://www.dragonpay.ph/wp-content/uploads/2014/05/Dragonpay-PS-API
	 *
	 * @return bool
	 */
	private static function checkParameters($params)
	{
		foreach($params as $key => $param){
			$i = (string) $key;
			if(!in_array($i, self::$required_request_parameters)){
				throw new \LogicException('Invalid Dragonpay Request Parameters. Please check your parameters');
			}
		}

		// some sort of useless return
		return true;
	}

	/**
	 * Setter: Payment Mode(sandbox or production)
	 *
	 * @param string $mode       Expected values: 'sandbox' | 'production'
	 *
	 * @return void
	 */
	public function setPaymentMode($mode)
	{
		$this->is_sandbox = $mode === 'production' ? false : true;
	}

	/**
	 * Getter: Get the the payment type
	 *
	 * @return bool
	 */
	public function getPaymentMode()
	{
		return $this->is_sandbox;
	}

	/**
	 * Setter: DragonPay digest type
	 *
	 * @param string $type       Default: sha1
	 *
	 * @return void
	 */
	public function setDigestType($type = 'sha1')
	{
		$this->digest_type = $type;
	}

	/**
	 * Getter: DragonPay digest type
	 *
	 * @return string
	 */
	public function getDigestType()
	{
		return $this->digest_type;
	}

	/**
	 * Setter: DragonPay gateway url
	 *
	 * @param string $url
	 *
	 * @return void
	 */
	public function setGatewayUrl($url)
	{
		$this->gateway_url = $url;
	}

	/**
	 * Getter: DragonPay gateway url
	 *
	 * @return string
	 */
	public function getGateWayUrl()
	{
		return $this->gateway_url;
	}

	/**
	 * Redirect to DragonPay gateway
	 */
	public function away()
	{
		
		header("Location: " . $this->getGateWayUrl() . $this->getRequestParameters(), 302);exit();
	}

}