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

    private $mindmap_path;
    private $mindmap_name;
    private $mindmap_content;

    public function __construct()
    {


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         // The request is using the POST method
            return;
        }

        
        parent::__construct();

        $is_loaded = $this->loadUserData();

        if ($is_loaded) {    
            $this->includePluginCSS();
            $this->includepluginHTML();
            $this->includepluginJS();

        } else {

            AdiantiCoreApplication::loadPage('MapList');
        }
    }

    public function show($value='')
    {
        # code...
        echo "string";
    }

    function loadUserData()
    {
        if ($_GET)
        {
            $this->mindmap_path = isset($_GET['p'])  ? $_GET['p']  : NULL;
            $this->mindmap_name = isset($_GET['view']) ? $_GET['view'] : NULL;
            
            if ($this->mindmap_path and $this->mindmap_name)
            {
                $root = $_SERVER['DOCUMENT_ROOT'].'/bizumapa/userdata';
                $full_path = implode('/', [$root, $this->mindmap_path, $this->mindmap_name]);                
                $this->mindmap_content = file_get_contents($full_path);

                if ($this->mindmap_content) {
                    $this->loadMindMap();
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

    function saveUserData()
    {


        new TMessage('error', '5888888888888' );
        
        if ($_GET)
        {
            // $this->mindmap_path = isset($_POST['mindmap_path'])  ? $_POST['mindmap_path']  : NULL;
            // $this->mindmap_name = isset($_POST['mindmap_name']) ? $_POST['mindmap_name'] : NULL;
            // $this->mindmap_content = isset($_POST['mindmap_name']) ? $_POST['mindmap_name'] : NULL;
            
            if ($this->mindmap_path and $this->mindmap_name and $this->mindmap_content)
            {
                $root = $_SERVER['DOCUMENT_ROOT'].'/bizumapa/userdata';
                $full_path = implode('/', [$root, $this->mindmap_path, $this->mindmap_name]);

                new TMessage('error', $full_path );

                file_put_contents($full_path, $this->mindmap_content);

                if ($this->mindmap_content) {
                    return true;
                } else {
                    new TMessage('error', 'Mapa não encontrado' );
                    return false;
                }
            }
            else
            {
                // new TMessage('error', 'Erro ao recuperar Mapa Mental');
                return false;
            }
        }
    }

    function loadMindMap()
    {
        echo "<script>
            var mindmap_path = '". $this->mindmap_path ."';
            var mindmap_name = '". $this->mindmap_name ."';
            var mindmap_content = '". $this->mindmap_content ."';
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
