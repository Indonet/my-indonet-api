<?php 
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 

class Datek extends REST_Controller {  
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta'); 
        $this->load->helper('url'); 
    }   
	public function index_get(){ 
        $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
    }  
	public function index_post(){
		$post = $this->input->post(); 
		switch ($post['type']) {
			case 'create_user': 
				$this->create_user($post);
				break; 
            case 'get_port': 
                $this->get_port($post);
                break; 
            case 'get_dc_to_dc': 
                $this->get_dc_to_dc($post);
                break; 
            case 'get_dc_to_internet': 
                $this->get_dc_to_internet($post);
                break; 
            case 'get_dc_to_google_dci': 
                $this->get_dc_to_google_dci($post);
                break;  
            case 'get_dc_to_alibaba_dci': 
                $this->get_dc_to_alibaba_dci($post);
                break; 
            case 'get_dc_to_google_ntt': 
                $this->get_dc_to_google_ntt($post);
                break; 
            case 'get_dc_to_alibaba_ntt': 
                $this->get_dc_to_alibaba_ntt($post);
                break;  
			default: 
                $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
				break;
		}
	} 
    function create_user($post){  
        if(isset($post['uid_ax']) && isset($post['user_realname'])){
            $uid_ax = $post['uid_ax'];
            $user_realname = $post['user_realname'];   
            $url_api = '172.16.213.15/query_db/add_user_prod';
            $post = array('REAL_NAME'=>$user_realname, 'ID_USER_AX'=>$uid_ax); 
            $response = $this->curl->simple_post($url_api, $post);
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function get_port($post){    
        if(isset($post['uid_datek'])){
            $uid_datek = $post['uid_datek']; 
            $url_api = '172.16.213.15/query_db/get_port_user/'.$uid_datek; 
            $response = $this->curl->simple_patch($url_api); 
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function get_dc_to_dc($post){    
        if(isset($post['uid_datek'])){
            $uid_datek = $post['uid_datek']; 
            $url_api = '172.16.213.15/query_db/get_layanan_user/'.$uid_datek.'/1'; 
            $response = $this->curl->simple_patch($url_api);
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function get_dc_to_google_dci($post){    
        if(isset($post['uid_datek'])){
            $uid_datek = $post['uid_datek']; 
            $url_api = '172.16.213.15/query_db/get_layanan_user/'.$uid_datek.'/2'; 
            $response = $this->curl->simple_patch($url_api);
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function get_dc_to_alibaba_dci($post){    
        if(isset($post['uid_datek'])){
            $uid_datek = $post['uid_datek']; 
            $url_api = '172.16.213.15/query_db/get_layanan_user/'.$uid_datek.'/3';  
            $response = $this->curl->simple_patch($url_api);
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function get_dc_to_google_ntt($post){    
        if(isset($post['uid_datek'])){
            $uid_datek = $post['uid_datek']; 
            $url_api = '172.16.213.15/query_db/get_layanan_user/'.$uid_datek.'/5'; 
            $response = $this->curl->simple_patch($url_api);
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function get_dc_to_alibaba_ntt($post){    
        if(isset($post['uid_datek'])){
            $uid_datek = $post['uid_datek']; 
            $url_api = '172.16.213.15/query_db/get_layanan_user/'.$uid_datek.'/10';  
            $response = $this->curl->simple_patch($url_api);
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function get_dc_to_internet($post){    
        if(isset($post['uid_datek'])){ 
            $uid_datek = $post['uid_datek']; 
            $url_api = '172.16.213.15/query_db/get_layanan_user/'.$uid_datek.'/4'; 
            $response = $this->curl->simple_patch($url_api); 
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
}