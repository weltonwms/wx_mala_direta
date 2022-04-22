<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once(APPPATH . 'controllers/BaseController.php');


class MalaDireta extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('logado')){
            redirect("login");
        }
        $this->load->model('User_model');
        $this->load->model('Lista_model');
        $this->load->model('Modelo_model');
        $this->load->model('MalaDireta_model');
    }
    
    public function index()
    {
        $dados=[
            "files"=>$this->MalaDireta_model->getFilesFromMalaDireta(),
        ];
        //$diretorio=$this->MalaDireta_model->getDiretorio();
        //echo "<pre>"; print_r(scandir($diretorio)); exit();
        $this->renderView('malaDireta/index', $dados);
    }

    public function execute()
    {
        $carregamentoLista=$this->Lista_model->getCarregamentoLista(1);
        $carregamentoModelo=$this->Modelo_model->getCarregamentoModelo();
        $retorno= $this->MalaDireta_model->execute($carregamentoLista, $carregamentoModelo);
        if ($retorno) {
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('msg_confirm', 'Execução de Mala Direta realizada com Sucesso!');
        }

        redirect('malaDireta');
    }
    
    public function download($file)
    {
        $path=$this->MalaDireta_model->getPathMalaDireta();
        $link=urldecode($path.$file);
        //var_dump($link); exit();
        header('Content-Description: File Transfer');
        header('application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        readfile($link);
    }

    public function excluir()
    {
        $retorno=$this->MalaDireta_model->excluir($this->input->post('files'));
        if ($retorno) {
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('msg_confirm', 'Arquivos excluídos com Sucesso!');
        }

        redirect('malaDireta');
    }

    public function downloadAll()
    {
        $zip= $this->MalaDireta_model->getZipFilesFromMalaDireta();
        //var_dump($zip); exit();
		$fileName  = 'documentosMalaDireta.zip';
		header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		//Método abaixo pode ser substituído por mover o arquivo para uma área pública e redirecionar direto com o Apache.
		UtilHelper::readfile_chunked($zip);
		unlink($zip); //Após Download remover o arquivo
        
    }
}
