<?php
/**
 * Handles Request parameters
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * @author Jefferson Claud
 */


namespace Crazymeeks\Foundation\PaymentGateway;


class RequestBag
{
	

	static $requestParameters = array();

	/**
	 * Constructor
	 *
	 * @param array $request
	 *
	 * @return void
	 */
	public function __construct(array $request = array()){
		self::$requestParameters = $request;
	}


	/**
	 * Get the request parameters for the payment gateway
	 * and format it using http_build_query
	 *
	 * @return array
	 */
	public function getRequestParams(){
		return self::$requestParameters;
	}
}