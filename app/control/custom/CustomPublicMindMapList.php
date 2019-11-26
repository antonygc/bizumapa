<?php

class CustomPublicMindMapList extends TStandardList
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

        $this->createSearchForm();
        $this->createDatagrid();
        
        $panel = new TPanelGroup;
        $panel->add($this->datagrid);
        $panel->addFooter($this->pageNavigation);
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($panel);
        
        $subsc_ok = CustomSubscriptionInterface::checkSubscription();

        if (!$subsc_ok) {
            AdiantiCoreApplication::loadPage('CustomSubscriptionForm');
            return;
        }

        parent::add($container);
    }

    public function createSearchForm()
    {
        parent::setDatabase(DEFAULT_DB);            
        parent::setActiveRecord('CustomPublicMindMap');   
        parent::setDefaultOrder('id', 'asc');         
        parent::addFilterField('id', '=', 'id'); 
        parent::addFilterField('name', 'like', 'name'); 
        parent::addFilterField('theme_id', '=', 'theme_id'); 
        parent::addFilterField('subject_matter_id', '=', 'subject_matter_id');
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_search_CustomPublicMindMap');
        $this->form->setFormTitle('Buscar Mapa Mental');
        
        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $filter = new TCriteria;
        $filter->add(new TFilter('id', '<', '0'));
        $theme_id = new TDBCombo('theme_id', DEFAULT_DB, 'CustomTheme', 'id', 'name', 'name');
        $subject_matter_id = new TDBCombo('subject_matter_id', DEFAULT_DB, 'CustomSubjectMatter', 'id', 'name', 'name', $filter);
        
        // add the fields
        // $this->form->addFields( [new TLabel('ID')], [$id] );
        // $this->form->addFields( [new TLabel('Mapa Mental')], [$name] );
        $this->form->addFields( [new TLabel('Matéria')], [$theme_id] );
        $this->form->addFields( [new TLabel('Assunto')], [$subject_matter_id] );

        $id->setSize('30%');
        $name->setSize('70%');
        $theme_id->setSize('70%');
        $subject_matter_id->setSize('70%');

        $theme_id->enableSearch();
        $theme_id->setChangeAction( new TAction( array($this, 'onThemeChange' )) );
        $subject_matter_id->enableSearch();
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('CustomPublicMindMap_filter_data') );
        
        // add the search form actions
        $btn = $this->form->addAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';

        if (in_array('1', TSession::getValue('usergroupids'))) {
        $this->form->addAction('Novo Mapa Público',  new TAction(array('CustomPublicMindMapForm', 'onConfigure')), 'bs:plus-sign green');
        }
    }


    public function createDatagrid()
    {
        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        $this->datagrid->datatable = 'true';
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);
        
        // creates the datagrid columns
        $column_id                  = new TDataGridColumn('id', 'ID', 'center');
        $column_name                = new TDataGridColumn('name', 'Mapa Mental', 'left', '30%');
        $column_theme_name          = new TDataGridColumn('theme->name', 'Matéria', 'left');
        $column_subject_matter_name = new TDataGridColumn('subject_matter->name', 'Assunto', 'left');

        // add the columns to the DataGrid

        if (in_array('1', TSession::getValue('usergroupids'))) {
            $this->datagrid->addColumn($column_id);
        }

        $this->datagrid->addColumn($column_name);
        $this->datagrid->addColumn($column_theme_name);
        $this->datagrid->addColumn($column_subject_matter_name);

        // creates the datagrid column actions
        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);
        
        $order_name = new TAction(array($this, 'onReload'));
        $order_name->setParameter('order', 'name');
        $column_name->setAction($order_name);

        $this->createActions();
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
    }

    public function createActions()
    {


        $action_view = new TDataGridAction(array($this, 'onView'));
        $action_view->setButtonClass('btn btn-default');
        $action_view->setLabel('Ver');
        $action_view->setImage('fa:eye blue fa-lg');
        $action_view->setField('id');
        $this->datagrid->addAction($action_view);

        if (in_array('1', TSession::getValue('usergroupids'))) {
            // user is admin

            $action_config = new TDataGridAction(array('CustomPublicMindMapForm', 'onConfigure'));
            $action_config->setButtonClass('btn btn-default');
            $action_config->setLabel('Configurar');
            $action_config->setImage('fa:gear blue fa-lg');
            $action_config->setField('id');
            $this->datagrid->addAction($action_config);

            $action_edit = new TDataGridAction(array($this, 'onEdit'));
            $action_edit->setButtonClass('btn btn-default');
            $action_edit->setLabel(_t('Edit'));
            $action_edit->setImage('fa:pencil-square-o blue fa-lg');
            $action_edit->setField('id');
            $this->datagrid->addAction($action_edit);
        
            // create DELETE action
            $action_del = new TDataGridAction(array($this, 'onDelete'));
            $action_del->setButtonClass('btn btn-default');
            $action_del->setLabel(_t('Delete'));
            $action_del->setImage('fa:trash-o red fa-lg');
            $action_del->setField('id');
            $this->datagrid->addAction($action_del);
        } else {

            $action_copy = new TDataGridAction(array($this, 'onCopy'));
            $action_copy->setButtonClass('btn btn-default');
            $action_copy->setLabel('Copiar para Meus Mapas');
            $action_copy->setImage('fa:copy blue fa-lg');
            $action_copy->setField('id');
            $action_copy->setField('name');
            $this->datagrid->addAction($action_copy);
        }
    }

    public static function onThemeChange($param)
    {
        try
        {
            TTransaction::open(DEFAULT_DB);
            if (!empty($param['theme_id']))
            {
                $criteria = TCriteria::create( ['theme_id' => $param['theme_id'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('form_search_CustomPublicMindMap', 'subject_matter_id', DEFAULT_DB, 'CustomSubjectMatter', 'id', '{name}', 'name', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form__search_CustomPublicMindMap', 'subject_matter_id');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }    

    public function onView($params)
    {
        // Abre na mesma aba
        AdiantiCoreApplication::loadPage($class='MindMapPlugin', $method='view',
        // Abre em outra aba
        // AdiantiCoreApplication::openPage($class='MindMapPlugin', $method=NULL,
            $parameters=['id' => $params['id'], 'scope' => 'public']); 
    }

    public function onEdit($params)
    {
        // Abre na mesma aba
        AdiantiCoreApplication::loadPage($class='MindMapPlugin', $method=NULL,
        // Abre em outra aba
        // AdiantiCoreApplication::openPage($class='MindMapPlugin', $method=NULL,
            $parameters=['id' => $params['id'], 'scope' => 'public']); 
    }

    public function onCopy($params)
    {
        // echo var_dump($params);
        if (empty($params['id']) or empty($params['name'])) {
            new TMessage('error', 'Parâmetros inválidos');
            return;
        }

        $form = new TQuickForm('input_form');
        $form->style = 'padding:20px';

        $name = new TEntry('name');
        $name->setValue($params['name']);

        $filter = new TCriteria;
        $filter->add(new TFilter('user_id', '=', TSession::getValue('userid')));
        $folder_id = new TDBCombo('folder_id', DEFAULT_DB, 'CustomFolder', 'id', 'name', 'name', $filter);
        $folder_id->enableSearch();

        $form->addQuickField('Nome:', $name);        
        $form->addQuickField('Pasta de Destino:', $folder_id);        
        $folder_id->addValidation('folder_id', new TRequiredValidator);
        $action = new TAction([$this, 'onConfirm'], ['id'=>$params['id']]);
        $form->addQuickAction('Salvar', $action, 'fa:save green');
        
        // show the input dialog
        new TInputDialog('Copiar para Meus Mapas', $form);
    }

    public function onConfirm($params)
    {
        if (empty($params['id'])) {
            new TMessage('error', 'Parâmetros incorretos');
            return;
        }        

        if (empty($params['folder_id'])) {
            $params['folder_id'] = '1'; // ROOT
        }

        try 
        { 
            TTransaction::open(DEFAULT_DB); // open transaction 

            $public_mindmap = new CustomPublicMindMap((int) $params['id']);

            $mindmap = new CustomPrivateMindMap; 
            $mindmap->name = $params['name']; 
            $mindmap->content = $public_mindmap->content;
            $mindmap->user_id = TSession::getValue('userid');
            $mindmap->folder_id = $params['folder_id']; ;
            $mindmap->store();
            
            new TMessage('info', 'Mapa copiado com sucesso'); 
            TTransaction::close(); // Closes the transaction 
        } 
        catch (Exception $e) 
        { 
            new TMessage('error', $e->getMessage()); 
        }

    }

}
