<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'controllers/BaseController.php');

class Configuracao extends BaseController {

	public function __construct()
    {
        parent::__construct();
        $this->load->model('Configuracao_model');
	}
	
	public function index()
	{
		$this->load->model('Lista_model');
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
            redirect('configuração');
            return false;
	}

	public function ajaxTeste(){
		echo json_encode(['name'=>'Welton Moreira','POST'=>$_POST,'GET'=>$_GET]);
		exit();
	}
	public function ajaxTesteMail(){
		echo json_encode(['name'=>$_POST]);
		echo json_encode(['name'=>$_GET]);
		//print_r($this->input->get()); exit();
		//echo json_encode($this->input->get());
		//echo json_encode(['name'=>'Welton Moreira']);
		exit();
	}

	
}
