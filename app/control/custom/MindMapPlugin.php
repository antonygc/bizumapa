<?php

class MindMapPlugin extends TPage
{

    public function __construct()
    {

        parent::__construct();

        if (empty($_GET['id']) or 
            empty($_GET['scope'])) {
            return false;
        } 

        $data = array('id'=> $_GET['id'],
              'scope'=>$_GET['scope']);

        $query = http_build_query($data); // foo=bar&baz=boom&cow=milk&php=hypertext+processor

        $iframe = new TElement('iframe');
        $iframe->src = "plugin.php?" . $query;
        $iframe->frameborder = "0";
        $iframe->scrolling = "auto";
        $iframe->width = "100%";
        $iframe->height = "700px";
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($iframe);
        
        parent::add($vbox);

        echo "<style>
                .content {
                    padding: 0px;
                    padding-left: 0px;
                    padding-right: 0px;
                    padding-bottom: 0px;
            </style>";

        // Recolhe o menu lateral
        TScript::create('$("body").addClass("sidebar-collapse")
            .removeClass("sidebar-open").trigger("collapsed.pushMenu")');
        
        // Expande o menu lateral
        // TScript::create('$("body").addClass("sidebar-open").removeClass("sidebar-collapse").trigger("expanded.pushMenu")'); //Expande       

    }

}
