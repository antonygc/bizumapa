<?php

class CustomSubscriptionInterface
{

	// Fluxo de Assinatura
	// https://pagarme.zendesk.com/hc/pt-br/articles/360000215732-Fluxo-de-cobran%C3%A7a-de-uma-assinatura-

	public static $VALID   = 'VALID';
	public static $INVALID = 'INVALID';
	public static $CHECK   = 'CHECK';

	public static $STATUS_PAID      = 'paid';
	public static $STATUS_ENDED     = 'ended';
	public static $STATUS_UNPAID    = 'unpaid';
	public static $STATUS_PENDING   = 'pending_payment';
	public static $STATUS_TRIALING  = 'trialing';
	public static $STATUS_CANCELLED = 'cancelled';

	public static $TRIAL_PERIOD = '+1 day';
	public static $SUBSC_PERIOD = '+1 day';

	public static function getClient()
	{
		return new PagarMe\Client(API_KEY);
	}

	public static function checkSubscription()
	{

		// TODO:  DESLOGAR USUÁRIO, SE NÃO, É SÓ DEIXAR LOGADO QUE NUNCA VAI 
		// RETORNAR FALSE

		if (CustomApplicationUtils::isAdmin()) {
			return true;
		}

		if (CustomSubscriptionInterface::checkTrial()) {
			new TMessage('info', 'Período de avaliação em andamento');
			return true;
		}

		$subsc = TSession::getValue('subscription');

		switch ($subsc) {
			case CustomSubscriptionInterface::$VALID:
				return true;
				break;
			case CustomSubscriptionInterface::$INVALID:
				return false;
				break;
			case CustomSubscriptionInterface::$CHECK:
				return CustomSubscriptionInterface::checkValidation();
				break;							
			default:
				return CustomSubscriptionInterface::checkValidation();
				break;
		}
		
	}

	public static function checkTrial()
	{
		$user_creation = new DateTime(TSession::getValue('usercreation'));

		$date = $user_creation->format("d-m-y");

		echo var_dump(TSession::getValue('usercreation'));
		echo var_dump($date);

		$trial_expire = $user_creation->modify(CustomSubscriptionInterface::$TRIAL_PERIOD);
		$now = new DateTime();

		if ($now > $trial_expire) { return false; }
		return true;
	}

	public static function checkValidation()
	{

		$subsc = CustomSubscriptionInterface::getUserSubscription();

		if (is_null($subsc)) 
		{
			TSession::setValue('subscription', CustomSubscriptionInterface::$INVALID );
			return false;
		}

		$subsc_obj = CustomSubscriptionInterface::getSubscriptionObj($subs);

		if ($subsc_obj->status != CustomSubscriptionInterface::$STATUS_PAID) {
			TSession::setValue('subscription', CustomSubscriptionInterface::$INVALID );
			return false;
		}

		$period_start = new DateTime($subsc_obj->current_period_start);
		$period_expire = $period_start->modify(CustomSubscriptionInterface::$SUBSC_PERIOD);
		$now = new DateTime();

		if ($now > $period_expire) {
			TSession::setValue('subscription', CustomSubscriptionInterface::$INVALID );
			return false;
		}

		TSession::setValue('subscription', CustomSubscriptionInterface::$VALID );
		return true;
		
	}

	public static function createSubscription($data)
	{
		$pagarme = CustomSubscriptionInterface::getClient();
    	$data['customer']['external_id'] = TSession::getValue('userid');
    	$data['plan_id'] = PLAN_ID;

		try {

			return $pagarme->subscriptions()->create($data);
			
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