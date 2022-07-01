<?php 
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 

class Midtrans extends REST_Controller {  
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta'); 
        $this->load->helper('url'); 
        include_once(APPPATH . '/libraries/midtrans/index.php'); 
    }   
	public function index_get(){ 
        $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
    }  
	public function index_post(){
		$post = $this->input->post();  
		switch ($post['type']) {
			case 'create_inv_dev': 
				$this->create_inv_dev($post);
				break; 
			default: 
                $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
				break;
		}
	}
    function create_inv_dev($post){  
        if(isset($post['data'])){ 
            $mid_data = createTransactionMid_dev($post['data']);  
            if($mid_data){
                $res = array('mid_data'=>$mid_data);
                $this->response($res, REST_Controller::HTTP_OK);    
            }else{
                $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
            }  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
}