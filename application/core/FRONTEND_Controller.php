<?php if (!defined('BASEPATH')){ exit('No direct script access allowed');
}

/**
 *  FRONTEND Controller
 *
 * @package XGO CMS v3.0
 * @subpackage FRONTEND
 * @link http://sunsoft.vn
 */

class FRONTEND_Controller extends MY_Controller {

	public function __construct() {
		parent::__construct();
		@session_start();
		$this->load->library('twig');
		$this->load->database();
		$this->view_data = array(
			'frontend' 	=> "assets/static/templates/frontend/",
			'__' 		=> $this
		);
	}

	protected function display_error() {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}

	protected function enable_debug() {
		$this->output->enable_profiler(true);
	}

	protected function set_flashcookie($name,$value){
		setcookie($name,$value,time()+180,'/');
	}

	protected function flashcookie($name){
		$value = isset($_COOKIE[$name])?$_COOKIE[$name]:'';
		setcookie($name,'', time() - 3600,'/');
		return $value;
	}

	protected function is_pc(){
		$this->load->library('mobile_detect/mobile_detect');
		$is_pc = !$this->mobile_detect->isMobile() && !$this->mobile_detect->isTablet();
		return $is_pc;
	}

	protected function render($views,$data){
		$views = preg_replace('/\{tmp\}/i', $this->theme, $views);
		return $this->twig->render($views,$data);
	}
}

/* End of file FRONTEND_Controller.php */
/* Location: ./application/core/MY_Backend_Controller.php */