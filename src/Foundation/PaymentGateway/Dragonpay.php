<?php

/**
 * Dragonpay core library. 
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * @author Jefferson Claud
 */

namespace Crazymeeks\Foundation\PaymentGateway;

use Crazymeeks\Foundation\PaymentGateway\RequestBag;
use Crazymeeks\Foundation\Adapter\SoapClientAdapter;
use Crazymeeks\Foundation\PaymentGateway\Parameters;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Token;
use Crazymeeks\Foundation\PaymentGateway\Options\Processor;
use Crazymeeks\Foundation\PaymentGateway\BillingInfoVerifier;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\PaymentChannels;
use Crazymeeks\Foundation\Exceptions\InvalidPostbackInvokerException;
use Crazymeeks\Foundation\Exceptions\NoAvailablePaymentChannelsException;
use Crazymeeks\Foundation\PaymentGateway\Handler\PostbackHandlerInterface;
use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Action\ActionInterface;
use Crazymeeks\Contracts\Foundation\PaymentGateway\PaymentGatewayInterface;

class Dragonpay implements PaymentGatewayInterface
{
    
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

    const SUCCESS = 'S';
    const FAILED = 'F';
    const PENDING = 'P';
    const UKNOWN = 'U';
    const REFUND = 'R';
    const CHARGEBACK = 'K';
    const VOID = 'V';
    const AUTHORIZED = 'A';

    const STATUS = [
        self::SUCCESS => 'Success',
        self::FAILED  => 'Failure',
        self::PENDING => 'Pending',
        self::UKNOWN => 'Unknown',
        self::REFUND => 'Refund',
        self::CHARGEBACK => 'Chargeback',
        self::VOID => 'Void',
        self::AUTHORIZED => 'Authorized',
    ];

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
     * Code to get all available processors
     * when calling GetAvailableProcessors()
     * from SOAP
     */
    const ALL_PROCESSORS = -1000;


	/**
	 * DragonPay sandbox url
	 *
	 * @var string
	 */
	protected $sandbox_url = 'http://test.dragonpay.ph/Pay.aspx';

	/**
	 * DragonPay production url
	 *
	 * @var string
	 */
    protected $production_url = 'https://gw.dragonpay.ph/Pay.aspx';

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
     * SOAP web service url
     *
     * @var string
     */
    protected $soap_web_service;

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
	 * @var \Crazymeeks\Foundation\PaymentGateway\RequestBag
	 */
    public $request;
    
    /**
     * The parameters
     * 
     * @var \Crazymeeks\Foundation\PaymentGateway\Parameters
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

    /**
     * @var \Crazymeeks\Foundation\PaymentGateway\Dragonpay\PaymentChannels
     */
    public $channels;

    /**
     * @var \Ixudra\Curl\CurlService
     */
    private $curl;

    
    /**
     * Client merchant account
     *
     * @var array
     */
    private $merchant_account;

    /**
     * Constructor
     * 
     * @param array $merchant_account
     *
     */
    public function __construct(array $merchant_account, $sandbox = true)
    {

        $this->request = new RequestBag();
        $this->channels = new PaymentChannels();
        $this->parameters = new Parameters($this);
        $this->parameters->add($merchant_account);
        $this->setMerchantAccount($merchant_account);
        $this->is_sandbox = $sandbox;
    }


    /**
     * Set merchant account
     *
     * @param array $merchant_account
     * 
     * @return $this
     */
    private function setMerchantAccount(array $merchant_account)
    {
        $this->merchant_account = $merchant_account;

        return $this;
    }

    /**
     * Get merchant account
     *
     * @return array
     */
    public function getMerchantAccount()
    {
        return $this->merchant_account;
    }

    /**
     * Set Request Parameters
     * Alias of setRequestParameters
     * 
     * @param array $parameters
     *
     * @return void
     */
    public function setParameters(array $parameters)
    {
        $this->setRequestParameters($parameters);

        return $this;
    }
    
    /**
     * Set Request parameters
     *
     * @param array $parameters
     * 
     * @return void
     */
    public function setRequestParameters(array $parameters)
    {
        $this->parameters->setRequestParameters($parameters);
    }

