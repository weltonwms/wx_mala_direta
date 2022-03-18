<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once(APPPATH . 'controllers/BaseController.php');


class Email extends BaseController
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Email_model');
	}
	
    public function index()
    {
        $this->renderView('email/index');
    }

    public function teste()
    {
       //try{
		$r=$this->Email_model->teste();
		echo $r;
	  // }catch(\Exception $e){
	//	echo "deu ruim";
	   //}
    }



}
