<?php
require_once('vendor/autoload.php');

use Crazymeeks\Foundation\PaymentGateway\Dragonpay\Dragonpay;

## Sample Usage
/*
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
*/

/**
 * If you wish to use credit card for payment
 * You need to call the sendBillingInfo() and
 * pass the required parameters.
 *
 *
 * Please make sure also that you have installed SoapClient.
 * Ubuntu installation: sudo apt-get install php-soap
 * Windows: https://stackoverflow.com/questions/29934167/set-up-php-soap-extension-in-windows 
 *
 * @see Dragonpay's documentation for SendBillingInfo()
 * @link https://www.dragonpay.ph/wp-content/uploads/2014/05/Dragonpay-PS-API
 */
/*
$parameters = array(
  'merchantid' => 'MERCHANTID',
  'txnid' => rand(),
  'amount' => 10,
  'ccy' => 'PHP',
  'description' => 'Test',
  'email' => 'testemail@example.com',
  'key' => 'YOURKEY',
);

$dragonpay = new Dragonpay($parameters);

$sendbillinginfo_params = array(
		 'merchantId' => 'MERCHANTID',
		 'merchantTxnId' => 'Transaction_number',
		 'firstName' => 'firstname',
		 'lastName' => 'lastname',
		 'address1' => 'address1',
		 'address2' => 'address2'
		 'city' => 'your_city',
		 'state' => 'your_state',
		 'country' => 'your_country',
		 'zipCode' => 'your_zipcode',
		 'telNo' => 'your_telno',
		 'email' => 'your_valid_email',
		);
// check if validation pass with SendBillingInfo()
if($dragonpay->sendBillingInfo($sendbillinginfo_params)){

	$dragonpay->away();exit;
}
*/