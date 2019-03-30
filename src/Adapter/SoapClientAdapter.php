<?php

namespace Crazymeeks\Foundation\Adapter;

/**
 * Adapter for SoapClient to we can mock
 * the Crazymeeks\Foundation\PaymentGateway\BillingInfoVerifier
 */
use Crazymeeks\Foundation\Exceptions\SendBillingInfoException;

class SoapClientAdapter
{

    private $billing_info_parameters = [];

    /**
     * The parameters that will be pass to the soap client
     *
     * @param array $parameters
     * 
     * @return $this
     */
    public function setParameters( array $parameters )
    {
        $this->billing_info_parameters = $parameters;
        return $this;
    }

    /**
     * Execute call to soap client
     *
     * @param string $url
     * @param array $parameters
     * 
     * @return bool
     */
    public function execute( $url, array $parameters )
    {
       
        if ( ! \class_exists(\SoapClient::class) ) {
            throw new \Exception('SoapClient class not found. Please install it.');
        }
		$wsdl = new \SoapClient($url, $parameters);

        $result = $wsdl->SendBillingInfo($this->billing_info_parameters)->SendBillingInfoResult;
        if ( $result != 0 ) {
            throw new SendBillingInfoException("Dragonpay responded an error code " . $result . " when sending billing info. Please check your parameter or contact Dragonpay directly.");
        }
        return $result == 0 ? true : false;
        
    }

    /**
     * Initial Soap
     *
     * @param string $resource_url  SOAP webservice url
     * 
     * @return \SoapClient
     */
    public function initialize($resource_url)
    {
        if ( ! \class_exists(\SoapClient::class) ) {
            throw new \Exception('SoapClient class not found. Please install it.');
        }
        $soap_client = new \SoapClient($resource_url . '?wsdl', array(
            'location' => $resource_url,
            'trace'    => 1,
        ));

        return $soap_client;
    }
    
}