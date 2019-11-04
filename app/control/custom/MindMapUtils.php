<?php

class MindMapUtils
{

	public static function getFrameVBox($class)
	{
		$iframe = new TElement('iframe');
        $iframe->src = "filemanager.php";
        $iframe->frameborder = "0";
        $iframe->scrolling = "auto";
        $iframe->width = "100%";
        $iframe->height = "700px";
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', $class));
        $vbox->add($iframe);

        return $vbox;
	}

	public static function getMindMapFullPath($mappath, $mapname)
	{
        $scope = TSession::getValue('scope');
        $userid = TSession::getValue('userid');

        $root = $_SERVER['DOCUMENT_ROOT'] . '/userdata/';

        if ($scope == 'public') {

            $root = $root . 'public';

        } elseif ($scope == 'private') {

            $root = $root . $userid;

        } else {

            die(__CLASS__ . ': Não foi possível determinar escopo');
        }

        return implode('/', [$root, $mappath, $mapname]);
	}


    public static function isAdminUser()
    {
        $usergroupis = TSession::getValue('usergroupids');
        return in_array('1', $usergroupis);
    }
}