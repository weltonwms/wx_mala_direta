<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'controllers/BaseController.php';

class Login extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        
    }

    public function index()
    {
        $this->load->view('layouts/html_header');
        $this->load->view('login');
        $this->load->view('layouts/html_footer');
    }

}