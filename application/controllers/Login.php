<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'controllers/BaseController.php';

class Login extends BaseController
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $this->load->view('layouts/html_header');
        $this->load->view('login');
        $this->load->view('layouts/html_footer');
    }

    public function postLogin()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $this->db->where('username', $username);
        $userDb = $this->db->get('users')->row();

        if (!$userDb) {
            $this->setResponse(false);
            return false;
        }

        if (password_verify($password, $userDb->password)) {
            $this->setResponse(true, $userDb);
            return true;
        }

        $this->setResponse(false);

    }

    private function setResponse($status, $userDb = null)
    {
        if (!$status || !$userDb) {
            $this->session->set_flashdata('msg_error', 'Usuário ou Senha Incorretos!');
            redirect("login");
            return false;
        }
        $dados = [
            "logado" => true,
            "user_id"=>$userDb->id,
            "user_perfil" => $userDb->perfil,
            "user_secret" => $userDb->user_secret,
            "username" => $userDb->username,
            "name" => $userDb->name,
        ];

        $this->session->set_userdata($dados);
        redirect("home");
        return true;

    }

    public function deslogar()
    {
        $this->session->sess_destroy();
        redirect("login");

    }

}
