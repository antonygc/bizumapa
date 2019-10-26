<?php
/**
 * SinglePageView
 *
 * @version    1.0
 * @package    samples
 * @subpackage tutor
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class MindMapPlugin extends TPage
{
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();
        
        /*
        $this->html = new THtmlRenderer('lib/local-kitymind-master/index.html');
        #$this->html = new THtmlRenderer('app/resources/page_sample.html');


        $replaces = [];
        $replaces['title']  = 'Panel title';
        $replaces['footer'] = 'Panel footer';
        $replaces['name']   = 'Someone famous';
        
        // replace the main section variables
        $this->html->enableSection('main', $replaces);

        #echo var_dump($this->html);

        $vbox = new TVBox;
        $vbox->add($this->html);
        parent::add($vbox);            
        */


        $iframe = new TElement('iframe');
        $iframe->id = "iframe_external";
        $iframe->src = "/bizumapa/lib/local-kitymind-master/";
        $iframe->frameborder = "0";
        $iframe->scrolling = "yes";
        $iframe->width = "100%";
        $iframe->height = "700px";
        
        parent::add($iframe);

        
    }
}