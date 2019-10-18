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

use Crazymeeks\Foundation\PaymentGateway\Parameters;

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

	/**
	 * Alias to getRequestParams()
	 *
	 * @return array
	 */
	public function getParameters()
	{
		return $this->getRequestParams();
	}
}