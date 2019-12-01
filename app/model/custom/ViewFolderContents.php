    <?php
    
    class ViewFolderContents extends TRecord
    {
        const TABLENAME = 'view_folder_contents';
        const PRIMARYKEY= 'item_id';
        const IDPOLICY =  'max'; // {max, serial}

	    public function __construct($item_id = NULL)
	    {
	        parent::__construct($item_id);
			parent::addAttribute('item_name');
			parent::addAttribute('item_last_update');
			parent::addAttribute('user_id');
			parent::addAttribute('item_type');
			parent::addAttribute('parent_id');
	    }

    }
