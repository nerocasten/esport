<?php

class Admin extends BACKEND_Controller{

	protected $_role = "Dashboard";

	public function __construct(){
		parent::__construct(TRUE,FALSE);	
		$this->load->library('twig');
	}

	public function index(){
		/*echo '<pre>';
		print_r($this->view_data['user_login_info']);
		die;*/
		$this->twig->render("dashboard",$this->view_data);
	}
}