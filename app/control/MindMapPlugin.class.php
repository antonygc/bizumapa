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

        $loaded = $this->loadUserdata();

        if ($loaded) {    
            $this->includePluginCSS();
            $this->includepluginHTML();
            $this->includepluginJS();

        } else {
            AdiantiCoreApplication::loadPage('MapList');
        }
    }

    function loadUserdata()
    {
        if ($_GET)
        {
            $path  = isset($_GET['p'])  ? $_GET['p']  : NULL;
            $fname = isset($_GET['view']) ? $_GET['view'] : NULL;
            
            if ($path and $fname)
            {
                $root = $_SERVER['DOCUMENT_ROOT'].'/bizumapa/userdata';
                $full_path = implode('/', [$root, $path, $fname]);                
                $fcontent = file_get_contents($full_path);

                if ($fcontent) {
                    $this->loadJsonMindMap($fcontent);
                    return true;
                } else {
                    new TMessage('error', 'Mapa não encontrado' );
                    return false;
                }
            }
            else
            {
                new TMessage('error', 'Erro ao recuperar Mapa Mental');
                return false;
            }
        }
    }

    function loadJsonMindMap($jsonstr)
    {
        echo "<script id='userdata'>var userData = '". $jsonstr ."';</script>";
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
        $kityMinder = new TElement('kityminder-editor');
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
