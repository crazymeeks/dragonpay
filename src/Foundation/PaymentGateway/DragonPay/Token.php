<?php

namespace Crazymeeks\Foundation\PaymentGateway\Dragonpay;

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