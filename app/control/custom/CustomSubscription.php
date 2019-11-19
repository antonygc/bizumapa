<?php

/**
 * 
 */

// 5234215625186649


class CustomSubscription extends TPage
{
	
	protected $pagarme;


	function __construct()
	{
        parent::__construct();

		$this->pagarme = new PagarMe\Client('ak_test_tyzdxe39mTDFsC0Bfdwlx3hYafC5TH');

       	$this->getSubscription();

        if ( !empty($_REQUEST['data']) ) {
        	
        	// DADOS DO CHECKOUT: 
			// https://docs.pagar.me/docs/overview-checkout
        	$this->startSubscription();

        }

        $this->html = new THtmlRenderer('app/resources/custom_checkout.html');
        $this->html->enableSection('main', array());

        parent::add($this->html);  

	}

	public function getSubscription()
	{

		$substription = $this->pagarme->subscriptions()->get([
		    'id' => '451641'
		]);


		$substription_transactions = $this->pagarme->subscriptions()->transactions([
    		'subscription_id' => '451641'
		]);

		echo var_dump($substription);
		// echo var_dump($substription_transactions);
	}

	public function startSubscription()
	{
		$pagarme = new PagarMe\Client('ak_test_tyzdxe39mTDFsC0Bfdwlx3hYafC5TH');

    	$data = json_decode($_REQUEST['data']);
    	$data = $this->object2array($data);


    	$data['customer']['external_id'] = TSession::getValue('userid');
    	$data['plan_id'] = '442204';

		$subscription = $pagarme->subscriptions()->create($data);

		// echo var_dump($subscription);
	}

	public function doTransaction()
	{
    	$data = json_decode($_REQUEST['data']);
    	$data = $this->object2array($data);

    	$data['customer']['type'] = 'individual';
    	$data['customer']['country'] = 'br';
    	$data['customer']['documents'] = [[
    		'type' => 'cpf',
			'number' => $data['customer']['document_number']
    	]];

    	$phone = '+55' . $data['customer']['phone']['ddd'] . $data['customer']['phone']['number'];

    	$data['customer']['phone_numbers'] = [$phone];

		$data['billing'] = [
			'name' => $data['customer']['name'],
			'address' => $data['customer']['address']
		];

		$data['billing']['address']['country'] = 'br';

		if (empty($data['billing']['address']['complementary'])) {
			unset($data['billing']['address']['complementary']);
		}

        $data['items'] = [ 0=> [
            'id' => '1',
            'title' => 'ASSINATURA ÚNICA',
            'unit_price' => '2990',
            'quantity' => '1',
            'tangible' => 'false'
        ]];

    	unset($data['customer']['address']);
    	unset($data['customer']['document_number']);
    	unset($data['customer']['phone']);

		$pagarme = new PagarMe\Client(
			'ak_test_tyzdxe39mTDFsC0Bfdwlx3hYafC5TH'
		); 

		$transaction = $pagarme->transactions()->create($data);

		echo var_dump($transaction);
	}

	public function object2array( $o )
	{
	    $a = (array) $o;
	    foreach( $a as &$v )
	    {
	        if( is_object( $v ) )
	        {
	            $v = $this->object2array( $v );
	        }
	    }
	    return $a;
	}


}

