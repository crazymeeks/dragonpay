<?php
require_once('vendor/autoload.php');

use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Dragonpay;

$parameters = array(
  'merchantid' => 'MERCHANTID',
  'txnid' => rand(),
  'amount' => 10,
  'ccy' => 'PHP',
  'description' => 'Test',
  'email' => 'testemail@example.com',
  'key' => 'YOURKEY',
);
$dragonpay = (new Dragonpay($parameters))->away();exit();