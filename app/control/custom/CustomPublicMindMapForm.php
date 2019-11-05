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
        $id   = new TEntry('id');
        $name = new TEntry('name');
        $theme_id = new TDBSeekButton('theme_id', 'permission', 'form_CustomPublicMindMap', 'CustomTheme', 'name', 'theme_id', 'theme_name');
        $theme_name = new TEntry('theme_name');
        $theme_id->setSize('1');
        // $theme_name->setSize('calc(100% - 200px)');
        $theme_name->setEditable(FALSE);
        
        // define the sizes
        $id->setSize('30%');
        $name->setSize('70%');

        // validations
        $name->addValidation('Nome', new TRequiredValidator);
        $theme_name->addValidation('theme_name', new TRequiredValidator);
        
        // outras propriedades
        $id->setEditable(false);
        
        $hbox = new THBox;
        $hbox->add($theme_name, 'display:initial');
        $hbox->add($theme_id);

        $this->form->addFields( [new TLabel('ID')], [$id]);
        $this->form->addFields( [new TLabel(_t('Name'))], [$name]);
        $this->form->addFields( [new TLabel('Matéria')], [$hbox]);
        
        $vbox = new TVBox;
        $vbox->style='width:100%';
        $vbox->add( $hbox );
        // $vbox->add(TPanelGroup::pack('', $this->program_list));
                
        $btn = $this->form->addAction( _t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o' );
        $btn->class = 'btn btn-sm btn-primary';
        
        $this->form->addAction( _t('Clear'), new TAction(array($this, 'onEdit')),  'fa:eraser red' );
        $this->form->addAction( _t('Back'), new TAction(array('CustomPublicMindMapList','onReload')),  'fa:arrow-circle-o-left blue' );
        
        $this->form->addField($theme_id);
        $this->form->addField($theme_name);
        
        $container = new TVBox;
        $container->style = 'width:90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'CustomPublicMindMapList'));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Remove program from session
     */
    public static function deleteProgram($param)
    {
        $programs = TSession::getValue('program_list');
        unset($programs[ $param['id'] ]);
        TSession::setValue('program_list', $programs);
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

                $object->theme_id = $object->theme->id;
                $object->theme_name = $object->theme->name;
                
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
    
    /**
     * Add a program
     */
    public static function onAddProgram($param)
    {
        try
        {
            $id = $param['program_id'];
            $program_list = TSession::getValue('program_list');
            
            if (!empty($id) AND empty($program_list[$id]))
            {
                TTransaction::open('permission');
                $program = SystemProgram::find($id);
                $program_list[$id] = $program->toArray();
                TSession::setValue('program_list', $program_list);
                TTransaction::close();
                
                $i = new TElement('i');
                $i->{'class'} = 'fa fa-trash red';
                $btn = new TElement('a');
                $btn->{'onclick'} = "__adianti_ajax_exec(\'class=SystemGroupForm&method=deleteProgram&id=$id\');$(this).closest(\'tr\').remove();";
                $btn->{'class'} = 'btn btn-default btn-sm';
                $btn->add($i);
                
                $tr = new TTableRow;
                $tr->{'class'} = 'tdatagrid_row_odd';
                $tr->{'style'} = 'width: 100%;display: inline-table;';
                $cell = $tr->addCell( $btn );
                $cell->{'style'}='text-align:center';
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '5%';
                $cell = $tr->addCell( $program->id );
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '10%';
                $cell = $tr->addCell( $program->name );
                $cell->{'class'}='tdatagrid_cell';
                $cell->{'width'} = '85%';
                
                TScript::create("tdatagrid_add_serialized_row('program_list', '$tr');");
                
                $data = new stdClass;
                $data->program_id = '';
                $data->program_name = '';
                TForm::sendData('form_CustomPublicMindMap', $data);
            }
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
        }
    }
}
