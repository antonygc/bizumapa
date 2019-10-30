<?php
/**
 * CommonPage
 *
 * @version    1.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class MapList extends TPage
{

	private $current_wd;

    public function __construct()
    {
        parent::__construct();


        echo '<script>var orderNo = 2;</script>';

        $iframe = new TElement('iframe');
        $iframe->id = "iframe_external";
        $iframe->src = "/bizumapa/filemanager.php";
        $iframe->frameborder = "0";
        $iframe->scrolling = "auto";
        $iframe->width = "100%";
        $iframe->height = "700px";
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($iframe);

        parent::add($vbox);

        #require 'filemanager.php';

        return;

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        
        // create the datagrid columns
        $code     = new TDataGridColumn('code',    'Code',    'right',  '10%');
        $fname    = new TDataGridColumn('fname',    'Nome',    'center',   '30%');
        $ftype    = new TDataGridColumn('ftype', 'Tipo', 'left',   '70%');
        
        #$telephone->setDataProperty('hiddable', 400);
        
        // add the columns to the datagrid
        #$this->datagrid->addColumn($code);
        $this->datagrid->addColumn($fname);
        $this->datagrid->addColumn($ftype);
        #$this->datagrid->addColumn($telephone);
        
        // creates two datagrid actions
        $action1 = new TDataGridAction(array($this, 'onView'));
        $action1->setLabel('View fname');
        $action1->setImage('fa:search #7C93CF');
        $action1->setField('fname');
        
        $action2 = new TDataGridAction(array($this, 'onDelete'));
        $action2->setLabel('Try to delete');
        $action2->setImage('bs:remove red');
        $action2->setField('code');
        
        $action3 = new TDataGridAction(array($this, 'onView'));
        $action3->setLabel('View ftype');
        $action3->setImage('bs:hand-right green');
        $action3->setField('ftype');
        
        $action_group = new TDataGridActionGroup('', 'bs:th');
        
        #$action_group->addHeader('Opções');
        $action_group->addAction($action1);
        $action_group->addAction($action2);
        $action_group->addSeparator();
        #$action_group->addHeader('Another Options');
        $action_group->addAction($action3);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);
        
        // creates the datagrid model
        $this->datagrid->createModel();
        
        $action = new TAction( [$this, 'onInputDialog'] );
        $quickForm = new TQuickForm();
        $quickForm->addQuickAction('Criar Pasta', $action, 'fa:folder green');
        
        $wrapper = new TElement('h2');
        $wrapper->add('Navegação');

        $hbox = new THBox;
        $hbox->add($wrapper);
        $hbox->add($quickForm);

        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($hbox);
        $vbox->add($this->datagrid);

        parent::add($vbox);

    }


  	/**
     * Load the data into the datagrid
     */
    function onReload()
    {
    	// echo json_encode($_SESSION);
        try
        {
            TTransaction::open('communication');
            $wd = new WorkingDirectory($_SESSION['bizumapa']['userid']);
            TTransaction::close();
        }
        catch (Exception $e)
        {
        	return;
            // new TMessage('error', $e->getMessage());
        }

        $this->datagrid->clear();

        // if (is_null($wd)) {
        // 	new TMessage('error', 'nulo');
        // } else {
        // 	new TMessage('error', 'nao nulo');
        // }
        
        $wd = json_decode($wd->wd_content);

        foreach ($wd as $index => $file) {
			// echo var_dump($key), var_dump($value);        	
			$item = new StdClass;
	        $item->code  = $file->id;
	        $item->fname = $file->name;
	        $item->ftype = ($file->type=='DIR') ? 'Diretório' : 'Mapa Mental' ;
	        $this->datagrid->addItem($item);
        }
    }

    /**
     * method onDelete()
     * Executed when the user clicks at the delete button
     */
    function onDelete($param)
    {
        // get the parameter and shows the message
        $key=$param['key'];
        new TMessage('error', "The register $key may not be deleted");
    }
    
    /**
     * method onView()
     * Executed when the user clicks at the view button
     */
    function onView($param)
    {
        // get the parameter and shows the message
        $key=$param['key'];
        #new TMessage('info', "The information is : $key");

		AdiantiCoreApplication::openPage('MindMapPlugin', 'onLoad', array("foo"=>"bar"));
    }
    
    /**
     * shows the page
     */
    function show()
    {
        #$this->onReload();
        parent::show();
    }

    public function onInputDialog( $param )
    {

        $form = new TQuickForm('input_form');
        $form->style = 'padding:20px';
        $form->addQuickField('Nome:', new TEntry('fname'));
        $form->addQuickAction('Criar', new TAction([$this, 'onConfirm']), 'fa:save green');

        new TInputDialog('Criar Pasta', $form);
    }

    /**
     * Show the input dialog data
     */
    public function onConfirm( $param )
    {
		try 
        { 
	        TTransaction::open('communication'); // open transaction 

			$content = new StdClass;
			$content->id = uniqid();
			$content->name = $param['fname'];
			$content->type = 'DIR';
			$content->children = [];


	        $wd = new WorkingDirectory();
	        $wd->wd_content = json_encode([$content]);
	        $wd->system_user_id = $_SESSION['bizumapa']['userid'];
	        $wd->store();
                     
            new TMessage('info', 'Pasta criada com sucesso'); 
            TTransaction::close(); // Closes the transaction 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        } 

    }

}