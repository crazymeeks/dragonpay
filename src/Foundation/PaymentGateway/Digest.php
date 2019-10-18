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