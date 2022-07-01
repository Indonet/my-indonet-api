<?php
require_once(__DIR__ . '/blesta_api.php');

global $api;   
$user = "hsxapi";
$key = "2b98ba29e5e4e5a43202003698886203";
$url = "https://blesta.indonet.co.id/api/";  
$api = new BlestaApi($url, $user, $key);
function createNewUser($dataUser){  
    global $api; 
    $username = $dataUser['username'];
    $email = $dataUser['email'];
    $axid = $dataUser['axid'];
    $fname = $dataUser['fname'];
    $lname = $dataUser['lname'];
    $data = array(
        'vars' => array(
            'username' => $username,
            'new_password' => 'myportal2021#',
            'confirm_password' => 'myportal2021#', 
            'client_group_id' => 2,
            'first_name' => $fname,
            'last_name' => $lname,
            'title' => null, 
            'company' => null, 
            'email' => $email,
            'address1' => null,
            'address2' => null, 
            'city' => null, 
            'state' => null,
            'zip' => null, 
            'country' => 'ID', 
            'numbers' => null,
            'send_registration_email' => false,
            'settings' => array(
                'username_type' => 'username',
                'default_currency' => 'IDR',
                'language' => 'en_us'
            ), 
            'custom' => array(
                2 => $axid,
            )
        )
    ); 
    $res3 = $api->post("clients", "create", $data);
    if (!$res3->errors()) {
        $out = (array) $res3->response();
        if (isset($out['settings']))
            unset($out['settings']); 
        return $out;    
    } else {
        // print_r($res3->errors());
        return false;
    } 
}
function createNewInv($dataUser){ 
    global $api;
    $client_id = $dataUser['id_blesta'];
    $tax = false;
    $item = array();
    foreach ($dataUser['inv_list'] as $key => $value) {
        $textDesc = ''; 
        if($textDesc == ''){
            $textDesc = $value['desc'];
        }
        $dataInvItem = array(
                'description' => $textDesc,
                'amount' =>  $value['amount'],
                'qty' => 1,
                'tax' => $tax);
        array_push($item, $dataInvItem);
    } 
    $data = array(  
        'vars' => array(
            'client_id' => $client_id,
            'date_billed' => date("c"),
            'date_due' => date("c"),
            'currency' => "IDR",
            'lines' => $item,
            'delivery' => array("email")
        )
    );
    $response = $api->post("invoices", "add", $data); 
    
    if (!$response->errors()) {
        $out = $response->response();
        return $out; 
    } else {
        return $response->errors(); 
        return false;
    }
} 
function editInv($dataUser){ 
    global $api;
    $client_id = $dataUser['id_blesta'];
    $inv_id = $dataUser['inv_id'];
    $tax = false;
    $item = array();
    foreach ($dataUser['inv_list'] as $key => $value) {
        $textDesc = ''; 
        if($textDesc == ''){
            $textDesc = $value['desc'];
        }
        $dataInvItem = array( 
                'id' => $value['item_id'],
                'description' => $textDesc,
                'amount' =>  $value['amount'],
                'qty' => 1,
                'tax' => $tax);
        array_push($item, $dataInvItem);
    } 
    $data = array(  
        'invoice_id' => $inv_id,
        'vars' => array(
            'client_id' => $client_id,
            'date_billed' => date("c"),
            'date_due' => date("c"),
            'currency' => "IDR",
            'lines' => $item,
            'delivery' => array("email")
        )
    );
    $response = $api->post("invoices", "edit", $data); 
    
    if (!$response->errors()) {
        $out = $response->response();
        return $out; 
    } else {
        return $response->errors(); 
    }
}
function checkInv($dataUser){ 
    global $api;
    $data = array(
        'invoice_id' => $dataUser['inv_id']
    );
    $response = $api->post("invoices", "get", $data); 
    if(!$response->errors()){
        return (array)$response->response();
    }else{
        return false;
    }
}
function getUserExist($dataUser){ 
    global $api;
    $data = array(
        'username' => $dataUser['username']  
    );  
    $response = $api->get("users", "getByUsername", $data); 
    if(!$response->errors()){
        return (array)$response->response();
    }else{
        return $response->errors();  
    }
}
function getClientIdByUserId($user_id){
    global $api;
    $data = array(
        'user_id' => $user_id  
    );  
    $response = $api->get("clients", "getByUserId", $data); 
    if(!$response->errors()){
        return (array)$response->response();
    }else{
        return $response->errors();  
    }
    return $user_id;  

}
?>
