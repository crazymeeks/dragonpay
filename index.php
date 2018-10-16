<?php
require_once('vendor/autoload.php');

use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
use Dotenv\Dotenv;
$dotenv = new Dotenv(__DIR__);
$dotenv->load();

?>
<html>
<form action="" method="POST">
<input type="submit" name="submit" value="Pay">
</form>
<?php
if ( isset($_POST['submit']) ) {
    /*$parameters = [

      'merchantid' => 'MERCHANTID', # Varchar(20) A unique code assigned to Merchant
      'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
      'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
      'ccy' => 'PHP', # Char(3) The currency of the amount
      'description' => 'Test', # Varchar(128) A brief description of what the payment is for
      'email' => 'some@merchant.ph', # Varchar(40) email address of customer
      'password' => 'PASSWORD', # This will be use to generate a digest key
      'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
      'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

    ];

  $parameters['merchantid'] = getenv('MERCHANT_ID');
  $parameters['password'] = getenv('MERCHANT_KEY');
  $parameters['txnid'] = 'TXNID-' . rand();

  $dragonpay = new Dragonpay();
  //$dragonpay->filterPaymentChannel( Dragonpay::OTC_NON_BANK );
  // With token
   /*$token = $dragonpay->getToken(
            $parameters
   );

  # Using query parameters
  $dragonpay->setParameters($parameters)->away();
  exit;*/
  
  
  # Using credit card
  
  $parameters = [
    'merchantid' => 'MERCHANTID', # Varchar(20) A unique code assigned to Merchant
    'txnid' => 'TXNID', # Varchar(40) A unique id identifying this specific transaction from the merchant site
    'amount' => 1, # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
    'ccy' => 'PHP', # Char(3) The currency of the amount
    'description' => 'Test', # Varchar(128) A brief description of what the payment is for
    'email' => 'some@merchant.ph', # Varchar(40) email address of customer
    'password' => 'PASSWORD', # This will be use to generate a digest key
    'param1' => 'param1', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed
    'param2' => 'param2', # Varchar(80) [OPTIONAL] value that will be posted back to the merchant url when completed

    'firstName' => 'John',
    'lastName'  => 'Doe',
    'address1'  => '#123 Chocolate Hills',
    'address2'  => 'Sweet Mountain',
    'city'      => 'Hillside',
    'state'     => 'Bohol',
    'country'   => 'PH',
    'zipCode'   => '1201',
    'telNo'     => '63 2029',
  ];

  $test = false;
  $dragonpay = new Dragonpay( $test );

  $parameters['merchantid'] = getenv('MERCHANT_ID');
  $parameters['password'] = getenv('MERCHANT_PROD_KEY');
  $parameters['txnid'] = 'TXNID-' . rand();
  
  $dragonpay->useCreditCard($parameters)->getToken($parameters);
  
  $dragonpay->away();exit;
}
?>
</html>

