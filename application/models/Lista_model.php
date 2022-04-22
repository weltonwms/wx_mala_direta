<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lista_model extends CI_Model
{
    public function getErrorCsvLista($file)
    {
       if ($file['error'] != 0) {
            return "Erro de Upload NR: " . $file['error'];

        }

        if (UtilHelper::getExtensionFile($file['name']) != 'csv') {
            return "O Arquivo deve ser CSV";

        }
        $fileCsv = file($file['tmp_name']);
        $cabecalhoCsv = str_getcsv($fileCsv[0]);

        foreach ($cabecalhoCsv as $campo) {
            if (!$campo) {
                return "Cabeçalho CSV Inválido: Campo em Branco";

            }
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $campo)) {
                $error = "Cabeçalho CSV Inválido: Campo com Caracteres Inválidos !";
                $error .= "<br>Leia Instruções De Envio da Lista";
                return $error;

            }
        }
        return '';
    }

    public function executeList($fileCsv)
    {
        $user_id=$this->session->userdata('user_id');
        $cabecalho = UtilHelper::getCabecalhoCsv($fileCsv);
        $nameTable = "user" . $user_id . '_lista_dados';

        $this->db->trans_start();

        $this->createTableListaDados($nameTable, $cabecalho);
        $this->insertTableListaDados($fileCsv, $nameTable);
        $this->updateCarregamentoLista($nameTable, $user_id, $cabecalho);

        $this->db->trans_complete();
        return $cabecalho;
    }

    private function createTableListaDados($nameTable, $cabecalho)
    {
        //Excluir antes para deixar livre para a nova ListaDados
        $sql1 = "DROP TABLE IF EXISTS $nameTable";
        $this->db->query($sql1);
        //Montagem da SQL de criação
        $sql = 'CREATE TABLE ' . $nameTable . '  ( ';
        $sql .= 'wx_id INT NOT NULL AUTO_INCREMENT, ';

        foreach ($cabecalho as $campo) {
            $sql .= '`' . $campo . '` VARCHAR(255) NULL, ';
        }
        $sql .= 'wx_ativo TINYINT(2) DEFAULT 1, ';
        $sql .= 'PRIMARY KEY(wx_id) ';
        $sql .= ');';

        $this->db->query($sql);
    }

    private function insertTableListaDados($fileCsv, $nameTable)
    {
        $dados = UtilHelper::convertCsvToArray($fileCsv);
        $this->db->insert_batch($nameTable, $dados);
    }

    private function updateCarregamentoLista($nameTable, $user_id, $cabecalho)
    {
        $jsonCampos = json_encode($cabecalho);
        $this->db->where('user_id', $user_id);
        $registro = $this->db->get('carregamentos_lista')->row();
        $dados = [
            'nome_tabela' => $nameTable,
            'campos' => $jsonCampos,
            'user_id' => $user_id,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($registro) {
            $this->db->where('user_id', $user_id);
            $this->db->update('carregamentos_lista', $dados);
        } else {
            $this->db->insert('carregamentos_lista', $dados);
        }

    }

    public function getCarregamentoLista($status=null)
    {
        $carregamento=$this->getHeadCarregamentoLista();
        if($carregamento){
            if(is_numeric($status)){
                $this->db->where('wx_ativo',$status);
            }
            $listaDados=$this->db->get($carregamento->nome_tabela)->result();
            $lista=[
                "updated_at" => $carregamento->updated_at,
                "campos"=>$carregamento->campos,
                "campo_identificador"=>$carregamento->campo_identificador,
                "campo_email"=>$carregamento->campo_email,
                "user_id"=>$carregamento->user_id,
                "dados" =>$listaDados,

            ];
            return $lista;
        }
        return [];
    }

    public function getHeadCarregamentoLista()
    {
        $user_id=$this->session->userdata('user_id');
        $this->db->where('user_id',$user_id);
        $carregamento=$this->db->get('carregamentos_lista')->row();
        if($carregamento){
            $carregamento->campos=json_decode($carregamento->campos);
            return $carregamento;
        }
        return false;

    }

    public function getItemLista($wx_id)
    {
        $head=$this->getHeadCarregamentoLista();
        if($head){
            $this->db->where('wx_id', (int) $wx_id);
            $item=$this->db->get($head->nome_tabela)->row();
            return $item;
        }
    }

    public function saveItem($post)
    {
        $head=$this->getHeadCarregamentoLista();
        if(!$head){
            return false;
        }
        if($post['wx_id']){
            $this->db->where('wx_id', $post['wx_id']);
           return  $this->db->update($head->nome_tabela, $post);
        }
        else{
           return  $this->db->insert($head->nome_tabela, $post);
        }
    }

    public function deleteItems($wx_ids)
    {
        $head=$this->getHeadCarregamentoLista();
        if(!$head){
            return false;
        }
        
        $this->db->where_in('wx_id', $wx_ids);
        $this->db->delete($head->nome_tabela);
        return $this->db->affected_rows();

    }

    public function ativarItens($wx_ids)
    {
        $head=$this->getHeadCarregamentoLista();
        if(!$head){
            return false;
        }
        
        $this->db->where_in('wx_id', $wx_ids);
        $this->db->set('wx_ativo', 1);
        $this->db->update($head->nome_tabela);
        return $this->db->affected_rows();

    }

    public function InativarItens($wx_ids)
    {
        $head=$this->getHeadCarregamentoLista();
        if(!$head){
            return false;
        }
        
        $this->db->where_in('wx_id', $wx_ids);
        $this->db->set('wx_ativo', 0);
        $this->db->update($head->nome_tabela);
        return $this->db->affected_rows();

    }

    public function setIdentificador($post)
    {
        $head=$this->getHeadCarregamentoLista();
        if(!$head){
            return false;
        }
        
        $this->db->where('wx_id', $head->wx_id);
        $this->db->set('campo_identificador',$post['campo_identificador']);
        $this->db->set('campo_email',$post['campo_email']);
        return $this->db->update('carregamentos_lista');
        
    }

   

}
