<?php

class CustomPrivateMindMapList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->createDatagrid();
        $this->createPanel();
        
        $container = new TVBox;
        $container->style = 'width: 80%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->panel);
        
        parent::add($container);
    }

    public function createDatagrid()
    {
        parent::setDatabase('permission');            // defines the database
        parent::setActiveRecord('ViewFolderContents');   // defines the active record
        parent::setDefaultOrder('item_name', 'asc');         // defines the default order

        $criteria = new TCriteria;
        $user_id = TSession::getValue('userid');
        $criteria->add(new TFilter('user_id', '=', $user_id));
        $criteria->add(new TFilter('folder_id', '=', '1'));
        parent::setCriteria($criteria);

        // parent::addFilterField('id', '=', 'id'); // filterField, operator, formField
        // parent::addFilterField('name', 'like', 'name'); // filterField, operator, formField
        // parent::addFilterField('theme_id', '=', 'theme_id'); // filterField, operator, formField
        // parent::addFilterField('subject_matter_id', '=', 'subject_matter_id'); // filterField, operator, formField

        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        // $column_id                  = new TDataGridColumn('id', 'ID', 'center', 50);
        $col_item_name = new TDataGridColumn('item_name', 'Item', 'left');
        $col_item_type = new TDataGridColumn('item_type', 'Tipo', 'left');

        // add the columns to the DataGrid
        $this->datagrid->addColumn($col_item_name);
        $this->datagrid->addColumn($col_item_type);

        // creates the datagrid column actions
        $order_item_name = new TAction(array($this, 'onReload'));
        $order_item_name->setParameter('order', 'item_name');
        $col_item_name->setAction($order_item_name);
        
        $order_item_type = new TAction(array($this, 'onReload'));
        $order_item_type->setParameter('order', 'item_type');
        $col_item_type->setAction($order_item_type);

        $this->createActions();
        
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());        
    }    

    public function createActions()
    {
        // create EDIT action
        $action_edit = new TDataGridAction(array('CustomPublicMindMapForm', 'onEdit'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('item_id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('item_id');
        $this->datagrid->addAction($action_del);                
    }

    public function createPanel($value='')
    {
        $this->panel = new BootstrapFormBuilder;
        $this->panel->setFormTitle('Minhas Pastas');
        $this->panel->addHeaderAction('Send', new TAction(array($this, 'onCreateFolder')), 'fa:rocket orange');
        $this->panel->addAction('Nova Pasta', new TAction(array($this, 'onCreateFolder')), 'fa:search');
        // $this->$btn->class = 'btn btn-sm btn-primary';
        $this->panel->addFields([$this->datagrid]);
        $this->panel->addFields([$this->pageNavigation]);
    }    

    public function onCreateFolder()
    {
        $form = new TQuickForm('input_form');
        $form->style = 'padding:15px';
        
        $name = new TEntry('name');        
        $form->addQuickField('Nome', $name);        
        $name->addValidation('name', new TRequiredValidator);
        $form->addQuickAction('Salvar', new TAction(array($this, 'onConfirm')), 'fa:save green');
        
        // show the input dialog
        new TInputDialog('Nova Pasta', $form);
    }


    public function onConfirm( $param )
    {
        try 
        { 
            TTransaction::open('permission'); // open transaction 

            $folder = new CustomFolder; 
            $folder->name = $param['name']; 
            $folder->user_id = TSession::getValue('userid');
            $folder->parent_id = 1; //Root
            $folder->store();
            
            new TMessage('info', 'Pasta Criada'); 
            TTransaction::close(); // Closes the transaction 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }
    }    

    // public function onReload($param)
    // {
        // parent::onReload($param);
        // $this->datagrid->clear();

        // $item = new StdClass;
        // $item->code     = '2';
        // $item->name     = 'Julia Haubert';
        // $item->address  = 'Rua Expedicionarios';
        // $item->fone     = '2222-2222';
        // $this->datagrid->addItem($item);
        // $this->onReload($param);

        // try 
        // { 
        //     TTransaction::open('permission'); // open transaction 

        //     $repo = new TRepository('ViewFolderContents');
        //     $criteria = new TCriteria;
        //     $user_id = TSession::getValue('userid');
        //     $criteria->add(new TFilter('f_user_id', '=', $user_id));

        //     $objetos = $repo->load($criteria);

        //     // echo var_dump($objetos);

        //     TTransaction::close(); // Closes the transaction 
        // } 
        // catch (Exception $e) 
        // { 
        //     new TMessage('error', $e->getMessage()); 
        // }
    // }


}


        // $this->form = new BootstrapFormBuilder('form_search_CustomPublicMindMap');
        // $this->form->setFormTitle('Buscar Mapa Mental');
        
        // // create the form fields
        // $id = new TEntry('id');
        // $name = new TEntry('name');
        // $filter = new TCriteria;
        // $filter->add(new TFilter('id', '<', '0'));
        // $theme_id = new TDBCombo('theme_id', 'permission', 'CustomTheme', 'id', 'name', 'name');
        // $subject_matter_id = new TDBCombo('subject_matter_id', 'permission', 'CustomSubjectMatter', 'id', 'name', 'name', $filter);
        
        // // add the fields
        // $this->form->addFields( [new TLabel('ID')], [$id] );
        // $this->form->addFields( [new TLabel('Mapa')], [$name] );
        // $this->form->addFields( [new TLabel('Matéria')], [$theme_id] );
        // $this->form->addFields( [new TLabel('Assunto')], [$subject_matter_id] );

        // $id->setSize('30%');
        // $name->setSize('70%');
        // $theme_id->setSize('70%');
        // $subject_matter_id->setSize('70%');

        // $theme_id->enableSearch();
        // $theme_id->setChangeAction( new TAction( array($this, 'onThemeChange' )) );
        // $subject_matter_id->enableSearch();
        
        // // keep the form filled during navigation with session data
        // $this->form->setData( TSession::getValue('CustomPublicMindMap_filter_data') );
        
        // // add the search form actions
        // $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        // $btn->class = 'btn btn-sm btn-primary';
        // $this->form->addAction('Novo Mapa Público',  new TAction(array('CustomPublicMindMapForm', 'onEdit')), 'bs:plus-sign green');