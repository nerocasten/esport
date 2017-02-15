<?php if(!defined('BASEPATH')) exit('No direct script access allowed');


  class Nrdatabase
  {
      public $table_name = "";
      public $select = "";
      public $where  = array();
      public $like  = array();
      public $join  = array();
      public $to_array = FALSE;
      public $limit = 0;
      public $offset = 0;
      public $order_by = array();
      public $db = "";

      public function __construct($table_name = "", $database = "default"){
        $this->ci =& get_instance();
        $this->table_name = $table_name;
        $this->db = $database;
      }

      public function from($table_name){
        $this->table_name = $table_name;
        return $this;
      }

      public function select($select){
        $this->select = $select;
        return $this;
      }

      public function where($where){
        $this->where = array_merge($this->where,$where);
        return $this;
      }

      public function like($like){
        $this->like = array_merge($this->like,$like);
        return $this;
      }

      public function limit($limit){
        $this->limit = $limit;
        return $this;
      }

      public function offset($offset){
        $this->offset = $offset;
        return $this;
      }

      public function order_by($order_by){
        $this->order_by = array_merge($this->order_by,$order_by);
        return $this;
      }

      public function join($join){
        $this->join = $join;
      }

      public function to_array($bool=TRUE){
        $this->to_array = $bool;
        return $this;
      }

      public function count_all(){
        $db = $this->get_db();
        $db->from($this->table_name);
        return $db->count_all_results();
      }

      public function count_all_where(){
        $db = $this->get_db();
        $db->from($this->table_name);
        $db = $this->set_operator($db);
        return $db->count_all_results();        
      }

      public function result(){
        $db = $this->get_db();
        $db->from($this->table_name);
        $db = $this->set_operator($db);
        if($this->limit>0){          
          if($this->offset>1){
            $db->limit($this->limit,$this->limit*($this->offset-1));
          } else {
            $db->limit($this->limit);
          }
        }
        $query = $db->get();
        if($this->to_array==TRUE){
          return $query->result_array();
        } else {
          return $query->result();
        }         
      }

      private function set_operator($db){
        foreach($this->where as $key=>$value){
          if(is_numeric($key)){
            $db->where($value);
          } else {
            $db->where($key,$value);
          }
        }  
        foreach($this->like as $key=>$value){
          $db->like($key,$value);
        }
        foreach($this->order_by as $key=>$value){
          $db->order_by($key,$value);
        }
        foreach ($this->join as $key => $value) {
          $db->join($key, $value);
        }
        $db->select($this->select);
        return $db;
      }

      private function get_db(){
        $this->ci->load->database($this->db, TRUE);
        $db = $this->ci->db;
        return $db;
      }
  }