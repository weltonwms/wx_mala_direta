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
        $this->load->model('MalaDireta_model');
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
    /**
     * @param array $tipos_anexo tipos de anexos desejados: 1, 2 ou 3
     * @param string $filename nome do arquivo desejado sem a extensão (geralmente é o campo identificador)
     * @param string $extModelo nome da extensão do arquivo utilizado pelo modelo
     * @param array $upload_now_file lista com os uploads realizados temporariamente para o tipo de anexo 3 (upload_now_file)
     */
    private function getAnexos($tipos_anexo, $filename, $extModelo, $upload_now_file)
    {
        $lista = [];
        //Se não tiver algum tipo de anexo então não tem motivo para prosseguir
        if (!is_array($tipos_anexo) || !$tipos_anexo) {
            return [];
        }
        
        if (in_array(1, $tipos_anexo)) {
            //docx ou odt
            $pathMalaDireta = $this->MalaDireta_model->getPathMalaDireta();
            $obj = new \stdClass();
            $obj->filePath = $pathMalaDireta . $filename . "." . $extModelo;
            $obj->fileName = $filename . "." . $extModelo;

            $lista[] = $obj;
        }

        if (in_array(2, $tipos_anexo)) {
            //pdf
        }

        if (in_array(3, $tipos_anexo)) {
            //upload_now_file uploads realizados no momento do disparo de emails
            $pathUser = $this->Email_model->getPathUser();
            $pathTmp = $pathUser."tmp/";
            foreach ($upload_now_file as $name) {
                $obj = new \stdClass();
                $obj->filePath = $pathTmp . $name;
                $obj->fileName =$name;
                $lista[] = $obj;
            }
        }

        return $lista;
    }

    public function ajaxDisparo()
    {
        //capturar informações necessárias
        $carregamentoLista = $this->Lista_model->getCarregamentoLista(1);
        $post = $this->input->post();
        $config = $this->Configuracao_model->getConfiguracoes();
        $dados = array_merge((array) $config, $post, (array) $carregamentoLista); //juntando tudo em $dados
        //validar informações
        $erro = $this->getError($dados);
       
        if ($erro) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode(["message" => $erro]);
            exit();
        }
        $infoUpload = '';
        if (isset($dados['tipos_anexo']) && in_array(3, $dados['tipos_anexo'])) {
            $infoUpload = $this->uploadNowFile(); //$infoUpload é objeto que possui atributo error e files updados (caminhos) temporariamente
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
            "ext" => $carregamentoModelo->ext,

        ];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resposta);
        exit();
        //fim do processamento bem sucedido
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
            //Necessita ter acesso a variável global $_FILES, para validar o que os files que estão vindo de um formulário cliente
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
        //criando diretório temporário para realizar upload dos arquivos
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
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
            $dados['anexos']=$this->getAnexos($tipos_anexo, $filename, $dados['ext'], $upload_now_file);
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
