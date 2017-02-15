<?php 
if (!defined('BASEPATH')){
	exit('No direct script access allowed');
}

/**
 *  MY Backend Controller
 *
 * @package XGO CMS v3.0
 * @subpackage MY Backend
 * @link http://sunsoft.vn
 */

class BACKEND_Controller extends MY_Controller {

	public function __construct($checkLogin=TRUE,$checkRole=TRUE) {
		parent::__construct();		
		$this->load->library('session');
		$this->load->database();
		if($checkLogin){
			if(!($user_data=$this->loggedin())){
				$this->session->set_flashdata('message',"Please sign in.");
				redirect('admin/signin');
			}
			if(!$this->permission($user_data) && $checkRole){
				$this->session->set_flashdata('message',"Forbidden Zone.");
				redirect('admin');
			}
		}
		$this->view_data['user_login_info'] = $this->loggedin();
		$this->view_data['message'] = $this->session->flashdata('message');		
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

	public function permission($userdata){
		$username=$userdata['user_id'];
		if(!($roles = $this->userroles())){
			$roles = $this->permission_role($username);
			$this->set_userroles($roles);
		}
		return isset($this->_role) && isset($roles[$this->_role]) && $roles[$this->_role];
	}

	public function permission_role($username){
		$this->load->model('users_model');
		$this->load->model('roles_model');
		$this->load->model('permissions_model');
		$user_roles = $this->users_model->find(array(
			'select' => 'gt.role,gt.permission',
			'from'	=> $this->users_model->table_name." u",
			'join'	=> array(
				'grant gt'=>'gt.group=u.group',
				'permissions p'=>'p.code=gt.permission',
				'groups g'=>'g.code=gt.group',
				'roles r'=>'r.code=gt.role',
			),
		));
		$permission_model=$this->permissions_model->find(array('select'=>'code'));
		$roles_model=$this->roles_model->find(array('select'=>'code'));
		$role_permission = NULL;
		if($roles_model && $permission_model && !empty($roles_model) && !empty($permission_model)){
			$role_permission = array();
			foreach($roles_model as $role){
				$role_permission[$role->code] = array();
				foreach($permission_model as $permission){
					$role_permission[$role->code][$permission->code] = FALSE;
				}
				foreach($user_roles as $user_role){

				}
			}
			foreach($user_roles as $user_role){
				foreach($role_permission as $role=>$permission){
					if($user_role->role==$role){
						$role_permission[$role][$user_role->permission] = TRUE;break;
					}
				}
			}
			
		}
		return $role_permission;
	}

	/*public function datatables(){
		$this->view_data['js'] = array(
			'bot' => array(
				$this->a('datatables/datatables.min.js','3party'),
				$this->a('datatables/plugins/bootstrap/datatables.bootstrap.js','3party'),
				$this->a('bootstrap-datepicker/js/bootstrap-datepicker.min.js','3party'),
				$this->a('common/js/nero-datatables.js'),
			)
		);
		$this->view_data['css'] = array(
			$this->a('datatables/datatables.min.css','3party'),
			$this->a('datatables/plugins/bootstrap/datatables.bootstrap.css','3party'),
			$this->a('bootstrap-datepicker/css/bootstrap-datepicker3.min.css','3party')
		);
	}*/

	protected function datatables($controller, $model, $json_link = NULL){
		$this->load->library('Xgo_datatables', '', 'datatables');

		$this->view_data['js']	= array(
			'top' => array(
				base_url().'assets/third_party/datatables/js/jquery.dataTables.js',
				base_url().'assets/third_party/datatables/js/dataTables.bootstrap.js',
				base_url().'assets/third_party/datatables/js/jquery.dataTables.columnFilter.js',
			)
		);

		$this->view_data['css']			= array(
			// base_url().'assets/third_party/bootstrap/css/bootstrap.min.css',
			base_url().'assets/third_party/datatables/css/dataTables.bootstrap.css',
		);


		$this->view_data['datatables']		= array(
			'json_data'		=> $json_link?$json_link:site_url('admin/'.$controller.'/json_data'),
			'init_data' 	=> $model->init_data(),
			'filter'		=> '',
			'label'			=> 'z',
		);
	}

	protected function get_json_data($controller, $model){
		$this->load->library('Xgo_datatables', '', 'datatables');
		echo $this->input->get('callback').'('.$model->json_data($controller).')';
	}

}

/* End of file BACKEND_Controller.php */
/* Location: ./application/core/BACKEND_Controller.php */