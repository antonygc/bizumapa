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
        



        $content = file_get_contents("app/templates/theme3/login.html");
        $content = file_get_contents("lib/kitymind/index.html");
        $content = ApplicationTranslator::translateTemplate($content);
        $content = AdiantiTemplateParser::parse($content);

        echo $content;


        // $iframe = new TElement('iframe');
        // $iframe->id = "iframe_external";
        // $iframe->src = "/bizumapa/lib/kitymind/index.html";
        // $iframe->frameborder = "0";
        // $iframe->scrolling = "yes";
        // $iframe->width = "100%";
        // $iframe->height = "700px";
        
        // parent::add($iframe);

        
    }
}