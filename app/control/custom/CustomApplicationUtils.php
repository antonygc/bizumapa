<?php

class CustomApplicationUtils
{

    public static function isAdmin()
    {
        $usergroupis = TSession::getValue('usergroupids');
        return in_array('1', $usergroupis);
    }

    public static function exceptionHandler($e)
    {
		$reflect = new ReflectionClass($e);

		switch ($reflect->getShortName()) {
			case 'PagarMeException':
				new TMessage('error', $e->errorMessage);
				break;
			
			default:
				new TMessage('error', $e->getMessage());
				break;
		}
    }

}