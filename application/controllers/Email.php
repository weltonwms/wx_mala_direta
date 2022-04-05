<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'controllers/BaseController.php';

class Email extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Email_model');
        $this->load->model("Configuracao_model");
        $this->load->model('Lista_model');
        $this->load->model('Modelo_model');
    }

    public function index()
    {
        $config = $this->Configuracao_model->getConfiguracoes();
        $head_lista = $this->Lista_model->getHeadCarregamentoLista();

        $dados['configsAusentes'] = $this->getConfigsAusentes($config, $head_lista);
        $dados['config'] = $config;
        $dados['head_lista'] = $head_lista;
        //echo "<pre>"; print_r($dados); exit();
        $this->renderView('email/index', $dados);
    }

    private function getConfigsAusentes($config, $head_lista)
    {
        $configs = (object) array_merge((array) $config, (array) $head_lista);
        $configsAusentes = [];

        $required_fields = [
            'smtp_host' => "SMTP HOST",
            'smtp_port' => "SMTP PORT",
            'email_from' => "Email Remetente",
            'email_from_name' => "Email Nome",
            'campo_identificador' => "Campo Identificador",
            "campo_email" => "Campo Email",

        ];

        foreach ($required_fields as $key => $field) {
            if (!isset($configs->$key) || !$configs->$key) {
                $configsAusentes[] = $field;
            }
        }
        return $configsAusentes;
        //echo "<pre>"; print_r($configsAusentes); exit();
    }

    public function ajaxTeste()
    {

        header('Content-Type: application/json; charset=utf-8');
        try {
            $post = $this->input->post();
            $this->Email_model->teste($post);
            echo json_encode(["message" => "Email de Teste enviado com Sucesso"]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
        exit();
    }

    public function disparar()
    {
        //pegar configuracao
        //pegar carregamento_lista
        //pegar dados

        //$x=$this->getAnexos([]); print_r($x);exit();
        $carregamentoLista = $this->Lista_model->getCarregamentoLista(1);
        $post = $this->input->post();
        //echo "<pre>"; print_r($post); exit();
        $config = $this->Configuracao_model->getConfiguracoes();
        $dados = array_merge((array) $config, $post);
        $campo_email = $carregamentoLista['campo_email'];
        $campo_identificador = $carregamentoLista['campo_identificador'];
        $tipos_anexo = isset($post['tipos_anexo']) ? $post['tipos_anexo'] : [];
        //echo "<pre>"; print_r($dados); exit();
        // echo "<pre>";
        foreach ($carregamentoLista['dados'] as $dado) {

            try {
                $dados['email_to'] = $dado->$campo_email;
                $filename = $dado->$campo_identificador;
                $dados['anexos'] = $this->getAnexos($tipos_anexo, $filename, $_FILES['upload_now_file']);
                $this->Email_model->enviar($dados);
                //print_r($dados);
                echo "<br> enviado com sucesso: " . $dados['email_to'] . "<br>";
            } catch (\Exception $e) {
                echo $e->getMessage();
                echo "<br>deu ruim<br>";
            }
        }

        exit('<br>acabou');
        //echo "<pre>"; print_r($carregamentoLista); exit();
    }

    private function getAnexos($tipos_anexo, $filename, $upload_now_file)
    {

        $lista = [];
        // print_r($tipos_anexo);

        if (in_array(1, $tipos_anexo)) {
            //docx ou odt

            $this->load->model('MalaDireta_model');
            $pathMalaDireta = $this->MalaDireta_model->getPathMalaDireta();
            $ext = "docx"; //puxar do modelo
            $obj = new \stdClass();
            $obj->filePath = $pathMalaDireta . $filename . "." . $ext;
            $obj->fileName = $filename . "." . $ext;

            $lista[] = $obj;
        }

        if (in_array(2, $tipos_anexo)) {
            //pdf
        }

        if (in_array(3, $tipos_anexo)) {
            foreach ($anexos['tmp_name'] as $key => $tmp_name) {
                $obj = new \stdClass();
                $obj->filePath = $tmp_name;
                $obj->fileName = $anexos['name'][$key];
                $lista[] = $obj;
            }
        }

        return $lista;

    }

    public function ajaxDisparo()
    {
        //validar informações

        $carregamentoLista = $this->Lista_model->getCarregamentoLista(1);
        $post = $this->input->post();
        $config = $this->Configuracao_model->getConfiguracoes();
        $dados = array_merge((array) $config, $post, (array) $carregamentoLista);
        $erro = $this->getError($dados, $carregamentoLista);
        // echo "<pre>"; print_r($dados); exit();
        

        if ($erro) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(["message" => $erro]);
            exit();

        }
        $infoUpload = '';
        if (isset($dados['tipos_anexo']) && in_array(3, $dados['tipos_anexo'])) {
            $infoUpload = $this->uploadNowFile();
            //print_r($infoUpload);
            if ($infoUpload->error) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(["message" => $infoUpload->error]);
                exit();
            }
        }

        $carregamentoModelo = $this->Modelo_model->getCarregamentoModelo();
        $resposta = [
            'smtp_host' => $dados['smtp_host'],
            'smtp_port' => $dados['smtp_port'],
            'email_from' => $dados['email_from'],
            'email_from_name' => $dados['email_from_name'],
            'campo_identificador' => $dados['campo_identificador'],
            "campo_email" => $dados['campo_email'],
            "assunto" => $dados['assunto'],
            "corpo" => $dados['corpo'],
            "tipos_anexo" => isset($dados['tipos_anexo']) ? $dados['tipos_anexo'] : [],
            "lista" => $dados['dados'],
            "upload_now_file" => $infoUpload ? $infoUpload->files : [],
            "ext" => $carregamentoModelo->ext,

        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resposta);
        exit();
    }

    private function getError($dados)
    {
        $required_fields = [
            'smtp_host' => "Config SMTP HOST",
            'smtp_port' => "Config SMTP PORT",
            'email_from' => "Config Email Remetente",
            'email_from_name' => "Config Email Nome",
            'campo_identificador' => "Config Campo Identificador",
            "campo_email" => "Config Campo Email",
            'email_password' => "Senha do Email",
            'assunto' => "Assunto do Email",
            'corpo' => "Corpo do Email",

        ];

        foreach ($required_fields as $key => $field) {
            if (!isset($dados[$key]) || !$dados[$key]) {
                return "$field Ausente!";
            }
        }

        if (isset($dados['tipos_anexo']) && in_array(3, $dados['tipos_anexo'])) {
            //print_r($_FILES['upload_now_file']['tmp_name']);
            if (!$_FILES['upload_now_file']['tmp_name'][0]) {
                return 'Upload Agora sem nenhum arquivo anexado';
            }

        }
        return false;
    }

    private function uploadNowFile()
    {
        $pathUser = $this->Email_model->getPathUser();
        $path = $pathUser . "tmp/";
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        //$this->clearUpdateNowFile($path,['IMG-3383.jpg','documentoTokioMarine.pdf',' merge_diploma.png']);exit();
        $info = new \stdClass();
        $info->error = "";
        $info->files = [];

        $this->load->library('upload');
        //upload an image options
        $config = array();
        $config['upload_path'] = $path;
        $config['allowed_types'] = 'gif|jpg|png|pdf';
        $config['overwrite'] = true;

        $files = $_FILES;
        $cpt = count($_FILES['upload_now_file']['name']);

        for ($i = 0; $i < $cpt; $i++) {
            $_FILES['upload_now_file']['name'] = $files['upload_now_file']['name'][$i];
            $_FILES['upload_now_file']['type'] = $files['upload_now_file']['type'][$i];
            $_FILES['upload_now_file']['tmp_name'] = $files['upload_now_file']['tmp_name'][$i];
            $_FILES['upload_now_file']['error'] = $files['upload_now_file']['error'][$i];
            $_FILES['upload_now_file']['size'] = $files['upload_now_file']['size'][$i];

            $this->upload->initialize($config);
            if (!$this->upload->do_upload('upload_now_file')) {
                $info->error = $this->upload->display_errors();
                //destruir os files updados se tiver;
                $this->clearUpdateNowFile($path, $info->files);
                return $info;
            }

            $infoData = $this->upload->data();
            $info->files[] = $infoData['file_name'];

        }

        return $info;
    }

    private function clearUpdateNowFile($path, $files)
    {
        foreach ($files as $file) {
            @unlink($path . $file);
        }

    }

}
