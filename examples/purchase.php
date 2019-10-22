<?php
require_once(__DIR__ . '/../vendor/autoload.php');
use Crazymeeks\Foundation\PaymentGateway\Dragonpay;
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase</title>
    <style>
    .row {
        padding: 10px 10px;
    }
    div.col {
        margin: 0;
        padding: 0;
        padding-bottom: 1.25em;
    }
    div.col label {
        margin: 0;
        padding: 0;
        display: block;
        font-size: 100%;
        padding-top: .1em;
        padding-right: .25em;
        width: 10em;
        text-align: right;
        float: left;
    }
    div.col input {
        margin: 0;
        padding: 0;
        display: block;
        font-size: 100%;
    }
    div.col #checkout {
        
        display: block;
        margin-left: 210px;
        color: #212529;
        background-color: #d39e00;
        border-color: #c69500;
        border: 1px solid transparent;
        padding: .375rem .75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: .25rem;
        cursor: pointer;
    }
    </style>
</head>
<body>
    <div class="row">
        <form method="POST">
            <div class="col">
                <label for="txnid">Transaction ID:</label>
                <input type="text" name="txnid" id="txnid" value="<?php echo strtoupper(uniqid());?>">
            </div>
            <div class="col">
                <label for="amount">Amount:</label>
                <input type="text" name="amount" id="amount" value="1">
            </div>
            <div class="col">
                <label for="description">Description:</label>
                <input type="text" name="description" id="description" value="Purchase 1">
            </div>
            <div class="col">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="email@merchant.com">
            </div>
            <div class="col">
                <input type="submit" name="checkout" id="checkout" value="Checkout">
            </div>
        </form>
    </div>

<?php
if (isset($_POST['checkout'])) {

    $parameters = [
        'txnid' => $_POST['txnid'], # Varchar(40) A unique id identifying this specific transaction from the merchant site
        'amount' => $_POST['amount'], # Numeric(12,2) The amount to get from the end-user (XXXX.XX)
        'ccy' => 'PHP', # Char(3) The currency of the amount
        'description' => $_POST['description'], # Varchar(128) A brief description of what the payment is for
        'email' => $_POST['email'], # Varchar(40) email address of customer

    ];

    $merchant_account = [
          'merchantid' => getenv('MERCHANT_ID'),
          'password'   => getenv('MERCHANT_KEY')
    ];
    
    // Initialize Dragonpay
    $dragonpay = new Dragonpay($merchant_account);
    // Set parameters, then redirect to dragonpay
    $dragonpay->setParameters($parameters)->away();


}
?>
</body>
</html>