    /**
     * Preselecting payment channels by
     * passing "procid" in the parameters
     *
     * @param string $procid     The valid procid
     * 
     * @return $this
     */
    public function withProcid($procid)
    {
        Processor::allowedProcId($procid);

        $this->parameters->add(['procid' => $procid]);

        return $this;
    }

    /**
     * When using SOAP/XML Web Service Model
     * 
     * @param array $parameters
     * @param null|Crazymeeks\Foundation\Adapter\SoapClientAdapter $soap_adapter
     * 
     * @return Crazymeeks\Foundation\Token
     * 
     * @throws Exceptions
     */
    public function getToken(array $parameters, SoapClientAdapter $soap_adapter = null)
    {

        $parameters = $this->parameters->prepareRequestTokenParameters($parameters);
        
        $webservice_url = $this->getWebserviceUrl();

        if (is_null($soap_adapter)) {
            $soap_adapter = new SoapClientAdapter();
            $soap_adapter = $soap_adapter->initialize($webservice_url);
        }

		$token = $soap_adapter->GetTxnToken($parameters);
        $code = $token->GetTxnTokenResult;
        
		if (array_key_exists($code, $this->error_codes)) {
            $this->throwException($code);
        }

        $this->token = new Token($code);

		return $this->token;
    }

    private function throwException($code)
    {  
        $this->setDebugMessage($this->error_codes[$code]);
        $exception = "Crazymeeks\Foundation\Exceptions\\" . $this->getExceptionClass( $code );
        throw new $exception( $this->seeError() );
    }

    /**
     * Get exception class based on error code
     * that was previously returned by PS
     *
     * @param int $code
     * 
     * @return string
     */
    private function getExceptionClass($code)
    {
        $exceptions = [
            self::INVALID_PAYMENT_GATEWAY_ID => 'InvalidPaymentGatewayIdException',
            self::INCORRECT_SECRET_KEY => 'IncorrectSecretKeyException',
            self::INVALID_REFERENCE_NUMBER => 'InvalidReferenceNumberException',
            self::UNAUTHORIZED_ACCESS => 'UnauthorizedAccessException',
            self::INVALID_TOKEN => 'InvalidTokenException',
            self::CURRENCY_NOT_SUPPORTED => 'CurrencyNotSupportedException',
            self::TRANSACTION_CANCELLED => 'TransactionCancelledException',
            self::INSUFFICIENT_FUNDS => 'InsufficientFundsException',
            self::TRANSACTION_LIMIT_EXCEEDED => 'TransactionLimitExceededException',
            self::ERROR_IN_OPERATION => 'ErrorInOperationException',
            self::INVALID_PARAMETERS => 'InvalidParametersException',
            self::INVALID_MERCHANT_ID => 'InvalidMerchantIdException',
            self::INVALID_MERCHANT_PASSWORD => 'InvalidMerchantPasswordException',
        ];

        return $exceptions[$code];
    }

    /**
     * Using credit card payment.
     *
     * @param array $parameters
     * @param Crazymeeks\Foundation\PaymentGateway\BillingInfoVerifier
     * 
     * @return $this
     */
    public function useCreditCard(array $parameters, BillingInfoVerifier $verifier = null, SoapClientAdapter $soap = null)
    {
        
        $this->setParameters($parameters);

        $this->parameters->setBillingInfoParameters($parameters);
        
		$this->filterPaymentChannel(Dragonpay::CREDIT_CARD);

        $url = $this->getBillingInfoUrl();
        
        if (is_null($verifier)) {
            $verifier = new BillingInfoVerifier();
        }
        if (is_null($soap)) {
            $soap = new SoapClientAdapter();
        }
        
        $verifier->setParameterObject($this->parameters);

        $verifier->send($soap, $url);
        
		return $this;
    }

    /**
     * Set PS billing info url.
     * 
     * Billing info is used when using Credit Card
     *
     * @param string $url
     * 
     * @return $this
     */
    public function setBillingInfoUrl($url)
    {
        $url = rtrim((rtrim($url, '/')), '?');

        $this->sendbillinginfo_url = $url;

        return $this;
    }

