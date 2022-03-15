<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BaseController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function renderView($view, $dados=[])
	{
		$this->load->view('layouts/html_header');
        //$this->load->view('cabecalho');
        $this->load->view('layouts/menu_navegacao', $dados);
        $this->load->view($view, $dados);
        //$this->load->view('layouts/rodape');
        $this->load->view('layouts/html_footer');
	}
}
