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

	public static function getCookie($value)
	{
		return TSession::getValue('subscription');
	}

	public static function setCookie($value)
	{
		TSession::setValue('subscription', $value );
	}	

	public static function checkSubscription($redirect=false)
	{

		// TODO:  DESLOGAR USUÁRIO, SE NÃO, É SÓ DEIXAR LOGADO QUE NUNCA VAI 
		// RETORNAR FALSE

		if (CustomApplicationUtils::isAdmin()) {
			return true;
		}

		$is_trial_valid = CustomSubscriptionInterface::checkExpirationDate(
				TSession::getValue('usercreation'),
				CustomSubscriptionInterface::$TRIAL_PERIOD);

		if ($is_trial_valid) {
			// new TMessage('info', 'Período de avaliação em andamento');
			return true;
		}

		$subsc = CustomSubscriptionInterface::getCookie();

		switch ($subsc) {
			case CustomSubscriptionInterface::$VALID:
				$isvalid = true;
				break;
			case CustomSubscriptionInterface::$INVALID:
				$isvalid = false;
				break;
			case CustomSubscriptionInterface::$CHECK:
				$isvalid = CustomSubscriptionInterface::checkValidation();
				break;							
			default:
				$isvalid = CustomSubscriptionInterface::checkValidation();
				break;
		}

		if ($isvalid) {

			CustomSubscriptionInterface::setCookie(CustomSubscriptionInterface::$VALID);

		} else {

			CustomSubscriptionInterface::setCookie(CustomSubscriptionInterface::$INVALID);

			if ($redirect) {
				AdiantiCoreApplication::loadPage('CustomSubscriptionForm');
			}
		}

		return $isvalid;
		
	}

	public static function checkExpirationDate($start_date, $period)
	{
		$period_start = new DateTime($start_date);
		$period_expire = $period_start->modify($period);
		$now = new DateTime();
		$is_expired = ($now > $period_expire) ? false : true ;
		return $is_expired;
	}	

	public static function checkValidation()
	{

		$subsc = CustomSubscriptionInterface::getUserSubscription();

		if (is_null($subsc)) { return false; }

		$subsc_obj = CustomSubscriptionInterface::getSubscriptionObj($subs);

		switch ($subsc_obj->status) {

			case CustomSubscriptionInterface::$STATUS_PAID:
				return true;

			case CustomSubscriptionInterface::$STATUS_CANCELLED:
				$is_valid = CustomSubscriptionInterface::checkExpirationDate(
					$subsc_obj->current_period_start,
					CustomSubscriptionInterface::$SUBSC_PERIOD
				);
				return $is_valid;
			
			default:
				return false;
		}
		
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
            TTransaction::open(DEFAULT_DB); 
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
            TTransaction::open(DEFAULT_DB); 
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