<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;

class Email_model extends CI_Model
{
    public function teste($post)
    {
        $post['email_to']=$post['email_from'];
        $post['timeout']=8;
        $post['assunto']='Email de Teste do Wx Mala Direta';
        $post['corpo']='Este é um Email de Teste<br>
            Se Você recebeu significa que a Configuraçao SMTP está correta';
        return $this->enviar($post);

    }

    public function enviar($dados)
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = 0; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = $dados['smtp_host']; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = $dados['email_from']; //SMTP username
        $mail->Password = $dados['email_password']; //SMTP password
        $mail->SMTPSecure = ''; //Enable implicit TLS encryption
        $mail->Port = $dados['smtp_port']; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ),
        );
        if(isset($dados['timeout']) && $dados['timeout']){
            $mail->Timeout  = $dados['timeout'];
        }
        
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($dados['email_from'], $dados['email_from_name']);
        $mail->addAddress($dados['email_to']); //Add a recipient
        $anexos=(isset($dados['anexos']) && is_array($dados['anexos']) )?$dados['anexos']:[];
        foreach($anexos as $anexo){
             $mail->addAttachment($anexo->filePath, $anexo->fileName);  
        }
       

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $dados['assunto'];
        $mail->Body = $dados['corpo'];
        $mail->AltBody = UtilHelper::plainText($dados['corpo']); //tirar tags HTML

        $mail->send();
        return true;
    }

    public function getConfigsAusentes($config, $head_lista)
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

    public function getError($dados,$files)
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
            if (!$files['upload_now_file']['tmp_name'][0]) {
                return 'Upload Agora sem nenhum arquivo anexado';
            }
        }

        return false;
    }


    /**
     * Extrai anexos baseados nos tipos_anexos;
     * @param array $tipos_anexo tipos de anexos desejados: 1, 2 ou 3
     * @param string $filename nome do arquivo desejado sem a extensão (geralmente é o campo identificador)
     * @param string $extModelo nome da extensão do arquivo utilizado pelo modelo
     * @param array $upload_now_file lista com os uploads realizados temporariamente para o tipo de anexo 3 (upload_now_file)
     * @return array Lista com objetos que possuem atributos filename e filePath
     */
    public function getAnexos($tipos_anexo, $filename, $extModelo, $upload_now_file)
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


    public function getPathUser()
    {
        $user_id=1;
        $secret_user="1b7b5358"; //pegar da table users quando tiver ou session
        $pathUser="./uploads/user{$user_id}_{$secret_user}/";
        return $pathUser;
    }

    public function saveLogSendMail($dados)
    {
        $user_id=1;
        $this->db->where('user_id', $user_id);
        $registro = $this->db->get('emails_enviados')->row();
        //TO DO :sanitizar entrada $dados;
        $dados['updated_at']=date('Y-m-d H:i:s');
        $dados['user_id']=$user_id;
       

        if ($registro) {
            $this->db->where('user_id', $user_id);
            return $this->db->update('emails_enviados', $dados);
        } else {
            return $this->db->insert('emails_enviados', $dados);
        }

    }

    public function clearTmpFiles(array $filesTmp)
    {
        $pathUser = $this->getPathUser();
        $pathTmp = $pathUser."tmp/";
        foreach ($filesTmp as $file) {
            $fileFullPath= $pathTmp . $file;
            if (file_exists($fileFullPath)) {
               @unlink($fileFullPath);
            }
        }
        return true;
    }

   

}
