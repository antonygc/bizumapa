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
            TSession::setValue('current_folder_id', '1');        
            TSession::setValue('current_folder_name', 'Minhas Pastas');        
            TSession::setValue('current_folder_parent_id', NULL);        
        }

        parent::__construct();

        $this->createSearchButton();
        $this->createDatagrid();
        $this->createPanel();
        
        $container = new TVBox;
        $container->style = 'width: 80%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->panel);
        
        parent::add($container);

    }

    public function createSearchButton()
    {

        $q = (!empty($_REQUEST['q'])) ? $_REQUEST['q'] : TSession::getValue('q') ;

        $tpl = '
            <div class="input-group sidebar-form">
                <input type="text" name="q" value="'. $q .'" class="form-control" placeholder="Pesquisar meus mapas...">
                <span class="input-group-btn">
                    <!-- Previne abrir janela com mapa selecionado anteriormente -->
                    <input type="hidden" name="method" value="">
                    <button type="submit" id="search-btn" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>';

        $this->search_btn = new TPanelGroup('');
        $this->search_btn->style = 'border-color:#fff';
        $this->search_btn->add($tpl);
    }


    public function createDatagrid()
    {
        parent::setDatabase('permission');            // defines the database
        parent::setActiveRecord('ViewFolderContents');   // defines the active record
        parent::setDefaultOrder('item_name', 'asc');         // defines the default order

        $user_id = TSession::getValue('userid');

        $criteria = new TCriteria;

        function add_criteria($criteria, $user_id, $q)
        {            
            $subsel = "(SELECT id FROM custom_private_mind_map 
                WHERE user_id='$user_id' and content LIKE '%$q%')";
            $criteria->add(new TFilter('item_id', 'IN', $subsel));
            $criteria->add(new TFilter('item_type', '=', 'mindmap'));
        }

        if (empty($_REQUEST['q'])) {

            if (empty(TSession::getValue('q'))) {
                
                $criteria->add(new TFilter('parent_id', '=', TSession::getValue('current_folder_id')));

            } else {

                add_criteria($criteria, $user_id, TSession::getValue('q'));
            }

        } else {

            TSession::setValue('q', $_REQUEST['q']);
            add_criteria($criteria, $user_id, TSession::getValue('q'));
        }

        $criteria->add(new TFilter('user_id', '=', $user_id));

        parent::setCriteria($criteria);

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
        $action_view->setField('parent_id');

        // create EDIT action
        $action_edit = new TDataGridAction(array($this, 'onEditMindMap'));
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel('Editar');
        $action_edit->setImage('fa:sitemap fa-rotate-270 blue fa-lg');
        $action_edit->setField('item_id');
        $action_edit->setField('item_name');
        $action_edit->setField('item_type');        
        $action_edit->setField('parent_id');
        $action_edit->setDisplayCondition( array($this, 'isMindMap') );

        // create RENAME action
        $action_rename = new TDataGridAction(array($this, 'onRenameItem'));
        $action_rename->setButtonClass('btn btn-default');
        $action_rename->setLabel('Renomear');
        $action_rename->setImage('fa:pencil-square-o blue fa-lg');
        $action_rename->setField('item_id');
        $action_rename->setField('item_name');
        $action_rename->setField('item_type');        
        $action_rename->setField('parent_id');

        // create COPY action
        $action_copy = new TDataGridAction(array($this, 'onCopyItem'));
        $action_copy->setButtonClass('btn btn-default');
        $action_copy->setLabel('Copiar');
        $action_copy->setImage('fa:copy  blue fa-lg');
        $action_copy->setField('item_id');  
        $action_copy->setField('item_name');
        $action_copy->setField('item_type');              
        $action_copy->setField('parent_id');
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDeleteItem'));
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('item_id');
        $action_del->setField('item_name');
        $action_del->setField('item_type');              
        $action_del->setField('parent_id');

        $action_group = new TDataGridActionGroup('', 'bs:th');
        $action_group->addHeader('Opções');
        $action_group->addAction($action_view);
        $action_group->addAction($action_edit);
        $action_group->addAction($action_rename);
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

        $this->panel->addFields([$this->search_btn]);
        $this->panel->addFields([$this->datagrid]);
        $this->panel->addFields([$this->pageNavigation]);
    }    

    public function onBackAction($params)
    {

        TSession::setValue('q', null);  

        $curr_parent_id = TSession::getValue('current_folder_parent_id');  

        if (!$curr_parent_id){
            // Já está na raiz
            AdiantiCoreApplication::loadPage(__CLASS__);
            return;
        }

        try 
        { 
            TTransaction::open('permission'); // open transaction 

            $criteria = new TCriteria;
            $criteria->add(new TFilter('id', '=', $curr_parent_id));

            $repository = new TRepository('CustomFolder'); 
            $folders = $repository->load($criteria); 

            $folder = $folders[0];

            TSession::setValue('current_folder_id', $folder->id);
            TSession::setValue('current_folder_name', $folder->name);              
            TSession::setValue('current_folder_parent_id', $folder->parent_id);              
            
            TTransaction::close(); // Closes the transaction 
        } 
        catch (Exception $e) 
        { 
            echo var_dump($e);
            
            new TMessage('error', var_dump($e)); 
            // new TMessage('error', $e->getMessage()); 
        }

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

        AdiantiCoreApplication::loadPage(__CLASS__);
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
            empty($params['item_type']) or
            empty($params['parent_id'])) {

            new TMessage('error', 'Parâmetros inválidos');
            return;
        } 

        TSession::setValue('current_folder_id', $params['item_id']);
        TSession::setValue('current_folder_name', $params['item_name']);
        TSession::setValue('current_folder_parent_id', $params['parent_id']);
            
        AdiantiCoreApplication::loadPage(__CLASS__);
    }

    public function isMindMap($item)
    {
        if ($item->item_type == 'mindmap') {
            return true;
        }
        return false;
    }

    public function onViewMindMap($params)
    {

        if (empty($params['item_id'])) {
            new TMessage('error', 'Parâmetros inválidos');
            return;
        } 

        AdiantiCoreApplication::openPage($class='MindMapPlugin', $method='view',
            $parameters=['id' => $params['item_id'], 'scope' => 'private']);            
    }

    public function onEditMindMap($params)
    {
        if (empty($params['item_id'])) {
            new TMessage('error', 'Parâmetros inválidos');
            return;
        } 

        AdiantiCoreApplication::openPage($class='MindMapPlugin', $method=NULL,
            $parameters=['id' => $params['item_id'], 'scope' => 'private']);  
    }

    public function onRenameItem($params)
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
        try 
        { 
            TTransaction::open('permission'); // open transaction 

            $folder = new CustomFolder($params['item_id']); 
            $folder->name = $params['new_name']; 
            $folder->user_id = TSession::getValue('userid');
            $folder->parent_id = TSession::getValue('current_folder_id');
            $folder->store();
            
            new TMessage('info', 'Pasta renomeada com sucesso!'); 
            TTransaction::close(); // Closes the transaction 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }

        AdiantiCoreApplication::loadPage(__CLASS__);
    }

    public function onEditMindMapConfirm($params)
    {
        try 
        { 
            TTransaction::open('permission'); // open transaction 

            $mindmap = new CustomPrivateMindMap($params['item_id']); 
            $mindmap->name = $params['new_name']; 
            $mindmap->content = $mindmap->content;
            $mindmap->user_id = TSession::getValue('userid');
            $mindmap->parent_id = TSession::getValue('current_folder_id');
            $mindmap->store();
            
            new TMessage('info', 'Mapa Mental renomeado com sucesso!'); 
            TTransaction::close(); // Closes the transaction 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }

        AdiantiCoreApplication::loadPage(__CLASS__);
    }    

    public function onCopyItem($params)
    {
    }

    public function onDeleteItem($params)
    {

        if (empty($params['item_id']) or 
            empty($params['item_type'])) {

            new TMessage('error', 'Parâmetros inválidos');
            return;
        } 

        $action = new TAction(array($this, 'onConfirmDelete'));
        $action->setParameters($params);
        
        new TQuestion('Deseja realmente excluir o item ?', $action);

    }          

    public function onConfirmDelete($params)
    {
        try
        {
            TTransaction::open('permission');

            if ($params['item_type'] == 'folder') {
                $object = new CustomFolder($params['item_id']);
            } else {
                $object = new CustomPrivateMindMap($params['item_id']);
            }

            $object->delete();      

            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            return;
        }

        new TMessage('info', 'Itens excuídos com sucesso!'); 
        AdiantiCoreApplication::loadPage(__CLASS__);

    }     


}

