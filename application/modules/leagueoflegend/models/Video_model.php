<?php

class Video_model extends MY_Model{

	public $table_name = "video";

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}
}