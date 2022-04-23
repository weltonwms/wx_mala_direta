<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'controllers/BaseController.php');

class Configuracao extends BaseController {

	public function __construct()
    {
        parent::__construct();
		if(!$this->session->userdata('logado')){
            redirect("login");
        }
		$this->load->model('User_model');
		$this->load->model('Lista_model');
        $this->load->model('Configuracao_model');
	}
	
	public function index()
	{
		$dados=[
			"config"=>$this->Configuracao_model->getConfiguracoes(),
			"head_lista"=>$this->Lista_model->getHeadCarregamentoLista()
		];

		$this->renderView('configuracao/index',$dados);
	}

	public function save()
	{
		//echo "<pre>"; print_r($this->input->post()); exit();
		$dados=$this->input->post();
		$retorno=$this->Configuracao_model->updateConfiguracoes($dados);
		if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"Configuração Salva com Sucesso"); 
            redirect('configuracao');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Salvar Configuração");
            redirect('configuracao');
            return false;
	}

	

	public function autoDestroy(){
		$retorno=$this->User_model->autoDestroy();
		if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"Arquivos e Registros Apagados com Sucesso"); 
            redirect('configuracao');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Apagar Arquivos e Registros");
            redirect('configuracao');
            return false;
	}

	
}