    /**
     * Get billing info url
     *
     * @return string
     */
    public function getBillingInfoUrl()
    {
        return $this->sendbillinginfo_url;
    }

    /**
     * Filter Payment Channel
     * 
     * @param int $channel
     *
     * @return $this
     */
    public function filterPaymentChannel($channel)
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
    public function setDebugMessage($message)
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
     * Set web service url for SOAP/XML model
     *
     * @param string $url
     * 
     * @return $this
     */
    public function setWebServiceUrl($url)
    {

        $this->getPaymentMode() === 'sandbox' ? $this->sandboxWebServiceUrl = $url : $this->productionWebServiceUrl = $url;

        return $this;
    }

    /**
     * Get PS url
     *
     * @return string
     */
    public function getWebserviceUrl()
    {
        return  $this->getPaymentMode() === 'sandbox' ? $this->sandboxWebServiceUrl : $this->productionWebServiceUrl;
    }

    /**
     * Return PS url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->is_sandbox ? $this->sandbox_url : $this->production_url;
    }

    /**
     * Alias of getUrl()
     *
     * @return string
     */
    public function getPaymentUrl()
    {
        return $this->getUrl();
    }

    /**
     * Set payment url
     * 
     * @param string $url
     *
     * @return $this
     */
    public function setPaymentUrl( $url)
    {
        /*$mode = strtolower($mode);
        if ( ! in_array($mode, ['sandbox', 'production']) ) {
            throw new \Exception(sprintf("Invalid mode '%s'. Please select 'sandbox' or 'production' as payment mode.", $mode));
        }

        $url = rtrim(rtrim($url, '/'), '?');
        
        if ( $mode === 'sandbox' ) {
            $this->is_sandbox = true;
            if ( $this->token instanceof Token ) {
                $this->sandboxWebServiceUrl = $url;
            } else {
                $this->sandbox_url = $url;
            }

            
        } else {
            $this->is_sandbox = false;

            if ( $this->token instanceof Token ) {
                $this->productionWebServiceUrl = $url;
            } else {
                $this->production_url = $url;
            }

        }*/

        $this->getPaymentMode() === 'sandbox' ? $this->sandbox_url = $url : $this->production_url = $url;;

        return $this;
    }

    /**
     * Get payment mode
     * 
     * @return string
     */
    public function getPaymentMode()
    {
        return $this->is_sandbox ? 'sandbox' : 'production';
    }

    /**
     * Redirect to Dragonpay Payment page
     * 
     * @param bool $test   Weather we are running thru unit test
     *
     * @return void
     */
    public function away($test = false)
    {
        if ($test) {
            return $this->getUrl() . '?' . $this->parameters->query();   
        }
        header("Location: " . $this->getUrl() . '?' . $this->parameters->query(), 302);exit();
    }

    /**
     * Postback handler
     * 
     * @param Crazymeeks\Foundation\PaymentGateway\Handler\PostbackHandlerInterface|\Closure $callback
     * 
     * @return mixed
     */
    public function handlePostback($callback)
    {

        if (isset($_POST['status'])) {
            $description = $this->getStatusDescription($_POST['status']);
            $data = $_POST;
            $data['description'] = $description;
            if ($callback instanceof \Closure) {
                return call_user_func_array($callback, [$data]);
            }

            if ($callback instanceof PostbackHandlerInterface) {
                return call_user_func_array(array($callback, 'handle'), [$data]);
            }
        }
    }

    /**
     * Create postback response
     *
     * @param string $status
     * 
     * @return string
     * 
     * @throws Crazymeeks\Foundation\Exceptions\InvalidPostbackInvokerException
     * 
     */
    private function getStatusDescription($status)
    {
        if (isset(self::STATUS[$status])) {
            return self::STATUS[$status];
        }
        throw new InvalidPostbackInvokerException();
    }

