<?php
/**
 * SystemPreference
 *
 * @version    1.0
 * @package    model
 * @subpackage admin
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class WorkingDirectory extends TRecord
{
    const TABLENAME  = 'working_directory';
    const PRIMARYKEY = 'wd_id';
    const IDPOLICY   = 'max'; // {max, serial}
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('wd_content');
        parent::addAttribute('system_user_id');
    }
    
    public static function getContent($id)
    {
        $wd = new WonkingDirectory($id);
        return $wd->wd_content;
    }
    
    /**
     * Altera uma preferência
     * @param $id  Id da preferência
     * @param $value Valor da preferência
     */
    public static function setPreference($id, $value)
    {
        $preference = SystemPreference::find($id);
        if ($preference)
        {
            $preference->value = $value;
            $preference->store();
        }
    }
    
    /**
     * Retorna um array com todas preferências
     */
    public static function getAllPreferences()
    {
        $rep = new TRepository('SystemPreference');
        $objects = $rep->load(new TCriteria);
        $dataset = array();
        
        if ($objects)
        {
            foreach ($objects as $object)
            {
                $property = $object->id;
                $value    = $object->value;
                $dataset[$property] = $value;
            }
        }
        return $dataset;
    }
}
