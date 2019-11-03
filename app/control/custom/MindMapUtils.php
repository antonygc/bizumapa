<?php

class MindMapUtils
{

	public static function getFrameVBox($class)
	{
		$iframe = new TElement('iframe');
        // $iframe->id = "iframe_external";
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
}