<?php

/**
 * Dragonpay core library. 
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * @author Jefferson Claud
 */

namespace Crazymeeks\Foundation\PaymentGateway;

use SoapClient;
use Crazymeeks\Foundation\PaymentGateway\Digest;
use Crazymeeks\Foundation\PaymentGateway\RequestBag;
use Crazymeeks\Foundation\PaymentGateway\Parameters;
use Crazymeeks\Foundation\Exceptions\PaymentException;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token;
use Crazymeeks\Contracts\Foundation\PaymentGateway\PaymentGatewayInterface;

class Dragonpay implements PaymentGatewayInterface
{

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
    

    const INVALID_PAYMENT_GATEWAY_ID = 101;
    const INCORRECT_SECRET_KEY = 102;
    const INVALID_REFERENCE_NUMBER = 103;
    const UNAUTHORIZED_ACCESS = 104;
    const INVALID_TOKEN = 105;
    const CURRENCY_NOT_SUPPORTED = 106;
    const TRANSACTION_CANCELLED = 107;
    const INSUFFICIENT_FUNDS = 108;
    const TRANSACTION_LIMIT_EXCEEDED = 109;
    const ERROR_IN_OPERATION = 110;
    const INVALID_PARAMETERS = 111;
    const INVALID_MERCHANT_ID = 201;
    const INVALID_MERCHANT_PASSWORD = 202;

    
    /**
     * PS Error Codes
     *
     * @var array
     */
    private $error_codes = [
        self::INVALID_PAYMENT_GATEWAY_ID => 'Invalid payment gateway id',
        self::INCORRECT_SECRET_KEY => 'Incorrect secret key',
        self::INVALID_REFERENCE_NUMBER => 'Invalid reference number',
        self::UNAUTHORIZED_ACCESS => 'Unauthorized access',
        self::INVALID_TOKEN => 'Invalid token',
        self::CURRENCY_NOT_SUPPORTED => 'Currency not supported',
        self::TRANSACTION_CANCELLED => 'Transaction cancelled',
        self::INSUFFICIENT_FUNDS => 'Insufficient funds',
        self::TRANSACTION_LIMIT_EXCEEDED => 'Transaction limit exceeded',
        self::ERROR_IN_OPERATION => 'Error in operation',
        self::INVALID_PARAMETERS => 'Invalid parameters',
        self::INVALID_MERCHANT_ID => 'Invalid merchant id',
        self::INVALID_MERCHANT_PASSWORD => 'Invalid merchant password',
    ];


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
     * Payment Channels
     * 
     * @var int
     */
    const ONLINE_BANK  = 1;
    const OTC_BANK     = 2;
    const OTC_NON_BANK = 4;
    const PAYPAL       = 32;
    const CREDIT_CARD  = 64;
    const GCASH        = 128;
    const INTL_OTC     = 256;

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

	/**
	 * Dragon pay sandbox web service url
	 *
	 * For greater security, developer can implement the API using the XML Web
	 * Services Model. Under this model, the parameters are not passed
	 * through browser redirects which are visile to end-users. Instead,
	 * parameters are exchanged directly between the Merchant site and
	 * PS servers through SOAP calls.
	 * 
	 * @var string
	 */
	protected $sandboxWebServiceUrl = 'http://test.dragonpay.ph/DragonPayWebService/MerchantService.asmx';

	protected $productionWebServiceUrl = 'https://secure.dragonpay.ph/DragonPayWebService/MerchantService.asmx';

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
	 * @var Crazymeeks\Foundation\PaymentGateway\RequestBag
	 */
    public $request;
    
    /**
     * The parameters
     * 
     * @var Crazymeeks\Foundation\PaymentGateway\Parameters
     *
     */
    public $parameters;

    /**
	 * The request token we pass after redirected
	 * to dragonpay PS
	 * 
	 * @var string
	 */
    public $token = null;

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
     * The message when error occured
     *
     * @var string
     */
    private $debug_message;
    

    public function __construct( $sandbox = true )
    {

        $this->is_sandbox = $sandbox;

        $this->request = new RequestBag();

        $this->parameters = new Parameters( $this );
    }


    /**
     * Set Request Parameters
     * Alias of setRequestParameters
     * 
     * @param array $parameters
     *
     * @return void
     */
    public function setParameters( array $parameters )
    {
        $this->setRequestParameters( $parameters );
    }
    
    /**
     * Set Request parameters
     *
     * @param array $parameters
     * 
     * @return void
     */
    public function setRequestParameters( array $parameters )
    {
        $this->parameters->setRequestParameters($parameters);
    }

    /**
     * When using SOAP/XML Web Service Model
     * 
     * @param array $parameters
     *
     * @return Crazymeeks\Foundation\Token
     * 
     * @throws Exceptions
     */
    public function getToken( array $parameters )
    {

        $parameters = $this->parameters->prepareRequestTokenParameters( $parameters );
        
        $webservice_url = $this->getWebserviceUrl();
        
		$wsdl = new SoapClient($webservice_url . '?wsdl', array(
			'location' => $webservice_url,
			'trace'    => 1,
		));

		$token = $wsdl->GetTxnToken( $parameters );
        $code = $token->GetTxnTokenResult;
        
		if ( array_key_exists($code, $this->error_codes) ) {
            $this->throwException($token->GetTxnTokenResult);
        }

        $this->token = new Token($token->GetTxnTokenResult);

		return $this->token;
    }

    private function throwException( $code )
    {  
        $this->setDebugMessage( $this->error_codes[$code] );
        throw new PaymentException( $this->seeError() );
    }

    /**
     * Filter Payment Channel
     * 
     * @param int $channel
     *
     * @return $this
     */
    public function filterPaymentChannel( $channel )
    {
        $this->payment_channel = $channel;

        return $this;
    }

    /**
     * Get the payment channel
     *
     * @return string|null
     */
    public function getPaymentChannel()
    {
        return $this->payment_channel;
    }

    /**
     * Set the debug message for debugging
     *
     * @param string $message
     * 
     * @return void
     */
    public function setDebugMessage( $message )
    {
        $this->debug_message = $message;
    }

    /**
     * See error happens
     *
     * @return string
     */
    public function seeError()
    {
        return $this->debug_message;
    }


    /**
     * Get PS url
     *
     * @return string
     */
    public function getWebserviceUrl()
    {
        return $this->getUrl();
    }

    /**
     * Return PS url
     *
     * @return string
     */
    private function getUrl()
    {
        return $this->is_sandbox ? $this->sandboxWebServiceUrl : $this->productionWebServiceUrl;
    }

    /**
     * Redirect to Dragonpay Payment page
     * 
     * @param bool $test   Weather we are running thru unit test
     *
     * @return void
     */
    public function away( $test = false )
    {
        if ( $test ) {

            return $this->getUrl() . '?' . $this->parameters->query();exit;
            
        }

        header("Location: " . $this->getUrl() . '?' . $this->parameters->query(), 302);exit();

    }

}