<?php

class CustomPublicMindMap extends TRecord
{
    const TABLENAME  = 'custom_public_mind_map';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max'; // {max, serial}
    
    private $theme;
    private $subject_matter;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('content');
        parent::addAttribute('last_update');
        parent::addAttribute('theme_id');
        parent::addAttribute('subject_matter_id');
    }

    public function set_theme(CustomTheme $object)
    {
        $this->custom_theme = $object;
        $this->custom_theme_id = $object->id;
    }
    
    public function get_theme()
    {
        // loads the associated object
        if (empty($this->theme))
            $this->theme = new CustomTheme($this->theme_id);
    
        // returns the associated object
        return $this->theme;
    }

    public function set_subject_matter(CustomSubjectMatter $object)
    {
        $this->custom_theme = $object;
        $this->custom_theme_id = $object->id;
    }
    
    public function get_subject_matter()
    {
        // loads the associated object
        if (empty($this->subject_matter))
            $this->subject_matter = new CustomSubjectMatter($this->subject_matter_id);
    
        // returns the associated object
        return $this->subject_matter;
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
