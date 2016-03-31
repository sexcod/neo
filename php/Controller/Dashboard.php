<?php

namespace Controller;

use Controller\App\Base;
use Model;
use Neo\App;

/**
 * Description of home
 *
 * @author Bill
 */
class Dashboard extends Base 
{

	function __construct(){
        parent::__construct();

        $this->navbar = 'admin/navbar';
	}


    function index()
    {
        $this->response('admin/dashboard',null, 'dashboard',null, ['main']);
    }



}