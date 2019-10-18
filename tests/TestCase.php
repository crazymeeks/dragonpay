<?php

/*
 * This file is part of the Dragonpay library.
 *
 * (c) Jefferson Claud <jeffclaud17@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use Dotenv\Dotenv;

class TestCase extends PHPUnitTestCase
{

    public function setUp()
    {
        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();
    }

    public function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }

}