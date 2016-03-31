<?php

namespace Controller\Api;

use Model;
use Neo\App;
use Neo\Data;

class Pesquisa extends Base
{

	public $model = null;


	function __construct()
	{
		parent::__construct();
	}

	function ajax()
	{
		if(isset($_POST['action'])){
			if(is_callable([$this, $_POST['action']]))
				$this->{$_POST['action']}($_POST['data']);
		}
	}


	function getEstado($data)
	{
		exit(json_encode($this->model->estado($data['default'])));
    }


    function getCidade($data)
    {
    	exit(json_encode($this->model->cidade($data['estado'], $data['default'])));
	}

}