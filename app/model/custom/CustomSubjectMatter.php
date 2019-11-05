<?php

class CustomSubjectMatter extends TRecord
{
    const TABLENAME  = 'custom_subject_matter';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max'; // {max, serial}
    
    private $theme;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('theme_id');
    }

    public function set_custom_theme(CustomTheme $object)
    {
        $this->theme = $object;
        $this->theme_id = $object->id;
    }
    
    public function get_custom_theme()
    {
        // loads the associated object
        if (empty($this->theme))
            $this->theme = new CustomTheme($this->theme_id);
    
        // returns the associated object
        return $this->theme;
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
