<?php

class Roles_model extends MY_Model{

	public $table_name = "roles";

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
}