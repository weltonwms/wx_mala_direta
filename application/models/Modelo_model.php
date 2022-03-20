<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Modelo_model extends CI_Model
{
    public function updateCarregamentoModelo($user_id, $filename)
    {
        $this->db->where('user_id', $user_id);
        $registro = $this->db->get('carregamentos_modelo')->row();
        $dados = [
            'user_id' => $user_id,
            'filename' =>$filename,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($registro) {
            $this->db->where('user_id', $user_id);
            return $this->db->update('carregamentos_modelo', $dados);
        } else {
            return $this->db->insert('carregamentos_modelo', $dados);
        }

    }

    public function getCarregamentoModelo()
    {
        $user_id=1;
        $this->db->where('user_id',$user_id);
        $carregamento=$this->db->get('carregamentos_modelo')->row();
        return $carregamento;
    }

    public function getPathModelo()
    {
        $user_id=1;
        $secret_user="1b7b5358"; //pegar da table users quando tiver ou session
        $pathMalaDireta="./uploads/user{$user_id}_{$secret_user}/modelo/";
        return $pathMalaDireta;
    }

    
   

  
   

}
