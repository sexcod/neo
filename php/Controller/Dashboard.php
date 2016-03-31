<?php

namespace Controller;

use Controller\Admin\Base;
use Model;
use Neos\App;

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