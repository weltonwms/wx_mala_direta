<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'controllers/BaseController.php';

class User extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('logado')){
            redirect("login");
        }
        if($this->session->userdata('user_perfil')!=1){
           redirect("home");
        }
       
        $this->load->model('User_model');
    }

    public function index()
    {
        $dados=[
            'users'=>$this->User_model->getUsers(),
        ];
        $this->renderView('user/index',$dados);
    }

    public function edit($id)
    {
        $dados=[
            "user"=>$this->User_model->getUser($id),
        ];
       // echo "<pre>"; print_r($dados); exit();
       
        $this->renderView('user/edit',$dados);
    }

    public function create()
    {
        $this->renderView('user/edit');
    }

    public function save()
    {
        $retorno=$this->User_model->save($this->input->post());
        if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"Usuário Salvo com Sucesso"); 
            redirect('user');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Salvar Usuário");
            redirect('user');
            return false;
    }

    public function excluir(){
        $ids=$this->input->post('ids');
        $retorno=$this->User_model->delete($ids);
        if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"$retorno excluído(s) com sucesso!"); 
            redirect('user');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Excluir Usuário");
            redirect('user');
            return false;
    }

   

}
