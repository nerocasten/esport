<?php

class Profile extends BACKEND_Controller{

	protected $_role = "Profile";

	public function __construct(){
		parent::__construct(TRUE,FALSE);
		$this->load->library('twig');
	}

	public function index(){
		$this->load->library('crypt');
		$this->load->model('users_model');
		$user_info = $this->view_data['user_login_info'];	
		$user = new stdClass;
		$user->first_name = $this->input->post('first_name');
		$user->last_name = $this->input->post('last_name');
		$user->email = $this->input->post('email');
		if($this->input->server('REQUEST_METHOD')=="POST"){
			$this->load->helper('form');
			$this->load->library('form_validation');
			$this->twig->addFunction(array('form_error'));
			$this->form_validation->set_error_delimiters('<span id="name-error" class="help-block help-block-error">', '</span>');
			//require field and xss clean
			$rules = array(
				array(
					'field'   => 'avatar',
					'label'   => 'Mật khẩu xác nhận',
					'rules'   => 'trim|callback_avatar_checker'
				),
				array(
					'field'   => 'email',
					'label'   => 'Thư điện tử',
					'rules'   => 'trim|required|valid_email'
				),
			);
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run()==TRUE){
				$username = $user_info['user_id'];
				/* Upload video image file */
				$user->avatar = $this->adminlib->upload_file($this->avatar_upload_file,"user_avatar");
				if($user_info['avatar']!==$user->avatar){
					$this->adminlib->remove_file($user_info['avatar'], 'user_avatar');
				}
				
				$this->users_model->update($user,array('username'=>$username));
				$user = $this->users_model->find(array(
					'where' => array('username'=>$user_info['user_id']),
					'one'	=> TRUE
				));
				$this->create_login_session($user,$user_info['remember']);
				redirect('admin/profile');
			}
		} else {
			$user = $this->users_model->find(array(
				'where' => array('username'=>$user_info['user_id']),
				'one'	=> TRUE
			));
		}

		$this->view_data['user_profile'] = $user;
		$this->twig->render("users/profile",$this->view_data);
	}

	public function change_password(){
		$old_password=$this->input->post('old_password');
		$new_password=$this->input->post('new_password');
		$retype_password=$this->input->post('retype');
		try{
			$user_info = $this->view_data['user_login_info'];

			if(strlen($old_password)<6 || strlen($new_password)<6){
				throw new Runtimeexception("Mật khẩu phải có ít nhất 6 ký tự");				
			}

			if($new_password !== $retype_password){
				throw new Runtimeexception("Mật khẩu xác nhận không đúng");
			}

			if(!$this->users_model->password_equals($user_info['user_id'],$old_password)){
				throw new Runtimeexception("Mật khẩu hiện tại không chính xác");
			}

			$user = $this->users_model->find(array(
				'where' => array('username'=>$user_info['user_id']),
				'one'	=> TRUE
			));
			$this->load->library('crypt');
			$salt = $this->crypt->salt(20);
			$password = $this->crypt->encode($new_password,$salt);
			$this->users_model->update(array(
				'salt' => $salt,
				'password' => $password
			),array(
				'username'=>$user_info['user_id']
			));
			$this->session->set_flashdata('message', 'Mật khẩu đã được thay đổi');
		} catch(RuntimeException $e){
			$this->load->library('session');
			$this->session->set_flashdata('message',$e->getMessage());
		}
		redirect('admin/profile');
	}

	public function avatar_checker(){
		$this->avatar_upload_file = NULL;
		$limit_file_upload = 1000000; //1MB
		if(isset($_FILES['avatar']) && $_FILES['avatar']['size']>0 && $_FILES['avatar']['error']==0){
			try {    
			    // Undefined | Multiple Files | $_FILES Corruption Attack
			    // If this request falls under any of them, treat it invalid.
			    if (
			        !isset($_FILES['avatar']['error']) ||
			        is_array($_FILES['avatar']['error'])
			    ) {
			        throw new RuntimeException('Invalid parameters.');
			    }

			    // Check $_FILES['upfile']['error'] value.
			    switch ($_FILES['avatar']['error']) {
			        case UPLOAD_ERR_OK:
			            break;
			        case UPLOAD_ERR_NO_FILE:
			            throw new RuntimeException('No file sent.');
			        case UPLOAD_ERR_INI_SIZE:
			        case UPLOAD_ERR_FORM_SIZE:
			            throw new RuntimeException('Exceeded filesize limit.');
			        default:
			            throw new RuntimeException('Unknown errors.');
			    }

			    // You should also check filesize here. 
			    if ($_FILES['avatar']['size'] > 1000000) {
			        throw new RuntimeException('Exceeded filesize limit.');
			    }

			    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
			    // Check MIME Type by yourself.
			    $finfo = new finfo(FILEINFO_MIME_TYPE);
			    if (false === $ext = array_search(
			        $finfo->file($_FILES['avatar']['tmp_name']),
			        array(
			            'jpg' => 'image/jpeg',
			            'png' => 'image/png',
			            'gif' => 'image/gif',
			        ),
			        true
			    )) {
			        throw new RuntimeException('Invalid file format.');
			    }

			    // You should name it uniquely.
			    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
			    // On this example, obtain safe unique name from its binary data.
			    
			    $this->avatar_upload_file = array(
			    	'extention' => $ext,
			    	'filesize' 	=> $_FILES['avatar']['size'],
			    	'tmp_name' 	=> $_FILES['avatar']['tmp_name']
			    );

			} catch (RuntimeException $e) {
			    $this->form_validation->set_message('avatar_checker',$e->getMessage());
		    	return FALSE;
			}
		}
		return TRUE;
	}
}