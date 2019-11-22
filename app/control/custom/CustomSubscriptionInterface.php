<?php

class CustomSubscriptionInterface
{

	public static function getClient()
	{
		return new PagarMe\Client(API_KEY);
	}

	public static function checkSubscription()
	{

		$is_admin = CustomApplicationUtils::isAdmin();
		$subs = TSession::getValue('subscription');

		$user_creation = TSession::getValue('usercreation');
		$user_creation = new DateTime($user_creation);

		echo var_dump($user_creation);

		if (is_null($subs)) {

			// Ver se existe assinatura no BD
			$subs = CustomSubscriptionInterface::getUserSubscription();

			if (is_null($subs)) {

				// Usuário logou agora, mas não tem assinatura
				TSession::setValue('subscription', '0');
				new TMessage('info', 'Sem assinatura'); 

			
			} else {

				// Usuário logou agora, e tem assinatura
				// CustomSubscriptionInterface::checkValidation($subs);

			}
			
		} elseif ($subs == '0') {

			// Usuário já estava logado e não tem assinatura
			// CustomSubscriptionInterface::checkValidation($subs);
			
		} else {

			// Usuário já estava logado e tem assinatura
			// CustomSubscriptionInterface::checkValidation($subs);
		}

		return true;

		
	}

	public static function checkValidation($subs)
	{
		$subs_obj = CustomSubscriptionInterface::getSubscriptionObj($subs);
		TSession::setValue('subscription', $subs_obj->current_period_end);
		# code...
	}

	public static function createSubscription($data)
	{
    	$data['customer']['external_id'] = TSession::getValue('userid');
    	$data['plan_id'] = PLAN_ID;

		try {

			return $this->pagarme->subscriptions()->create($data);
			
		} catch (Exception $e) {
			
		}

	}

	public static function getUserSubscription()
	{
        try 
        { 
            TTransaction::open('permission'); 
			$user = new SystemUser(TSession::getValue('userid'));
            TTransaction::close(); 
            return $user->subscription;
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }

	}

	public static function setUserSubscription($id)
	{
        try 
        { 
            TTransaction::open('permission'); 
			$user = new SystemUser(TSession::getValue('userid'));
			$user->subscription = $id;
			$user->store();
            TTransaction::close(); 
            return $user;
        } 
        catch (Exception $e) 
        { 
            TTransaction::rollback();
            new TMessage('error', $e->getMessage()); 
        }

	}

	public static function getSubscriptionObj($id)
	{

		$id = (string) $id;
		$pagarme = CustomSubscriptionInterface::getClient();

		if (empty($id)) {
			return;
		}

		try {	

			return $pagarme->subscriptions()->get(['id' => $id]);

		} catch (Exception $e) {
			return;
		}

	}

	public function getTransactionsObj($id)
	{

		$pagarme = CustomSubscriptionInterface::getClient();
		return  $pagarme->subscriptions()->transactions([
    		'subscription_id' => (string) $id
		]);
	}

}