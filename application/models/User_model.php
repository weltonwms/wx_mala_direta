<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function getPathUser()
    {
        $user_id=$this->session->userdata('user_id');
        $secret_user=$this->session->userdata('user_secret');
        $pathUser="./uploads/user{$user_id}_{$secret_user}/";
        return $pathUser;
    }

}