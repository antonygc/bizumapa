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

    // public function onBeforeDelete($object)
    public function delete($id = NULL)
    {

        $object = (object) $this->toArray();

        $children = CustomFolder::getChildren($object->id);
        rsort($children);

        $maps_parents = $children + [$object->id];

        CustomPrivateMindMap::where('folder_id', 'IN', $maps_parents )->delete();

        // TODO: ERRO DE FK
        // CustomFolder::where('id', 'IN', $children)->delete();

        $children = $children + [$object->id];

        foreach ($children as $i => $v) {
            CustomFolder::where('id', '=', $v)->delete();
        }

    }

    public static function getChildren($id)
    {

        $conn = TTransaction::get(); // obtém a conexão
        
        $_id = (int) $id;

        // https://stackoverflow.com/questions/28363893/mysql-select-recursive-get-all-child-with-multiple-level

        $stmt = "
            SELECT GROUP_CONCAT(lv SEPARATOR ',') as children 
            FROM (
                SELECT @pv:=(SELECT GROUP_CONCAT(id SEPARATOR ',') FROM custom_folder 
                WHERE FIND_IN_SET(parent_id, @pv)) AS lv FROM custom_folder 
            JOIN
                (SELECT @pv:=". $_id . ") tmp) a;" ;

        $sth = $conn->prepare($stmt);
        
        $sth->execute(array(3,12));
        $result = $sth->fetchAll();
            
        $children = $result[0]['children'];

        if (!is_null($children)) {
            $children = explode(',', $children);
        }

        return (array) $children; 
    }    






}
