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

}
