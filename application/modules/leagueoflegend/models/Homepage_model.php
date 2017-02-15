<?php

class Homepage_model extends MY_Model{

	public $table_name = "homepage";

	public function __construct(){
		parent::__construct();
	}

	public function get_home_video($type='overview'){
		$configs = $this->find(array(
			'where' => array(
				'type' => $type
			),
			'order_by' => array('position'=>'ASC')
		));
		$video_data = array();
		if($configs && !empty($configs)){	
			$this->load->model('video_model');		
			foreach($configs as $config){
				$params = array(
					'select' => 'video.*'
				);
				$limit = isset($config->limit) && $config->limit>0?$config->limit:0;
				$video_data_item = array(
					'id' 		=> $config->id,
					'code' 		=> $config->code,
					'title' 	=> $config->title,
					'position' 	=> $config->position,
					'video_data'=> array()
				);		
				$join = array();
				$where = array();
				if(isset($config->label_id) && $config->label_id>0){
					$join["taxonomy txl"] = "txl.id=video.label_id and txl.type = 'label'";
					$where['video.label_id'] = $config->label_id;
					$params['select'] .= ", txl.title as label";
				}
				if(isset($config->category_id) && $config->category_id>0){
					$join["category_video catv"] = "catv.video_id=video.id";
					$join["taxonomy txc"] = "txc.id=catv.taxonomy_id and txc.type<> 'label'";
					$where['catv.taxonomy_id'] = $config->category_id;
					$params['select'] .= ", txc.title as category";
				}

				if(isset($config->refer_id) && $config->refer_id>0){
					switch($config->code){
						case 'star_video':
							$join["refer_video refv"] = "refv.video_id=video.id and refv.type='player'";
							$join["player pl"] = "refv.ref_id=pl.id";
							$where['pl.id'] = $config->refer_id;
						break;
						case 'team_video':
							$join["refer_video refv"] = "refv.video_id=video.id and refv.type='team'";
							$join["team"] = "refv.ref_id=team.id";
							$where['team.id'] = $config->refer_id;
						break;
						case 'league_video':
							$join["league lg"] = "lg.id=video.league_id";
							$where['video.league_id'] = $config->refer_id;
						break;
						case 'recent_video':
							
						break;
					}
				}

				if(!empty($join)){
					$params['join'] = $join;
				}
				if(!empty($where)){
					$params['where'] = $where;
				}

				$params['limit'] = $limit;
				$params['order_by'] = array('publish_date'=>'DESC');
				$video_data_item['video_data'] = $this->video_model->find($params);
				$video_data[] = $video_data_item;
			}
		}
		return $video_data;
	}
}