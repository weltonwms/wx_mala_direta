<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$message = preg_replace('/(<\/?p>)+/', ' ', $message);
throw new Exception("Database error occured with message : {$message}");