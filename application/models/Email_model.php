<?php
defined('BASEPATH') or exit('No direct script access allowed');
use PHPMailer\PHPMailer\PHPMailer;

class Email_model extends CI_Model
{
    public function teste($post)
    {

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        //Server settings
        $mail->SMTPDebug = 0; //Enable verbose debug output
        $mail->isSMTP(); //Send using SMTP
        $mail->Host = $post['smtp_host']; //Set the SMTP server to send through
        $mail->SMTPAuth = true; //Enable SMTP authentication
        $mail->Username = $post['email_from']; //SMTP username
        $mail->Password = $post['email_password']; //SMTP password
        $mail->SMTPSecure = ''; //Enable implicit TLS encryption
        $mail->Port = $post['smtp_port']; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ),
        );
        $mail->Timeout       =   8;
        $mail->CharSet = 'UTF-8';
        //Recipients
        $mail->setFrom($post['email_from'], $post['email_from_name']);
        $mail->addAddress($post['email_from']); //Add a recipient
       

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = 'Email de Teste do Wx Mala Direta';
        $mail->Body = 'Este é um Email de Teste<br>Se Você recebeu significa que a Configuraçao SMTP está correta';
        $mail->AltBody = 'Este é um Email de Teste\n Se Você recebeu significa que a Configuraçao SMTP está correta';

        $mail->send();
        return true;

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
        //$mail->Timeout       =   8;
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
        $mail->AltBody = $dados['corpo']; //tirar tags HTML

        $mail->send();
        return true;
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
