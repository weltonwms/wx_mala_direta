<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'controllers/BaseController.php');

class Pdf extends BaseController {

	
	public function index()
	{
		$this->renderView('pdf/index');
	}

	
}
