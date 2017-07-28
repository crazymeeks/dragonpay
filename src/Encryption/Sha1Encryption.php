<?php

/**
 * The sha1 encryption class
 *
 * (c) Jefferson Claud
 *
 * @author Jefferson Claud <jeffclaud17@gmail.com>
 */

namespace Crazymeeks\Encryption;

class Sha1Encryption
{

	/**
	 * @var string
	 */
	protected $digest;

	/**
	 * Constructor
	 *
	 * @param string $digest        The string to digest. Usually a combination of merchantid and key.
	 * @see Dragonpay Request parameters
	 * @link https://www.dragonpay.ph/wp-content/uploads/2014/05/Dragonpay-PS-API
	 */
	public function __construct($digest)
	{
		$this->digest = $digest;
	}

	/**
	 * PHP's __toString Magic method
	 *
	 * @return string
	 */
	public function __toString()
	{
		return sha1($this->digest);
	}
}