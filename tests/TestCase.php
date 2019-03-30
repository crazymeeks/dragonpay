<?php

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