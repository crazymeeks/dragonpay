<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Foundation\PaymentGateway;

use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\PaymentGateway\DragonPay\Token;
use Crazymeeks\Foundation\Exceptions\InvalidArrayParameterException;

class Parameters
{

    const REQUEST_PARAM_MERCHANT_ID = 'merchantid';
	const REQUEST_PARAM_TXNID       = 'txnid';
	const REQUEST_PARAM_AMOUNT      = 'amount';
	const REQUEST_PARAM_CCY         = 'ccy';
	const REQUEST_PARAM_DESCRIPTION = 'description';
	const REQUEST_PARAM_EMAIL       = 'email';
	const REQUEST_PARAM_PASSWORD    = 'password';
	const REQUEST_PARAM_DIGEST      = 'digest';
	const REQUEST_PARAM_PARAM1      = 'param1';
    const REQUEST_PARAM_PARAM2      = 'param2';
    const PAYMENT_MODE              = 'mode'; # Use for payment Filtering
    

    # When using SOAP/XML Web service Model
    # These variables will be use
    const REQUEST_TOKEN_PARAM_MERCHANT_ID = 'merchantId';
	const REQUEST_TOKEN_PARAM_PASSWORD = 'password';
	const REQUEST_TOKEN_PARAM_MERCHANT_TXNID = 'merchantTxnId';
	const REQUEST_TOKEN_PARAM_AMOUNT = 'amount';
	const REQUEST_TOKEN_PARAM_CCY = 'ccy';
	const REQUEST_TOKEN_PARAM_DESCRIPTION = 'description';
	const REQUEST_TOKEN_PARAM_EMAIL = 'email';
	const REQUEST_TOKEN_PARAM_PARAM1 = 'param1';
    const REQUEST_TOKEN_PARAM_PARAM2 = 'param2';
    


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


	protected $parameters = [];
	
	protected $billing_info_parameters = [];

	/**
	 * @var @param Crazymeeks\Foundation\PaymentGateway\Dragonpay
	 */
	private $dragonpay;

	/**
	 * Constructor
	 * 
	 * @param Crazymeeks\Foundation\PaymentGateway\Dragonpay
	 */
	public function __construct(Dragonpay $dragonpay)
	{
		$this->dragonpay = $dragonpay;
	}

    /**
	 * Set Request Parameters
	 *
	 * @param array $parameters
	 * 
	 * @return array
	 */
	public function setRequestParameters(array $parameters)
	{
		$parameters = array_merge($this->parameters, array_filter($parameters));

		if ( ! array_key_exists('merchantid', $parameters)
			&& ! array_key_exists('txnid', $parameters)
			&& ! array_key_exists('amount', $parameters)
			&& ! array_key_exists('ccy', $parameters)
			&& ! array_key_exists('description', $parameters)
			&& ! array_key_exists('email', $parameters) ) {
				throw InvalidArrayParameterException::invalid_array_key();
		}

		$_parameters[Parameters::REQUEST_PARAM_MERCHANT_ID] = $parameters[Parameters::REQUEST_PARAM_MERCHANT_ID];
		$_parameters[Parameters::REQUEST_PARAM_TXNID] = $parameters[Parameters::REQUEST_PARAM_TXNID];
		$_parameters[Parameters::REQUEST_PARAM_AMOUNT] = number_format($parameters[Parameters::REQUEST_PARAM_AMOUNT], 2, '.', '');
		$_parameters[Parameters::REQUEST_PARAM_CCY] = $parameters[Parameters::REQUEST_PARAM_CCY];
		$_parameters[Parameters::REQUEST_PARAM_DESCRIPTION] = $parameters[Parameters::REQUEST_PARAM_DESCRIPTION];
		$_parameters[Parameters::REQUEST_PARAM_EMAIL] = $parameters[Parameters::REQUEST_PARAM_EMAIL];
		$_parameters['password'] = $parameters['password'];
		
		$_parameters = array_filter($_parameters);
		
		$_parameters['digest'] = $this->createDigest($_parameters);
		
		unset($parameters['password'], $_parameters['password']);
		$_parameters[Parameters::REQUEST_PARAM_PARAM1] = isset($parameters[Parameters::REQUEST_PARAM_PARAM1]) ? $parameters[Parameters::REQUEST_PARAM_PARAM1] : '';
		$_parameters[Parameters::REQUEST_PARAM_PARAM2] = isset($parameters[Parameters::REQUEST_PARAM_PARAM2]) ? $parameters[Parameters::REQUEST_PARAM_PARAM2] : '';
		
		return $this->parameters = array_filter( $_parameters );
	}

	private function createDigest(array $parameters)
	{	
		$digest = sha1(implode(':', $parameters));

		return $digest;

	}

