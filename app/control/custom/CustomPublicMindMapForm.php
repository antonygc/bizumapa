<?php

class CustomPublicMindMapForm extends TPage
{
    protected $form;
    // protected $program_list;
    
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_CustomPublicMindMap');
        $this->form->setFormTitle('Configurar Mapa Público');

        // create the form fields
        $id = new TEntry('id');
        $name = new TEntry('name');
        $filter = new TCriteria;
        $filter->add(new TFilter('id', '<', '0'));
        $theme_id = new TDBCombo('theme_id', 'permission', 'CustomTheme', 'id', 'name', 'name');
        $subject_matter_id = new TDBCombo('subject_matter_id', 'permission', 'CustomSubjectMatter', 'id', 'name', 'name', $filter);
        
        $id->setSize('30%');
        $name->setSize('70%');
        $theme_id->setSize('70%');
        $subject_matter_id->setSize('70%');

        $id->setEditable(false);
        $name->addValidation('Nome', new TRequiredValidator);
        $theme_id->setChangeAction( new TAction( array($this, 'onThemeChange' )) );
        $subject_matter_id->enableSearch();
        $theme_id->enableSearch();
        
        $this->form->addFields( [new TLabel('ID')], [$id]);
        $this->form->addFields( [new TLabel('Mapa')], [$name]);
        $this->form->addFields( [new TLabel('Matéria')], [$theme_id]);
        $this->form->addFields( [new TLabel('Assunto')], [$subject_matter_id]);
                        
        $btn = $this->form->addAction( _t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o' );
        $btn->class = 'btn btn-sm btn-primary';
        
        $this->form->addAction( _t('Clear'), new TAction(array($this, 'onEdit')),  'fa:eraser red' );
        $this->form->addAction( _t('Back'), new TAction(array('CustomPublicMindMapList','onReload')),  'fa:arrow-circle-o-left blue' );

        $container = new TVBox;
        $container->style = 'width:100%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CustomPublicMindMapList'));
        $container->add($this->form);
        
        parent::add($container);
    }

   
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    public static function onSave($param)
    {
        try
        {
            // open a transaction with database 'permission'
            TTransaction::open('permission');
            
            // get the form data into an active record System_group
            $object = new CustomPublicMindMap;
            $object->fromArray( $param );
            $object->store();
            // $object->clearParts();
            
            $data = new stdClass;
            $data->id = $object->id;
            TForm::sendData('form_CustomPublicMindMap', $data);
            
            TTransaction::close(); // close the transaction
            new TMessage('info', _t('Record saved')); // shows the success message

            AdiantiCoreApplication::loadPage('CustomPublicMindMapList');

        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                // get the parameter $key
                $key=$param['key'];
                
                // open a transaction with database 'permission'
                TTransaction::open('permission');
                
                // instantiates object System_group
                $object = new CustomPublicMindMap($key);
                
                // fill the form with the active record data
                $this->form->setData($object);
                
                // close the transaction
                TTransaction::close();
                
            }
            else
            {
                $this->form->clear();
                // TSession::setValue('program_list', null);
            }
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    

public static function onThemeChange($param)
    {
        try
        {
            TTransaction::open('permission');
            if (!empty($param['theme_id']))
            {
                $criteria = TCriteria::create( ['theme_id' => $param['theme_id'] ] );
                
                // formname, field, database, model, key, value, ordercolumn = NULL, criteria = NULL, startEmpty = FALSE
                TDBCombo::reloadFromModel('form_CustomPublicMindMap', 'subject_matter_id', 'permission', 'CustomSubjectMatter', 'id', '{name}', 'name', $criteria, TRUE);
            }
            else
            {
                TCombo::clearField('form_CustomPublicMindMap', 'subject_matter_id');
            }
            
            TTransaction::close();
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
    
   
}
