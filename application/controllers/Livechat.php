<?php 
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 

class Livechat extends REST_Controller {  
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');  
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
			default: 
                $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
				break;
		}
	} 
    function create_user($post){ 
        if(isset($post['email']) && isset($post['user_realname'])){ 
            $email = $post['email'];
            $username = $post['user_realname'];
            $password = 'hsx2020!@#'; 
            $url_api = 'https://livechat.indonet.id/chat/api/customer/register';   
            $post = array('email'=>$email, 'username'=>$username, 'password'=>$password); 
            $response = $this->curl->simple_post($url_api, $post);
            echo $response;
        }else{ 
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
}