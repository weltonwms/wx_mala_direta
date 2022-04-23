<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function getPathUser()
    {
        $user_id = $this->session->userdata('user_id');
        $secret_user = $this->session->userdata('user_secret');
        $pathUser = "./uploads/user{$user_id}_{$secret_user}/";
        return $pathUser;
    }

    public function getUsers()
    {
        return $this->db->get('users')->result();
    }

    public function getUser($id)
    {
        $this->db->where('id',(int) $id);
        return $this->db->get('users')->row();
    }

    public function save($post)
    {
        if ($post['id']) {
            return $this->update($post);
        }
        return $this->create($post);
    }

    public function create($post)
    {
        $dados = [
            "username" => $post['username'],
            "name" => $post['name'],
            "password" => password_hash($post['password'], PASSWORD_DEFAULT),
            "perfil" => $post['perfil'],
            "user_secret" => $this->gerarUserSecret(),
        ];
        return $this->db->insert("users", $dados);

    }

    public function update($post)
    {
        $dados = [
            "username" => $post['username'],
            "name" => $post['name'],
            "perfil" => $post['perfil'],
        ];
        if ($post['password']) {
            $dados['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
        }
        $this->db->where('id', (int) $post['id']);
        return $this->db->update("users", $dados);

    }

    public function delete($ids)
    {
        if(!$ids || !is_array($ids)){
            return false;
        }
        $this->db->where_in('id', $ids);
        $this->db->delete("users");
        return $this->db->affected_rows();

    }

    private function gerarUserSecret()
    {
        //random unique caracteres for UserSecret; Utilizado com Path do Usuário
        $bytes = random_bytes(4);
		$key=bin2hex($bytes);
        return $key;
    }

    public function autoDestroy()
    {
        $pathUser=$this->getPathUser();
        $this->delTree($pathUser);
        $this->dropTableUser();
        $this->deleteRegisters();
        return true;
    }

    private function delTree($dir) { 
        if(!file_exists($dir)){
            return false;
        }
        $files = array_diff(scandir($dir), array('.','..')); 
        foreach ($files as $file) { 
          (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file"); 
        } 
        return rmdir($dir); 
      }

    private function dropTableUser()
    {
       $head=$this->Lista_model->getHeadCarregamentoLista();
       if(!$head){
           return false;
       }
       $nameTable=$head->nome_tabela;
       $sql1 = "DROP TABLE IF EXISTS $nameTable";
       return $this->db->query($sql1);
    }

    private function deleteRegisters()
    {
        $user_id=$this->session->userdata('user_id');
        $this->db->where('user_id',$user_id);
        $this->db->delete("emails_enviados");

        $this->db->where('user_id',$user_id);
        $this->db->delete("configuracoes");

        $this->db->where('user_id',$user_id);
        $this->db->delete("carregamentos_modelo");

        $this->db->where('user_id',$user_id);
        $this->db->delete("carregamentos_lista");
    }

}
