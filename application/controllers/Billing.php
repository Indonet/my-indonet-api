<?php 
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 
use setasign\Fpdf\Fpdf;
use setasign\Fpdi\Fpdi;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



class Billing extends REST_Controller {  
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta'); 
        $this->load->helper('url'); 
        include_once(APPPATH . '/libraries/ax/index.php'); 
        $this->load->model(array('api_model')); 
    }   
    public function index_get(){ 
        $this->response('File found', REST_Controller::HTTP_OK);     
        // $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
    }
    public function index_post(){
		$post = $this->input->post(); 
		switch ($post['type']) {
            case 'check_billing_faktur_indonet': 
                $this->check_billing_faktur_indonet($post);
                break;  
			case 'upload_billing_faktur_indonet': 
				$this->upload_billing_faktur_indonet($post);
				break;      
            case 'view_billing_faktur_indonet': 
                $this->view_billing_faktur_indonet($post);
                break;    
            case 'create_billing_indonet_by_periode_cust_id': 
                $this->create_billing_indonet_by_periode_cust_id($post);
                break;     
            case 'check_count_billing_faktur_indonet': 
                $this->check_count_billing_faktur_indonet($post);
                break;     
            case 'get_pdf_billing_indonet': 
                $this->get_pdf_billing_indonet($post);
                break;   
            case 'get_pdf_faktur_indonet': 
                $this->get_pdf_faktur_indonet($post);
                break;    
            case 'create_faktur_pajak_indonet_by_periode_cust_id': 
                $this->create_faktur_pajak_indonet_by_periode_cust_id($post);
                break;  

                
            case 'check_billing_faktur_alibaba': 
                $this->check_billing_faktur_alibaba($post);
                break;  
			case 'upload_billing_faktur_alibaba': 
				$this->upload_billing_faktur_alibaba($post);
				break;      
            case 'view_billing_faktur_alibaba': 
                $this->view_billing_faktur_alibaba($post);
                break;    
            case 'create_billing_alibaba_by_periode_cust_id': 
                $this->create_billing_alibaba_by_periode_cust_id($post);
                break;     
            case 'check_count_billing_faktur_alibaba': 
                $this->check_count_billing_faktur_alibaba($post);
                break;        
            case 'get_pdf_billing_alibaba': 
                $this->get_pdf_billing_alibaba($post);
                break;   
            case 'get_pdf_faktur_alibaba': 
                $this->get_pdf_faktur_alibaba($post);
                break;    
            case 'create_faktur_pajak_alibaba_by_periode_cust_id': 
                $this->create_faktur_pajak_alibaba_by_periode_cust_id($post);
                break;  

			default:  
                $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
				break;
		}
	} 
    function check_billing_faktur_indonet($post){
        if(isset($post)){ 
            $periode = $post['periode']; 
            $file_name = './files/data_billing/'.$periode.'/billing_indonet.txt';  // tempat lokasi file dan nama file serta format file txt 
            if(file_exists($file_name)){    // jika filenya sudah ada    
                $this->response('File found', REST_Controller::HTTP_OK);     
            }else{
                $this->response('File not found', REST_Controller::HTTP_BAD_REQUEST); 
            }   
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
    function upload_billing_faktur_indonet($post){
        if(isset($post)){ 
            $periode = $post['periode'];
            $data = $post['data']; 
            $file_name = './files/data_billing/'.$periode.'/billing_indonet.txt';  // tempat lokasi file dan nama file serta format file txt 
            if(file_exists($file_name)){    // jika filenya sudah ada 
                unlink($file_name); // hapus dulu filenya
            } 
            if($data){  
                if (!is_dir('./files/data_billing/'.$periode.'/')) {
                    mkdir('./files/data_billing/'.$periode.'/', 0755, TRUE);                
                }
                $fp = fopen($file_name, 'w'); // buka filenya dulu yang tadi 
                fwrite($fp, $data); // tulis filenya
                fclose($fp); // tutup filenya 
            }     
            $res = true;
            $this->response($res, REST_Controller::HTTP_OK);    
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function view_billing_faktur_indonet($post){
        if(isset($post)){ 
            $periode = $post['periode']; 
            $file_name = './files/data_billing/'.$periode.'/billing_indonet.txt';  // tempat lokasi file dan nama file serta format file txt 
            if(file_exists($file_name)){    // jika filenya sudah ada   
                $file_data = file_get_contents($file_name);
                $file_data = json_decode($file_data, true);  
                $this->response($file_data, REST_Controller::HTTP_OK);     
            }else{
                $this->response('File not found', REST_Controller::HTTP_BAD_REQUEST); 
            }   
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
    function create_billing_indonet_by_periode_cust_id($post){
        if(isset($post)){ 
            $cust_id = $post['cust_id']; 
            $periode_folder = $post['periode_folder']; 
            $periode_bill = $post['periode_bill']; 
            $file_name_billing = $post['billing_file'];   
            $billing_code = $post['billing_code'];    
            $inv_month = substr($periode_bill,0,2);
            $inv_year = substr($periode_bill,2,2);

            $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
            $dt = DateTime::createFromFormat('y', $inv_year);
            $inv_year_name = $dt->format('Y');    
 
            $folder_billing = './files/data_billing/'.$periode_folder.'/e-statement/'; 
            if (!is_dir($folder_billing)) {
                mkdir($folder_billing, 0755, TRUE);                
            }  
            $file_billing_path = $folder_billing.'/'.$file_name_billing;   
            // if(file_exists($file_billing_path)){  
            //     // unlink($file_billing); 
            // }else{ 
                $data_ax = getCustInfoToInvMonth($cust_id, $inv_year_name, $inv_month);
                if($data_ax){     
                    $this->data_inv['data_cust'] = $data_ax[0]; 
                    $this->data_inv['inv_detail_bill'] = $data_ax['INV_DETAIL_DATA']; 
                    $this->data_inv['inv_month_bill'] = $data_ax['INV_MONTH_TOTAL']; 
                    $this->data_inv['virtual_acc_bca'] = $data_ax['VIRTUAL_ACC'][0]; 
                    $this->data_inv['year_bill'] = $inv_year;
                    $this->data_inv['month_bill'] = $inv_month; 
                    $this->data_inv['billing_code'] = $billing_code;    
                    $html = $this->load->view('customer/billing_template', $this->data_inv, true);    
                    $save = $this->pdf->save($html, $file_name_billing, $folder_billing);  
                    $res = array('inv_total'=>$data_ax['INV_MONTH_TOTAL'], 'email'=>$data_ax[0]['EMAIL']);
                    $this->response($res, REST_Controller::HTTP_OK);  
                }
            // }
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    } 
    function check_count_billing_faktur_indonet($post){
        if(isset($post)){ 
            $periode = $post['periode']; 
            $file_name = './files/data_billing/'.$periode.'/billing_indonet.txt';  // tempat lokasi file dan nama file serta format file txt 
            if(file_exists($file_name)){    // jika filenya sudah ada    
                $file_data = file_get_contents($file_name);
                $file_data = json_decode($file_data, true);
                $count_billing = count($file_data);

                $e_statement_pdf = './files/data_billing/'.$periode.'/e-statement/'; 
                $count_file_billing = 0; 
                $files2 = glob( $e_statement_pdf ."*" ); 
                if( $files2 ) {
                    $count_file_billing = count($files2);
                } 
                
                $e_faktur_pdf = './files/data_billing/'.$periode.'/e-faktur/'; 
                $count_file_faktur = 0; 
                $files3 = glob( $e_faktur_pdf ."*" ); 
                if( $files3 ) {
                    $count_file_faktur = count($files3);
                } 

                $res = array('count_list'=>$count_billing, 'count_file_billing'=>$count_file_billing, 'count_file_faktur'=>$count_file_faktur); 
                $this->response($res, REST_Controller::HTTP_OK);     
            }else{
                $this->response('File not found', REST_Controller::HTTP_BAD_REQUEST); 
            }   
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    } 
    function get_pdf_billing_indonet($post){
        if(isset($post)){ 
            $periode = $post['periode'];
            $billing_name = $post['billing_name'];
            $file_name = 'files/data_billing/'.$periode.'/e-statement/'.$billing_name;  
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
    function get_pdf_faktur_indonet($post){
        if(isset($post)){ 
            $periode = $post['periode'];
            $faktur_name = $post['faktur_name'];
            $file_name = 'files/data_billing/'.$periode.'/e-faktur/'.$faktur_name;  
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
    function create_faktur_pajak_indonet_by_periode_cust_id($post){
        if(isset($post)){  
            $periode_folder = $post['periode_folder']; 
            $faktur_file = $post['faktur_file'];    
            $faktur_file_code = explode(".",$faktur_file);  
            $faktur_file_code = $faktur_file_code[0]; 
            $url_nextcloud = 'https://pjk-stor.indonet.id/remote.php/dav/files/myportal/faktur_pajak/'.$periode_folder.'/'; 
            $username = 'myportal';
            $password = 'pizzA2021!@';
            $folder_faktur = './files/data_billing/'.$periode_folder.'/e-faktur/'; 
            if (!is_dir($folder_faktur)) {
                mkdir($folder_faktur, 0755, TRUE);                
            }   
            $this->curl->http_method('PROPFIND');
            $this->curl->http_login($username, $password);
            $response = $this->curl->simple_get($url_nextcloud);   
            $service = new Sabre\Xml\Service();
            $list_files = $service->parse($response);
            $file_found = '';
            foreach ($list_files as $key => $value) {
                $list_files_name = $value['value'][0]['value']; 
                if(preg_match('/\b'.$faktur_file_code.'\b/', $list_files_name)){
                    $file_found = $list_files_name;
                    break;
                }  
            }
            if($file_found){  
                $url_file_found = 'https://pjk-stor.indonet.id'.$file_found;   
                $file_save = $folder_faktur.$faktur_file;    
                $this->curl->http_login($username, $password);
                $response = $this->curl->simple_get($url_file_found);   
                file_put_contents($file_save, $response);  
                $this->response('created', REST_Controller::HTTP_OK);  
            }    
            
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
    
    function check_billing_faktur_alibaba($post){
        if(isset($post)){ 
            $periode = $post['periode']; 
            $file_name = './files/data_billing/'.$periode.'/billing_alibaba.txt';  // tempat lokasi file dan nama file serta format file txt 
            if(file_exists($file_name)){    // jika filenya sudah ada    
                $this->response('File found', REST_Controller::HTTP_OK);     
            }else{
                $this->response('File not found', REST_Controller::HTTP_BAD_REQUEST); 
            }   
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
    function upload_billing_faktur_alibaba($post){
        if(isset($post)){ 
            $periode = $post['periode'];
            $data = $post['data']; 
            $file_name = './files/data_billing/'.$periode.'/billing_alibaba.txt';  // tempat lokasi file dan nama file serta format file txt 
            if(file_exists($file_name)){    // jika filenya sudah ada 
                unlink($file_name); // hapus dulu filenya
            } 
            if($data){  
                if (!is_dir('./files/data_billing/'.$periode.'/')) {
                    mkdir('./files/data_billing/'.$periode.'/', 0755, TRUE);                
                }
                $fp = fopen($file_name, 'w'); // buka filenya dulu yang tadi 
                fwrite($fp, $data); // tulis filenya
                fclose($fp); // tutup filenya 
            }     
            $res = true;
            $this->response($res, REST_Controller::HTTP_OK);    
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function check_count_billing_faktur_alibaba($post){
        if(isset($post)){ 
            $periode = $post['periode']; 
            $file_name = './files/data_billing/'.$periode.'/billing_alibaba.txt';  // tempat lokasi file dan nama file serta format file txt 
            if(file_exists($file_name)){    // jika filenya sudah ada    
                $file_data = file_get_contents($file_name);
                $file_data = json_decode($file_data, true);
                $count_billing = count($file_data);

                $e_statement_pdf = './files/data_billing/'.$periode.'/e-statement-alibaba/'; 
                $count_file_billing = 0; 
                $files2 = glob( $e_statement_pdf ."*" ); 
                if( $files2 ) {
                    $count_file_billing = count($files2);
                } 
                
                $e_faktur_pdf = './files/data_billing/'.$periode.'/e-faktur-alibaba/'; 
                $count_file_faktur = 0; 
                $files3 = glob( $e_faktur_pdf ."*" ); 
                if( $files3 ) {
                    $count_file_faktur = count($files3);
                } 

                $res = array('count_list'=>$count_billing, 'count_file_billing'=>$count_file_billing, 'count_file_faktur'=>$count_file_faktur); 
                $this->response($res, REST_Controller::HTTP_OK);     
            }else{
                $this->response('File not found', REST_Controller::HTTP_BAD_REQUEST); 
            }   
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    } 
    function view_billing_faktur_alibaba($post){
        if(isset($post)){ 
            $periode = $post['periode']; 
            $file_name = './files/data_billing/'.$periode.'/billing_alibaba.txt';  // tempat lokasi file dan nama file serta format file txt 
            if(file_exists($file_name)){    // jika filenya sudah ada   
                $file_data = file_get_contents($file_name);
                $file_data = json_decode($file_data, true);  
                $this->response($file_data, REST_Controller::HTTP_OK);     
            }else{
                $this->response('File not found', REST_Controller::HTTP_BAD_REQUEST); 
            }   
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
    function get_pdf_billing_alibaba($post){
        if(isset($post)){ 
            $periode = $post['periode'];
            $billing_name = $post['billing_name'];
            $file_name = 'files/data_billing/'.$periode.'/e-statement-alibaba/'.$billing_name;  
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
    function get_pdf_faktur_alibaba($post){
        if(isset($post)){ 
            $periode = $post['periode'];
            $faktur_name = $post['faktur_name'];
            $file_name = 'files/data_billing/'.$periode.'/e-faktur-alibaba/'.$faktur_name;  
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
    function create_billing_alibaba_by_periode_cust_id($post){
        if(isset($post)){ 
            $cust_id = $post['cust_id']; 
            $periode_folder = $post['periode_folder']; 
            $periode_bill = $post['periode_bill']; 
            $file_name_billing = $post['billing_file'];   
            $billing_code = $post['billing_code'];    
            $inv_month = substr($periode_bill,0,2);
            $inv_year = substr($periode_bill,2,2);

            $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
            $dt = DateTime::createFromFormat('y', $inv_year);
            $inv_year_name = $dt->format('Y');    
 
            $folder_billing = './files/data_billing/'.$periode_folder.'/e-statement-alibaba/'; 
            if (!is_dir($folder_billing)) {
                mkdir($folder_billing, 0755, TRUE);                
            }  
            $file_billing_path = $folder_billing.'/'.$file_name_billing;   
            // if(file_exists($file_billing_path)){  
            //     // unlink($file_billing); 
            // }else{ 
                $data_ax = getCustInfoToInvMonth($cust_id, $inv_year_name, $inv_month);
                if($data_ax){     
                    $this->data_inv['data_cust'] = $data_ax[0]; 
                    $this->data_inv['inv_detail_bill'] = $data_ax['INV_DETAIL_DATA']; 
                    $this->data_inv['inv_month_bill'] = $data_ax['INV_MONTH_TOTAL']; 
                    $this->data_inv['virtual_acc_bca'] = $data_ax['VIRTUAL_ACC'][0]; 
                    $this->data_inv['year_bill'] = $inv_year;
                    $this->data_inv['month_bill'] = $inv_month; 
                    $this->data_inv['billing_code'] = $billing_code;    
                    $html = $this->load->view('customer/billing_template', $this->data_inv, true);    
                    $save = $this->pdf->save($html, $file_name_billing, $folder_billing);  
                    $res = array('inv_total'=>$data_ax['INV_MONTH_TOTAL'], 'email'=>$data_ax[0]['EMAIL']);
                    $this->response($res, REST_Controller::HTTP_OK);  
                }
            // }
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    } 
    function create_faktur_pajak_alibaba_by_periode_cust_id($post){
        if(isset($post)){  
            $periode_folder = $post['periode_folder']; 
            $faktur_file = $post['faktur_file'];    
            $faktur_file_code = explode(".",$faktur_file);  
            $faktur_file_code = $faktur_file_code[0]; 
            $url_nextcloud = 'https://pjk-stor.indonet.id/remote.php/dav/files/myportal/faktur_pajak/'.$periode_folder.'/'; 
            $username = 'myportal';
            $password = 'pizzA2021!@';
            $folder_faktur = './files/data_billing/'.$periode_folder.'/e-faktur-alibaba/'; 
            if (!is_dir($folder_faktur)) {
                mkdir($folder_faktur, 0755, TRUE);                
            }   
            $this->curl->http_method('PROPFIND');
            $this->curl->http_login($username, $password);
            $response = $this->curl->simple_get($url_nextcloud);   
            $service = new Sabre\Xml\Service();
            $list_files = $service->parse($response);
            $file_found = '';
            foreach ($list_files as $key => $value) {
                $list_files_name = $value['value'][0]['value']; 
                if(preg_match('/\b'.$faktur_file_code.'\b/', $list_files_name)){
                    $file_found = $list_files_name;
                    break;
                }  
            }
            if($file_found){  
                $url_file_found = 'https://pjk-stor.indonet.id'.$file_found;   
                $file_save = $folder_faktur.$faktur_file;    
                $this->curl->http_login($username, $password);
                $response = $this->curl->simple_get($url_file_found);   
                file_put_contents($file_save, $response);  
                $this->response('created', REST_Controller::HTTP_OK);  
            }    
            
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    } 
     
    function check_count_billing_faktur_post(){
        $post = $this->input->post();
        if($post){
            $periode = $post['periode'];
            $data_db = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode, 'type'=>1, 'status'=>1]);
            if($data_db){ 
                $res = ['result'=>true, 'data'=>$data_db];
                $this->response($res, REST_Controller::HTTP_OK);    
            }else{
                $res = ['result'=>false];
                $this->response($res, REST_Controller::HTTP_OK);   
            }
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function create_billing_pdf_by_periode_post(){
        $post = $this->input->post();
        if($post){
            $periode = $post['periode'];  
            $inv_month = substr($periode,4,2);
            $inv_year = substr($periode,2,2);
            $inv_year_name = substr($periode,0,4); 
            $periode_name = $inv_month.$inv_year;
            $count_billing = 0;
            $file_name_data = './files/data_billing/'.$periode.'/cust_data_list.txt';  
            if(file_exists($file_name_data)){    // jika filenya sudah ada 
                $file_data = file_get_contents($file_name_data);
                $file_data = json_decode($file_data, true); 
                foreach ($file_data as $key => $value) { 
                    $cust_id = $value['cust_id'];
                    $cust_name = $value['cust_name'];
                    $cust_email = $value['cust_email'];
                    $cust_type = $value['cust_type'];
                    $no_npwp = $value['no_npwp'];
                    $cust_type = $value['cust_type'];
                    $billing_file = 'SO-'.$periode_name.'-'.$cust_id.'.pdf';
                    $billing_code = 'SO-'.$periode_name.'-'.$cust_id;
                    $faktur_file =  $value['faktur_file'];
                    $faktur_pajak_id =  $value['faktur_pajak_id'];
                    $faktur_pajak_complete =  $value['faktur_pajak_complete'];
                    $faktur_status = $value['faktur_status'];
                    $send_email_date = $value['send_email_date']; 
                    $folder_billing = './files/data_billing/'.$periode.'/e-statement/'; 
                    if (!is_dir($folder_billing)) {
                        mkdir($folder_billing, 0755, TRUE);                
                    }   
                    $data_ax = getCustInfoToInvMonth($cust_id, $inv_year_name, $inv_month);                            
                    $billing_amount = 0;
                    $billing_status = 0; 
                    if($data_ax){     
                        $this->data_inv['data_cust'] = $data_ax[0]; 
                        $this->data_inv['inv_detail_bill'] = $data_ax['INV_DETAIL_DATA']; 
                        $this->data_inv['inv_month_bill'] = $data_ax['INV_MONTH_TOTAL']; 
                        $this->data_inv['virtual_acc_bca'] = $data_ax['VIRTUAL_ACC'][0]; 
                        $this->data_inv['year_bill'] = $inv_year;
                        $this->data_inv['month_bill'] = $inv_month; 
                        $this->data_inv['inv_year_name'] = $inv_year_name;  
                        $this->data_inv['billing_code'] = $billing_code;    
                        $html = $this->load->view('customer/billing_template', $this->data_inv, true);    
                        $save = $this->pdf->save($html, $billing_file, $folder_billing);                               
                        $billing_amount = $data_ax['INV_MONTH_TOTAL'];
                        $billing_status = 1;  
                        $count_billing = $count_billing+1;
                    } 
                    $data[] = array(
                        'cust_id'=>$cust_id, 'cust_name'=>$cust_name, 'cust_email'=>$cust_email, 'cust_type'=>$cust_type, 'billing_code'=>$billing_code,
                        'no_npwp'=>$no_npwp, 'cust_type'=>$cust_type, 'billing_file'=>$billing_file, 'billing_amount'=>$billing_amount,
                        'faktur_file'=>$faktur_file, 'faktur_pajak_id' => $faktur_pajak_id, 'faktur_pajak_complete' => $faktur_pajak_complete,
                        'billing_status'=>$billing_status, 'faktur_status'=>$faktur_status, 'send_email_date'=>$send_email_date
                    );   
                    $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['count_billing'=>$count_billing]); 
                    $inp = file_get_contents($file_name_data);
                    $tempArray = json_decode($inp, true);   
                    unset($tempArray[$key]);   
                    $tempArray[$key] = $data[0]; 
                    $tempArray = array_values($tempArray);
                    $jsonData = json_encode($tempArray); 
                    file_put_contents($file_name_data, $jsonData);   
                } 
                $res = ['result'=>true];
                $this->response($res, REST_Controller::HTTP_OK);  
            }else{
                $res = ['result'=>false];
                $this->response($res, REST_Controller::HTTP_OK);   
            }
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function create_billing_pdf_by_periode_new_post(){
        $post = $this->input->post(); 
        if($post){
            $periode = $post['periode'];  
            $billing_non_alibaba = $post['billing_non_alibaba'];  
            $billing_alibaba = $post['billing_alibaba'];  
            $billing_non_npwp = $post['billing_non_npwp']; 
            $billing_npwp = $post['billing_npwp'];  
            $alibaba_posting_date = $post['alibaba_posting_date'];  
            if($billing_alibaba == 1){  
                $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['alibaba_posting_date'=>$alibaba_posting_date]);
            }
            $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['get_billing_pdf_status'=>2]);
            $arr1 = ['periode'=>$periode]; 
            $arr3 = ''; 
            if($billing_non_alibaba == 1 && $billing_alibaba == 1){
                $arr3 = $arr1; 
            }else{ 
                if($billing_non_alibaba == 1){
                    $arr2 = array('cust_instal_name != '=>'Alicloud');
                    $arr3 = $arr1 + $arr2;
                }else if($billing_alibaba == 1){
                    $arr2 = array('cust_instal_name'=>'Alicloud');
                    $arr3 = $arr1 + $arr2; 
                }   
            } 
            if($billing_non_npwp == 1 && $billing_npwp == 1){ 
                $where = $arr3;
            }else{
                if($billing_non_npwp == 1){
                    $arr4 = array('cust_npwp_no'=>'');
                    $where = $arr3 + $arr4; 
                }
                if($billing_npwp == 1){
                    $arr4 = array('cust_npwp_no !='=>'');
                    $where = $arr3 + $arr4; 
                } 
            } 
            $data_cust_list = $this->api_model->get_where_data('billing_faktur_list', $where);    
            if($data_cust_list){
                foreach ($data_cust_list as $key => $value) {
                    $id = $value['id'];
                    $cust_id = $value['cust_id'];
                    $billing_status = $value['billing_status'];
                    $subnet_code = $value['cust_subnet_code'];  
                    $cust_instal_name = $value['cust_instal_name'];
                    $posting_date_from_array = array(); 
                    $posting_date_to_array = array(); 
                    if($billing_status == 0){ 
                        $inv_month = substr($periode,4,2);
                        $inv_year = substr($periode,2,2);
                        $inv_year_name = substr($periode,0,4); 
                        $periode_name = $inv_month.$inv_year;
                        $periodeYM = $inv_year_name.$inv_month;
                        $billing_file = 'SO-'.$periode_name.'-'.$cust_id.'.pdf';
                        $billing_code = 'SO-'.$periode_name.'-'.$cust_id;
                        $count_billing = 0;
                        if($cust_instal_name == 'Alicloud'){
                            $periode_from = date("Ym", strtotime("-1 months", strtotime(date($inv_year_name.'-'.$inv_month))));
                            $periode_to = $periodeYM;
                            $billing_info_from = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode_from]); 
                            $billing_info_to = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode_to]);
                            $posting_date_from =  date("Y-m-d", strtotime("+1 days", strtotime($billing_info_from['alibaba_posting_date'])));
                            $posting_date_to =  date("Y-m-d", strtotime($billing_info_to['alibaba_posting_date']));  
                            array_push($posting_date_from_array, $posting_date_from);  
                            array_push($posting_date_to_array, $posting_date_to);    
                            $data_ax = getCustInfoToInvMonth_new($cust_id, $inv_year_name, $inv_month, $posting_date_from_array, $posting_date_to_array);
                        }else{ 
                            $periode_from = date("Ym", strtotime("-1 months", strtotime(date($inv_year_name.'-'.$inv_month))));
                            $periode_to = $periodeYM; 
                            $posting_date_from =  date("Y-m-02", strtotime($periode_from));
                            $posting_date_to = date("Y-m-01", strtotime($periode_to));
                            array_push($posting_date_from_array, $posting_date_from);  
                            array_push($posting_date_to_array, $posting_date_to);  
                            $data_ax = getCustInfoToInvMonth($cust_id, $inv_year_name, $inv_month);  
                        }  
                        $billing_amount = 0;  
                        if($data_ax){     
                            $amount_8 = 0;
                            $crete_inv = 0;
                            foreach ($data_ax['INV_DETAIL_DATA'] as $key_det => $val_det) {
                                if($val_det['TRANSTYPE'] == 8){
                                    $amount_8 = $amount_8 + $val_det['AMOUNTMST'];
                                    if($amount_8 > 0){
                                        $crete_inv = 1;
                                    }else{
                                        $crete_inv = 0;
                                    }
                                } 
                            }   
                            if($crete_inv == 1){ 
                                $folder_billing = './files/data_billing/'.$periode.'/e-statement/'; 
                                if (!is_dir($folder_billing)) {
                                    mkdir($folder_billing, 0755, TRUE);                
                                }  
                                $file_to_text = $cust_id.'.txt';
                                $fp = fopen($folder_billing.$file_to_text, 'w'); 
                                fwrite($fp, json_encode($data_ax));
                                fclose($fp);
                                $this->data_inv['data_cust'] = $data_ax[0]; 
                                $this->data_inv['inv_detail_bill'] = $data_ax['INV_DETAIL_DATA']; 
                                $this->data_inv['inv_month_bill'] = $data_ax['INV_MONTH_TOTAL']; 
                                $this->data_inv['virtual_acc_bca'] = $data_ax['VIRTUAL_ACC'][0]; 
                                $this->data_inv['year_bill'] = $inv_year;
                                $this->data_inv['month_bill'] = $inv_month; 
                                $this->data_inv['inv_year_name'] = $inv_year_name;  
                                $this->data_inv['billing_code'] = $billing_code;    
                                $html = $this->load->view('customer/billing_template', $this->data_inv, true);    
                                $save = $this->pdf->save($html, $billing_file, $folder_billing);                               
                                $billing_amount = $data_ax['INV_MONTH_TOTAL']; 
                                $post_update = ['billing_status'=>1, 'billing_amount'=>$billing_amount, 'billing_file'=>$billing_file]; 
                            }else{
                                $billing_amount = $data_ax['INV_MONTH_TOTAL']; 
                                $post_update = ['billing_status'=>2, 'billing_amount'=>$billing_amount, 'billing_file'=>'No Usage'];  
                            }
                                $this->api_model->update_db('billing_faktur_list', ['id'=>$id], $post_update);  
                        } 
                    } 
                }
                $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['get_billing_pdf_status'=>1, 'get_billing_pdf_date'=>date('d-m-Y H:i:s')]);
            } 
            $res = ['result'=>true];
            $this->response($res, REST_Controller::HTTP_OK);   
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function split_pdf_post(){   
        $post = $this->input->post();
        if($post){
            $periode = $post['periode'];
            $filename = $post['filename']; 
            $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['upload_faktur_pdf_status'=>2]);
            $path_file = '/var/www/admin-my.indonet.id/files/temp/'; 
            $path = './files/data_billing/'.$periode.'/e-faktur/';  
            if (!is_dir($path)) {
                mkdir($path, 0755, TRUE);                
            }  
            $file_source = $path_file.$filename.'.pdf';
            $pdf = new FPDI();
            $pagecount = $pdf->setSourceFile($file_source);  
            for ($i = 1; $i <= $pagecount; $i++) {  
                $newPdf = new Fpdi();
                $newPdf->addPage();
                $newPdf->setSourceFile($file_source);
                $newPdf->useTemplate($newPdf->importPage($i));
        
                $new_filename = $path.$filename.'_'.$i.".pdf"; 
                $newPdf->output($new_filename, 'F');   
            }
            
            $pdf->close(); 
            $this->rename_pdf($filename, $path, $pagecount, $periode);
        }
    }
    function rename_pdf($filename, $path, $pagecount, $periode){  
        $file_name_before = '';
        for ($i = 1; $i <= $pagecount; $i++) {  
            $parser = new \Smalot\PdfParser\Parser();
            $file_split = $path.$filename.'_'.$i.'.pdf';  
            $pdf = $parser->parseFile($file_split);  
            $textContent = $pdf->getText(); 
            $arr = explode("\n", $textContent); 
            $key_arr = '';
            foreach ($arr as $key => $value) { 
                if( strpos( $value, 'Kode dan Nomor Seri Faktur Pajak' ) !== false) {
                    $key_arr = $key; 
                    break;
                }  
            }  
            if($key_arr != ''){
                $kode = explode(":", $arr[$key_arr]); 
                $kode_no_seri = str_replace(" ","",$kode[1]);
                $new_name = $path.$kode_no_seri.'.pdf';
                rename($file_split, $new_name);
                $file_name_before = $new_name;
            }else{ 
                $merge = new \Jurosh\PDFMerge\PDFMerger;   
                $merge->addPDF($file_name_before, 'all', 'vertical')
                    ->addPDF($file_split, 'all', 'vertical');  
                $merge->merge('file', $file_name_before); 
                 
                echo 'page no '.$i.' error'."\n";
            }
        }
        $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['upload_faktur_pdf_status'=>1, 'upload_faktur_pdf_date'=>date('d-m-Y H:i:s')]);
    }
    function set_seri_faktur_pajak_post(){   
        $post = $this->input->post();
        if($post){
            $periode = $post['periode'];
            $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['upload_faktur_excel_status'=>2]);
            $filename = $post['filename']; 
            $path_file = '/var/www/admin-my.indonet.id/files/temp/'; 
            $file_source = $path_file.'/'.$filename.'.xlsx';
            $objPHPExcel = PHPExcel_IOFactory::load($file_source); 
            $worksheet = $objPHPExcel->getSheet(0);  
            $highestRow = $worksheet->getHighestRow(); 
            $highestColumn = $worksheet->getHighestColumn(); 
            $count_npwp = 0;
            for($row=2; $row<=$highestRow; $row++){ 
                $npwp_no = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                $seri_no = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                $cust_id = $worksheet->getCellByColumnAndRow(14, $row)->getValue();  
                if(strlen($cust_id) < 10){
                    $add_zero = 10 - strlen($cust_id);
                    $count_zero = '';
                    for ($i=0; $i < $add_zero; $i++) { 
                        $count_zero = $count_zero.'0';
                    } 
                    $cust_id = $count_zero.$cust_id; 
                }
                $check_db = $this->api_model->get_where_data_row('billing_faktur_list', ['cust_id'=>$cust_id, 'cust_npwp_no'=>$npwp_no]);  
                if($check_db){
                    $update_db = $this->api_model->update_db('billing_faktur_list', ['id'=>$check_db['id']], ['faktur_pajak_no_seri'=>$seri_no]);
                    if($update_db){
                        $file_faktur_pdf = $seri_no.'.pdf';
                        $file_source = './files/data_billing/'.$periode.'/e-faktur/';
                        $path_file = $file_source.$file_faktur_pdf;
                        if (file_exists($path_file)) {
                            $update_db = $this->api_model->update_db('billing_faktur_list', ['id'=>$check_db['id']], ['faktur_pajak_file'=>$file_faktur_pdf, 'faktur_pajak_status'=>1]);
                        } else {
                            $update_db = $this->api_model->update_db('billing_faktur_list', ['id'=>$check_db['id']], ['faktur_pajak_status'=>2]);
                        }
                    }
                } 
            }  
            $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['upload_faktur_excel_status'=>1, 'upload_faktur_excel_date'=>date('d-m-Y H:i:s')]);
        }
    }
    function send_email_pf_periode_post(){ 
        $post = $this->input->post();
        if($post){  
            $periode = $post['periode'];  
            $send_corporate = $post['send_corporate'];  
            $send_retail = $post['send_retail'];  
            $send_personal = $post['send_personal']; 
            $send_non_alibaba = $post['send_non_alibaba'];  
            $send_alibaba = $post['send_alibaba'];  
            $send_npwp = $post['send_npwp'];  
            $send_non_npwp = $post['send_non_npwp'];  
            $arr1 = ['periode'=>$periode];    
            $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['send_email_all_status'=>2]); 
            if($send_corporate == 1 && $send_retail == 1 && $send_personal == 1){
                $arr3 = $arr1;  
                if($send_non_alibaba == 1){
                    $arr4 = array('cust_instal_name != '=>'Alicloud');
                    $arr5 = $arr3 + $arr4;

                }
                if($send_alibaba == 1){
                    $arr4 = array('cust_instal_name'=>'Alicloud');
                    $arr5 = $arr3 + $arr4;
                } 
                if($send_non_npwp == 1 && $send_npwp == 1){ 
                    $where = $arr5;
                }else{
                    if($send_non_npwp == 1){
                        $arr6 = array('cust_npwp_no'=>'');
                        $where = $arr5 + $arr6; 
                    }
                    if($send_npwp == 1){
                        $arr6 = array('cust_npwp_no !='=>'');
                        $where = $arr5 + $arr6; 
                    } 
                } 
                $data_cust_list = $this->api_model->get_where_data('billing_faktur_list', $where); 
                if($data_cust_list){
                    foreach ($data_cust_list as $key => $value) {
                        $periode = $value['periode']; 
                        $id = $value['id']; 
                        $cust_id = $value['cust_id'];
                        $cust_email = $value['cust_email']; 
                        $cust_name = $value['cust_name'];
                        $billing_amount = $value['billing_amount'];
                        $billing_file = $value['billing_file'];
                        $faktur_file = $value['faktur_pajak_file'];
                        $no_npwp = $value['cust_npwp_no'];  
            
                        $send_email_status = $value['send_email_status']; //new
                        if($send_email_status == 0){  //new
                            $inv_month = substr($periode,4,2);
                            $inv_year = substr($periode,2,2);
                            $inv_year_name = substr($periode,0,4);  
                            $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
                            $dt = DateTime::createFromFormat('y', $inv_year);  
                            $periode_name = $inv_month_name.' '.$inv_year_name; 
                            $this->data['periode_name'] = $periode_name;
                            $this->data['cust_email'] = $cust_email;
                            $this->data['cust_name'] = $cust_name;
                            $this->data['cust_id'] = $cust_id;
                            $this->data['billing_amount'] = number_format($billing_amount,2, '.', ','); 
                            $this->load->library('email'); 
                            $this->email->clear(TRUE);
                            $send_status = 0;
                            if($billing_file != ''){
                                $send_status = 1; //new
                                $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-statement/'.$billing_file);  
                            }else{ 
                                $send_status = 0; //new
                            } 
                            if($no_npwp != ''){
                                if($faktur_file != ''){ 
                                    $send_status = 1; //new
                                    $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-faktur/'.$faktur_file);  
                                }else{ 
                                    $send_status = 0; //new
                                }
                            } 
                            if($send_status == 1){ 
                                $this->email->from('my.indonet@indonet.co.id', 'Indonet'); 
                                $this->email->subject('Billing statement Indonet '.$periode_name);   
                                $message = $this->load->view('email_template/billing', $this->data, true); 
                                $this->email->message($message); 
                                // $this->email->to($cust_email);  
                                $this->email->to('syarip.hidayatullah@indonet.co.id');  
                                if ( ! $this->email->send()){   
                                    $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>2,  'send_email_date'=>date('Y-m-d H:i:s'), 'send_email_info'=> $this->email->print_debugger()]);
                                    // echo $this->email->print_debugger();
                                    // echo 'tidak terkirim '.$cust_id; 
                                }else{ 
                                    $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>1, 'send_email_date'=>date('Y-m-d H:i:s')]);
                                    // echo 'terkirim '.$cust_id;
                                }  
                            }
                        } 
                    }
                } 
            }else{ 
                if($send_corporate == 1){
                    $arr2 = array('cust_type'=>'Corporate');
                    $arr3 = $arr1 + $arr2;
                    if($send_non_alibaba == 1){
                        $arr4 = array('cust_instal_name != '=>'Alicloud');
                        $arr5 = $arr3 + $arr4; 
                    }
                    if($send_alibaba == 1){
                        $arr4 = array('cust_instal_name'=>'Alicloud');
                        $arr5 = $arr3 + $arr4;
                    }
                    if($send_non_npwp == 1 && $send_npwp == 1){ 
                        $where = $arr5;
                    }else{
                        if($send_non_npwp == 1){
                            $arr6 = array('cust_npwp_no'=>'');
                            $where = $arr5 + $arr6; 
                        }
                        if($send_npwp == 1){
                            $arr6 = array('cust_npwp_no !='=>'');
                            $where = $arr5 + $arr6; 
                        } 
                    } 
                    $data_cust_list = $this->api_model->get_where_data('billing_faktur_list', $where); 
                    if($data_cust_list){
                        foreach ($data_cust_list as $key => $value) {
                            $periode = $value['periode']; 
                            $id = $value['id']; 
                            $cust_id = $value['cust_id'];
                            $cust_email = $value['cust_email']; 
                            $cust_name = $value['cust_name'];
                            $billing_amount = $value['billing_amount'];
                            $billing_file = $value['billing_file'];
                            $faktur_file = $value['faktur_pajak_file'];
                            $no_npwp = $value['cust_npwp_no'];  
                
                            $send_email_status = $value['send_email_status']; //new
                            if($send_email_status == 0){  //new
                                $inv_month = substr($periode,4,2);
                                $inv_year = substr($periode,2,2);
                                $inv_year_name = substr($periode,0,4);  
                                $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
                                $dt = DateTime::createFromFormat('y', $inv_year);  
                                $periode_name = $inv_month_name.' '.$inv_year_name; 
                                $this->data['periode_name'] = $periode_name;
                                $this->data['cust_email'] = $cust_email;
                                $this->data['cust_name'] = $cust_name;
                                $this->data['cust_id'] = $cust_id;
                                $this->data['billing_amount'] = number_format($billing_amount,2, '.', ',');
                                $this->load->library('email'); 
                                $this->email->clear(TRUE); 
                                $send_status = 0;
                                if($billing_file != ''){
                                    $send_status = 1; //new
                                    $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-statement/'.$billing_file);  
                                }else{ 
                                    $send_status = 0; //new
                                } 
                                if($no_npwp != ''){
                                    if($faktur_file != ''){ 
                                        $send_status = 1; //new
                                        $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-faktur/'.$faktur_file);  
                                    }else{ 
                                        $send_status = 0; //new
                                    }
                                } 
                                if($send_status == 1){ 
                                    $this->email->from('my.indonet@indonet.co.id', 'Indonet'); 
                                    $this->email->subject('Billing statement Indonet '.$periode_name);   
                                    $message = $this->load->view('email_template/billing', $this->data, true); 
                                    $this->email->message($message); 
                                    // $this->email->to($cust_email);  
                                    $this->email->to('syarip.hidayatullah@indonet.co.id');  
                                    if ( ! $this->email->send()){   
                                        $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>2,  'send_email_date'=>date('Y-m-d H:i:s'), 'send_email_info'=> $this->email->print_debugger()]);
                                        // echo $this->email->print_debugger();
                                        // echo 'tidak terkirim '.$cust_id; 
                                    }else{ 
                                        $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>1, 'send_email_date'=>date('Y-m-d H:i:s')]);
                                        // echo 'terkirim '.$cust_id;
                                    }  
                                }
                            }   
                        }
                    } 
                }  
                if($send_retail == 1){
                    $arr2 = array('cust_type'=>'Retail');
                    $arr3 = $arr1 + $arr2; 
                    if($send_non_alibaba == 1){
                        $arr4 = array('cust_instal_name != '=>'Alicloud');
                        $arr5 = $arr3 + $arr4; 
                    }
                    if($send_alibaba == 1){
                        $arr4 = array('cust_instal_name'=>'Alicloud');
                        $arr5 = $arr3 + $arr4;
                    }
                    if($send_non_npwp == 1 && $send_npwp == 1){ 
                        $where = $arr5;
                    }else{
                        if($send_non_npwp == 1){
                            $arr6 = array('cust_npwp_no'=>'');
                            $where = $arr5 + $arr6; 
                        }
                        if($send_npwp == 1){
                            $arr6 = array('cust_npwp_no !='=>'');
                            $where = $arr5 + $arr6; 
                        } 
                    }   
                    $data_cust_list = $this->api_model->get_where_data('billing_faktur_list', $where); 
                    // print_r($data_cust_list); die();
                    if($data_cust_list){
                        foreach ($data_cust_list as $key => $value) {
                            $periode = $value['periode']; 
                            $cust_id = $value['cust_id'];
                            $cust_email = $value['cust_email']; 
                            $cust_name = $value['cust_name'];
                            $billing_amount = $value['billing_amount'];
                            $billing_file = $value['billing_file'];
                            $faktur_file = $value['faktur_pajak_file'];
                            $no_npwp = $value['cust_npwp_no'];   
                            $send_email_status = $value['send_email_status']; //new
                            if($send_email_status == 0){  //new
                                $inv_month = substr($periode,4,2);
                                $inv_year = substr($periode,2,2);
                                $inv_year_name = substr($periode,0,4);  
                                $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
                                $dt = DateTime::createFromFormat('y', $inv_year);  
                                $periode_name = $inv_month_name.' '.$inv_year_name; 
                                $this->data['periode_name'] = $periode_name;
                                $this->data['cust_email'] = $cust_email;
                                $this->data['cust_name'] = $cust_name;
                                $this->data['cust_id'] = $cust_id;
                                $this->data['billing_amount'] = number_format($billing_amount,2, '.', ','); 
                                $this->load->library('email'); 
                                $this->email->clear(TRUE);
                                $send_status = 0;
                                if($billing_file != ''){
                                    $send_status = 1; //new
                                    $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-statement/'.$billing_file);  
                                }else{ 
                                    $send_status = 0; //new
                                } 
                                if($no_npwp != ''){
                                    if($faktur_file != ''){ 
                                        $send_status = 1; //new
                                        $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-faktur/'.$faktur_file);  
                                    }else{ 
                                        $send_status = 0; //new
                                    }
                                } 
                                if($send_status == 1){ 
                                    $this->email->from('my.indonet@indonet.co.id', 'Indonet'); 
                                    $this->email->subject('Billing statement Indonet '.$periode_name);   
                                    $message = $this->load->view('email_template/billing', $this->data, true); 
                                    $this->email->message($message); 
                                    // $this->email->to($cust_email);  
                                    $this->email->to('syarip.hidayatullah@indonet.co.id');  
                                    if ( ! $this->email->send()){   
                                        $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>2,  'send_email_date'=>date('Y-m-d H:i:s'), 'send_email_info'=> $this->email->print_debugger()]);
                                        // echo $this->email->print_debugger();
                                        // echo 'tidak terkirim '.$cust_id; 
                                    }else{ 
                                        $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>1, 'send_email_date'=>date('Y-m-d H:i:s')]);
                                        // echo 'terkirim '.$cust_id;
                                    }  
                                }
                            } 
                        }
                    }  
                }  
                if($send_personal == 1){
                    $arr2 = array('cust_type'=>'Personal', 'send_email_status'=>'0');
                    $arr3 = $arr1 + $arr2;  
                    if($send_non_alibaba == 1){
                        $arr4 = array('cust_instal_name !='=>'Alicloud');
                        $arr5 = $arr3 + $arr4; 
                    }
                    if($send_alibaba == 1){
                        $arr4 = array('cust_instal_name'=>'Alicloud');
                        $arr5 = $arr3 + $arr4;
                    }
                    if($send_non_npwp == 1 && $send_npwp == 1){ 
                        $where = $arr5;
                    }else{
                        if($send_non_npwp == 1){
                            $arr6 = array('cust_npwp_no'=>'');
                            $where = $arr5 + $arr6; 
                        }
                        if($send_npwp == 1){
                            $arr6 = array('cust_npwp_no !='=>'');
                            $where = $arr5 + $arr6; 
                        } 
                    } 
                    // print_r($where); die();
                    $data_cust_list = $this->api_model->get_where_data('billing_faktur_list', $where);  
                    if($data_cust_list){
                        foreach ($data_cust_list as $key => $value) {
                            $periode = $value['periode']; 
                            $id = $value['id']; 
                            $cust_id = $value['cust_id'];
                            $cust_email = $value['cust_email']; 
                            $cust_name = $value['cust_name'];
                            $billing_amount = $value['billing_amount'];
                            $billing_file = $value['billing_file'];
                            $faktur_file = $value['faktur_pajak_file'];
                            $no_npwp = $value['cust_npwp_no'];    
                            $inv_month = substr($periode,4,2);
                            $inv_year = substr($periode,2,2);
                            $inv_year_name = substr($periode,0,4);  
                            $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
                            $dt = DateTime::createFromFormat('y', $inv_year);  
                            $periode_name = $inv_month_name.' '.$inv_year_name; 
                            $this->data['periode_name'] = $periode_name;
                            $this->data['cust_email'] = $cust_email;
                            $this->data['cust_name'] = $cust_name;
                            $this->data['cust_id'] = $cust_id;
                            $this->data['billing_amount'] = number_format($billing_amount,2, '.', ','); 
                            $this->load->library('email'); 
                            $this->email->clear(TRUE);
                            $send_status = 0;
                            if($billing_file != ''){
                                $send_status = 1; //new
                                $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-statement/'.$billing_file);  
                            }else{ 
                                $send_status = 0; //new
                            } 
                            if($no_npwp != ''){
                                if($faktur_file != ''){ 
                                    $send_status = 1; //new
                                    $this->email->attach('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-faktur/'.$faktur_file);  
                                }else{ 
                                    $send_status = 0; //new
                                }
                            } 
                            if($send_status == 1){ 
                                $this->email->from('my.indonet@indonet.co.id', 'Indonet'); 
                                $this->email->subject('Billing statement Indonet '.$periode_name);   
                                $message = $this->load->view('email_template/billing', $this->data, true); 
                                $this->email->message($message); 
                                // $this->email->to($cust_email);  
                                $this->email->to('bayu.oktaanggara@indonet.co.id, syarip.hidayatullah@indonet.co.id');  
                                if ( ! $this->email->send()){   
                                    $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>2,  'send_email_date'=>date('Y-m-d H:i:s'), 'send_email_info'=> $this->email->print_debugger()]);
                                    // echo $this->email->print_debugger();
                                    // echo 'tidak terkirim '.$cust_id; 
                                }else{ 
                                    $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>1, 'send_email_date'=>date('Y-m-d H:i:s')]);
                                    // echo 'terkirim '.$cust_id;
                                }  
                            } 
                        }
                    } 
                }    
            }  
            $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['send_email_all_status'=>1, 'send_email_all_date'=>date('d-m-Y H:i:s')]);
        }  
    }
    function blacklist_get(){
        $data_db = $this->api_model->get_where_data('billing_faktur_blacklist', ['status'=>1]);
        if($data_db){ 
            $res = ['result'=>true, 'data_list'=>$data_db];
            $this->response($res, REST_Controller::HTTP_OK);    
        }else{
            $res = ['result'=>false];
            $this->response($res, REST_Controller::HTTP_OK);   
        }
    }
    function save_blacklist_post(){
        $post = $this->input->post();
        if(isset($post)){  
            $cust_id = $post['cust_id'];
            $billing_type = $post['billing_type'];
            $data_db = $this->api_model->get_where_data_row('billing_faktur_blacklist', ['cust_id'=>$cust_id]);
            if($data_db){
                $res = ['result'=>false]; 
            }else{ 
                $cust_id = $post['cust_id']; 
                $data_cust = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);
                if($data_cust){
                    $res = array('result'=>true, 'data_cust'=>$data_cust);    
                    $add_db = $this->api_model->add_db('billing_faktur_blacklist', ['cust_id'=>$cust_id, 'cust_name'=>$data_cust['cust_name'], 'billing_type'=>$billing_type,
                    'created_date'=>date('d-m-Y H:i:s')]); 
                    $res = ['result'=>true];
                }else{
                    $res = array('result'=>false); 
                } 
            } 
            $this->response($res, REST_Controller::HTTP_OK);  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    } 
    function remove_blacklist_post(){
        $post = $this->input->post();
        if(isset($post)){  
            $cust_id = $post['cust_id']; 
            $data_db = $this->api_model->get_where_data_row('billing_faktur_blacklist', ['cust_id'=>$cust_id]);
            if($data_db){ 
                $rm_data = $this->api_model->delete_db('billing_faktur_blacklist', ['cust_id'=>$cust_id]); 
                $res = ['result'=>true]; 
            }else{ 
                $res = ['result'=>false]; 
            } 
            $this->response($res, REST_Controller::HTTP_OK);  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
    function upload_blacklist_post(){
        $post = $this->input->post();
        if($post){  
            $filename = $post['filename']; 
            $path_file = '/var/www/admin-my.indonet.id/files/temp/'; 
            $file_source = $path_file.'/'.$filename.'.xlsx';
            $objPHPExcel = PHPExcel_IOFactory::load($file_source); 
            $worksheet = $objPHPExcel->getSheet(0);  
            $highestRow = $worksheet->getHighestRow(); 
            $highestColumn = $worksheet->getHighestColumn();  
            for($row=2; $row<=$highestRow; $row++){ 
                $cust_id = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                $cust_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();  
                $cust_noted = $worksheet->getCellByColumnAndRow(3, $row)->getValue();  
                if(strlen($cust_id) < 10){
                    $add_zero = 10 - strlen($cust_id);
                    $count_zero = '';
                    for ($i=0; $i < $add_zero; $i++) { 
                        $count_zero = $count_zero.'0';
                    } 
                    $cust_id = $count_zero.$cust_id; 
                }
                $check_db = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);  
                if($check_db){ 
                    $check_db_blacklist = $this->api_model->get_where_data_row('billing_faktur_blacklist', ['cust_id'=>$cust_id]);
                    if(!$check_db_blacklist){
                        $add_db = $this->api_model->add_db('billing_faktur_blacklist', ['cust_id'=>$cust_id, 'cust_name'=>$check_db['cust_name'], 
                                                            'billing_type'=>$cust_noted, 'created_date'=>date('d-m-Y H:i:s')]);
                    }else{
                        $update_db = $this->api_model->update_db('billing_faktur_blacklist', ['id'=>$check_db_blacklist['id']], ['billing_type'=>$cust_noted,
                                                                'cust_name'=>$check_db['cust_name'], 'created_date'=>date('d-m-Y H:i:s')]); 
                    } 
                }else{
                    $check_db_blacklist = $this->api_model->get_where_data_row('billing_faktur_blacklist', ['cust_id'=>$cust_id]);
                    if(!$check_db_blacklist){
                        $add_db = $this->api_model->add_db('billing_faktur_blacklist', ['cust_id'=>$cust_id, 'cust_name'=>$cust_name, 
                                                            'billing_type'=>$cust_noted, 'created_date'=>date('d-m-Y H:i:s')]);
                    }else{
                        $update_db = $this->api_model->update_db('billing_faktur_blacklist', ['id'=>$check_db_blacklist['id']], ['billing_type'=>$cust_noted,
                                                                'cust_name'=>$cust_name, 'created_date'=>date('d-m-Y H:i:s')]); 
                    } 
                } 
            } 
        }
    }
    function get_cust_data_by_id_post(){
        $post = $this->input->post();
        if(isset($post)){  
            $id = $post['id']; 
            $data_db = $this->api_model->get_where_data_row('billing_faktur_list', ['id'=>$id]);
            if($data_db){  
                $res = ['result'=>true, 'data'=>$data_db]; 
            }else{ 
                $res = ['result'=>false]; 
            } 
            $this->response($res, REST_Controller::HTTP_OK);  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
    function send_email_pf_single_post(){
        $post = $this->input->post();
        if($post){  
            $list_id = $post['list_id'];   
            $data_db = $this->api_model->get_where_data_row('billing_faktur_list', ['id'=>$list_id]);
            if($data_db){ 
                $periode = $data_db['periode']; 
                $id = $data_db['id']; 
                $cust_id = $data_db['cust_id'];
                $cust_email = $data_db['cust_email']; 
                $cust_name = $data_db['cust_name'];
                $billing_amount = $data_db['billing_amount'];
                $billing_file = $data_db['billing_file'];
                $faktur_file = $data_db['faktur_pajak_file'];
                $no_npwp = $data_db['cust_npwp_no'];    
                $inv_month = substr($periode,4,2);
                $inv_year = substr($periode,2,2);
                $inv_year_name = substr($periode,0,4);  
                $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
                $dt = DateTime::createFromFormat('y', $inv_year);  
                $periode_name = $inv_month_name.' '.$inv_year_name; 
                $this->data['periode_name'] = $periode_name;
                $this->data['cust_email'] = $cust_email;
                $this->data['cust_name'] = $cust_name;
                $this->data['cust_id'] = $cust_id;
                $this->data['billing_amount'] = number_format($billing_amount,2, '.', ','); 
                // $this->load->library('email'); 
                // $this->email->clear(TRUE);
                $send_status = 0;

                $mail = new PHPMailer(true); 

                try {

                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                    $mail->isSMTP();                                            //Send using SMTP
                    $mail->Host       = 'mail.indonet.co.id';                     //Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                    $mail->Username   = 'noreply@indonet.co.id';                     //SMTP username
                    $mail->Password   = 'd0d0L2022&*()';                               //SMTP password
                    $mail->SMTPSecure = 'starttls';            //Enable implicit TLS encryption
                    $mail->Port       = 25;             
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    
                    
                    $mail->setFrom('noreply@indonet.co.id', 'Indonet');
                    $mail->addAddress('syarip.hidayatullah@indonet.co.id', 'Syarip ');     //Add a recipient   
                    $mail->addAddress('jason.wanardi@indonet.co.id', 'Jason ');     //Add a recipient   
                    // $mail->addAddress('geri.anggara@indonet.co.id', 'Geri ');     //Add a recipient   
                    // $mail->addAddress('geri.anggara@gmail.com', 'Geri ');     //Add a recipient   
                    $mail->addAddress('itssyarip@gmail.com', 'sh ');     //Add a recipient   
                    $mail->addAddress('eldzee@gmail.com', 'ez ');     //Add a recipient   
                    // $mail->addAddress('rendra.purwanda@indonet.co.id', 'Rendra ');     //Add a recipient   
  
                    if($billing_file != ''){
                        $send_status = 1; //new
                        $mail->addAttachment('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-statement/'.$billing_file);  
                    }else{ 
                        $send_status = 0; //new
                    } 
                    if($no_npwp != ''){
                        if($faktur_file != ''){ 
                            $send_status = 1; //new
                            $mail->addAttachment('/var/www/api-my.indonet.id/files/data_billing/'.$periode.'/e-faktur/'.$faktur_file);  
                        }else{ 
                            $send_status = 0; //new
                        }
                    } 

                    //Content
                    $mail->isHTML(false);                                  //Set email format to HTML
                    $mail->Subject = 'Here is the subject';
                    $mail->Body    = 'This is the HTML message 1527';
                    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients'; 

                    $cert_key = '/etc/ssl/noreply/cert.key';
                    $cert_crt = '/etc/ssl/noreply/cert.crt';
                    $cert_ca = '/etc/ssl/noreply/certca.pem';
                    $mail->sign($cert_crt, $cert_key, '',$cert_ca);
                
                    $mail->send(); 
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }


                /*
                // if($send_status == 1){ 
                    // $this->email->from('my.indonet@indonet.co.id', 'Indonet'); 
                    $this->email->from('noreply@indonet.co.id', 'Kodok'); 
                    $this->email->subject('Billing statement Indonet '.$periode_name);   
                    $message = $this->load->view('email_template/billing', $this->data, true); 
                    $this->email->message($message); 
                    // $this->email->to($cust_email);  
                    $this->email->to('syarip.hidayatullah@indonet.co.id, adhe.rifai@indonet.co.id, jason.wanardi@indonet.co.id');  
                    // $this->email->to('syarip.hidayatullah@indonet.co.id');  
                    if ( ! $this->email->send()){   
                        // $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>2,  'send_email_date'=>date('Y-m-d H:i:s'), 'send_email_info'=> $this->email->print_debugger()]);
                        // echo $this->email->print_debugger();
                        echo 'tidak terkirim '.$cust_id; 
                    }else{ 
                        // $update = $this->api_model->update_db('billing_faktur_list', ['id'=>$id], ['send_email_status'=>1, 'send_email_date'=>date('Y-m-d H:i:s')]);
                        echo 'terkirim '.$cust_id;
                    }  
                // } 
                */
            }
          
        }
    }
    function view_billing_mini_post(){
        $post = $this->input->post();
        if($post){  
            $cust_id = $post['cust_id'];
            $periode = $post['periode'];
            $folder_billing = './files/data_billing/'.$periode.'/e-statement/'; 
            $file_name = $folder_billing.$cust_id.'.txt';
            if(file_exists($file_name)){  
                $file_data = file_get_contents($file_name);
                $file_data = json_decode($file_data, true); 
                $res = ['result'=>true, 'data'=>$file_data]; 
            }else{
                $res = ['result'=>false]; 
            }
            $this->response($res, REST_Controller::HTTP_OK);     
        }
    }
    function view_faktur_mini_post(){
        $post = $this->input->post();
        if($post){  
            $cust_id = $post['cust_id'];
            $periode = $post['periode'];
            $data_cust = $this->api_model->get_where_data_row('billing_faktur_list', ['cust_id'=>$cust_id, 'periode'=>$periode]); 
            if($data_cust){
                $faktur_file = $data_cust['faktur_pajak_file'];
                $folder_billing = './files/data_billing/'.$periode.'/e-faktur/'; 
                $file_name = $folder_billing.$faktur_file;
                if(file_exists($file_name)){   
                    $url = 'https://api-my.indonet.id/files/data_billing/'.$periode.'/e-faktur/'.$faktur_file; 
                    $file_data = ['url'=>$url];
                    $res = ['result'=>true, 'data'=>$file_data]; 
                } 
            }else{
                $res = ['result'=>false]; 
            }
            $this->response($res, REST_Controller::HTTP_OK);
        }
    }
    
    function create_billing_pdf_by_cust_post(){
        $post = $this->input->post(); 
        if($post){
            $cust_id = $post['cust_id'];   
            $data_cust_list = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);    
            if($data_cust_list){ 
                $id = $data_cust_list['id'];
                $periode = $data_cust_list['periode'];
                $cust_id = $data_cust_list['cust_id'];
                $billing_status = $data_cust_list['billing_status'];
                $subnet_code = $data_cust_list['cust_subnet_code'];  
                $cust_instal_name = $data_cust_list['cust_instal_name'];
                $posting_date_from_array = array(); 
                $posting_date_to_array = array(); 
                // if($billing_status == 0){ 
                    $inv_month = substr($periode,4,2);
                    $inv_year = substr($periode,2,2);
                    $inv_year_name = substr($periode,0,4); 
                    $periode_name = $inv_month.$inv_year;
                    $periodeYM = $inv_year_name.$inv_month;
                    $billing_file = 'SO-'.$periode_name.'-'.$cust_id.'.pdf';
                    $billing_code = 'SO-'.$periode_name.'-'.$cust_id;
                    $count_billing = 0;
                    if($cust_instal_name == 'Alicloud'){
                        $periode_from = date("Ym", strtotime("-1 months", strtotime(date($inv_year_name.'-'.$inv_month))));
                        $periode_to = $periodeYM;
                        $billing_info_from = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode_from]); 
                        $billing_info_to = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode_to]);
                        $posting_date_from =  date("Y-m-d", strtotime("+1 days", strtotime($billing_info_from['alibaba_posting_date'])));
                        $posting_date_to =  date("Y-m-d", strtotime($billing_info_to['alibaba_posting_date']));  
                        array_push($posting_date_from_array, $posting_date_from);  
                        array_push($posting_date_to_array, $posting_date_to);    
                        $data_ax = getCustInfoToInvMonth_new($cust_id, $inv_year_name, $inv_month, $posting_date_from_array, $posting_date_to_array);
                    }else{ 
                        $periode_from = date("Ym", strtotime("-1 months", strtotime(date($inv_year_name.'-'.$inv_month))));
                        $periode_to = $periodeYM; 
                        $posting_date_from =  date("Y-m-02", strtotime($periode_from));
                        $posting_date_to = date("Y-m-01", strtotime($periode_to));
                        array_push($posting_date_from_array, $posting_date_from);  
                        array_push($posting_date_to_array, $posting_date_to);  
                        $data_ax = getCustInfoToInvMonth($cust_id, $inv_year_name, $inv_month);  
                    }  
                    print_r($data_ax); die();
                    $billing_amount = 0;   
                    if($data_ax){     
                        $amount_8 = 0;
                        $crete_inv = 0;
                        foreach ($data_ax['INV_DETAIL_DATA'] as $key_det => $val_det) {
                            if($val_det['TRANSTYPE'] == 8){
                                $amount_8 = $amount_8 + $val_det['AMOUNTMST'];
                                if($amount_8 > 0){
                                    $crete_inv = 1;
                                }else{
                                    $crete_inv = 0;
                                }
                            } 
                        }   
                        if($crete_inv == 1){  
                            $folder_billing = './files/data_billing/'.$periode.'/e-statement/'; 
                            if (!is_dir($folder_billing)) {
                                mkdir($folder_billing, 0755, TRUE);                
                            }  
                            $file_to_text = $cust_id.'.txt';
                            $fp = fopen($folder_billing.$file_to_text, 'w'); 
                            fwrite($fp, json_encode($data_ax));
                            fclose($fp);
                            $this->data_inv['data_cust'] = $data_ax[0]; 
                            $this->data_inv['inv_detail_bill'] = $data_ax['INV_DETAIL_DATA']; 
                            $this->data_inv['inv_month_bill'] = $data_ax['INV_MONTH_TOTAL']; 
                            $this->data_inv['virtual_acc_bca'] = $data_ax['VIRTUAL_ACC'][0]; 
                            $this->data_inv['year_bill'] = $inv_year;
                            $this->data_inv['month_bill'] = $inv_month; 
                            $this->data_inv['inv_year_name'] = $inv_year_name;  
                            $this->data_inv['billing_code'] = $billing_code;    
                            $html = $this->load->view('customer/billing_template', $this->data_inv, true);    
                            $save = $this->pdf->save($html, $billing_file, $folder_billing);                               
                            $billing_amount = $data_ax['INV_MONTH_TOTAL']; 
                            $post_update = ['billing_status'=>1, 'billing_amount'=>$billing_amount, 'billing_file'=>$billing_file]; 
                            $this->api_model->update_db('billing_faktur_list', ['id'=>$id], $post_update);  
                        }else{                         
                            $billing_amount = $data_ax['INV_MONTH_TOTAL']; 
                            $post_update = ['billing_status'=>2, 'billing_amount'=>$billing_amount, 'billing_file'=>'No Usage'];  
                            $this->api_model->update_db('billing_faktur_list', ['id'=>$id], $post_update);  
                        }
                    } 
                // }  
                $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['get_billing_pdf_status'=>1, 'get_billing_pdf_date'=>date('d-m-Y H:i:s')]);
            } 
            $res = ['result'=>true];
            $this->response($res, REST_Controller::HTTP_OK);   
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function send_email_demo_post(){
        $post = $this->input->post();
        if($post){  
            $list_id = $post['list_id'];   
            $data_db = $this->api_model->get_where_data_row('billing_faktur_list', ['id'=>$list_id]);
            if($data_db){ 
                $periode = $data_db['periode']; 
                $id = $data_db['id']; 
                $cust_id = $data_db['cust_id'];
                $cust_email = $data_db['cust_email']; 
                $cust_name = $data_db['cust_name'];
                $billing_amount = $data_db['billing_amount'];
                $billing_file = $data_db['billing_file'];
                $faktur_file = $data_db['faktur_pajak_file'];
                $no_npwp = $data_db['cust_npwp_no'];    
                $inv_month = substr($periode,4,2);
                $inv_year = substr($periode,2,2);
                $inv_year_name = substr($periode,0,4);  
                $inv_month_name = date("F", mktime(0, 0, 0, $inv_month, 10));  
                $dt = DateTime::createFromFormat('y', $inv_year);  
                $periode_name = $inv_month_name.' '.$inv_year_name; 

 

                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true); 

                try {

                    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                    $mail->isSMTP();                                            //Send using SMTP
                    $mail->Host       = 'mail.indonet.co.id';                     //Set the SMTP server to send through
                    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                    $mail->Username   = 'noreply@indonet.co.id';                     //SMTP username
                    $mail->Password   = 'd0d0L2022&*()';                               //SMTP password
                    $mail->SMTPSecure = 'starttls';            //Enable implicit TLS encryption
                    $mail->Port       = 25;             
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    
                    
                    $mail->setFrom('noreply@indonet.co.id', 'Indonet');
                    $mail->addAddress('syarip.hidayatullah@indonet.co.id', 'Syarip ');     //Add a recipient   
                    $mail->addAddress('jason.wanardi@indonet.co.id', 'Jason ');     //Add a recipient    
                
                    // //Attachments
                    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
                
                    //Content
                    $mail->isHTML(false);                                  //Set email format to HTML
                    $mail->Subject = 'Here is the subject';
                    $mail->Body    = 'This is the HTML message 1527';
                    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients'; 

                    $cert_key = '/etc/ssl/noreply/cert.key';
                    $cert_crt = '/etc/ssl/noreply/cert.crt';
                    $cert_ca = '/etc/ssl/noreply/certca.pem';
                    $mail->sign($cert_crt, $cert_key, '',$cert_ca);
                
                    $mail->send(); 
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
          
        }
    }

}