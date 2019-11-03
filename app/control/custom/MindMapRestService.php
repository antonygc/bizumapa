<?php

class MindMapRestService extends AdiantiRecordService
{
    // const DATABASE      = 'mindmap';
    // const ACTIVE_RECORD = 'Contact';

    public function load($param)
    {
    	#require 'filemanager.php';
		return 'Não implementado';
    }


	public function delete($param)
    {

		return 'Não implementado';
    }
    
    /**
     * Store the objects into the database
     * @param $param HTTP parameter
     */
    public function store($param)
    {

	    $mappath = isset($param['mindmap_path']) ? $param['mindmap_path'] : '';
	    $mapname = isset($param['mindmap_name']) ? $param['mindmap_name'] : '';
	    $mapcontent = isset($param['mindmap_content']) ? $param['mindmap_content'] : '';

        $fullpath = MindMapUtils::getMindMapFullPath($mappath, $mapname);

        try {

        	file_put_contents($fullpath, $mapcontent);
        	
        } catch (Exception $e) {

        	return var_dump($e);
        	
        }

		return 'OK';
    }
    
    /**
     * List the Active Records by the filter
     * @return The Active Record list as array
     * @param $param HTTP parameter
     */
    public function loadAll($param)
    {
		return 'Não implementado';
    }
    
    /**
     * Delete the Active Records by the filter
     * @return The result of operation
     * @param $param HTTP parameter
     */
    public function deleteAll($param)
    {
    	return 'Não implementado';
    }



	public function handle($param)
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        
        unset($param['class']);
        unset($param['method']);
        // $param['data'] = $param;
        
        switch( $method )
        {
            case 'GET':
                if (!empty($param['id']))
                {
                    return $this->load($param);
                }
                else
                {
                    return $this->loadAll($param);
                }
                break;
            case 'POST':
                return $this->store($param);
                break;
            case 'PUT':
                return $this->store($param);
                break;        
            case 'DELETE':
                if (!empty($param['id']))
                {
                    return $this->delete($param);
                }
                else
                {
                    return $this->deleteAll($param);
                }
                break;
        }
    }

    

}


?>