<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'controllers/BaseController.php');

class MalaDireta extends BaseController {

	
	public function index()
	{
		$this->renderView('malaDireta/index');
	}

	
}
