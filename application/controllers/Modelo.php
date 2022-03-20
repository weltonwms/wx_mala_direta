<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'controllers/BaseController.php';

class Modelo extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modelo_model');
    }

    public function index()
    {
        $dados=[
            'carregamentoModelo'=>$this->Modelo_model->getCarregamentoModelo(),
        ];
        $this->renderView('modelo/index',$dados);
    }

    public function loadModelo()
    {
        //path= uploads/user{user_id}_{secret_user}/modelo/modelo.docx|odt
        $user_id=1;
        $path= $this->Modelo_model->getPathModelo();
		$this->do_upload($path, $user_id);
        redirect('modelo');
       

    }

	private function do_upload($path, $user_id)
	{
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		
		$config['upload_path'] = $path;
		$config['file_name'] = "modelo";
		$config['overwrite'] = true;
        $config['allowed_types'] = 'docx|odt';
      

        $this->load->library('upload', $config);

        if($this->upload->do_upload('formModelo')){
            $data=$this->upload->data();
            $this->Modelo_model->updateCarregamentoModelo($user_id,$data['file_name']);
            $this->session->set_flashdata('status','success');
            $this->session->set_flashdata('msg_confirm',"Modelo Carregado com Sucesso");
            return true;

        }

        $error=$this->upload->display_errors();
        $this->session->set_flashdata('status','danger');
        $this->session->set_flashdata('msg_confirm',$error);
        return false;
       

	}

    public function teste()
    {
		$secret_user="1b7b5358";
		$bytes = random_bytes(4);
		$key=bin2hex($bytes);
		echo $key;
		var_dump($key);
        exit('teste');
    }

    public function download($link)
    {
        $path=$this->Modelo_model->getPathModelo();
        $fullPath=$path.$link;
		header('Content-Description: File Transfer');
        header('application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$link.'"');
        readfile($fullPath);

    }

}
