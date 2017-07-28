<?php

/**
 * This class serves as the key digester for the payment gateway
 *
 * (c) Jefferson Claud
 *
 * @author Jefferson Claud <jeffclaud17@gmail.com>
 */

namespace Crazymeeks\Foundation\PaymentGateway;

class Digest
{
	
	protected $digest;

	protected $encryption;

	/**
	 * Constructor
	 *
	 * @param string $encryption          The encryption type to be use
	 * @param string $key                 The digest key
	 *
	 */
	public function __construct($encryption = null, $key = null)
	{
		$this->encryption = $encryption;
		//$this->digest = $key;
		$this->digest($encryption, $key);
	}

	/**
	 * 
	 */
	public function digest($encryption, $key)
	{
		if(! is_null($encryption)){
			$class = ucfirst($encryption) . 'Encryption';
			$reflection = new \ReflectionClass("Crazymeeks\Encryption\\$class");
			$this->digest = $reflection->newInstanceArgs([$key]);
		}
	}

	public function __toString()
	{
		if(! is_null($this->digest)){
			return (string) $this->digest;
		}
		
		throw new \LogicException('Invalid digest value');
	}
}