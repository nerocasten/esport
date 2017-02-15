<?php

class Homepage extends BACKEND_Controller{

	protected $_role = "Homepage";

	public function __construct(){
		parent::__construct(TRUE,FALSE);	
		$this->load->library('twig');
		$this->load->model('homepage_model');
		$this->load->model('league_model');
		$this->load->model('team_model');
		$this->load->model('player_model');
		$this->load->model('taxonomy_model');
	}

	public function index($action = NULL){
		switch($action){
			case 'overview':
				return $this->overview();
			break;
		}		
	}

	public function overview(){
		if($this->input->server('REQUEST_METHOD')=='POST'){
			$data = $this->input->post('data');
			if($data && !empty($data)){
				$position = 0;				
				foreach($data as $item){
					if(!empty($item)){
						$id = 0;
						$code = $item['type'];
						$label_id 	= isset($item['label_id'])?$item['label_id']:NULL;
						$category_id = isset($item['category_id'])?$item['category_id']:NULL;
						$refer_id = isset($item['refer_id'])?$item['refer_id']:NULL;
						$status = isset($item['status'])?$item['status']:NULL;
						unset($item['type']);
						if(isset($item['id'])){						
							if($item['id']>0){
								$id = $item['id'];
							}
							unset($item['id']);
						}
						if($status && $status=='close'){
							if($id>0){
								$this->homepage_model->delete(array(
									'id' 	=> $id,
									'type'	=> 'overview'
								));
							}
						} else {
							$data_item = array(
								'code' 			=> $code,
								'title' 		=> $item['title'],
								'label_id' 		=> $label_id,
								'category_id' 	=> $category_id,
								'refer_id' 		=> $refer_id,
								'type'			=> 'overview',
								'position' 		=> ++$position
							);
							if($id>0){
								$this->homepage_model->eupdate($data_item,$id);
							} else {
								$this->homepage_model->create($data_item);
							}
						}						
					}					
				}
			}
			redirect('admin/homepage/index/overview');
		}
		$overview = $this->homepage_model->find(array(
			'where' => array('type' => 'overview'),
			'order_by' => array('position' => 'ASC')
		));
		$overview_data = array();
		$labels = $this->taxonomy_model->find(array('where'=>array('type'=>'label')));
		$categorys = $this->taxonomy_model->find(array('where'=>array('type <>'=>'label')));
		$params = array(			
			'labels'	=> $labels,
			'categorys'	=> $categorys,
		);
		foreach($overview as $item){
			$params['data'] = $item;
			switch($item->code){
				case 'league_video':
					$leagues = $this->league_model->find();
					$overview_data[] = $this->render("homepage/widget/league_video",array_merge($params,array(
						'leagues' 	=> $leagues,
						'action'	=> 'delete'
					)));
				break;
				case 'team_video':
					$team = $this->team_model->find();
					$overview_data[] = $this->render("homepage/widget/team_video",array_merge($params,array(
						'team' => $team,
						'action'	=> 'delete'
					)));
				break;
				case 'star_video':
					$star = $this->player_model->find();
					$overview_data[] = $this->render("homepage/widget/star_video",array_merge($params,array(
						'star' => $star,
						'action'	=> 'delete'
					)));
				break;
				case 'recent_video':
					$overview_data[] = $this->render("homepage/widget/recent_video",array_merge($params,array(
						'action'	=> 'delete'
					)));
				break;
			}
		}
		/*echo '<pre>';
		print_r($overview_data);
		die;*/
		$this->view_data['overview'] = $overview_data;
		$this->view_data['js'] = array(
			'top' => array(
				$this->a('jquery-ui/jquery-ui.min.js','3party')
			)
		);
		$this->widget_overview();
		return $this->twig->render("homepage/overview",$this->view_data);
	}

	private function widget_overview(){
		$labels = $this->taxonomy_model->find(array('where'=>array('type'=>'label')));
		$categorys = $this->taxonomy_model->find(array('where'=>array('type <>'=>'label')));
		$leagues = $this->league_model->find();
		$star = $this->player_model->find();
		$team = $this->team_model->find();
		$params = array(			
			'labels'	=> $labels,
			'categorys'	=> $categorys,
		);
		$this->view_data['widget_overview'] = array(
			$this->render("homepage/widget/recent_video",$params),
			$this->render("homepage/widget/star_video",array_merge($params,array(
				'star' 	=> $star,
			))),
			$this->render("homepage/widget/team_video",array_merge($params,array(
				'team' 	=> $team,
			))),
			$this->render("homepage/widget/league_video",array_merge($params,array(
				'leagues' 	=> $leagues,
			))),
		);
	}

	private function render($view, $params = array()){
		$this->twig->render($view,$params);
		return $this->output->get_output();
	}
}