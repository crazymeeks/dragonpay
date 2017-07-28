**Usage**

- Using Constructor
	$dragonpay = (new Dragonpay([
				'merchantid' => '3388e78e',
				'txnid' => 'dfd',
				'amount' => (int) 1500,
				'ccy' => 'PHP',
				'description' => 'dffd',
				'email' => 'jeffclaud17@gmail.com',
				'key' => 'dafdf'
			]))->away();

- Using method
	$dragonpay = new Dragonpay;
	$dragonpay->setRequestParameters(
		 	[
		 		'merchantid' => '3388e78e',
		 		'txnid' => 'dfd',
		 		'amount' => (int) 1500,
		 		'ccy' => 'PHP',
		 		'description' => 'dffd',
		 		'email' => 'jeffclaud17@gmail.com',
		 	]
		 )->away();

- Get the list of Request Parameters($_POST)
 	- $dragonpay->getRequestParameters();