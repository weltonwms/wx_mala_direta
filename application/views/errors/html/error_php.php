<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$message = preg_replace('/(<\/?p>)+/', ' ', $message);
throw new Exception("Erro PHP : {$message}");