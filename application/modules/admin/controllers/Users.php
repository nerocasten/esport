<?php

class Users extends BACKEND_Controller{

	protected $_role = "user-manager";

	public function __construct(){
		parent::__construct();
		$this->load->library('twig');
	}

	public function index($action = NULL){
		switch($action){
			case 'create':
				return $this->update();
			break;
			case 'update':
				$user_id = $this->input->get('user_id');
				if($user_id>0){
					return $this->update($user_id);
				} else {
					redirect('admin/users');
				}
			break;
			case 'delete':
				$user_id = $this->input->get('user_id');
				if($user_id>0){
					return $this->team_model->delete(array('id'=>$user_id));
				}
				redirect('admin/users');
			break;
			case 'change_password':
				$user_id = $this->input->get('user_id');
				if($user_id>0){
					return $this->change_password($user_id);
				} else {
					redirect('admin/users');
				}
			break;
			default:
				$this->datatables('users',$this->users_model);
				$this->twig->render("users/list",$this->view_data);
			break;
		}		
	}

	private function update($user_id = NULL){
		$my_user_info = $this->view_data['user_login_info'];
		$my_user = $this->users_model->find(array(
			'where' => array('username' => $my_user_info['user_id']),
			'one'	=> TRUE
		));
		if($my_user->id == $user_id){
			redirect('admin/profile');
		}
		$this->load->library('crypt');
		$this->load->model('users_model');	
		$user = new stdClass;
		$user->username = $this->input->post('username');
		$user->first_name = $this->input->post('first_name');
		$user->last_name = $this->input->post('last_name');
		$user->email = $this->input->post('email');
		$user->active = $this->input->post('active');
		$password = $this->input->post('password');
		$retype = $this->input->post('retype');
		$this->avatar_upload_file = NULL;
		
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

			if(!($user_id>0)){
				$rules = array_merge($rules,array(
					array(
						'field'   => 'username',
						'label'   =>  'Tài khoản',
						'rules'   => 'trim|required|max_length[150]|min_length[5]|callback_username_unique'
					),
					array(
						'field'   => 'password',
						'label'   => 'Mật khẩu',
						'rules'   => 'trim|required|max_length[250]|min_length[6]'
					),
					array(
						'field'   => 'retype',
						'label'   => 'Mật khẩu xác nhận',
						'rules'   => 'trim|required|matches[password]'
					),
				));
			}

			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run()==TRUE){
				$user->avatar = $this->adminlib->upload_file($this->avatar_upload_file,"user_avatar");
				if($user_id>0){
					unset($user->username);

					$user_old_data = $this->users_model->find(array(
						'where' => array('id'=> $user_id),
						'one'	=> TRUE
					));

					if(!$user_old_data){
						$this->session->flashdata('message','Tài khoản không tồn tại');
						redirect('admin/users');
					}
					if(empty($user->avatar)){
						unset($user->avatar);
					} elseif($user_old_data->avatar!==$user->avatar){
						$this->adminlib->remove_file($user_old_data->avatar, 'user_avatar');
					}
					$user->updated = date('Y-m-d H:i:s');
					$this->users_model->update($user, $user_id);
				} else {
					$user->salt = $this->crypt->salt(20);
					$user->password = $this->crypt->encode($password,$user->salt);
					$user->created = $user->updated = date('Y-m-d H:i:s');
					if(!($user_id=$this->users_model->create_user($user->username,$user))>0){
						$this->session->flashdata('message',"Hệ thống đang bận, vui lòng thử lại sau.");
						//Xóa file vừa up
						if(!empty($user->avatar)){
							$this->adminlib->remove_file($user->avatar, 'user_avatar');
						}						
					}
				}
				redirect(site_url('admin/users/index/update').'?user_id='.$user_id);
			}
		} else {
			if($user_id>0){
				$user = $this->users_model->find(array(
					'where' => array('id' => $user_id),
					'one'	=> TRUE
				));
				if(!$user){
					redirect('admin/users');
				}
			}
		}
		$user->id = $user_id;
		$this->view_data['user'] = $user;
		if($user_id>0){
			return $this->twig->render("users/edit",$this->view_data);
		} else {
			return $this->twig->render("users/new",$this->view_data);
		}		
	}

	private function change_password($user_id){
		$new_password=$this->input->post('new_password');
		$retype_password=$this->input->post('retype');
		try{
			$user = $this->users_model->find(array(
				'where' => array('id' => $user_id),
				'one'	=> TRUE
			));
			if(!$user){
				$this->session->flashdata('message','Tài khoản không tồn tại');
			}

			if(strlen($new_password)<6){
				throw new Runtimeexception("Mật khẩu phải có ít nhất 6 ký tự");				
			}

			if($new_password !== $retype_password){
				throw new Runtimeexception("Mật khẩu xác nhận không đúng");
			}

			$this->load->library('crypt');
			$salt = $this->crypt->salt(20);
			$password = $this->crypt->encode($new_password,$salt);
			$this->users_model->update(array(
				'salt' => $salt,
				'password' => $password
			),array(
				'username'=>$user_id
			));
			$this->session->set_flashdata('message', 'Mật khẩu đã được thay đổi');
		} catch(RuntimeException $e){
			$this->load->library('session');
			$this->session->set_flashdata('message',$e->getMessage());
		}
		redirect(site_url('admin/users/index/update').'?user_id='.$user_id);
	}

	public function username_unique($username){
		if($this->users_model->find(array('where'=>array('username'=>$username),'one'=>TRUE))){
			$this->form_validation->set_message('username_unique',"Username is exists");
		    return FALSE;
		}
		return TRUE;
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

	public function json_data(){
		$this->get_json_data('users',$this->users_model);
	}
}