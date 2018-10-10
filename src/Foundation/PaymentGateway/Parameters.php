<?php

namespace Crazymeeks\Foundation\PaymentGateway;

use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token;

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
    

    protected $parameters = [];

	/**
	 * @var @param Crazymeeks\Foundation\PaymentGateway\Dragonpay
	 */
	private $dragonpay;

	/**
	 * Constructor
	 * 
	 * @param Crazymeeks\Foundation\PaymentGateway\Dragonpay
	 */
	public function __construct( Dragonpay $dragonpay )
	{
		$this->dragonpay = $dragonpay;
	}

    /**
	 * Set Request Parameters
	 *
	 * @param array $parameters
	 * 
	 * @return static
	 */
	public function setRequestParameters( array $parameters )
	{
		$_parameters[Parameters::REQUEST_PARAM_MERCHANT_ID] = $parameters[Parameters::REQUEST_PARAM_MERCHANT_ID];
		$_parameters[Parameters::REQUEST_PARAM_TXNID] = $parameters[Parameters::REQUEST_PARAM_TXNID];
		$_parameters[Parameters::REQUEST_PARAM_AMOUNT] = number_format($parameters[Parameters::REQUEST_PARAM_AMOUNT], 2, '.', '');
		$_parameters[Parameters::REQUEST_PARAM_CCY] = $parameters[Parameters::REQUEST_PARAM_CCY];
		$_parameters[Parameters::REQUEST_PARAM_DESCRIPTION] = $parameters[Parameters::REQUEST_PARAM_DESCRIPTION];
		$_parameters[Parameters::REQUEST_PARAM_EMAIL] = $parameters[Parameters::REQUEST_PARAM_EMAIL];
		$_parameters[Parameters::REQUEST_PARAM_DIGEST] = $this->createDigest($parameters);
		$_parameters[Parameters::REQUEST_PARAM_PARAM1] = $parameters[Parameters::REQUEST_PARAM_PARAM1];
		$_parameters[Parameters::REQUEST_PARAM_PARAM2] = $parameters[Parameters::REQUEST_PARAM_PARAM2];
		
		unset($parameters['password']);
		$_parameters = array_filter( $_parameters );

		return $this->parameters = $_parameters;
	}

	private function createDigest( array $parameters )
	{
		unset($parameters['param1'], $parameters['param2']);
		
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
	public function prepareRequestTokenParameters( array $parameters )
	{
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_MERCHANT_ID] = $parameters[Parameters::REQUEST_PARAM_MERCHANT_ID];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_PASSWORD] = $parameters[Parameters::REQUEST_PARAM_PASSWORD];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_MERCHANT_TXNID] = $parameters[Parameters::REQUEST_PARAM_TXNID];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_AMOUNT] = number_format($parameters[Parameters::REQUEST_PARAM_AMOUNT], 2, '.', '');
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_CCY] = $parameters[Parameters::REQUEST_PARAM_CCY];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_DESCRIPTION] = $parameters[Parameters::REQUEST_PARAM_DESCRIPTION];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_EMAIL] = $parameters[Parameters::REQUEST_PARAM_EMAIL];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_PARAM1] = $parameters[Parameters::REQUEST_PARAM_PARAM1];
		$_parameters[Parameters::REQUEST_TOKEN_PARAM_PARAM2] = $parameters[Parameters::REQUEST_PARAM_PARAM2];

		$_parameters = array_filter( $_parameters );

		return $this->parameters = $_parameters;

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
		if ( ! is_null( $this->dragonpay->getPaymentChannel() ) ) {
			$parameters['mode'] = $this->dragonpay->getPaymentChannel();
		}

		if ( $this->dragonpay->token instanceof Token ) {
			if ( isset($parameters['mode']) ) {
				$parameters = ['tokenid' => $this->dragonpay->token->getToken(),'mode' => $parameters['mode']];
			} else {
				$parameters = ['tokenid' => $this->dragonpay->token->getToken()];
			}
		}
		
        return $parameters;
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