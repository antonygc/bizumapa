<?php

require_once 'init.php';

class PluginRenderer
{
	
	function __construct()
	{
		// session_id($_COOKIE['PHPSESSID']);
		// session_id();
		session_start();


		$this->request = $_REQUEST; 

		if (empty($this->request['id']) or empty($this->request['scope'])) {
			$this->die();
        } 

		if (!empty($this->request['method'])) {
			$this->{$this->request['method']}();
		}

		$id = (int) $_GET['id'];

		$model = $this->getModel();
		$mindmap = $this->loadMindMap($id, $model);

		$permission = 'EDIT';

		if ($this->request['scope'] == 'public') {

			if (!CustomApplicationUtils::isAdmin()) {
				$permission = 'VIEW';
			} 

		} else {

			if ($mindmap->user_id != TSession::getValue('userid')) {
			 	$this->die();
			}

		} 

		$this->RenderPlugin($mindmap);

	}

	public function getModel()
	{
		$scope = $this->request['scope'];

		if ($scope == 'public') {
		    return 'CustomPublicMindMap';
		} elseif ($scope == 'private') {
		    return 'CustomPrivateMindMap';
		} else {
		 	$this->die();
		}
	}

	public function loadMindMap($id, $model)
	{
		try 
		{ 
		    TTransaction::open(DEFAULT_DB); // open transaction 
		    $mindmap = new $model($id); 
		    TTransaction::close(); // Closes the transaction 
 		} 

		catch (Exception $e) 
		{ 
		    new TMessage('error', $e->getMessage()); 
		    die($e->getMessage());
		}   

		return $mindmap;
	}

	public function RenderPlugin($mindmap)
	{

		$old = ['{SCOPE}',
				'{MINDMAP_ID}',
				'{MINDMAP_CONTENT}'];

		$new = [$this->request['scope'],
				json_encode($mindmap->id), 
				json_encode($mindmap->content)];

		$content = str_replace( $old, $new, file_get_contents("lib/kityminder/plugin-edit.html"));

		echo $content;
	}

	public function view()
	{

		$id = (int) $this->request['id'];
		$model = $this->getModel();

		try 
		{ 
		    TTransaction::open('permission');
		    $mindmap = new $model($id); 
		    TTransaction::close();  
 		} 

		catch (Exception $e) 
		{ 
		    new TMessage('error', $e->getMessage()); 
		    die($e->getMessage());
		} 

		echo str_replace(
			'{MINDMAP_CONTENT}', 
			$mindmap->content, 
			file_get_contents("lib/kityminder/plugin-view.html"));

		exit();
	}

	public function store()
	{
		$id = (int) $this->request['id'];
		$data = $this->request['data'];
		$model = $this->getModel();

		try 
		{ 
		    TTransaction::open('permission');
		    $mindmap = new $model($id); 
		    $mindmap->content = $data;
		    $mindmap->store();
		    TTransaction::close();  
 		} 

		catch (Exception $e) 
		{ 
		    TTransaction::rollback(); 
		    new TMessage('error', $e->getMessage()); 
		    die($e->getMessage());
		}  

		echo '200 OK';
		exit();
	}

	public function die()
	{
		die('Não Permitido');
	}

}

new PluginRenderer();







