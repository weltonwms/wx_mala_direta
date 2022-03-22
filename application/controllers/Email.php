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
    }

    public function index()
    {
        $dados = $this->Configuracao_model->getConfiguracoes();

        $dados['configsAusentes'] = $this->getConfigsAusentes($dados['config'], $dados['head_lista']);
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
            echo json_encode(["message"=>"Email de Teste enviado com Sucesso"]);
           
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["message"=>$e->getMessage()]);
        }
        exit();
    }

}
