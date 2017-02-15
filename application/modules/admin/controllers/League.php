<?php

class League extends BACKEND_Controller{

	protected $_role = "League";

	public function __construct(){
		parent::__construct(TRUE,FALSE);	
		$this->load->library('twig');
		$this->load->model('league_model');
	}

	public function index($action=NULL){
		switch($action){
			case 'create':
				return $this->update();
			break;
			case 'update':
				$league_id = $this->input->get('league_id');
				if($league_id>0){
					return $this->update($league_id);
				} else {
					redirect('admin/league');
				}
			break;
			case 'delete':
				$league_id = $this->input->get('league_id');
				if($league_id>0){
					$this->league_model->delete(array('id'=>$league_id));
				}
				redirect('admin/league');
			break;
			default:
				$this->datatables('league',$this->league_model,site_url('admin/league/json_data'));
				$this->twig->render("league/list",$this->view_data);
			break;
		}		
	}

	private function update($league_id = 0){
		$league  = new stdClass();
		$league->name = $this->input->post('name');
		$league->introduction = $this->input->post('introduction');
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
				$alias_name = alias_name($league->name);
				if($league_id>0){
					$old_league_data = $this->league_model->find(array(
						'where'=>array('id' => $league_id),
						'one'	=> TRUE
					));

					if(!$old_league_data){
						redirect('admin/league');
						$this->view_data['message']="Giải đấu không tồn tại";
					}

					if(strpos($old_league_data->code, $alias_name)===FALSE){
						$league->code = $alias_name.".{$this->league_model->code_id($alias_name)}";
					}

					$this->league_model->eupdate($league, $league_id);
				} else {
					$league->code = $alias_name.".{$this->league_model->code_id($alias_name)}";

					if(!($league_id = $this->league_model->create($league))>0){
						$this->view_data['message']="Hệ thống đang bận, vui lòng thử lại sau.";
					}
				}
				redirect(site_url('admin/league/index/update')."?league_id=".$league_id);
			}
		} else {
			if($league_id>0){
				$league = $this->league_model->find(array(
					'where'=>array('id' => $league_id),
					'one'	=> TRUE
				));
				if(!$league){
					redirect('admin/league');
					$this->view_data['message']="Khu vực không tồn tại";
				}
			}
		}
		$this->view_data['league'] = $league;
		$this->twig->render("league/edit",$this->view_data);
	}

	public function json_data(){
		$this->get_json_data('league',$this->league_model);
	}
}