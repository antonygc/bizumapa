<?php


/**
 * 
 */
class CustomSubscription extends TPage
{
	
	function __construct()
	{
        parent::__construct();

        // TPage::include_js('https://assets.pagar.me/checkout/1.1.0/checkout.js');
        // TPage::include_js('lib/custom/checkout.js');

        $this->html = new THtmlRenderer('app/resources/custom_checkout.html');

        $this->html->enableSection('main', array());

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->html);

        parent::add($vbox);  

	}
}