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

        if (TSession::getValue('current_folder_id') == NULL) {
            TSession::setValue('current_folder_name', 'Minhas Pastas');        
            TSession::setValue('current_folder_id', '1');        
        }

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
        // parent::setDefaultOrder('item_type', 'desc');         // defines the default order

        $criteria = new TCriteria;
        $user_id = TSession::getValue('userid');
        $criteria->add(new TFilter('user_id', '=', $user_id));
        $criteria->add(new TFilter('folder_id', '=', TSession::getValue('current_folder_id')));

        parent::setCriteria($criteria);

        // parent::addFilterField('id', '=', 'id'); // filterField, operator, formField
        // parent::addFilterField('name', 'like', 'name'); // filterField, operator, formField
        // parent::addFilterField('theme_id', '=', 'theme_id'); // filterField, operator, formField
        // parent::addFilterField('subject_matter_id', '=', 'subject_matter_id'); // filterField, operator, formField

        // creates a DataGrid
        // TScript::create('$("#my_table tr:eq(0) th:eq(0)").text("My Text");');
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->id = 'my_table';
        
        // creates the datagrid columns
        // $column_id                  = new TDataGridColumn('id', 'ID', 'center', 50);
        $col_item_type = new TDataGridColumn('item_type', 'Tipo', 'center', '15%');
        $col_item_name = new TDataGridColumn('item_name', 'Item', 'left');

        $col_item_type->setTransformer( function($value, $object, $row) {
            $div = new TElement('span');
            if ($object->item_type == 'folder') {
                $div->add('<i class="fa fa-folder-open blue"></i>');
            } else {
                $div->add('<i class="fa fa-sitemap fa-rotate-270"></i>');
            }
            // $div->add($object->item_type);
            return $div;
        });

        // add the columns to the DataGrid
        $this->datagrid->addColumn($col_item_type);
        $this->datagrid->addColumn($col_item_name);

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

        // create VIEW action
        $action_view = new TDataGridAction(array($this, 'onViewItem'));
        $action_view->setLabel('Ver');
        $action_view->setImage('fa:eye blue fa-lg');
        $action_view->setField('item_id');
        $action_view->setField('item_name');
        $action_view->setField('item_type');

        // create EDIT action
        $action_edit = new TDataGridAction(array($this, 'onEditItem'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel('Renomear');
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('item_id');
        $action_edit->setField('item_name');
        $action_edit->setField('item_type');        

        // create COPY action
        $action_copy = new TDataGridAction(array($this, 'onEditItem'));
        $action_copy->setButtonClass('btn btn-default');
        $action_copy->setLabel('Copiar');
        $action_copy->setImage('fa:copy  blue fa-lg');
        $action_copy->setField('item_id');  
        $action_copy->setField('item_name');
        $action_copy->setField('item_type');              
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDeleteItem'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('item_id');

        $action_group = new TDataGridActionGroup('', 'bs:th');
        $action_group->addHeader('Opções');
        $action_group->addAction($action_view);
        $action_group->addAction($action_edit);
        $action_group->addAction($action_copy);

        $action_group->addSeparator();
        $action_group->addHeader('Outras Opções');
        $action_group->addAction($action_del);
        
        // add the actions to the datagrid
        $this->datagrid->addActionGroup($action_group);

    }

    public function createPanel()
    {
        $this->panel = new BootstrapFormBuilder;
        $this->panel->setFormTitle(TSession::getValue('current_folder_name'));
        // $this->panel->setFormTitle('Minhas Pastas');
        $this->panel->addHeaderAction('Nova Pasta', new TAction(array($this, 'onCreateFolder')), 'fa:plus-circle green');
        // $this->$btn->class = 'btn btn-sm btn-primary';
        $this->panel->addHeaderAction('Voltar', new TAction(array($this,'onBackAction')), 'fa:arrow-circle-o-left blue' );

        $this->panel->addFields([$this->datagrid]);
        $this->panel->addFields([$this->pageNavigation]);
    }    

    public function onBackAction($params)
    {

        TSession::setValue('current_folder_id', '1');
        TSession::setValue('current_folder_name', 'Minhas Pastas');        
        AdiantiCoreApplication::loadPage(__CLASS__);
    }

    public function onCreateFolder()
    {

        $form = new TQuickForm('input_form');
        $form->style = 'padding:15px';
        
        $name = new TEntry('name');        
        $form->addQuickField('Nome', $name);        
        $name->addValidation('name', new TRequiredValidator);
        $action = new TAction(array($this, 'onCreateFolderConfirm'));
        $form->addQuickAction('Salvar', $action, 'fa:save green');
        
        // show the input dialog
        new TInputDialog('Nova Pasta', $form);
    }


    public function onCreateFolderConfirm( $params )
    {
        try 
        { 
            TTransaction::open('permission'); // open transaction 

            $folder = new CustomFolder; 
            $folder->name = $params['name']; 
            $folder->user_id = TSession::getValue('userid');
            $folder->parent_id = TSession::getValue('current_folder_id');
            $folder->store();
            
            new TMessage('info', 'Pasta Criada'); 
            TTransaction::close(); // Closes the transaction 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }

        $this->onReload();
    }    


    public function onViewItem($params)
    {
        if (empty($params['item_type'])) {
            return;
        }

        if ($params['item_type'] == 'folder') {
            $this->onViewFolder($params);
        } else {
            $this->onViewMindMap($params);
        }

    }

    public function onViewFolder($params)
    {
        if (empty($params['item_id']) or 
            empty($params['item_name']) or
            empty($params['item_type'])) {

            new TMessage('error', 'Parâmetros inválidos');
            return;
        } 

        TSession::setValue('current_folder_id', $params['item_id']);
        TSession::setValue('current_folder_name', $params['item_name']);
            
        AdiantiCoreApplication::loadPage(__CLASS__);
    }

    public function onViewMindMap($params)
    {

        if (empty($params['item_id'])) {
            new TMessage('error', 'Parâmetros inválidos');
            return;
        } 

        AdiantiCoreApplication::loadPage($class='MindMapPlugin', $method=NULL,
            $parameters=['id' => $params['item_id'] ]);            
    }


    public function onEditItem($params)
    {

        if (empty($params['item_id']) or 
            empty($params['item_name']) or
            empty($params['item_type'])) {

            new TMessage('error', 'Parâmetros inválidos');
            return;
        } 

        $form = new TQuickForm('input_form');
        $form->style = 'padding:15px';
        
        $name = new TEntry('new_name');        
        $name->setValue($params['item_name']);
        $form->addQuickField('Nome', $name);        
        $name->addValidation('new_name', new TRequiredValidator);

        if ($params['item_type'] == 'folder') {
            $callback = array($this, 'onEditFolderConfirm');
        } else {
            $callback = array($this, 'onEditMindMapConfirm');
        }
        
        $action = new TAction($callback, $params);
        $form->addQuickAction('Salvar', $action, 'fa:save green');
        
        new TInputDialog('Renomear Item', $form);
    }


    public function onEditFolderConfirm($params)
    {
        echo var_dump($params);
        return;
        try 
        { 
            TTransaction::open('permission'); // open transaction 

            $folder = new CustomFolder($params['item_id']); 
            $folder->name = $params['name']; 
            $folder->user_id = TSession::getValue('userid');
            $folder->parent_id = TSession::getValue('current_folder_id');
            $folder->store();
            
            new TMessage('info', 'Pasta Criada'); 
            TTransaction::close(); // Closes the transaction 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }

        $this->onReload();
    }

    public function onEditMindMapConfirm($params)
    {
        echo var_dump($params);
        return;
    }    

    public function onDeleteItem($params)
    {
        # code...
    }  


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