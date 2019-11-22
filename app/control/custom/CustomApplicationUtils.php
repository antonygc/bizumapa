<?php

class CustomApplicationUtils
{

    public static function isAdmin()
    {
        $usergroupis = TSession::getValue('usergroupids');
        return in_array('1', $usergroupis);
    }
}