    /**
     * Get all available payment channels/processors
     * using SOAP webservice model
     *
     * @param array $parameters
     *     $parameters = [
     *         'merchantid' => 'Unique code assigned to merchant',
     *         'password'   => 'Password associated with merchantid',
     *         'amount'     => Dragonpay::ALL_PROCESSORS,
     *     ];
     * @param null|Crazymeeks\Foundation\Adapter\SoapClientAdapter $soap_adapter
     * 
     * @return mixed
     */
    public function getPaymentChannels($amount = self::ALL_PROCESSORS, SoapClientAdapter $soap_adapter = null)
    {
        $parameters = $this->getMerchantAccount();

        if (isset($parameters['merchantid'])) {
            $parameters['merchantId'] = $parameters['merchantid'];
            unset($parameters['merchantid']);
        }

        $parameters['amount'] = $amount;

        $parameters = $this->filterAvailableProcessorsParameters($parameters);

        if (is_null($soap_adapter)) {
            $soap_adapter = new SoapClientAdapter();
            $soap_adapter = $soap_adapter->initialize($this->getWebserviceUrl());
        }

        $processors = $soap_adapter->GetAvailableProcessors($parameters);

        if (property_exists($processors, 'GetAvailableProcessorsResult')) {

            return $processors->GetAvailableProcessorsResult->ProcessorInfo;

        }

        throw new NoAvailablePaymentChannelsException();
    }

    /**
     * Filter parameters required by DP's GetAvailableProcessors() method
     *
     * @param array $parameters
     * 
     * @return array
     */
    private function filterAvailableProcessorsParameters(array $parameters)
    {
        return [
            'merchantId' => $parameters['merchantId'],
            'password' => $parameters['password'],
            'amount' => $parameters['amount'],
        ];
    }

    /**
     * Automatically determine either merchant
     * will be redirected to payment gateway url
     * or can do payment in the background process
     * 
     * @param mixed $options
     *  array(
     *     'procid' => 'BDO', # payment processor id
     *     'mustRedirect' => false, # indicates that the merchant can do payment behind-the-scenes.
     * )
     *
     * @param null|\Ixudra\Curl\CurlService $curl
     * 
     * @return void
     *
    public function silent($options, CurlService $curl = null)
    {

        $this->curl = is_null($curl) ? new CurlService() : $curl;

        $testing = isset($options['testing']) ? true : false;

        unset($options['testing']);

        $options = $this->getArray($options);
        
        return $this->doIntentedAction($options['procid'], $options['mustRedirect'], $testing);
    }*/

    /**
     * Dragonpay transaction action
     *
     * @param ActionInterface $action
     * @param SoapClientAdapter $soap_adapter
     * 
     * @return mixed
     */
    public function action(ActionInterface $action, SoapClientAdapter $soap_adapter = null)
    {
        return $action->doAction($this, $soap_adapter);
    }


    /**
     * Do intented action
     * 
     * @param string $procid      Payment processor id
     * @param bool $mustRedirect
     * @param bool $testing       Are we in the unit testing environment?
     * 
     * @return mixed
     *
    private function doIntentedAction($procid, $mustRedirect = false, $testing = false)
    {
        $this->parameters->add(['procid' => $procid]);

        if ($mustRedirect) {
            $url = $this->away($testing);
            return $url;
        }

        

        // $response = file_get_contents($this->getUrl() . '?' . $this->parameters->query());
        // echo "<pre>";
        // print_r($response);exit;
        $response = $this->curl->to($this->getPaymentUrl())
                               ->withData($this->parameters->get())
                               ->get();
        echo "<pre>";
        print_r($response);exit;

    }*/


    /**
     * Get array representation of \stdClass object
     *
     * @param object|array $options
     * 
     * @return array
     */
    private function getArray($options)
    {
        if ($options instanceof \stdClass) {
            if (!property_exists($options, 'procid') || !property_exists($options, 'mustRedirect')) {
                throw new \InvalidArgumentException("{procid} and {mustRedirect} property must be provided.");
            }

            $options = [
                'procid' => $options->procid,
                'mustRedirect' => $options->mustRedirect,
            ];
        } else {

            $options = (array) $options;
            
            if (!array_key_exists('procid', $options) || !array_key_exists('mustRedirect', $options)) {
                throw new \InvalidArgumentException("{procid} and {mustRedirect} keys must be provided.");
            }
        }
        return $options;
    }

}