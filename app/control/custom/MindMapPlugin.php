<?php

class MindMapPlugin extends TPage
{

    public function __construct()
    {

        parent::__construct();

        $subsc_ok = CustomSubscriptionInterface::checkSubscription();

        if (!$subsc_ok) {
            AdiantiCoreApplication::loadPage('CustomSubscriptionForm');
            return;
        }


        $iframe = new TElement('iframe');
        $iframe->src = "plugin.php?" . http_build_query($_REQUEST);;
        $iframe->frameborder = "0";
        $iframe->scrolling = "no";
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