// C:\wamp64\www\bizumapa\app\control\custom\CustomSubscription.php:63:
// object(stdClass)[53]
//   public 'object' => string 'subscription' (length=12)
//   public 'plan' => 
//     object(stdClass)[51]
//       public 'object' => string 'plan' (length=4)
//       public 'id' => int 442204
//       public 'amount' => int 2990
//       public 'days' => int 30
//       public 'name' => string 'Mapas' (length=5)
//       public 'trial_days' => int 0
//       public 'date_created' => string '2019-11-19T21:59:53.276Z' (length=24)
//       public 'payment_methods' => 
//         array (size=2)
//           0 => string 'boleto' (length=6)
//           1 => string 'credit_card' (length=11)
//       public 'color' => null
//       public 'charges' => null
//       public 'installments' => int 1
//       public 'invoice_reminder' => null
//       public 'payment_deadline_charges_interval' => int 1
//   public 'id' => int 451641
//   public 'current_transaction' => 
//     object(stdClass)[36]
//       public 'object' => string 'transaction' (length=11)
//       public 'status' => string 'paid' (length=4)
//       public 'refuse_reason' => null
//       public 'status_reason' => string 'acquirer' (length=8)
//       public 'acquirer_response_code' => string '0000' (length=4)
//       public 'acquirer_name' => string 'pagarme' (length=7)
//       public 'acquirer_id' => string '5dd010d21d5e0c08dbb05c7a' (length=24)
//       public 'authorization_code' => string '270286' (length=6)
//       public 'soft_descriptor' => null
//       public 'tid' => int 7344167
//       public 'nsu' => int 7344167
//       public 'date_created' => string '2019-11-19T22:02:16.109Z' (length=24)
//       public 'date_updated' => string '2019-11-19T22:02:16.903Z' (length=24)
//       public 'amount' => int 2990
//       public 'authorized_amount' => int 2990
//       public 'paid_amount' => int 2990
//       public 'refunded_amount' => int 0
//       public 'installments' => int 1
//       public 'id' => int 7344167
//       public 'cost' => int 120
//       public 'card_holder_name' => string 'antony carvalho' (length=15)
//       public 'card_last_digits' => string '6649' (length=4)
//       public 'card_first_digits' => string '523421' (length=6)
//       public 'card_brand' => string 'mastercard' (length=10)
//       public 'card_pin_mode' => null
//       public 'card_magstripe_fallback' => boolean false
//       public 'cvm_pin' => boolean false
//       public 'postback_url' => null
//       public 'payment_method' => string 'credit_card' (length=11)
//       public 'capture_method' => string 'ecommerce' (length=9)
//       public 'antifraud_score' => null
//       public 'boleto_url' => null
//       public 'boleto_barcode' => null
//       public 'boleto_expiration_date' => null
//       public 'referer' => string 'api_key' (length=7)
//       public 'ip' => string '186.235.87.81' (length=13)
//       public 'subscription_id' => int 451641
//       public 'metadata' => 
//         object(stdClass)[48]
//       public 'antifraud_metadata' => 
//         object(stdClass)[37]
//       public 'reference_key' => null
//       public 'device' => null
//       public 'local_transaction_id' => null
//       public 'local_time' => null
//       public 'fraud_covered' => boolean false
//       public 'order_id' => null
//       public 'risk_level' => string 'very_low' (length=8)
//       public 'receipt_url' => null
//       public 'payment' => null
//       public 'addition' => null
//       public 'discount' => null
//       public 'private_label' => null
//   public 'postback_url' => null
//   public 'payment_method' => string 'credit_card' (length=11)
//   public 'card_brand' => string 'mastercard' (length=10)
//   public 'card_last_digits' => string '6649' (length=4)
//   public 'current_period_start' => string '2019-11-19T22:02:16.050Z' (length=24)
//   public 'current_period_end' => string '2019-12-19T22:02:16.050Z' (length=24)
//   public 'charges' => int 0
//   public 'soft_descriptor' => null
//   public 'status' => string 'paid' (length=4)
//   public 'date_created' => string '2019-11-19T22:02:16.893Z' (length=24)
//   public 'date_updated' => string '2019-11-19T22:02:16.893Z' (length=24)
//   public 'phone' => 
//     object(stdClass)[40]
//       public 'object' => string 'phone' (length=5)
//       public 'ddi' => string '55' (length=2)
//       public 'ddd' => string '65' (length=2)
//       public 'number' => string '665656656' (length=9)
//       public 'id' => int 505607
//   public 'address' => 
//     object(stdClass)[38]
//       public 'object' => string 'address' (length=7)
//       public 'street' => string 'Quadra Quadra 8' (length=15)
//       public 'complementary' => string '' (length=0)
//       public 'street_number' => string '11' (length=2)
//       public 'neighborhood' => string 'Sobradinho' (length=10)
//       public 'city' => string 'Brasília' (length=9)
//       public 'state' => string 'DF' (length=2)
//       public 'zipcode' => string '73005080' (length=8)
//       public 'country' => string 'Brasil' (length=6)
//       public 'id' => int 2495192
//   public 'customer' => 
//     object(stdClass)[52]
//       public 'object' => string 'customer' (length=8)
//       public 'id' => int 2468957
//       public 'external_id' => string '3' (length=1)
//       public 'type' => null
//       public 'country' => null
//       public 'document_number' => string '03722621127' (length=11)
//       public 'document_type' => string 'cpf' (length=3)
//       public 'name' => string 'qswdqew@efwef.com' (length=17)
//       public 'email' => string 'wdffefwf@efwe.com' (length=17)
//       public 'phone_numbers' => null
//       public 'born_at' => null
//       public 'birthday' => null
//       public 'gender' => null
//       public 'date_created' => string '2019-11-19T22:02:16.015Z' (length=24)
//       public 'documents' => 
//         array (size=0)
//           empty
//   public 'card' => 
//     object(stdClass)[45]
//       public 'object' => string 'card' (length=4)
//       public 'id' => string 'card_ck36elc3z00tphp6ftyhtfak8' (length=30)
//       public 'date_created' => string '2019-11-19T22:02:16.079Z' (length=24)
//       public 'date_updated' => string '2019-11-19T22:02:16.885Z' (length=24)
//       public 'brand' => string 'mastercard' (length=10)
//       public 'holder_name' => string 'antony carvalho' (length=15)
//       public 'first_digits' => string '523421' (length=6)
//       public 'last_digits' => string '6649' (length=4)
//       public 'country' => string 'BRAZIL' (length=6)
//       public 'fingerprint' => string 'ck367deso0di60i23xxj6s4gt' (length=25)
//       public 'valid' => boolean true
//       public 'expiration_date' => string '0927' (length=4)
//   public 'metadata' => null
//   public 'settled_charges' => null
//   public 'manage_url' => string 'https://pagar.me/customers/#/subscriptions/451641?token=test_subscription_B2a2Carayj8PePrHy7LTtX2ijzQWHZ' (length=104)