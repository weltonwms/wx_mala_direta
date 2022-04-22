<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'controllers/BaseController.php';

class Lista extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('logado')){
            redirect("login");
        }
        $this->load->model('Lista_model');
    }

    public function index()
    {
        $dados=[
            "carregamentoLista"=>$this->Lista_model->getCarregamentoLista(),
            
        ];
        $this->renderView('lista/index',$dados);
    }

    public function loadList()
    {
        $error = $this->Lista_model->getErrorCsvLista($_FILES['formLista']);
        if ($error) {
            $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',$error);
            redirect('lista');
            return false;
        }

        try{
            $headCampos=$this->Lista_model->executeList($_FILES['formLista']['tmp_name']);
            $vw=$this->load->view('lista/identificador_form',compact('headCampos'),TRUE);
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',$vw); //colocar view para select identificador
            redirect('lista');
            return true;
        }
        catch(\Exception $e){
            $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Ocorreu um Erro ao carregar a Lista");
            redirect('lista');
            return false;
        }
      
        
    }

    public function create()
    {
        $head=$this->Lista_model->getHeadCarregamentoLista();
        if (!$head) {
            $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Não existe Lista Carregada para editar algo nela");
            redirect('lista');
            return false;
        }
        $dados=[
            "head"=>$head,
        ];
        $this->renderView('lista/edit',$dados);
    }

    public function editItem($wx_id)
    {
        $head=$this->Lista_model->getHeadCarregamentoLista();
        if (!$head) {
            $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Não existe Lista Carregada para editar algo nela");
            redirect('lista');
            return false;
        }
        $dados=[
            "head"=>$head,
            "item"=>$this->Lista_model->getItemLista($wx_id),
        ];
       
        $this->renderView('lista/edit',$dados);
    }

    public function saveItem()
    {
        $post=$this->input->post();
        $retorno=$this->Lista_model->saveItem($post);
        if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"Item Salvo com Sucesso"); 
            redirect('lista');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Salvar item");
            redirect('lista');
            return false;

    }

    public function excluir(){
        $ids=$this->input->post('wx_id');
        $retorno=$this->Lista_model->deleteItems($ids);
        if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"$retorno excluído(s) com sucesso!"); 
            redirect('lista');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Excluir itens");
            redirect('lista');
            return false;
    }

    public function ativar()
    {
        $ids=$this->input->post('wx_id');
        $retorno=$this->Lista_model->ativarItens($ids);
        if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"$retorno ativado(s) com sucesso!"); 
            redirect('lista');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Ativar itens");
            redirect('lista');
            return false;
    }

    public function inativar()
    {
        $ids=$this->input->post('wx_id');
        $retorno=$this->Lista_model->inativarItens($ids);
        if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"$retorno inativado(s) com sucesso!"); 
            redirect('lista');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Inativar itens");
            redirect('lista');
            return false;
    }

    public function setIdentificador()
    {
        $retorno=$this->Lista_model->setIdentificador($this->input->post());
        if($retorno){
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"Configuração realizada com sucesso!"); 
            redirect('lista');
            return true;
        }
        $this->session->set_flashdata('status','danger');
            $this->session->set_flashdata('msg_confirm',"Erro ao Realizar configuração");
            redirect('lista');
            return false;
    }

    

}
