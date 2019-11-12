<?php

require_once 'init.php';

class PluginRenderer
{
	
	function __construct()
	{
		session_id($_COOKIE['PHPSESSID']);
		session_start();

		switch($_SERVER['REQUEST_METHOD'])
		{
			case 'GET': 
				$this->request = $_GET; 
				break;
			case 'POST': 
				$this->request = $_POST; 
				break;
			default:
				$this->request = []; 
		}

		if (empty($this->request['id']) or empty($this->request['scope'])) {
			$this->die();
        } 

		if (!empty($this->request['method'])) {
			$this->{$this->request['method']}();
		}

		$id = (int) $_GET['id'];

		$model = $this->getModel();

		$mindmap = $this->loadMindMap($id, $model);

		if ($mindmap->user_id != TSession::getValue('userid')) {
		 	$this->die();
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
		    TTransaction::open('permission'); // open transaction 
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
		$content = str_replace(
			'{MINDMAP_CONTENT}', 
			json_encode($mindmap->content), 
			file_get_contents("lib/kityminder/plugin.html"));

		$content = str_replace(
			'{MINDMAP_ID}', 
			json_encode($mindmap->id), 
			$content);

		$content = str_replace(
			'{SCOPE}', 
			$this->request['scope'], 
			$content);

		echo $content;
	}

	public function store()
	{
		$id = (int) $this->request['id'];
		$data = $this->request['data'];
		$model = $this->getModel();

		try 
		{ 
		    TTransaction::open('permission'); // open transaction 
		    $mindmap = new $model($id); 
		    $mindmap->content = $data;
		    $mindmap->store();
		    TTransaction::close(); // Closes the transaction 
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







