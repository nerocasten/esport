<?php

class Signin extends MY_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('twig');
		$this->view_data = array(
			'__t' => $this
		);
	}

	public function index(){
		$redirect=$this->input->get('redirect_uri');
		if($this->loggedin()){
			if(!$redirect){
				$redirect = 'admin';
			}
			redirect($redirect);
		}	
		
		$this->load->language('admin');
		$this->load->model('users_model');
		$this->load->library('session');

		$user = new stdclass;
		$user->username = $this->input->post('username');
		$user->password = $this->input->post('password');
		if($this->input->server('REQUEST_METHOD')=='POST'){
			$this->load->library('form_validation');
			$this->load->helper('form');

			$this->twig->addFunction(array('form_error'));
			$this->form_validation->set_error_delimiters('', '</br>');
			$this->account_validation = array('valid'=>FALSE);

			$rules = array(
				array(
					'field'   => 'username',
					'label'   =>  'Tài khoản',
					'rules'   => 'trim|required|min_length[2]|callback_username_unique'
				),
				array(
					'field'   => 'password',
					'label'   =>  'Mật khẩu',
					'rules'   => 'trim|required|min_length[6]|callback_password_checker'
				)
			);
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run()==TRUE && $this->account_validation['valid']==TRUE && $this->account_validation['user_data']){
				if($this->account_validation['user_data']->active==1){
					$this->create_login_session($this->account_validation['user_data'],$this->input->post('remember'));
					redirect(site_url('admin/signin')."?redirect_uri=$redirect");
				} else {
					$this->view_data['validation_errors'] = "Account has blocked";
				}				
			} else {
				$this->view_data['validation_errors'] = validation_errors();
			}
		} else {
			$this->view_data['validation_errors'] = $this->session->userdata('message');
		}
		$this->view_data['user'] = $user;
		$this->twig->render('users/signin',$this->view_data);
	}

	public function out(){	
		$this->load->library('session');
		$this->session->set_flashdata('message',$this->session->flashdata('message'));
		if(!$this->logout()){
			$this->session->set_flashdata('message',"Error!\n Please contact to administrator");
		}
		redirect('admin/signin');
	}

	public function a($file = "",$type=NULL){
		switch($type){
			case '3party':
				$current_path="assets/third_party/";
			break;
			default:
				$current_path="assets/static/templates/admin/";
			break;
		}		
		return base_url().$current_path.$file;
	}

	public function username_unique($username){
		$user_data=$this->users_model->get_user($username);
		if(!$user_data){
			$this->form_validation->set_message('username_unique',"Username or Password is incorrect!!");
		    return FALSE;
		}
		$this->account_validation['user_data'] = $user_data;
		return TRUE;
	}

	public function password_checker($password){
		$message = "Username or Password is incorrect!!";
		try{			
			if(!isset($this->account_validation['user_data']) || !$this->account_validation['user_data']){
				throw new RuntimeException($message);
			}

			if(!$this->users_model->password_equals($this->account_validation['user_data']->username,$password)){
				throw new RuntimeException($message);
			}
			$this->account_validation['valid'] = TRUE;
			return TRUE;
		} catch(RuntimeException $e){
			$this->form_validation->set_message('password_checker',$e->getMessage());
			return FALSE;
		}
	}
}