<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\TemplateProcessorOdt;

class MalaDireta_model extends CI_Model
{
    public function execute($carregamentoLista, $carregamentoModelo)
    {
        $user_id=$carregamentoModelo->user_id;
        $fileModelo=$carregamentoModelo->filename;
        $extFile=UtilHelper::getExtensionFile($fileModelo);
        $campo_identificador=$carregamentoLista['campo_identificador']; //obrigatório

        if (!$carregamentoLista || !$carregamentoModelo) {
            $this->session->set_flashdata('status', 'danger');
            $this->session->set_flashdata('msg_confirm', "A Lista e o Modelo precisam estar carregados!!");
            return false;
        }

        if (!$campo_identificador) {
            $this->session->set_flashdata('status', 'danger');
            $this->session->set_flashdata('msg_confirm', "Para executar é necessário configurar o Campo Identificador!");
            return false;
        }

        $pathUser=$this->User_model->getPathUser();
        $pathModelo=$pathUser."modelo/";
        $pathMalaDireta=$this->getPathMalaDireta();

        if (!file_exists($pathMalaDireta)) {
            mkdir($pathMalaDireta, 0777, true);
        }
        
        foreach ($carregamentoLista['dados'] as $dado) {
            if ($extFile=="odt") {
                $templateProcessor = new TemplateProcessorOdt($pathModelo.$fileModelo);
            } else {
                $templateProcessor = new TemplateProcessor($pathModelo.$fileModelo);
            }
            
            foreach ($carregamentoLista['campos'] as $campo) {
                $templateProcessor->setValue($campo, $dado->$campo);
            }
            $pathToSave = "{$pathMalaDireta}{$dado->$campo_identificador}.{$extFile}";
            $templateProcessor->saveAs($pathToSave);
        }
        return true;
    }


    public function getPathMalaDireta()
    {
        $pathUser=$this->User_model->getPathUser();
        return $pathUser."malaDireta/";            
    }

    public function getFilesFromMalaDireta()
    {
        $pathMalaDireta=$this->getPathMalaDireta();
        if (!file_exists($pathMalaDireta)) {
            return [];
        }
        $myfiles = array_diff(scandir($pathMalaDireta), array('.', '..'));
        return array_values($myfiles);
    }

    public function excluir($filenames)
    {
        $pathMalaDireta=$this->getPathMalaDireta();
        foreach ($filenames as $filename) {
            @unlink($pathMalaDireta.$filename);
        }
        return true;
    }

    public function getZipFilesFromMalaDireta()
    {
        $pathMalaDireta=$this->getPathMalaDireta();
        $fileNameZip  = 'documentosMalaDireta.zip';
        $fullPathZip  = $pathMalaDireta.$fileNameZip;

        $files=$this->getFilesFromMalaDireta();
        $zip = new \ZipArchive();

        if ($zip->open($fullPathZip, \ZipArchive::CREATE)) {

            // adiciona ao zip todos os arquivos contidos no diretório.
            foreach ($files as $file) {
                $zip->addFile($pathMalaDireta.$file, $file);
            }
            // fechar o arquivo zip após a inclusão dos arquivos desejados
            $zip->close();
            return $fullPathZip;
        }
        return false;
        /*
        if (file_exists($fullPath)) {
           
            //removemos o arquivo zip após download
            unlink($fullPath);
        }
        */
    }
}
