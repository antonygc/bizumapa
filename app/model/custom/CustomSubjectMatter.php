<?php

class CustomSubjectMatter extends TRecord
{
    const TABLENAME  = 'custom_subject_matter';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('custom_theme_id');
    }

    public function getCustomTheme()
    {

        $repository = new TRepository('CustomTheme');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('id', '=', $this->custom_theme_id));

        return $repository->load($criteria)[0]->data;
    }

    /**
     * Reset aggregates
     */
    // public function clearParts()
    // {
    //     // delete the related objects
    //     SystemGroupProgram::where('system_group_id', '=', $this->id)->delete();
    // }
    
    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    // public function delete($id = NULL)
    // {
    //     // delete the related System_groupSystem_program objects
    //     $id = isset($id) ? $id : $this->id;
        
    //     SystemGroupProgram::where('system_group_id', '=', $id)->delete();
    //     SystemUserGroup::where('system_group_id', '=', $id)->delete();
        
    //     // delete the object itself
    //     parent::delete($id);
    // }
}
