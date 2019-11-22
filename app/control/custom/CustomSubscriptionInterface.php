<?php

class CustomSubscriptionInterface
{

	public static function getClient()
	{
		return new PagarMe\Client(API_KEY);
	}

	public static function getUserSubscription()
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

	public static function setUserSubscription($id)
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