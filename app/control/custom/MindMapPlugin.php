<?php

class MindMapPlugin extends TPage
{

    public function __construct()
    {

        parent::__construct();

        $is_loaded = $this->loadUserData();

        if ($is_loaded) {    
            $this->includePluginCSS();
            $this->includepluginHTML();
            $this->includepluginJS();

        } else {

            AdiantiCoreApplication::loadPage('PrivateMindMap');
        }
    }

    function loadUserData()
    {
        if (isset($_GET))
        {

            $mappath = isset($_GET['p'])  ? $_GET['p']  : NULL;
            $mapname = isset($_GET['view']) ? $_GET['view'] : NULL;

            $full_path = MindMapUtils::getMindMapFullPath($mappath, $mapname);

            $mapcontent = file_get_contents($full_path);

            if ($mapcontent) {
                $this->loadMindMap($mappath, $mapname, $mapcontent);
                return true;
            }

            new TMessage('error', 'Erro ao recuperar Mapa Mental');
            return false;
        }
    }


    function loadMindMap($mappath, $mapname, $mapcontent)
    {
        echo "<script>
            var mindmap_path = '". $mappath ."';
            var mindmap_name = '". $mapname ."';
            var mindmap_content = '". $mapcontent ."';
            </script>";
    }

    function includePluginCSS()
    {

        $pre_path = 'lib/kityminder';
        TPage::include_css($pre_path . "/bower_components/bootstrap/dist/css/bootstrap.css" );
        TPage::include_css($pre_path . "/bower_components/codemirror/lib/codemirror.css" );
        TPage::include_css($pre_path . "/bower_components/hotbox/hotbox.css" );
        TPage::include_css($pre_path . "/bower_components/kityminder-core/dist/kityminder.core.css" );
        TPage::include_css($pre_path . "/bower_components/color-picker/dist/color-picker.min.css");
        TPage::include_css($pre_path . "/kityminder.editor.css");
        #TPage::include_css("lib/kityminder/kityminder.editor.min.css");
    }

    function includePluginHTML()
    {

        $scope = TSession::getValue('scope');
        $kityMinder = new TElement('kityminder-editor');

        if ($scope == 'public' & !MindMapUtils::isAdminUser()) {

            $kityMinder = new TElement('kityminder-viewer');

            // echo "<script>
            //     editor.minder.disable();
            //     editor.minder.execCommand('hand');
            //     </script>";
        }
        

        $kityMinder->{'on-init'} = 'initEditor(editor, minder)';
        $kityMinder->{'data-theme'} = 'fresh-green';

        $iFrame = new TElement('iframe');
        $iFrame->{'name'} = 'frameFile';
        $iFrame->{'style'} = 'display:none;';

        $mainController = new TElement('div');
        $mainController->{'ng-app'} = 'kityminderDemo';
        $mainController->{'ng-controller'} = 'MainController';
        $mainController->add($kityMinder);
        $mainController->add($iFrame);

        $vbox = new TVBox;
        $vbox->add($mainController);

        parent::add($vbox);

    }

    function includePluginJS()
    {

        $pre_path = 'lib/kityminder';
        // TPage::include_js("lib/kityminder/bower_components/jquery/dist/jquery.js");
        // TPage::include_js("lib/kityminder/bower_components/bootstrap/dist/js/bootstrap.js");
        TPage::include_js($pre_path . "/bower_components/angular/angular.js");
        TPage::include_js($pre_path . "/bower_components/angular-bootstrap/ui-bootstrap-tpls.js");
        TPage::include_js($pre_path . "/bower_components/codemirror/lib/codemirror.js");
        TPage::include_js($pre_path . "/bower_components/codemirror/mode/xml/xml.js");
        TPage::include_js($pre_path . "/bower_components/codemirror/mode/javascript/javascript.js");
        TPage::include_js($pre_path . "/bower_components/codemirror/mode/css/css.js");
        TPage::include_js($pre_path . "/bower_components/codemirror/mode/htmlmixed/htmlmixed.js");
        TPage::include_js($pre_path . "/bower_components/codemirror/mode/markdown/markdown.js");
        TPage::include_js($pre_path . "/bower_components/codemirror/addon/mode/overlay.js");
        TPage::include_js($pre_path . "/bower_components/codemirror/mode/gfm/gfm.js");
        TPage::include_js($pre_path . "/bower_components/angular-ui-codemirror/ui-codemirror.js");
        TPage::include_js($pre_path . "/bower_components/marked/lib/marked.js");
        TPage::include_js($pre_path . "/bower_components/kity/dist/kity.min.js");
        TPage::include_js($pre_path . "/bower_components/hotbox/hotbox.js");
        TPage::include_js($pre_path . "/bower_components/json-diff/json-diff.js");
        TPage::include_js($pre_path . "/bower_components/kityminder-core/dist/kityminder.core.min.js");
        TPage::include_js($pre_path . "/bower_components/color-picker/dist/color-picker.min.js");
        TPage::include_js($pre_path . "/kityminder.editor.js");
        TPage::include_js($pre_path . "/diy.js");
    }

}
