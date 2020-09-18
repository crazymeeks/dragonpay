<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Crazymeeks\Foundation\PaymentGateway\DragonPay;

class Token
{

	protected $token;



	public function __construct($token)
	{
		$this->token = $token;
	}

	/**
	 * Get the token
	 * 
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	public function __toString()
	{
		return $this->token;
	}
}