<?php

class Team extends BACKEND_Controller{

	protected $_role = "team-manager";

	public function __construct(){
		parent::__construct(TRUE,FALSE);	
		$this->load->library('twig');
		$this->load->model('team_model');
		$this->load->model('zone_model');
		$this->load->model('league_model');
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
					redirect('admin/team');
				}
			break;
			case 'delete':
				$team_id = $this->input->get('team_id');
				if($team_id>0){
					$this->team_model->delete(array('id'=>$team_id));
				}
				redirect('admin/team');
			break;
			default:
				$this->datatables('team',$this->team_model,site_url('admin/team/json_data'));
				$this->twig->render("team/list",$this->view_data);
			break;
		}		
	}

	private function update($team_id = 0){
		$team  = new stdClass();
		$team->name = $this->input->post('name');
		$team->introduction = $this->input->post('introduction');
		$team->zone_id = $this->input->post('zone_id');
		$team->league_id = $this->input->post('league_id');
		$team->tag = $this->input->post('tag');
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
				$alias_name = alias_name($team->name);
				$team->tag = hashtag_parser($team->tag);
				if($team_id>0){
					$old_team_data = $this->team_model->find(array(
						'where'=>array('id' => $team_id),
						'one'	=> TRUE
					));

					if(!$old_team_data){
						redirect('admin/team/index');
						$this->view_data['message']="Team không tồn tại";
					}

					if(strpos($old_team_data->code, $alias_name)===FALSE){
						$team->code = $alias_name.".{$this->team_model->code_id($alias_name)}";
					}

					$this->team_model->eupdate($team, $team_id);
				} else {
					$team->code = $alias_name.".{$this->team_model->code_id($alias_name)}";

					if(!($team_id = $this->team_model->create($team))>0){
						$this->view_data['message']="Hệ thống đang bận, vui lòng thử lại sau.";
					}
				}
				redirect(site_url('admin/team/index/update')."?team_id=".$team_id);
			}
		} else {
			if($team_id>0){
				$team = $this->team_model->find(array(
					'where'=>array('id' => $team_id),
					'one'	=> TRUE
				));
				if(!$team){
					redirect('admin/team/index');
					$this->view_data['message']="Team không tồn tại";
				}
			}
		}
		$this->view_data['team'] = $team;
		$this->view_data['zones'] = $this->zone_model->find();
		$this->view_data['leagues'] = $this->league_model->find();
		$this->twig->render("team/edit",$this->view_data);
	}

	public function json_data(){
		$this->get_json_data('team',$this->team_model);
	}

	public function list_team_ajax(){
		$video_id = $this->input->get('video_id');
		$htmlElm = "";
		$teams = $this->team_model->find();
		$team_video = array();
		$this->twig->addFunction(array('in_array'));
		if($video_id>0){
			$this->load->model('refer_video_model');
			$refer_video_model = $this->refer_video_model->find(array(
				'select' => 'ref_id',
				'where'	 => array(
					'video_id' 	=> $video_id,
					'type'		=> 'team'
				)
			));
			if($refer_video_model){
				foreach($refer_video_model as $pv){
					$team_video[] = $pv->ref_id;
				}
			}
		}
		$this->view_data['teams'] = $teams;
		$this->view_data['team_video'] = $team_video;
		return $this->twig->render('team/content_ajax', $this->view_data);
	}
}