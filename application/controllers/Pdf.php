<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'controllers/BaseController.php');

class Pdf extends BaseController {

	public function __construct()
    {
        parent::__construct();
		if(!$this->session->userdata('logado')){
            redirect("login");
        }
		$this->load->model('User_model');
		$this->load->model('Pdf_model');
        $this->load->model('User_model');
	}

	public function index()
	{
		$dados=[
			"files_pdf"=>$this->Pdf_model->getFilesFromPdf(),
		];
		$this->renderView('pdf/index',$dados);
	}

	public function ajaxDisparo()
	{
		$filesToConvert=$this->Pdf_model->getFilesToConvert();
		if(!$filesToConvert):
			header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(["message" => "Nenhumm Arquivo Encontrado para Converter!"]);
            exit();
		endif;
		header('Content-Type: application/json; charset=utf-8');
		//echo "<pre>"; print_r(json_encode($filesToConvert)); exit();
		
        echo json_encode(["lista"=>$filesToConvert]);
        exit();
        //fim do processamento bem sucedido
	}

	public function teste()
	{
		$this->load->library("LibreConverter");
		$pathUser=$this->User_model->getPathUser();
		$pathPdf=$pathUser."pdf";
		$pathMalaDireta=$pathUser."malaDireta";

		//echo "<pre>";
		//print_r($pathMalaDireta); exit();

		$converter= new LibreConverter([
			"inputDirectory"=>$pathMalaDireta,
			"outputDirectory"=>$pathPdf,
			"inputExtension"=>".docx",
			]);

		$retorno=$converter->convertAll();
		//$retorno=$converter->getFilesNotConverted();
		echo "<pre>";
		print_r($retorno);
	}

	public function ajaxConvertFile()
	{
		
		$file=$this->input->post('file');
		$retorno=$this->Pdf_model->convertFile($file);
		if($retorno->success):
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["message" => "$file Convertido com Sucesso!" ]);
            exit();
        endif;

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(["message" => "Erro ao Converter $file","error"=>$retorno->error ]);
        exit();
		
       
	}

	public function ajaxFilesNotConverted()
	{
		$listFiles= $this->Pdf_model->getFilesNotConverted();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($listFiles);
		exit();
		
	}

	public function ajaxGetFilesPdf()
	{
		$listFiles= $this->Pdf_model->getFilesFromPdf();
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($listFiles);
		exit();
		
	}

	public function download($file)
    {
        $path=$this->Pdf_model->getPathPdf();
        $link=urldecode($path.$file);
        //var_dump($link); exit();
        header('Content-Description: File Transfer');
        header('application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        readfile($link);
    }

    public function excluir()
    {
        $retorno=$this->Pdf_model->excluir($this->input->post('files'));
        if ($retorno) {
            $this->session->set_flashdata('status', 'success');
            $this->session->set_flashdata('msg_confirm', 'Arquivos excluídos com Sucesso!');
        }

        redirect('pdf');
    }

    public function downloadAll()
    {
        $zip= $this->Pdf_model->getZipFilesFromPdf();
        //var_dump($zip); exit();
		$fileName  = 'documentosPdf.zip';
		header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="'.$fileName.'"');
		//Método abaixo pode ser substituído por mover o arquivo para uma área pública e redirecionar direto com o Apache.
		UtilHelper::readfile_chunked($zip);
		unlink($zip); //Após Download remover o arquivo
        
    }

	
}
