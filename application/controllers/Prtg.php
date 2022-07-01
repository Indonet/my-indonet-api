<?php 
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 

class Prtg extends REST_Controller {  
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
			case 'get_traffic': 
				$this->get_traffic($post);
				break;  
			default: 
                $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
				break;
		}
	} 
    function get_traffic($post){
        if(isset($post['id_prtg'])){
            $id_prtg = $post['id_prtg']; 
            $start_date = $post['start_date']; 
            $end_date = $post['end_date']; 
            $url_api = '172.16.213.15/prtg/traffik/'.$id_prtg.'/'.$start_date.'/'.$end_date; 
            $response = $this->curl->simple_patch($url_api); 
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
}