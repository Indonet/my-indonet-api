<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/*
| -------------------------------------------------------------------
| EMAIL CONFIG
| -------------------------------------------------------------------
| Konfigurasi email keluar melalui mail server
| */  
$config['protocol'] = 'starttls';
// $config['smtp_host'] = "smtp.indo.net.id";
$config['smtp_host'] = "mail.indonet.co.id";
$config['smtp_port'] = "25";
// $config['smtp_user']='my.indonet@indonet.co.id'; 
// $config['smtp_pass']='myIndonet2021#'; 


$config['smtp_user']='noreply@indonet.co.id'; 
$config['smtp_pass']='d0d0L2022&*()'; 




$config['charset']='utf-8'; 
$config['newline']="\r\n";
$config['mailtype']="text";
$config['charset']="utf-8";
$config['priority']="0";   
$config['starttls']=true;    
/* End of file email.php */ 
/* Location: ./system/application/config/email.php */ 