    /**
	 * Set|Prepare parameters for request token
	 * 
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function prepareRequestTokenParameters(array $parameters)
	{
		$parameters = array_merge($this->parameters, $parameters);
		
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_MERCHANT_ID] = $parameters[Parameters::REQUEST_PARAM_MERCHANT_ID];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_PASSWORD] = $parameters[Parameters::REQUEST_PARAM_PASSWORD];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_MERCHANT_TXNID] = $parameters[Parameters::REQUEST_PARAM_TXNID];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_AMOUNT] = number_format($parameters[Parameters::REQUEST_PARAM_AMOUNT], 2, '.', '');
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_CCY] = $parameters[Parameters::REQUEST_PARAM_CCY];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_DESCRIPTION] = $parameters[Parameters::REQUEST_PARAM_DESCRIPTION];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_EMAIL] = $parameters[Parameters::REQUEST_PARAM_EMAIL];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_PARAM1] = isset($parameters[Parameters::REQUEST_PARAM_PARAM1]) ? $parameters[Parameters::REQUEST_PARAM_PARAM1] : '';
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_PARAM2] = isset($parameters[Parameters::REQUEST_PARAM_PARAM2]) ? $parameters[Parameters::REQUEST_PARAM_PARAM2] : '';

		$_parameters = array_filter($_parameters);

		return $this->parameters = $_parameters;

    }
	
	/**
	 * Set|Prepare billing info parameter. Use for Credit Card transaction
	 * 
	 * @param array $parameters
	 *
	 * @return array
	 * 
	 * @throws InvalidArrayParameterException
	 */
	public function setBillingInfoParameters(array $parameters)
	{

		if (!array_key_exists('merchantid', $parameters)
			&& !array_key_exists('txnid', $parameters)
			&& !array_key_exists('firstName', $parameters)
			&& !array_key_exists('lastName', $parameters)
			&& !array_key_exists('address1', $parameters)
			&& !array_key_exists('address2', $parameters)
			&& !array_key_exists('city', $parameters)
			&& !array_key_exists('state', $parameters)
			&& !array_key_exists('country', $parameters)
			&& !array_key_exists('telNo', $parameters)
			&& !array_key_exists('email', $parameters)) {

				throw InvalidArrayParameterException::send_billing_info_parameters();
		}

		$_parameters[self::BILLINGINFO_MERCHANT_ID]    = $parameters[self::REQUEST_PARAM_MERCHANT_ID];
		$_parameters[self::BILLINGINFO_MERCHANT_TXNID] = $parameters[self::REQUEST_PARAM_TXNID];
		$_parameters[self::BILLINGINFO_FIRSTNAME]      = $parameters[self::BILLINGINFO_FIRSTNAME];
		$_parameters[self::BILLINGINFO_LASTNAME]       = $parameters[self::BILLINGINFO_LASTNAME];
		$_parameters[self::BILLINGINFO_ADDRESS1]       = $parameters[self::BILLINGINFO_ADDRESS1];
		$_parameters[self::BILLINGINFO_ADDRESS2]       = $parameters[self::BILLINGINFO_ADDRESS2];
		$_parameters[self::BILLINGINFO_CITY]           = $parameters[self::BILLINGINFO_CITY];
		$_parameters[self::BILLINGINFO_STATE]          = $parameters[self::BILLINGINFO_STATE];
		$_parameters[self::BILLINGINFO_COUNTRY]        = $parameters[self::BILLINGINFO_COUNTRY];
		$_parameters[self::BILLINGINFO_ZIPCODE]        = isset($parameters[self::BILLINGINFO_ZIPCODE]) ? $parameters[self::BILLINGINFO_ZIPCODE] : '';
		$_parameters[self::BILLINGINFO_TELNO]          = $parameters[self::BILLINGINFO_TELNO];
		$_parameters[self::BILLINGINFO_EMAIL]          = $parameters[self::BILLINGINFO_EMAIL];

		return $this->billing_info_parameters = array_filter($_parameters);
	}

	/**
	 * Add|Push new parameters in the parameter array
	 *
	 * @param array $parameters
	 * 
	 * @return void
	 */
	public function add(array $parameters)
	{
		$this->parameters =  array_merge($this->parameters, (array) $parameters);
	}

    /**
     * Get the parameters passed to DP
     *
     * @return array
     */
    public function get()
    {
		$parameters = $this->parameters;

		/**
		 * Set payment mode if specified
		 */
		if (!is_null($this->dragonpay->getPaymentChannel())) {
			$parameters['mode'] = $this->dragonpay->getPaymentChannel();
		}

		if ($this->dragonpay->token instanceof Token) {
			
			if (isset($parameters['mode'])) {
				$parameters = ['tokenid' => $this->dragonpay->token->getToken(),'mode' => $parameters['mode']];
			} elseif(isset($parameters['procid'])) {
				$parameters = ['tokenid' => $this->dragonpay->token->getToken(), 'procid' => $parameters['procid']];
			} else {
				$parameters = ['tokenid' => $this->dragonpay->token->getToken()];
			}
		}
        return $parameters;
	}
	
	/**
	 * Get billing info parameters
	 *
	 * @return array
	 */
	public function billing_info()
	{
		return $this->billing_info_parameters;
	}


    /**
	 * The query params
	 *
	 * @return string
	 */
	public function query()
	{
		return http_build_query($this->get(), '', '&');
	}
}