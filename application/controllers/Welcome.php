<?php
use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php'; 

class Welcome extends REST_Controller {
	public function index_get(){
        $this->response('Permission denied', REST_Controller::HTTP_BAD_REQUEST); 
    }   
}