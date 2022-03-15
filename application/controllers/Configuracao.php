<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'controllers/BaseController.php');

class Configuracao extends BaseController {

	
	public function index()
	{
		$this->renderView('configuracao/index');
	}

	
}
