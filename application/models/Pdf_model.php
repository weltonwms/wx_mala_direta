<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pdf_model extends CI_Model
{
    public function getPathPdf()
    {
        $pathUser=$this->User_model->getPathUser();
        return $pathUser."pdf/";        
    }

    public function getFilesToConvert()
    {
        $this->load->model("MalaDireta_model");
        $pathMalaDireta = $this->MalaDireta_model->getPathMalaDireta();
        if(file_exists($pathMalaDireta)){
            $scanned_directory = array_diff(scandir($pathMalaDireta), array('..', '.'));
            //cuidado que não foi feito filtro para arquivo. pode haver diretório.
            return array_values($scanned_directory);
        }
       
    }

    public function convertFile($filename)
    {
        $pathUser = $this->User_model->getPathUser();
        $pathPdf = $pathUser . "pdf";
        $pathMalaDireta = $pathUser . "malaDireta";
        if (!file_exists($pathPdf)) {
            mkdir($pathPdf, 0777, true);
        }

        $this->load->library("LibreConverter");
        $converter = new LibreConverter([
            // "bin"=>"libreoffic2",
            "inputDirectory" => $pathMalaDireta,
            "outputDirectory" => $pathPdf,
        ]);
        $infoConverter = new \stdClass();
        $infoConverter->success = true;
        $infoConverter->error = "";
        try {
            $rt = $converter->convertFile($filename);
            if ($rt['return'] !== 0) {
                $infoConverter->success = false;
                $infoConverter->error = $rt['stdout'] . " " . $rt['stderr'];
            }
        } catch (\Exception $e) {
            $infoConverter->success = false;
            $infoConverter->error = $e->getMessage();
        }

        return $infoConverter;
    }

    public function getFilesNotConverted()
    {
        $pathUser = $this->User_model->getPathUser();
        $pathPdf = $pathUser . "pdf";
        $pathMalaDireta = $pathUser . "malaDireta";

        $this->load->library("LibreConverter");
        $converter = new LibreConverter([
            "inputDirectory" => $pathMalaDireta,
            "outputDirectory" => $pathPdf,
        ]);
        return $converter->getFilesNotConverted();

    }

    public function getFilesFromPdf()
    {
         $pathPdf = $this->getPathPdf();
         if (!file_exists($pathPdf)) {
            return [];
        }
        $myfiles = array_diff(scandir($pathPdf), array('.', '..'));
        return array_values($myfiles);
    }

    public function excluir($filenames)
    {
        $path=$this->getPathPdf();
        foreach ($filenames as $filename) {
            @unlink($path.$filename);
        }
        return true;
    }

    public function getZipFilesFromPdf()
    {
        $pathPdf=$this->getPathPdf();
        $fileNameZip  = 'documentosPDF.zip';
        $fullPathZip  = $pathPdf.$fileNameZip;

        $files=$this->getFilesFromPdf();
        $zip = new \ZipArchive();

        if ($zip->open($fullPathZip, \ZipArchive::CREATE)) {

            // adiciona ao zip todos os arquivos contidos no diretório.
            foreach ($files as $file) {
                $zip->addFile($pathPdf.$file, $file);
            }
            // fechar o arquivo zip após a inclusão dos arquivos desejados
            $zip->close();
            return $fullPathZip;
        }
        return false;
       
    }

}
