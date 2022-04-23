<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'controllers/BaseController.php';

class Email extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('logado')){
            redirect("login");
        }
        $this->load->model('User_model');
        $this->load->model('Email_model');
        $this->load->model("Configuracao_model");
        $this->load->model('Lista_model');
        $this->load->model('Modelo_model');
        $this->load->model('MalaDireta_model');
        $this->load->model('Pdf_model');
    }

    public function index()
    {
        $config = $this->Configuracao_model->getConfiguracoes();
        $head_lista = $this->Lista_model->getHeadCarregamentoLista();

        $dados['configsAusentes'] = $this->Email_model->getConfigsAusentes($config, $head_lista);
        $dados['config'] = $config;
        $dados['head_lista'] = $head_lista;
        //echo "<pre>"; print_r($dados); exit();
        $this->renderView('email/index', $dados);
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

    public function ajaxDisparo()
    {
        //capturar informações necessárias
        $carregamentoLista = $this->Lista_model->getCarregamentoLista(1);
        $post = $this->input->post();
        $config = $this->Configuracao_model->getConfiguracoes();
        $dados = array_merge((array) $config, $post, (array) $carregamentoLista); //juntando tudo em $dados
        //validar informações
        $erro = $this->Email_model->getError($dados,$_FILES);
       
        if ($erro) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(["message" => $erro]);
            exit();
        }
        $infoUpload = '';
        if (isset($dados['tipos_anexo']) && in_array(3, $dados['tipos_anexo'])) {
            $infoUpload = $this->uploadNowFile(); 
            //$infoUpload é objeto que possui atributo error e files updados (caminhos) temporariamente
            if ($infoUpload->error) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(400);
                echo json_encode(["message" => $infoUpload->error]);
                //bloquear processamento aqui se houver erro no upload_file_now
                exit();
            }
        }
        //Iniciar processamento de resposta para solicitação bem sucedida
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
            "ext" => isset($carregamentoModelo->ext)?$carregamentoModelo->ext:'',

        ];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resposta);
        exit();
        //fim do processamento bem sucedido
    }

    private function uploadNowFile()
    {
        $pathUser = $this->User_model->getPathUser();
        $path = $pathUser . "tmp/";
        $dados=[
            "path"=>$path,
            "fieldUpload"=>"upload_now_file",
            "allowed_types"=>'gif|jpg|png|pdf'
        ];
        //usando biblioteca customizada que utiliza library('upload')
        $this->load->library('upload_multiple');
        $infoUpload=$this->upload_multiple->do($dados,$_FILES);
           
        if($infoUpload->error){
            //destruir os files updados se tiver;
            $this->Email_model->clearTmpFiles($infoUpload->files);
        }
       return $infoUpload;

    }

    public function ajaxSendMail()
    {
        //sleep(5);
        $dados=$this->input->post();
        $item=json_decode($dados['item']);
        unset($dados['item']);//manter $dados limpos apenas com configs
        $tipos_anexo= isset($dados['tipos_anexo'])?json_decode($dados['tipos_anexo']):[];
        $upload_now_file= isset($dados['upload_now_file'])?json_decode($dados['upload_now_file']):[];
       
        try {
            //capturando informações adicionais para enviar email
            $campo_email=$dados['campo_email'];
            $campo_identificador=$dados['campo_identificador'];
            $dados['email_to']=$item->$campo_email;
            $filename=$item->$campo_identificador;
            $dados['anexos']=$this->Email_model->getAnexos($tipos_anexo, $filename, $dados['ext'], $upload_now_file);
            //disparo de email para um item de uma lista. Passar todas informações necessárias como configs smtp, destinatário, anexos, etc
            //No caso as informações estão vindas de um POST do cliente, mas poderia pegar essas informações do banco de dados. Optou-se por tentar diminiur
            //acessos a bancos de dados para reduzir o tempo de resposta.
            $this->Email_model->enviar($dados);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["message" => "Envio com sucesso para o email: " . $dados['email_to'] ]);
            exit();
        } catch (\Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode(["message" => "Erro ao tentar enviar para o email: " . $dados['email_to'], "error" => $e->getMessage() ]);
            exit();
        }

        //fim processamento
    }

    public function ajaxSaveLogSendMail()
    {
        $post=$this->input->post();
        if(!isset($post['metaInfo']) || !$post['metaInfo']):
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(["message" => "MetaInfo não Informado" ]);
            exit();
        endif;
        $metaInfo=json_decode($post['metaInfo']);
        $dados=[
            "assunto"=>$metaInfo->assunto,
            "corpo"=>$metaInfo->corpo,
            "registros_enviados"=>$post['registros_enviados'],
            "registros_nao_enviados"=>$post['registros_nao_enviados'],
        ];
        $retorno=$this->Email_model->saveLogSendMail($dados);
        if($retorno):
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["message" => "Log Gravado com Sucesso" ]);
            exit();
        endif;

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(["message" => "Erro ao Gravar Log" ]);
        exit();
    }

    public function clearTmpFiles()
    {
        $upload_now_file=json_decode($this->input->post('upload_now_file'));
        $filesTmp= is_array($upload_now_file)?$upload_now_file:[];
        $retorno=$this->Email_model->clearTmpFiles($filesTmp);
        if($retorno):
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["message" =>"Arquivos Temporários Limpos","files"=>$filesTmp ]);
            exit();
        endif;

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode(["message" => "Erro ao limpar arquivos temporários" ]);
        exit();

    }


}
