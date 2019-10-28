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
    public function __construct()
    {
        parent::__construct();

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
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($quickForm);
        $vbox->add($this->datagrid);

        parent::add($vbox);

    }


  	/**
     * Load the data into the datagrid
     */
    function onReload()
    {
        $this->datagrid->clear();
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->code     = '1';
        $item->fname     = 'Pasta1';
        $item->ftype  = 'DIR';
        $this->datagrid->addItem($item);
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->code     = '2';
        $item->fname     = 'Pasta2';
        $item->ftype  = 'DIR';
        $this->datagrid->addItem($item);
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->code     = '3';
        $item->fname     = 'Mapa Teste';
        $item->ftype  = 'MAP';
        $this->datagrid->addItem($item);
        
        // add an regular object to the datagrid
        $item = new StdClass;
        $item->code     = '4';
        $item->fname     = 'Mapa Penal';
        $item->ftype  = 'MAP';
        $this->datagrid->addItem($item);


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
        $this->onReload();
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
        // new TMessage('info', 'Confirm1 : ' . json_encode($param));

        $item = new StdClass;
        $item->code     = '5';
        $item->fname     = $param['fname'];
        $item->ftype  = 'MAP';
        $this->datagrid->addItem($item);
    }
    

}