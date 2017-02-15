<?php

class Users_model extends MY_Model{

	public $table_name = "users";

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function get_user($username){
		return $this->find(array(
			'where'	=>array('username'=>$username),
			'one'	=> TRUE
		));
	}

	public function password_equals($username,$send_password){
		$this->load->library('crypt');
		$user=$this->get_user($username);
		if($user && $this->crypt->equals($user->password,$send_password,$user->salt)){
			return $user;
		}
		return FALSE;
	}

	public function create_user($username,$data){
		$user=$this->get_user($username);
		if(!$user){
			$data = $this->object_to_array($data);
			$data['username'] = $username;
			$data['created'] = date('Y-m-d H:i:s');
			$data['updated'] = date('Y-m-d H:i:s');
			return $this->create($data);
		}
		return FALSE;
	}

	public function update_user($username,$data){
		$user=$this->get_user($username);
		if($user){
			$data = $this->object_to_array($data);
			if(isset($data['username'])) unset($data['username']);
			$data['updated'] = date('Y-m-d H:i:s');
			return $this->update($data,array('username'=>$username));
		}
		return FALSE;
	}
	
	

	public function init_data(){
		return array(
			array(
				'name' => 'username',
				'label' => "Tài khoản",
				'width' => '30%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' => 'email',
				'label' => "Email",
				'width' => '30%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' => 'group',
				'label' => "Nhóm",
				'width' => '30%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' 	=> 'id',
				'label' => '<a class="btn btn-primary" href="'.site_url('admin/users/index/create').'">New</a>',
				'width' => '10%',
				'sort'  => false
			),
		);
	}

	public function json_data($controller){
		$this->datatables
		->select('username,email,group,id')
		->from($this->table_name);
		$this->datatables->set_produce_output(false);
		$ouput = $datatables = $this->datatables->generate();
		unset($ouput['aaData']);
		$ouput['aaData'] = array();
		foreach($datatables['aaData'] as $item){
			$ouput['aaData'][] = array(
				"<a href='".site_url("admin/users/index/update")."?user_id={$item['id']}"."'>{$item['username']}</a>",
				$item['email'],
				$item['group'],
				"<a href='".site_url("admin/users/index/delete")."?user_id={$item['id']}"."'>Xóa</a>",
			);
		}
		return json_encode($ouput);
	}
}