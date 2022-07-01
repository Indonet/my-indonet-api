<?php 
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 

class Emails extends REST_Controller {  
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta'); 
        $this->load->helper('url'); 
    }  
	public function index_get(){ 
        // $this->send_email_billing_indonet();
        $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST);     
    }   
	public function index_post(){
		$post = $this->input->post(); 
		switch ($post['type']) {
			case 'user': 
				$this->send_email_user($post);
				break;  
			case 'user_register': 
				$this->send_email_register($post);
				break; 
            case 'send_email_billing_indonet': 
                $this->send_email_billing_indonet($post);
                break; 
            case 'send_email_billing_alibaba': 
                $this->send_email_billing_alibaba($post);
                break; 
            case 'get_logs_send_all_email': 
                $this->get_logs_send_all_email($post);
                break; 
            case 'send_email_blast_1': 
                $this->send_email_blast_1($post);
                break;  
            case 'send_email_blast_2': 
                $this->send_email_blast_2($post);
                break;  
			default:
                $error_msg = 'Permission denied';
                $this->response($error_msg, REST_Controller::HTTP_BAD_REQUEST); 
				break;
		}
	}
    function send_email_user($post){  
        if($post){ 
            $from_email = $post['from_email'];
            $from_name = $post['from_name'];
            $to_email = $post['to_email'];
            $subject = $post['subject'];
            $message = $post['message'];
            $this->load->library('email'); 	  
            $this->email->from($from_email,$from_name); 
            $this->email->to($to_email);
            $this->email->subject($subject );    
            $this->email->message($message);             
            if ( ! $this->email->send()){    
                $this->response($this->email->print_debugger(), REST_Controller::HTTP_BAD_REQUEST); 
            }else{
                $this->response('send email', REST_Controller::HTTP_OK);   
            }  
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function send_email_register($post){ 
        if($post){  
            $this->data['url_token'] = $post['url_token'];
            $this->data['cust_id'] = $post['cust_id'];
            $this->data['cust_email'] = $post['cust_email'];
            $this->data['cust_name'] = $post['cust_name'];
            $this->load->library('email'); 	  
            $this->email->clear(TRUE);
            $this->email->from('no-reply@indonet.co.id', 'Indonet'); 
            $this->email->subject('Informasi Akses Portal Indonet');   
            $message = $this->load->view('email_template/registration', $this->data, true); 
            $this->email->attach('/var/www/api-my.indonet.id/files/Cara Akses ke Portal Indonet.pdf');  
            $this->email->to($post['cust_email']);  
            $this->email->message($message); 
            if ( ! $this->email->send()){  
                echo false;
                // $this->response($this->email->print_debugger(), REST_Controller::HTTP_BAD_REQUEST); 
            }else{
                echo true;
                // $this->response('send email', REST_Controller::HTTP_OK);    
            }   
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function send_email_billing_indonet($post){ 
        if($post){  
            $periode = $post['periode'];
            $periode_folder = $post['periode_folder'];
            $cust_id = $post['cust_id'];
            $cust_email = $post['cust_email']; 
            $cust_name = $post['cust_name'];
            $billing_amount = $post['billing_amount'];
            $billing_file = $post['billing_file'];
            $faktur_file = $post['faktur_file'];
            $no_npwp = $post['no_npwp'];  

            $inv_month = substr($periode,0,2);
            $inv_year = substr($periode,2,2);

            $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
            $dt = DateTime::createFromFormat('y', $inv_year);
            $inv_year_name = $dt->format('Y');  
 
            $periode_name = $inv_month_name.' '.$inv_year_name; 
            $this->data['periode_name'] = $periode_name;
            $this->data['cust_email'] = $cust_email;
            $this->data['cust_name'] = $cust_name;
            $this->data['cust_id'] = $cust_id;
            $this->data['billing_amount'] = number_format($billing_amount,2, '.', ','); 
            $this->load->library('email'); 	   
            $this->email->clear(TRUE);
            $this->email->from('no-reply@indonet.co.id', 'Indonet'); 
            $this->email->subject('Billing statement Indonet '.$periode_name);   
            $message = $this->load->view('email_template/billing', $this->data, true); 
            $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode_folder.'/e-statement/'.$billing_file);   
            if($no_npwp != '0'){
                $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode_folder.'/e-faktur/'.$faktur_file);  
            } 
            $this->email->to($cust_email);  
            $this->email->message($message); 
            if ( ! $this->email->send()){   
                $array_log = '- cust id = '.$cust_id.'; cust name = '.$cust_name.'; email = '.$cust_email.'; date = '.date('d-m-Y H:i:s').'; status = '.$this->email->print_debugger(); 
                file_put_contents('/var/www/api-my.indonet.id/files/data_billing/'.$periode_folder.'/send_email_indonet_logs.txt', $array_log.PHP_EOL , FILE_APPEND | LOCK_EX);
                $this->response($this->email->print_debugger(), REST_Controller::HTTP_BAD_REQUEST); 
            }else{ 
                $array_log = '- cust id = '.$cust_id.'; cust name = '.$cust_name.';  email = '.$cust_email.'; file = '.$billing_file.' - '.$faktur_file.'; date = '.date('d-m-Y H:i:s').'; status = terkirim;'; 
                file_put_contents('/var/www/api-my.indonet.id/files/data_billing/'.$periode_folder.'/send_email_indonet_logs.txt', $array_log.PHP_EOL , FILE_APPEND | LOCK_EX);
                $this->response('send email', REST_Controller::HTTP_OK);    
            }   
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function send_email_billing_alibaba($post){ 
        if($post){  
            $periode = $post['periode'];
            $periode_folder = $post['periode_folder'];
            $cust_id = $post['cust_id'];
            $cust_email = $post['cust_email']; 
            $cust_name = $post['cust_name'];
            $billing_amount = $post['billing_amount'];
            $billing_file = $post['billing_file'];
            $faktur_file = $post['faktur_file'];
            $no_npwp = $post['no_npwp'];  

            $inv_month = substr($periode,0,2);
            $inv_year = substr($periode,2,2);

            $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
            $dt = DateTime::createFromFormat('y', $inv_year);
            $inv_year_name = $dt->format('Y');  
 
            $periode_name = $inv_month_name.' '.$inv_year_name; 
            $this->data['periode_name'] = $periode_name;
            $this->data['cust_email'] = $cust_email;
            $this->data['cust_name'] = $cust_name;
            $this->data['cust_id'] = $cust_id;
            $this->data['billing_amount'] = number_format($billing_amount,2, '.', ','); 
            $this->load->library('email'); 	   
            $this->email->clear(TRUE);
            $this->email->from('no-reply@indonet.co.id', 'Indonet'); 
            $this->email->subject('Billing statement Indonet '.$periode_name);   
            $message = $this->load->view('email_template/billing', $this->data, true); 
            $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode_folder.'/e-statement-alibaba/'.$billing_file);   
            if($no_npwp != '0'){
                $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode_folder.'/e-faktur-alibaba/'.$faktur_file);  
            } 
            $this->email->to($cust_email);  
            $this->email->message($message); 
            if ( ! $this->email->send()){   
                $array_log = '- cust id = '.$cust_id.'; cust name = '.$cust_name.'; email = '.$cust_email.'; date = '.date('d-m-Y H:i:s').'; status = '.$this->email->print_debugger(); 
                file_put_contents('/var/www/api-my.indonet.id/files/data_billing/'.$periode_folder.'/send_email_alibaba_logs.txt', $array_log.PHP_EOL , FILE_APPEND | LOCK_EX);
                $this->response($this->email->print_debugger(), REST_Controller::HTTP_BAD_REQUEST); 
            }else{ 
                $array_log = '- cust id = '.$cust_id.'; cust name = '.$cust_name.';  email = '.$cust_email.'; file = '.$billing_file.' - '.$faktur_file.'; date = '.date('d-m-Y H:i:s').'; status = terkirim;'; 
                file_put_contents('/var/www/api-my.indonet.id/files/data_billing/'.$periode_folder.'/send_email_alibaba_logs.txt', $array_log.PHP_EOL , FILE_APPEND | LOCK_EX);
                $this->response('send email', REST_Controller::HTTP_OK);    
            }   
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function get_logs_send_all_email($post){
        if($post){  
            $periode = $post['periode']; 
            $logs_name_file = $post['logs_name_file'];
            $file_name = 'files/data_billing/'.$periode.'/'.$logs_name_file;  
            if(file_exists($file_name)){       
                $path = '/var/www/api-my.indonet.id/'.$file_name;
                $this->response($path, REST_Controller::HTTP_OK);  
            }else{
                $this->response('File not found', REST_Controller::HTTP_BAD_REQUEST); 
            }    
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function send_email_blast_1($post){ 
        if($post){   
            $this->data['cust_id'] = $post['cust_id'];
            $this->data['cust_email'] = $post['cust_email'];
            $this->data['cust_name'] = $post['cust_name'];
            $this->load->library('email'); 	  
            $this->email->clear(TRUE);
            $this->email->from('billing@indonet.co.id', 'Indonet'); 
            $this->email->subject('Informasi Tambahan Terkait Invoice Layanan Alibaba Cloud Periode Februari & Maret 2022');   
            $message = $this->load->view('email_template/email_blast_1', $this->data, true);  
            $this->email->to($post['cust_email']);  
            $this->email->message($message); 
            if ( ! $this->email->send()){  
                echo false;
                // $this->response($this->email->print_debugger(), REST_Controller::HTTP_BAD_REQUEST); 
            }else{
                echo true;
                // $this->response('send email', REST_Controller::HTTP_OK);    
            }   
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function send_email_blast_2($post){ 
        if($post){   
            $this->data['cust_id'] = $post['cust_id'];
            $this->data['cust_email'] = $post['cust_email'];
            $this->data['cust_name'] = $post['cust_name'];
            $this->load->library('email'); 	  
            $this->email->clear(TRUE);
            $this->email->from('support-alibaba@indonet.co.id', 'Support Indonet'); 
            $this->email->subject('Aktifkan MFA untuk akses RAM User Alibaba Cloud');   
            $message = $this->load->view('email_template/email_blast_2', $this->data, true);  
            $this->email->to($post['cust_email']);  
            $this->email->message($message); 
            if ( ! $this->email->send()){  
                echo false;
                // $this->response($this->email->print_debugger(), REST_Controller::HTTP_BAD_REQUEST); 
            }else{
                echo true;
                // $this->response('send email', REST_Controller::HTTP_OK);    
            }   
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
}