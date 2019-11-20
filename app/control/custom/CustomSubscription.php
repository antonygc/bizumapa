<?php

/**
 * 
 */

// 5234215625186649


class CustomSubscription extends TPage
{
	
	protected $pagarme;
	protected $form;


	function __construct()
	{
        parent::__construct();

		$this->pagarme = new PagarMe\Client('ak_test_tyzdxe39mTDFsC0Bfdwlx3hYafC5TH');

        if ( !empty($_REQUEST['data']) ) {
        	
        	// DADOS DO CHECKOUT: 
			// https://docs.pagar.me/docs/overview-checkout
        	$this->startSubscription();

        } 

       	$id = $this->getUserSubscription();
       	$subs = $this->getSubscriptionObj($id);

       	if ($subs) {

	        $this->html = $this->getSubscriptionStatusForm($subs);

       	} else {
	        $this->html = new THtmlRenderer('app/resources/custom_checkout.html');
	        $this->html->enableSection('main', array());
       	}

        parent::add($this->html);  

	}

	public function startSubscription()
	{

    	$data = json_decode($_REQUEST['data']);
    	$data = $this->object2array($data);

    	$data['customer']['external_id'] = TSession::getValue('userid');
    	$data['plan_id'] = '442204';

		$subs = $this->pagarme->subscriptions()->create($data);

		$this->setUserSubscription($subs->id);

		return $subs;
	}

	public function getUserSubscription()
	{
        try 
        { 
            TTransaction::open('permission'); // open transaction 
			$user = new SystemUser(TSession::getValue('userid'));
            TTransaction::close(); // Closes the transaction 
            return $user->subscription;
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }

	}

	public function setUserSubscription($id)
	{
        try 
        { 
            TTransaction::open('permission'); // open transaction 
			$user = new SystemUser(TSession::getValue('userid'));
			$user->subscription = $id;
			$user->store();
            TTransaction::close(); // Closes the transaction 
            return $user;
        } 
        catch (Exception $e) 
        { 
            TTransaction::rollback();
            new TMessage('error', $e->getMessage()); 
        }

	}

	public function getSubscriptionObj($id)
	{

		$id = (string) $id;

		if (empty($id)) {
			return '';
		}

		try {	

			return $this->pagarme->subscriptions()->get([
			    'id' => $id
			    // 'id' => '451641'
			]);

		} catch (Exception $e) {
			return '';
		}

	}

	public function getTransactionsObj($id)
	{
		return  $this->pagarme->subscriptions()->transactions([
    		'subscription_id' => (string) $id
		]);
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

	public function getSubscriptionStatusForm($subs)
	{

        $form = new BootstrapFormBuilder;
        $form->setFormTitle('Assinatura');
        
        // create the form fields
        $id          = new TEntry('id');
        $status 	 = new TEntry('status');
        $plan    	 = new TEntry('plan');
        $created     = new TDateTime('created');
        $expires     = new TDateTime('expires');
        $value       = new TEntry('value');
        $paymethod   = new TEntry('paymethod');
        $pagarmebtn  = new THyperLink('pagar.me', $subs->manage_url, 'white', 10, '', 'fa:external-link white');
        $pagarmebtn->class='btn btn-success';
        
        $id->setEditable(FALSE);
        $status->setEditable(FALSE);
        $plan->setEditable(FALSE);
        $created->setEditable(FALSE);
        $expires->setEditable(FALSE);
        $value->setEditable(FALSE);
        $paymethod->setEditable(FALSE);

        $id->setSize('38%');
        // $plan->setSize('38%');
        // $status->setSize('38%');
        $created->setMask('dd/mm/yyyy hh:ii');
        $expires->setMask('dd/mm/yyyy hh:ii');
        $created->setDatabaseMask('yyyy-mm-dd hh:ii');
        $expires->setDatabaseMask('yyyy-mm-dd hh:ii');
        $value->setNumericMask(2, ',', '.', true);
        $value->setSize('100%');
        $paymethod->setSize('100%');
        $created->setSize('100%');
        $expires->setSize('100%');

        $id->setValue($subs->id);
        $status->setValue($subs->status);
        $plan->setValue($subs->plan->name);
        $created->setValue($subs->current_period_start);
        $expires->setValue($subs->current_period_end);
        $value->setValue($subs->plan->amount/100);
        $paymethod->setValue($subs->payment_method);

        $label_style = 'text-align:left;border-bottom:1px solid #c0c0c0;width:100%';

		$label = new TLabel('Identificação', '', 12, 'i');
        $label->style= $label_style;
        $form->addContent( [$label] );

        $form->addFields( [new TLabel('Número')],      [$id]);
        $form->addFields( [new TLabel('Plano')],   	   [$plan] );
        $form->addFields( [new TLabel('Forma de Pagamento')], [$paymethod],
    					  [new TLabel('Valor (R$)')],  [$value]);

		$label = new TLabel('Situação', '', 12, 'i');
        $label->style= $label_style;
        $form->addContent( [$label] );

        $form->addFields( [new TLabel('Status')], 	   [$status] );
        $form->addFields( [new TLabel('Início em')],  [$created], 
                          [new TLabel('Expira em')],  [$expires]);
        
        $label = new TLabel('Alterações e Cancelamento', '', 12, 'i');
        $label->style= $label_style;
        $form->addContent( [$label] );
        
        $form->addFields( [new TLabel('Acesse o link externo')], [$pagarmebtn] );
 
        return $form;
        
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