<?php

class CustomFolder extends TRecord
{
    const TABLENAME  = 'custom_folder';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max'; // {max, serial}
    
    private $user;
    private $parent_folder;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('user_id');
        parent::addAttribute('parent_id');
    }

    public function set_user(SystemUser $object)
    {
        $this->user = $object;
        $this->user_id = $object->id;
    }
    
    public function get_user()
    {
        // loads the associated object
        if (empty($this->user))
            $this->user = new SystemUser($this->user_id);
    
        // returns the associated object
        return $this->user;
    }

    public function set_parent_folder(CustomFolder $object)
    {
        $this->parent_folder = $object;
        $this->parent_id = $object->id;
    }
    
    public function get_parent_folder()
    {
        // loads the associated object
        if (empty($this->parent_folder))
            $this->parent_folder = new CustomFolder($this->parent_id);
    
        // returns the associated object
        return $this->parent_folder;
    }

}
