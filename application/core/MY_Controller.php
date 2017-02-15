<?php if (!defined('BASEPATH'))  exit('No direct script access allowed');

require APPPATH . 'core/FRONTEND_Controller.php';
require APPPATH . 'core/BACKEND_Controller.php';

class MY_Controller extends CI_Controller{

	private $login_token_secrect = "yzX,w#kDk_5Q]d;*x2Qq";
	
	public function __construct(){
		parent::__construct();
		$this->view_data = array(
			'__t' => $this,
			'__'  => $this
		);
		$this->load->library('adminlib');
	}

	protected function get_ip()
	{
		$ip;
		if (getenv("HTTP_CLIENT_IP"))
			$ip = getenv("HTTP_CLIENT_IP");
		else if(getenv("HTTP_X_FORWARDED_FOR"))
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		else if(getenv("REMOTE_ADDR"))
			$ip = getenv("REMOTE_ADDR");
		else
			$ip = "UNKNOWN";
			
		$ip = explode(',', $ip);
		return $ip[0];
	}
	
	protected function json_validate($string) {
        if (is_string($string)) {
            @json_decode($string);
            return (json_last_error() === JSON_ERROR_NONE);
        }
        return false;
    }

	protected function create_login_session($user, $remember = FALSE){
		$first_name=$user->first_name;
		$last_name=$user->last_name;
		$email=$user->email;
		$username=$user->username;
		$name_display = "";		
		$time = time();

		if(!empty($first_name)){
			$name_display.=$first_name." ";
		}

		if(!empty($last_name)){
			$name_display.=$last_name;
		}

		if(empty($name_display)){
			if(!empty($email)){
				$name_display=$email;
			} else {
				$name_display=$username;
			}
		}

		$session_data = array(
			'name_display'  => $name_display,
			'user_id'       => $user->username,
			'time'			=> $time,
			'token'			=> $this->login_token($user, $time),
			'avatar'		=> $user->avatar,
			'remember'		=> $remember
		);
		$this->load->library('session');
		$this->session->set_userdata('userinformation',$session_data);
		if($remember==TRUE){
			// Expire in 5 minutes
			$this->session->mark_as_temp('userinformation', 3600*24*30); 
		}
	}

	protected function loggedin(){
		$this->load->library('session');
		if($userinformation = $this->session->userdata('userinformation')){
			$username 	= $userinformation['user_id'];
			$time 		= $userinformation['time'];
			$token 		= $userinformation['token'];
			$this->load->database();
			$this->load->model('users_model');
			if($user = $this->users_model->get_user($username) and $token === $this->login_token($user,$time)){
				return $userinformation;
			}
		}
		return FALSE;
	}

	protected function logout(){
		$this->load->library('session');
		$this->session->unset_userdata('userinformation');
		if($this->session->userdata('userinformation')){
			return FALSE;
		}
		return TRUE;
	}

	protected function userroles(){
		$this->load->library('session');
		if($userinformation = $this->session->userdata('userinformation')){
			if(isset($userinformation['roles'])){
				return $userinformation['roles'];
			}
		}
		return FALSE;
	}

	protected function set_userroles($roles){
		$this->load->library('session');
		if($userinformation = $this->session->userdata('userinformation')){
			$userinformation['roles'] = $roles;
			$this->session->set_userdata('userinformation',$userinformation);
			// $this->session->set_userdata('userinformation',$session_data);	
		}
	}

	private function login_token($user, $time){
		$ip = $this->get_ip();
		$p1 = md5($this->login_token_secrect.$user->username.$time.$ip);
		$p2 = md5($user->username.$time.$this->login_token_secrect.$ip);
		return base64_encode($p1).".".base64_encode($p2);
	}
}