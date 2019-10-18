<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
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