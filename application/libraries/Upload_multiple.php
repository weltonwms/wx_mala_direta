<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upload_multiple
{

    function do($dados, $globalFiles) {
        $info = new \stdClass();
        $info->error = "";
        $info->files = [];

        if (!isset($dados['path']) || !$dados['path']) {
            $info->error="Path não Informado";
            return $info;
        }
        if (!isset($dados['fieldUpload']) || !$dados['fieldUpload']) {
            $info->error="fieldUpload não Informado";
            return $info;
        }

        if (!file_exists($dados['path'])) {
            mkdir($dados['path'], 0777, true);
        }

        $CI = &get_instance();
        $CI->load->library('upload');
        $fieldUpload = $dados['fieldUpload'];

        $config = array();
        $config['upload_path'] = $dados['path'];
        $config['allowed_types'] = isset($dados['allowed_types'])?$dados['allowed_types']:'';
        $config['overwrite'] = true;

        $files = $globalFiles;
        $cpt = count($globalFiles[$fieldUpload]['name']);

        for ($i = 0; $i < $cpt; $i++) {
            $_FILES[$fieldUpload]['name'] = $files[$fieldUpload]['name'][$i];
            $_FILES[$fieldUpload]['type'] = $files[$fieldUpload]['type'][$i];
            $_FILES[$fieldUpload]['tmp_name'] = $files[$fieldUpload]['tmp_name'][$i];
            $_FILES[$fieldUpload]['error'] = $files[$fieldUpload]['error'][$i];
            $_FILES[$fieldUpload]['size'] = $files[$fieldUpload]['size'][$i];

            $CI->upload->initialize($config);
            if (!$CI->upload->do_upload($fieldUpload)) {
                $info->error = $CI->upload->display_errors();
                return $info; //fim após primeiro erro
            }

            $infoData = $CI->upload->data();
            $info->files[] = $infoData['file_name'];
        }

        return $info;

    }

    
}
