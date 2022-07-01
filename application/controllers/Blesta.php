<?php 
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 

class Blesta extends REST_Controller {  
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta'); 
        $this->load->helper('url'); 
        include_once(APPPATH . '/libraries/blesta/index.php'); 
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
            case 'create_inv': 
                $this->create_inv($post);
                break; 
            case 'check_inv': 
                $this->check_inv($post);
                break; 
            case 'update_inv': 
                $this->update_inv($post);
                break;                 
			default: 
                $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
				break;
		}
	}
    function create_user($post){  
        if(isset($post['username']) && $post['email'] && $post['axid'] && $post['fname']){
            $username = $post['username'];
            $email = $post['email'];
            $uid_ax = $post['axid'];   
            $fname = $post['fname'];
            $lname = 'Indonet';  
            $data = array('username'=>$username, 'email'=>$email, 'axid'=>$uid_ax, 'fname'=>$fname, 'lname'=>$lname);  
            $user_blesta = createNewUser($data);   
            if($user_blesta){
                $res = array('blesta_id'=>$user_blesta['id']);
                $this->response($res, REST_Controller::HTTP_OK);    
            }else{
                $data = array('username'=>$username);
                $user_blesta = getUserExist($data);  
                $user_id_blesta = $user_blesta['id'];
                if($user_id_blesta){
                    $user_client_blesta = getClientIdByUserId($user_id_blesta);  
                    $res = array('blesta_id'=>$user_client_blesta['id']);
                    $this->response($res, REST_Controller::HTTP_OK);   
                }else{
                    $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
                }
            }  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function create_inv($post){   
        if(isset($post['id_blesta']) && $post['inv_list']){ 
            $array_data = array('id_blesta'=>$post['id_blesta'], 'inv_list'=>$post['inv_list']);
            $inv_id = createNewInv($array_data);   
            if($inv_id){
                $res = array('inv_id'=>$inv_id);
                $this->response($res, REST_Controller::HTTP_OK);    
            }else{
                $this->response('Create Invoice Failed', REST_Controller::HTTP_BAD_REQUEST); 
            }  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        }
    } 
    function check_inv($post){
        if(isset($post['inv_id'])){ 
            $array_data = array('inv_id'=>$post['inv_id']);
            $inv_data = checkInv($array_data);  
            if($inv_data){
                $res = array('inv_data'=>$inv_data);
                $this->response($res, REST_Controller::HTTP_OK);    
            }else{
                $this->response('Create Invoice Failed', REST_Controller::HTTP_BAD_REQUEST); 
            }  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
    function update_inv($post){
        if(isset($post['inv_id'])){ 
            $array_data = array('id_blesta'=>$post['id_blesta'], 'inv_id'=>$post['inv_id'], 'inv_list'=>$post['inv_list']); 
            $inv_data = editInv($array_data);  
            if($inv_data){
                $res = array('inv_data'=>$inv_data);
                $this->response($res, REST_Controller::HTTP_OK);    
            }else{
                $this->response('Create Invoice Failed', REST_Controller::HTTP_BAD_REQUEST); 
            }  
        }else{
            $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
        } 
    }
}