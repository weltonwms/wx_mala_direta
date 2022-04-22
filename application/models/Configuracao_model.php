<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Configuracao_model extends CI_Model
{
    public function updateConfiguracoes($post)
    {
        $user_id=$this->session->userdata('user_id');
        $this->db->where('user_id', $user_id);
        $registro = $this->db->get('configuracoes')->row();
        $dados = [
            'user_id' => $user_id,
            'smtp_host' =>$post['smtp_host'],
            'smtp_port' =>$post['smtp_port'],
            'email_from' =>$post['email_from'],
            'email_from_name' =>$post['email_from_name'],
            'updated_at' => date('Y-m-d H:i:s'),
        ];
       $this->load->model('Lista_model');
       $this->Lista_model->setIdentificador($post);

        if ($registro) {
            $this->db->where('user_id', $user_id);
            return $this->db->update('configuracoes', $dados);
        } else {
            return $this->db->insert('configuracoes', $dados);
        }

    }

    public function getConfiguracoes()
    {
        $user_id=$this->session->userdata('user_id');
        $this->db->where('user_id',$user_id);
        $configuracoes=$this->db->get('configuracoes')->row();
        return $configuracoes;
    }



   

    
   

  
   

}
