<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Configuracao_model extends CI_Model
{
    public function updateConfiguracoes($post)
    {
        $user_id=1;
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
        $user_id=1;
        $this->db->where('user_id',$user_id);
        $configuracoes=$this->db->get('configuracoes')->row();
        //echo "<pre>";print_r($configuracoes); exit();
        $this->load->model('Lista_model');
        $head=$this->Lista_model->getHeadCarregamentoLista();
        return ["config"=>$configuracoes,"head_lista"=>$head];
    }

   

    
   

  
   

}
