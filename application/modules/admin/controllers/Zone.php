<?php

class Zone extends BACKEND_Controller{

	protected $_role = "Zone";

	public function __construct(){
		parent::__construct(TRUE,FALSE);	
		$this->load->library('twig');
		$this->load->model('zone_model');
	}

	public function index($action=NULL){
		switch($action){
			case 'create':
				return $this->update();
			break;
			case 'update':
				$team_id = $this->input->get('team_id');
				if($team_id>0){
					return $this->update($team_id);
				} else {
					redirect('admin/zone');
				}
			break;
			case 'delete':
				$team_id = $this->input->get('team_id');
				if($team_id>0){
					$this->zone_model->delete(array('id'=>$team_id));
				}
				redirect('admin/zone');
			break;
			default:
				$this->datatables('zone',$this->zone_model,site_url('admin/zone/json_data'));
				$this->twig->render("zone/list",$this->view_data);
			break;
		}		
	}

	private function update($zone_id = 0){
		$zone  = new stdClass();
		$zone->name = $this->input->post('name');
		if($this->input->server('REQUEST_METHOD')=='POST'){
			$this->load->helper('form');
			$this->load->helper('string_helper');
			$this->load->library('form_validation');
			$this->twig->addFunction(array('form_error'));
			$this->form_validation->set_error_delimiters('<span id="name-error" class="help-block help-block-error">', '</span>');
			//require field and xss clean
			$rules = array(
				array(
					'field'   => 'name',
					'label'   => 'Tiêu đề',
					'rules'   => 'trim|required'
				),
			);
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run()==TRUE){
				$alias_name = alias_name($zone->name);
				if($zone_id>0){
					$old_zone_data = $this->zone_model->find(array(
						'where'=>array('id' => $zone_id),
						'one'	=> TRUE
					));

					if(!$old_zone_data){
						redirect('admin/zone');
						$this->view_data['message']="Khu vực không tồn tại";
					}

					if(strpos($old_zone_data->code, $alias_name)===FALSE){
						$zone->code = $alias_name.".{$this->zone_model->code_id($alias_name)}";
					}

					$this->zone_model->eupdate($zone, $zone_id);
				} else {
					$zone->code = $alias_name.".{$this->zone_model->code_id($alias_name)}";

					if(!($zone_id = $this->zone_model->create($zone))>0){
						$this->view_data['message']="Hệ thống đang bận, vui lòng thử lại sau.";
					}
				}
				redirect(site_url('admin/zone/index/update')."?zone_id=".$zone_id);
			}
		} else {
			if($zone_id>0){
				$zone = $this->zone_model->find(array(
					'where'=>array('id' => $zone_id),
					'one'	=> TRUE
				));
				if(!$zone){
					redirect('admin/zone');
					$this->view_data['message']="Khu vực không tồn tại";
				}
			}
		}
		$this->view_data['zone'] = $zone;
		$this->twig->render("zone/edit",$this->view_data);
	}

	public function json_data(){
		$this->get_json_data('zone',$this->zone_model);
	}
}