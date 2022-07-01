<?php 
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 

class Ax extends REST_Controller {  
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta'); 
        $this->load->helper('url'); 
        include_once(APPPATH . '/libraries/ax/index.php'); 
        $this->load->model(array('api_model')); 
    }   
	public function index_get(){    
        $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
    }
    public function index_post(){
		$post = $this->input->post(); 
		switch ($post['type']) {
			case 'get_info_by_ax_id': 
				$this->get_info_by_ax_id($post);
				break;  
            case 'get_total_inv': 
                $this->get_total_inv($post);
                break;  
            case 'get_file_cust_ax': 
                $this->get_file_cust_ax($post);
                break;  
			default: 
                $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
				break;
		}
	} 
    function renew_cust_list_ax_get(){  
        $arraySubnets = array(  'S-001','S-002','S-003','S-004','S-005','S-006','S-007','S-008','S-009','S-010','S-011','S-012','S-013','S-014','S-015','S-016',
                                'S-017','S-018','S-019','S-020','S-021','S-022', 'S-023');   
 
        $this->api_model->truncate_table('ax_customer_list');  
        foreach ($arraySubnets as $key => $value) {
            $subnet_code = $value;   
            $cust_type = 'Corporate';
            $out = getCustAccListUnderSubnetType($subnet_code, $cust_type);
            if($out){  
                $date_now = date('Y-m-d H:i:s');
                foreach ($out as $key_cust => $value_cust) {  
                    $cust_status_name = '';
                    switch ($value_cust['MK_CUSTSTATUS']) {
                        case 0:
                            $cust_status_name = 'Active';
                            break;
                        case 1:
                            $cust_status_name = 'Hold';
                            break;
                        case 2:
                            $cust_status_name = 'Close';
                            break; 
                    }
                    $array_post = [ 'update_date'=>$date_now, 'cust_id'=>$value_cust['ACCOUNTNUM'], 'cust_name'=>$value_cust['NAME'], 'cust_knownas'=>$value_cust['KNOWNAS'],
                                    'cust_email'=>$value_cust['EMAIL'], 'cust_type'=>$value_cust['TYPECUST'], 'cust_instal_name'=>$value_cust['INSTALATIONNAME'],
                                    'cust_status'=>$value_cust['MK_CUSTSTATUS'], 'cust_npwp_no'=>$value_cust['VATNUM'], 'cust_npwp_name'=>$value_cust['NPWPNAME'],
                                    'cust_subnet_code'=>$value_cust['SALESDISTRICTID'], 'cust_subnet_name'=>$value_cust['DISTRICTNAME'], 'cust_status_name'=>$cust_status_name];
                    $add_cust = $this->api_model->add_db('ax_customer_list', $array_post);
                    if($add_cust){
                        $custUserData = getCustUsernameList($value_cust['ACCOUNTNUM']);  
                        $user_id_array = ''; 
                        foreach ($custUserData as $keyUser => $valUser) {
                            $user_id = $valUser['USERNAME'];
                            if($user_id_array == ''){
                                $user_id_array = $user_id;
                            }else{ 
                                $user_id_array = $user_id_array.', '.$user_id;
                            }
                        }
                        $update = $this->api_model->update_db('ax_customer_list', array('id'=>$add_cust), array('cust_user_id'=>$user_id_array));
                    }
                
                } 
            }  
            $cust_type = 'Retail';
            $out = getCustAccListUnderSubnetType($subnet_code, $cust_type);
            if($out){  
                $date_now = date('Y-m-d H:i:s');
                foreach ($out as $key_cust => $value_cust) {  
                    $cust_status_name = '';
                    switch ($value_cust['MK_CUSTSTATUS']) {
                        case 0:
                            $cust_status_name = 'Active';
                            break;
                        case 1:
                            $cust_status_name = 'Hold';
                            break;
                        case 2:
                            $cust_status_name = 'Close';
                            break; 
                    }
                    $array_post = [ 'update_date'=>$date_now, 'cust_id'=>$value_cust['ACCOUNTNUM'], 'cust_name'=>$value_cust['NAME'], 'cust_knownas'=>$value_cust['KNOWNAS'],
                                    'cust_email'=>$value_cust['EMAIL'], 'cust_type'=>$value_cust['TYPECUST'], 'cust_instal_name'=>$value_cust['INSTALATIONNAME'],
                                    'cust_status'=>$value_cust['MK_CUSTSTATUS'], 'cust_npwp_no'=>$value_cust['VATNUM'], 'cust_npwp_name'=>$value_cust['NPWPNAME'],
                                    'cust_subnet_code'=>$value_cust['SALESDISTRICTID'], 'cust_subnet_name'=>$value_cust['DISTRICTNAME'], 'cust_status_name'=>$cust_status_name];
                    $add_cust = $this->api_model->add_db('ax_customer_list', $array_post);
                    if($add_cust){
                        $custUserData = getCustUsernameList($value_cust['ACCOUNTNUM']);  
                        $user_id_array = ''; 
                        foreach ($custUserData as $keyUser => $valUser) {
                            $user_id = $valUser['USERNAME'];
                            if($user_id_array == ''){
                                $user_id_array = $user_id;
                            }else{ 
                                $user_id_array = $user_id_array.', '.$user_id;
                            }
                        }
                        $update = $this->api_model->update_db('ax_customer_list', array('id'=>$add_cust), array('cust_user_id'=>$user_id_array));
                    }
                
                } 
            }  
            $cust_type = 'Personal';
            $out = getCustAccListUnderSubnetType($subnet_code, $cust_type);
            if($out){  
                $date_now = date('Y-m-d H:i:s');
                foreach ($out as $key_cust => $value_cust) {  
                    $cust_status_name = '';
                    switch ($value_cust['MK_CUSTSTATUS']) {
                        case 0:
                            $cust_status_name = 'Active';
                            break;
                        case 1:
                            $cust_status_name = 'Hold';
                            break;
                        case 2:
                            $cust_status_name = 'Close';
                            break; 
                    }
                    $array_post = [ 'update_date'=>$date_now, 'cust_id'=>$value_cust['ACCOUNTNUM'], 'cust_name'=>$value_cust['NAME'], 'cust_knownas'=>$value_cust['KNOWNAS'],
                                    'cust_email'=>$value_cust['EMAIL'], 'cust_type'=>$value_cust['TYPECUST'], 'cust_instal_name'=>$value_cust['INSTALATIONNAME'],
                                    'cust_status'=>$value_cust['MK_CUSTSTATUS'], 'cust_npwp_no'=>$value_cust['VATNUM'], 'cust_npwp_name'=>$value_cust['NPWPNAME'],
                                    'cust_subnet_code'=>$value_cust['SALESDISTRICTID'], 'cust_subnet_name'=>$value_cust['DISTRICTNAME'], 'cust_status_name'=>$cust_status_name];
                    $add_cust = $this->api_model->add_db('ax_customer_list', $array_post);
                    if($add_cust){
                        $custUserData = getCustUsernameList($value_cust['ACCOUNTNUM']);  
                        $user_id_array = ''; 
                        foreach ($custUserData as $keyUser => $valUser) {
                            $user_id = $valUser['USERNAME'];
                            if($user_id_array == ''){
                                $user_id_array = $user_id;
                            }else{ 
                                $user_id_array = $user_id_array.', '.$user_id;
                            }
                        }
                        $update = $this->api_model->update_db('ax_customer_list', array('id'=>$add_cust), array('cust_user_id'=>$user_id_array));
                    }
                
                } 
            }  
        }
        $this->set_cust_by_name();
    } 
    function set_cust_by_name(){ 
        $cust_list = $this->api_model->get_where_data('ax_customer_list', array('cust_name !='=>''), 'cust_name', 'asc');
        if($cust_list){
            $this->api_model->truncate_table('ax_customer_group');  
            $group_name = '';
            foreach ($cust_list as $key => $value) {
                $cust_name = $value['cust_name'];
                $check_exist = $this->api_model->get_where_data_row('ax_customer_group', array('cust_name '=>$cust_name), 'cust_name', 'asc');
                if(!$check_exist){
                    $cust_list = $this->api_model->get_where_data('ax_customer_list', array('cust_name '=>$cust_name), 'cust_name', 'asc');
                    $array_group = array();
                    foreach ($cust_list as $key_list => $value_list) {
                        $cust_id = $value_list['cust_id'];
                        $cust_knownas = $value_list['cust_knownas'];
                        $cust_subnet_code = $value_list['cust_subnet_code'];
                        $cust_subnet_name = $value_list['cust_subnet_name'];
                        $cust_status_name = $value_list['cust_status_name'];
                        $cust_type = $value_list['cust_type'];
                        $array_data = array('cust_id'=>$cust_id, 'cust_knownas'=>$cust_knownas, 'cust_subnet_code'=>$cust_subnet_code, 'cust_subnet_name'=>$cust_subnet_name, 
                                            'cust_status_name'=>$cust_status_name, 'cust_type'=>$cust_type);
                        array_push($array_group, $array_data); 
                    }
                    $post_data = array('cust_name'=>$cust_name, 'cust_data'=>json_encode($array_group), 'cust_count'=>count($cust_list));
                    $add_group = $this->api_model->add_db('ax_customer_group', $post_data);
                }  
            } 
            $this->set_count_subnet();
        }
    }  
    function set_count_subnet(){  
        $cust_list = $this->api_model->get_all_data('ax_customer_list'); 
        if($cust_list){
            $subnet_list = getSubnetList();  
            $this->api_model->truncate_table('subnets'); 
            $array_count = array(); 
            $start_time = date('Y-m-d H:s:i'); 
            $no = 1; 
            foreach ($subnet_list as $key => $value) {
                $subnet_code = $value['SALESDISTRICTID'];
                $subnet_name = $value['DESCRIPTION']; 
                $subnet_count = array_count_values(array_column($cust_list, 'cust_subnet_code'))[$subnet_code];   
                $post_data = array('subnet_code'=>$subnet_code, 'subnet_name'=>$subnet_name, 'subnet_count'=>$subnet_count); 
                $add_subnet = $this->api_model->add_db('subnets', $post_data); 
                $no++;
            }
            $this->set_count_status_user();
        }
    } 
    function set_count_status_user(){  
        $status_list = array(array('status_code'=>0, 'status_name'=>'Active'), array('status_code'=>1, 'status_name'=>'Hold'), array('status_code'=>2, 'status_name'=>'Close'));   
        $cust_list = $this->api_model->get_all_data('ax_customer_list'); 
        if($cust_list){
            $this->api_model->truncate_table('user_status');  
            $array_count = array(); 
            foreach ($status_list as $key => $value) {
                $status_code = $value['status_code'];
                $status_name = $value['status_name']; 
                $status_count = array_count_values(array_column($cust_list, 'cust_status'))[$status_code];  
                $post_data = array('status_code'=>$status_code, 'status_name'=>$status_name, 'status_count'=>$status_count); 
                $add_status = $this->api_model->add_db('user_status', $post_data); 
            }  
        }
    } 
    function set_cust_info_ax_by_user_login_post(){  
        $post = $this->input->post();
        if($post){
            $status = $post['status'];
            $cust_list = $this->api_model->get_where_data('user', array('is_admin'=>0, 'status'=>$status)); 
            $count_list = count($cust_list); 
            print_r($count_list); die();
            foreach ($cust_list as $key => $value) {  
                $cust_id = $value['CUSTID'];
                $subnet_code = $value['subnets'];   
                $data = $this->get_data_ax_cust_id($cust_id, $subnet_code);  
            }  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }


    function set_data_cust_list_ax_post(){
        $post = $this->input->post();
        if($post){
            $cust_list = $this->api_model->get_where_data('ax_customer_list', ['cust_status'=>1]);
            print_r(count($cust_list)); 
            die();
            foreach ($cust_list as $key => $value) {
                $cust_id = $value['cust_id']; 
                $data = $this->set_data_cust_to_file($cust_id); 
                echo $cust_id;
                die();
            }
            print_r(count($cust_list)); 
            die();
        }
    }
    function get_info_by_ax_id($post){
        if(isset($post['ax_id'])){
            $ax_id = $post['ax_id'];
            $res = getCustInfoOnly($ax_id); 
            $this->response($res, REST_Controller::HTTP_OK);    
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function get_total_inv($post){
        if(isset($post['ax_id'])){
            $ax_id = $post['ax_id'];
            $year = $post['year'];
            $month = $post['month'];
            $res = getCustTotalInvoice($ax_id, $year, $month);
            $this->response($res, REST_Controller::HTTP_OK);    
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function get_file_cust_ax($post){ 
        if(isset($post['cust_id'])){ 
            $cust_id = $post['cust_id']; 
            $subnet_code = $post['subnet_code'];  
            $date_now = date('d');  
            $year_now =  date('Y');  
            $month_now =  date('m');  
            $year_array = array();
            $month_array = array(); 

            $cust_data = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);
            if($cust_data){
                $cust_instal_name = $cust_data['cust_instal_name']; 
                $posting_date_from = "01";
                $posting_date_to = "01";
                // if($cust_instal_name == 'Alicloud'){
                //     $
                // }
                for ($i=2; $i >=0; $i--) {   
                    $fromyear = date("Y", strtotime(-$i." months", strtotime(date("Y-m")."-01")));
                    array_push($year_array, $fromyear);
                    $frommonth = date("m", strtotime(-$i." months", strtotime(date("Y-m")."-01")));
                    array_push($month_array, $frommonth); 
                    
                }    
                $file_name = './files/data_ax/'.$subnet_code.'/'.$cust_id.'.txt';  // tempat lokasi file dan nama file serta format file txt  
                if(file_exists($file_name)){    // jika filenya sudah ada
                    $datetime1 = date("Y-m-d H:i:s");
                    $datetime2 = date ("Y-m-d H:i:s", filemtime($file_name)); 
                    $timestamp1 = strtotime($datetime1);
                    $timestamp2 = strtotime($datetime2);
                    $diff = $timestamp1 - $timestamp2;
                    $diff_hours = $diff /3600;   
                    // echo $diff_hours;
                    if($diff_hours > 1){
                        unlink($file_name); // hapus dulu filenya
                        $out = getCustInfoAll($cust_id, $year_now, $month_now, $year_array, $month_array);   // data yang mau diinput kedalam file text
                        if($out){  
                            $fp = fopen($file_name, 'w'); // buka filenya dulu yang tadi 
                            fwrite($fp, json_encode($out)); // tulis filenya
                            fclose($fp); // tutup filenya 
                        }                      
                    }             
                }else{ 
                    $out = getCustInfoAll($cust_id, $year_now, $month_now, $year_array, $month_array);   // data yang mau diinput kedalam file text
                    if($out){  
                        $fp = fopen($file_name, 'w'); // buka filenya dulu yang tadi 
                        fwrite($fp, json_encode($out)); // tulis filenya
                        fclose($fp); // tutup filenya 
                    }                 
                }          
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
    function get_info_by_ax_id_post(){
        $post = $this->input->post();
        if($post){
            $cust_id = $post['cust_id'];
            $res = getCustInfoOnly($cust_id); 
            if($res){
                $res = array('result'=>true);
                $this->response($res, REST_Controller::HTTP_OK);
            }else{
                $res = array('result'=>false);
                $this->response($res, REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function get_file_cust_ax_new_post(){ 
        $post = $this->input->post();
        if($post){
            $cust_id = $post['cust_id']; 
            $date_now = date('d');  
            $year_now =  date('Y');  
            $month_now =  date('m');  
            $year_array = array();
            $month_array = array(); 
            $posting_date_from_array = array(); 
            $posting_date_to_array = array(); 
            $data_cust = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);
            if($data_cust){
                $subnet_code = $data_cust['cust_subnet_code'];  
                $cust_instal_name = $data_cust['cust_instal_name'];
                for ($i=2; $i >=0; $i--) {   
                    $fromyear = date("Y", strtotime(-$i." months", strtotime(date("Y-m")."-01")));
                    array_push($year_array, $fromyear);
                    $frommonth = date("m", strtotime(-$i." months", strtotime(date("Y-m")."-01")));
                    array_push($month_array, $frommonth); 
                    if($cust_instal_name == 'Alicloud'){
                        $periode_from = date("Ym", strtotime("-1 months", strtotime(date($fromyear.'-'.$frommonth))));
                        $periode_to = $fromyear.$frommonth;
                        $billing_info_from = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode_from]); 
                        $billing_info_to = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode_to]);
                        $posting_date_from =  date("Y-m-d", strtotime("+1 days", strtotime($billing_info_from['alibaba_posting_date'])));
                        $posting_date_to =  date("Y-m-d", strtotime("+1 days", strtotime($billing_info_to['alibaba_posting_date'])));
                        // $posting_date_to =  $billing_info_to['alibaba_posting_date'];
                        array_push($posting_date_from_array, $posting_date_from);  
                        array_push($posting_date_to_array, $posting_date_to);   
                    }else{
                        $periode_from = date("Y-m", strtotime("-1 months", strtotime(date($fromyear.'-'.$frommonth))));
                        $periode_to = $fromyear.'-'.$frommonth; 
                        $posting_date_from =  date("Y-m-02", strtotime($periode_from));
                        $posting_date_to = date("Y-m-01", strtotime($periode_to));
                        array_push($posting_date_from_array, $posting_date_from);  
                        array_push($posting_date_to_array, $posting_date_to);  
                    }
                }    

                $file_name = './files/data_ax/'.$subnet_code.'/'.$cust_id.'.txt';  // tempat lokasi file dan nama file serta format file txt  
                if(file_exists($file_name)){ 
                    unlink($file_name); // hapus dulu filenya
                }
                if($cust_instal_name == 'Alicloud'){
                    $out = getCustInfoAll_new($cust_id, $year_now, $month_now, $year_array, $month_array, $posting_date_from_array, $posting_date_to_array);   // data yang mau diinput kedalam file text
                    // $out =  getCustInvoiceMonthly_new($cust_id, 2022, 04 , '2022-03-08', '2022-04-08');
                    // print_r($out);
                    // print_r($year_array);
                    // print_r($month_array);
                    // print_r($posting_date_from_array);
                    // print_r($posting_date_to_array);
                    // die();
                }else{
                    $out = getCustInfoAll($cust_id, $year_now, $month_now, $year_array, $month_array);   // data yang mau diinput kedalam file text
                }
                if($out){  
                    $fp = fopen($file_name, 'w'); // buka filenya dulu yang tadi 
                    fwrite($fp, json_encode($out)); // tulis filenya
                    fclose($fp); // tutup filenya 
                }  
                $file_data = file_get_contents($file_name);
                $file_data = json_decode($file_data, true);  
                $this->response($file_data, REST_Controller::HTTP_OK);   
            } 
        } 
    }
    function get_file_cust_data_post(){
        $post = $this->input->post();
        if($post){
            $cust_id = $post['cust_id']; 
            $data_cust = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);
            if($data_cust){
                $subnet_code = $data_cust['cust_subnet_code'];  
                $file_name = './files/data_ax/'.$subnet_code.'/'.$cust_id.'.txt';  // tempat lokasi file dan nama file serta format file txt  
                if(file_exists($file_name)){  
                    $datetime1 = date("Y-m-d H:i:s");
                    $datetime2 = date ("Y-m-d H:i:s", filemtime($file_name)); 
                    $timestamp1 = strtotime($datetime1);
                    $timestamp2 = strtotime($datetime2);
                    $diff = $timestamp1 - $timestamp2; 
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
                    $hour = $diff / 3600;   
                    if($hour > 1){
                        $file_data = $this->set_data_cust_to_file($cust_id);
                    }else{
                        $file_data = file_get_contents($file_name);
                        $file_data = json_decode($file_data, true); 
                    }  
                }else{ 
                    $file_data = $this->set_data_cust_to_file($cust_id);
                } 
                $this->response($file_data, REST_Controller::HTTP_OK);   
            }
        } 
    }    
    function set_data_cust_to_file($cust_id){  
    // function set_data_cust_to_file_post(){  
        // $cust_id = $this->input->post('cust_id');
        $date_now = date('d');  
        $year_now =  date('Y');  
        $month_now =  date('m');  
        $year_array = array();
        $month_array = array(); 
        $posting_date_from_array = array(); 
        $posting_date_to_array = array(); 
        $data_cust = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);
        if($data_cust){
            $subnet_code = $data_cust['cust_subnet_code'];  
            $cust_instal_name = $data_cust['cust_instal_name'];
            for ($i=2; $i >=0; $i--) {   
                $fromyear = date("Y", strtotime(-$i." months", strtotime(date("Y-m")."-01")));
                array_push($year_array, $fromyear);
                $frommonth = date("m", strtotime(-$i." months", strtotime(date("Y-m")."-01")));
                array_push($month_array, $frommonth); 
                if($cust_instal_name == 'Alicloud'){
                    $periode_from = date("Ym", strtotime("-1 months", strtotime(date($fromyear.'-'.$frommonth))));
                    $periode_to = $fromyear.$frommonth;
                    $billing_info_from = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode_from]); 
                    $billing_info_to = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode_to]);
                    $posting_date_from =  date("Y-m-d", strtotime("+1 days", strtotime($billing_info_from['alibaba_posting_date']))); 
                    $posting_date_to =  date("Y-m-d", strtotime($billing_info_to['alibaba_posting_date'])); 
                    array_push($posting_date_from_array, $posting_date_from);  
                    array_push($posting_date_to_array, $posting_date_to);   
                }else{
                    $periode_from = date("Y-m", strtotime("-1 months", strtotime(date($fromyear.'-'.$frommonth))));
                    $periode_to = $fromyear.'-'.$frommonth; 
                    $posting_date_from =  date("Y-m-02", strtotime($periode_from));
                    $posting_date_to = date("Y-m-01", strtotime($periode_to));
                    array_push($posting_date_from_array, $posting_date_from);  
                    array_push($posting_date_to_array, $posting_date_to);  
                }
            }     
            $file_name = './files/data_ax/'.$subnet_code.'/'.$cust_id.'.txt';  // tempat lokasi file dan nama file serta format file txt  
            if(file_exists($file_name)){ 
                unlink($file_name); // hapus dulu filenya
            }
            if($cust_instal_name == 'Alicloud'){
                $out = getCustInfoAll_new($cust_id, $year_now, $month_now, $year_array, $month_array, $posting_date_from_array, $posting_date_to_array);   // data yang mau diinput kedalam file text
               
            }else{
                $out = getCustInfoAll($cust_id, $year_now, $month_now, $year_array, $month_array);   // data yang mau diinput kedalam file text
            }
            if($out){  
                $fp = fopen($file_name, 'w'); // buka filenya dulu yang tadi 
                fwrite($fp, json_encode($out)); // tulis filenya
                fclose($fp); // tutup filenya 
            }  
            $file_data = file_get_contents($file_name);
            $file_data = json_decode($file_data, true);  
            return $file_data; 
        }  
    }
    function check_file_cust_ax_post(){
        $post = $this->input->post();
        if($post){
            $cust_id = $post['cust_id']; 
            $data_cust = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);
            if($data_cust){
                $subnet_code = $data_cust['cust_subnet_code'];  
                $file_name = './files/data_ax/'.$subnet_code.'/'.$cust_id.'.txt';  // tempat lokasi file dan nama file serta format file txt  
                if(file_exists($file_name)){  
                    $res = array('result'=>true);  
                }else{
                    $res = array('result'=>false); 
                }
                $this->response($res, REST_Controller::HTTP_OK); 
            }
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function set_cust_list_billing_month_now_post(){
        $post = $this->input->post();
        if($post){ 
            $periode = $post['periode'];   
            $arraySubnets = array(  'S-004');
            // // $arraySubnets = array(  'S-001','S-002','S-003','S-004','S-005','S-006','S-007','S-008','S-009','S-010','S-011','S-012','S-013','S-014','S-015','S-016',
            // //                         'S-017','S-018','S-019','S-020','S-021','S-022'); 
            $array_acc = array();    
            foreach ($arraySubnets as $key => $value) {            
                $subnet_code = $value; 
                $out = getCustAccActiveListUnderSubnet($subnet_code);
                if($out){  
                    $array_acc = array_merge($array_acc, $out);  
                }
            } 
            print_r($array_acc);
            if($array_acc){
                $this->api_model->truncate_table('billing_faktur_list');
                foreach ($array_acc as $key => $value) {
                    if($value['MK_CUSTSTATUS'] == 0 && $value['MK_CUSTSTATUS'] == 1){
                        $cust_id = $value['ACCOUNTNUM'];
                        $cust_name = $value['NAME'];
                        $cust_email = $value['EMAIL'];
                        $no_npwp = $value['VATNUM'];
                        $cust_id = $value['ACCOUNTNUM'];
                        $cust_id = $value['ACCOUNTNUM'];
                        $cust_id = $value['ACCOUNTNUM'];
                        $cust_id = $value['ACCOUNTNUM'];
                        $cust_id = $value['ACCOUNTNUM'];
                    }
                    echo $cust_id;
                    # code...
                }
            //     if (!is_dir('./files/data_billing/'.$periode.'/')) {
            //         mkdir('./files/data_billing/'.$periode.'/', 0755, TRUE);                
            //     }
            //     $fp = fopen($file_name, 'w');
            //     fwrite($fp, json_encode($array_acc));
            //     fclose($fp); 
            }     
            $this->response('OK', REST_Controller::HTTP_OK);    
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    function set_cust_num_faktur_pajak_db_post(){
        $post = $this->input->post();
        if($post){ 
            $periode = $post['periode'];  
            $list_corporate = $post['list_corporate']; 
            $list_retail = $post['list_retail']; 
            $list_personal = $post['list_personal']; 
            $user_id = $post['user_id'];  
            $arraySubnets = array(  'S-001','S-002','S-003','S-004','S-005','S-006','S-007','S-008','S-009','S-010','S-011','S-012','S-013','S-014','S-015','S-016',
                                    'S-017','S-018','S-019','S-020','S-021','S-022'); 
            $check_db = $this->api_model->get_where_data_row('billing_faktur_list', ['periode'=>$periode]);
            if(!$check_db){ 
                $this->api_model->truncate_table('billing_faktur_list');
            }  
            $check_db_info = $this->api_model->get_where_data_row('billing_faktur_info', ['periode'=>$periode]);
            if($check_db_info){ 
                $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['get_cust_list_status'=>2]); 
            }else{
                $this->api_model->add_db( 'billing_faktur_info', ['periode'=>$periode, 'type'=>1, 'created_date'=>date('Y-m-d H:i:s'), 'get_cust_list_status'=>2, 
                                          'created_by'=>$post['user_id']]);
            } 
            $array_log = array('periode'=>$periode,'log_type'=>1,  'log_name'=>'Get Customer List Process', 'create_date'=>date('Y-m-d H:i:s'));
            $add_log = $this->api_model->add_db('billing_faktur_log', $array_log);
            foreach ($arraySubnets as $key => $value) {            
                $subnet_code = $value; 
                if($list_corporate == 1){
                    $cust_type = 'Corporate';
                    $list_data = $this->api_model->get_where_data('ax_customer_list', ['cust_subnet_code'=>$subnet_code, 'cust_type'=>$cust_type]);  
                    if($list_data){  
                        foreach ($list_data as $key_cust => $value_cust) { 
                            if($value_cust['cust_status'] == 0 || $value_cust['cust_status'] == 1){
                                $check_blacklist_db = $this->api_model->get_where_data_row('billing_faktur_blacklist', ['cust_id'=>$value_cust['cust_id']]);
                                if(!$check_blacklist_db){
                                    $check_db = $this->api_model->get_where_data_row('billing_faktur_list', ['periode'=>$periode, 'cust_id'=>$value_cust['cust_id']]);
                                    if(!$check_db){
                                        $array_post = [ 'periode'=>$periode, 'cust_id'=>$value_cust['cust_id'], 'cust_name'=>$value_cust['cust_name'],
                                                        'cust_knownas'=>$value_cust['cust_knownas'],
                                                        'cust_email'=>$value_cust['cust_email'], 'cust_type'=>$value_cust['cust_type'], 
                                                        'cust_instal_name'=>$value_cust['cust_instal_name'],
                                                        'cust_status'=>$value_cust['cust_status'], 'cust_npwp_no'=>$value_cust['cust_npwp_no'], 
                                                        'cust_npwp_name'=>$value_cust['cust_npwp_name'],
                                                        'cust_subnet_code'=>$value_cust['cust_subnet_code'], 'cust_subnet_name'=>$value_cust['cust_subnet_name']];
                                        $this->api_model->add_db('billing_faktur_list', $array_post);
                                    }
                                }
                            }
                        } 
                    }
                } 
                if($list_retail == 1){
                    $cust_type = 'Retail';
                    $list_data = $this->api_model->get_where_data('ax_customer_list', ['cust_subnet_code'=>$subnet_code, 'cust_type'=>$cust_type]);  
                    if($list_data){  
                        foreach ($list_data as $key_cust => $value_cust) { 
                            if($value_cust['cust_status'] == 0 || $value_cust['cust_status'] == 1){
                                $check_blacklist_db = $this->api_model->get_where_data_row('billing_faktur_blacklist', ['cust_id'=>$value_cust['cust_id']]);
                                if(!$check_blacklist_db){
                                    $check_db = $this->api_model->get_where_data_row('billing_faktur_list', ['periode'=>$periode, 'cust_id'=>$value_cust['cust_id']]);
                                    if(!$check_db){
                                        $array_post = [ 'periode'=>$periode, 'cust_id'=>$value_cust['cust_id'], 'cust_name'=>$value_cust['cust_name'],
                                                        'cust_knownas'=>$value_cust['cust_knownas'],
                                                        'cust_email'=>$value_cust['cust_email'], 'cust_type'=>$value_cust['cust_type'], 
                                                        'cust_instal_name'=>$value_cust['cust_instal_name'],
                                                        'cust_status'=>$value_cust['cust_status'], 'cust_npwp_no'=>$value_cust['cust_npwp_no'], 
                                                        'cust_npwp_name'=>$value_cust['cust_npwp_name'],
                                                        'cust_subnet_code'=>$value_cust['cust_subnet_code'], 'cust_subnet_name'=>$value_cust['cust_subnet_name']];
                                        $this->api_model->add_db('billing_faktur_list', $array_post);
                                    }
                                }
                            }
                        } 
                    }
                }
                if($list_personal == 1){
                    $cust_type = 'Personal';
                    $list_data = $this->api_model->get_where_data('ax_customer_list', ['cust_subnet_code'=>$subnet_code, 'cust_type'=>$cust_type]);  
                    if($list_data){  
                        foreach ($list_data as $key_cust => $value_cust) { 
                            if($value_cust['cust_status'] == 0 || $value_cust['cust_status'] == 1){
                                $check_blacklist_db = $this->api_model->get_where_data_row('billing_faktur_blacklist', ['cust_id'=>$value_cust['cust_id']]);
                                if(!$check_blacklist_db){
                                    $check_db = $this->api_model->get_where_data_row('billing_faktur_list', ['periode'=>$periode, 'cust_id'=>$value_cust['cust_id']]);
                                    if(!$check_db){
                                        $array_post = [ 'periode'=>$periode, 'cust_id'=>$value_cust['cust_id'], 'cust_name'=>$value_cust['cust_name'],
                                                        'cust_knownas'=>$value_cust['cust_knownas'],
                                                        'cust_email'=>$value_cust['cust_email'], 'cust_type'=>$value_cust['cust_type'], 
                                                        'cust_instal_name'=>$value_cust['cust_instal_name'],
                                                        'cust_status'=>$value_cust['cust_status'], 'cust_npwp_no'=>$value_cust['cust_npwp_no'], 
                                                        'cust_npwp_name'=>$value_cust['cust_npwp_name'],
                                                        'cust_subnet_code'=>$value_cust['cust_subnet_code'], 'cust_subnet_name'=>$value_cust['cust_subnet_name']];
                                        $this->api_model->add_db('billing_faktur_list', $array_post);
                                    }
                                }
                            }
                        } 
                    }
                }
            }  
            $data_cust_list = $this->api_model->get_where_data('billing_faktur_list', ['periode'=>$periode]);
            $count_cust_list = count($data_cust_list); 
            $data_npwp = $this->api_model->get_where_data('billing_faktur_list', ['periode'=>$periode, 'cust_npwp_no !='=>'']);
            $count_npwp = count($data_npwp); 
            $data_non_npwp = $this->api_model->get_where_data('billing_faktur_list', ['periode'=>$periode, 'cust_npwp_no'=>'']);
            $count_non_npwp = count($data_non_npwp);  
            $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['count_npwp'=>$count_npwp, 'count_non_npwp'=>$count_non_npwp, 
                                        'count_cust'=>$count_cust_list, 'get_cust_list_status'=>1, 'get_cust_list_date'=>date('d-m-Y H:i:s')]);  
            $array_log = array('periode'=>$periode,'log_type'=>1,  'log_name'=>'Get Customer List Done', 'create_date'=>date('Y-m-d H:i:s'));
            $add_log = $this->api_model->add_db('billing_faktur_log', $array_log);
            $this->response('OK', REST_Controller::HTTP_OK);  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    function set_cust_num_faktur_pajak_post(){
        $post = $this->input->post();
        if($post){ 
            $periode = $post['periode']; 
            $file_name = './files/data_billing/'.$periode.'/cust_num_list.txt'; 
            if(file_exists($file_name)){    // jika filenya sudah ada 
                unlink($file_name); // hapus dulu filenya
            }   
            $arraySubnets = array(  'S-004');
            // $arraySubnets = array(  'S-001','S-002','S-003','S-004','S-005','S-006','S-007','S-008','S-009','S-010','S-011','S-012','S-013','S-014','S-015','S-016',
            //                         'S-017','S-018','S-019','S-020','S-021','S-022'); 
            $array_acc = array();    
            foreach ($arraySubnets as $key => $value) {            
                $subnet_code = $value; 
                $out = getCustAccActiveListUnderSubnet($subnet_code);
                if($out){  
                    $array_acc = array_merge($array_acc, $out);  
                }
            } 
            if($array_acc){
                if (!is_dir('./files/data_billing/'.$periode.'/')) {
                    mkdir('./files/data_billing/'.$periode.'/', 0755, TRUE);                
                }
                $fp = fopen($file_name, 'w');
                fwrite($fp, json_encode($array_acc));
                fclose($fp); 
            }     
            $this->response('OK', REST_Controller::HTTP_OK);    
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    function set_cust_data_faktur_pajak_post(){
        $post = $this->input->post();
        if($post){ 
            $periode = $post['periode']; 
            $file_name = './files/data_billing/'.$periode.'/cust_num_list.txt'; 
            $file_name_data = './files/data_billing/'.$periode.'/cust_data_list.txt'; 
            if(file_exists($file_name)){    // jika filenya sudah ada 
                $file_data = file_get_contents($file_name);
                $file_data = json_decode($file_data, true); 
                if(file_exists($file_name_data)){    // jika filenya sudah ada 
                    unlink($file_name_data); // hapus dulu filenya
                }  
                $array_acc_list = array();  
                $count_cust = 0;
                $count_npwp = 0;
                $count_non_npwp = 0;
                foreach ($file_data as $key => $value) { 
                    if($value['TYPECUST'] == 'Retail' || $value['TYPECUST'] == 'Personal' || $value['TYPECUST'] == 'Corporate'){ 
                        $array_cust = array('cust_id'=>$value['ACCOUNTNUM'], 'cust_name'=>$value['NAME'], 'cust_email'=>$value['EMAIL'], 'cust_type'=>$value['TYPECUST'],
                                            'no_npwp'=>$value['VATNUM'], 'cust_type'=>$value['TYPECUST'], 'billing_file'=>'', 'billing_amount'=>0,
                                            'faktur_file'=>'', 'faktur_pajak_id' => '', 'faktur_pajak_complete' => '',
                                            'billing_status'=>0, 'faktur_status'=>0, 'send_email_date'=>'');
                        array_push($array_acc_list, $array_cust);
                        $count_cust = $count_cust+1;
                        if($value['VATNUM'] != ''){
                            $count_npwp = $count_npwp+1;
                        }else{ 
                            $count_non_npwp = $count_non_npwp+1;
                        }
                    } 
                } 
                if($array_acc_list){
                    $fp = fopen($file_name_data, 'w');
                    fwrite($fp, json_encode($array_acc_list));
                    fclose($fp); 
                } 
                $file_data = file_get_contents($file_name);
                $file_data = json_decode($file_data, true);   
                $this->api_model->update_db('billing_faktur_info', ['periode'=>$periode], ['count_npwp'=>$count_npwp, 'count_non_npwp'=>$count_non_npwp, 
                                            'count_cust'=>$count_cust]);
                $this->response('OK', REST_Controller::HTTP_OK);   
            }else{ 
                $this->response('Error File', REST_Controller::HTTP_OK);     
            }
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function get_cust_data_faktur_pajak_post(){
        $post = $this->input->post();
        if($post){ 
            $periode = $post['periode']; 
            $file_name_data = './files/data_billing/'.$periode.'/cust_data_list.txt';  
            $file_data = file_get_contents($file_name_data);   
            $file_data = json_decode($file_data, true);  
            $res = array('data_list'=>$file_data);
            $this->response($res, REST_Controller::HTTP_OK);  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function get_cust_data_faktur_pajak_db_post(){
        $post = $this->input->post();
        if($post){ 
            $periode = $post['periode'];  
            $data_cust_list = $this->api_model->get_where_data('billing_faktur_list', ['periode'=>$periode]);   
            $res = array('data_list'=>$data_cust_list);
            $this->response($res, REST_Controller::HTTP_OK);  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function get_cust_info_db_post(){
        $post = $this->input->post();
        if($post){ 
            $cust_id = $post['cust_id']; 
            $data_cust = $this->api_model->get_where_data_row('ax_customer_list', ['cust_id'=>$cust_id]);
            if($data_cust){
                $res = array('result'=>true, 'data_cust'=>$data_cust);                
            }else{
                $res = array('result'=>false);
            } 
            $this->response($res, REST_Controller::HTTP_OK);  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    }
    function view_cust_info_post(){
        $cust_id = $this->input->post('cust_id');
        // $out = getCustInfoTransAX($cust_id);
        $out = getCustAccListUnderSubnet('S-023');
        print_r($out);
    }
}