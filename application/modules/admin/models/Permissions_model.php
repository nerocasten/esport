<?php

class Permissions_model extends MY_Model{

	public $table_name = "permissions";

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
}