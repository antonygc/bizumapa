    <?php
    
    class ViewFolderContents extends TRecord
    {
        const TABLENAME = 'view_folder_contents';
        const PRIMARYKEY= 'f_id';
        const IDPOLICY =  'max'; // {max, serial}

	    public function __construct($f_id = NULL)
	    {
	        parent::__construct($f_id);
			parent::addAttribute('f_name');
			parent::addAttribute('f_parent_id');
			parent::addAttribute('f_user_id');
			parent::addAttribute('pmm_id');
			parent::addAttribute('pmm_name');
			parent::addAttribute('pmm_user_id');
			parent::addAttribute('pmm_folder_id');
	    }

    }