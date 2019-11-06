<?php

class CustomPrivateMindMap extends TRecord
{
    const TABLENAME  = 'custom_private_mind_map';
    const PRIMARYKEY = 'id';
    const IDPOLICY   = 'max'; // {max, serial}
    
    private $user;
    private $folder;

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('name');
        parent::addAttribute('content');
        parent::addAttribute('user_id');
        parent::addAttribute('folder_id');
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

    public function set_folder(CustomFolder $object)
    {
        $this->folder = $object;
        $this->folder_id = $object->id;
    }
    
    public function get_folder()
    {
        // loads the associated object
        if (empty($this->folder))
            $this->folder = new CustomFolder($this->folder_id);
    
        // returns the associated object
        return $this->folder;
    }

}
