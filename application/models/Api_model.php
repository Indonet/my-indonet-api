<?php
class Api_model extends CI_Model{	
	  function __construct(){
        parent::__construct();     
  	}  
    function get_where_data($tabel_name, $where, $orderBy = ''){
        $query = $this->db->get_where($tabel_name, $where);
        if($orderBy != ''){
            $query = $this->db->order_by($orderBy, 'ASC')->get_where($tabel_name, $where);
        }
        $result = $query->result_array();
        return $result;
    }
    function get_where_data_row($tabel_name, $where, $orderBy='', $sort=''){
        $query = $this->db->get_where($tabel_name, $where);
        if($orderBy != ''){
          $query = $this->db->order_by($orderBy, $sort)->get_where($tabel_name, $where);
        }
        $result = $query->row_array();
        return $result;
    }   
    function update_db($tabel_name, $where, $postData){      
        $this->db->where($where);
        $this->db->update($tabel_name, $postData);
        $result = TRUE;
        return $result; 
    }
    function add_db($tabel_name, $postData){           
        $this->db->insert($tabel_name, $postData);  
        return $this->db->insert_id();
    }
    function delete_db($tabel_name, $where){  
        $this->db->delete($tabel_name, $where);
        $result = TRUE;
        return $result; 
    }
    function truncate_table($tabel_name){  
        $this->db->truncate($tabel_name);
        $result = TRUE;
        return $result; 
    }
    function get_where_multiple_data($tabel_name, $where){  
        $this->db->select("*");
        $this->db->from($tabel_name); 
        if ($where) {
            foreach($where as $key) { 
                foreach($key as $key2 => $val) {
                    $this->db->where("$key2", $val);
                }
            }
        } 
        $result_array = $this->db->get()->result_array(); 
        // return $result_array;
        return $this->db->last_query();
    }
    function get_all_data($tabel_name){ 
        $query = $this->db->get($tabel_name); 
        $result = $query->result_array();
        return $result; 
    }
}