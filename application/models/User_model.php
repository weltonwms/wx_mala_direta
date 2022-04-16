<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function getPathUser()
    {
        $user_id=1;
        $secret_user="1b7b5358"; //pegar da table users quando tiver ou session
        $pathUser="./uploads/user{$user_id}_{$secret_user}/";
        return $pathUser;
    }

}