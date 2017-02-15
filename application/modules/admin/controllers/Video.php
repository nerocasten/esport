<?php

class Video extends BACKEND_Controller {

	protected $_role = "video-manager";

	public function __construct(){
		parent::__construct(TRUE,FALSE);	
		$this->load->library('twig');
		$this->load->model('video_model');
		$this->load->model('taxonomy_model');
		$this->load->model('league_model');
		$this->load->model('player_model');
		$this->load->model('team_model');
		$this->load->model('refer_video_model');
	}

	public function index($action=NULL){
		switch($action){
			case 'create':
				return $this->video_update();
			break;
			case 'update':
				$video_id = $this->input->get('video_id');
				if($video_id>0){
					return $this->video_update($video_id);
				} else {
					redirect('admin/video');
				}
			break;
			case 'delete':
				$video_id = $this->input->get('video_id');
				if($video_id>0){
					$this->video_model->delete(array('id'=>$video_id));
				}
				redirect('admin/video');
			break;
			default:
				$this->datatables('video',$this->video_model);
				return $this->twig->render("video/list",$this->view_data);
			break;
		}
	}

	public function label($action=NULL){
		switch($action){
			case 'create':
				return $this->label_update();
			break;
			case 'update':
				$label_id = $this->input->get('label_id');
				if($label_id>0){
					return $this->label_update($label_id);
				} else {
					redirect('admin/video/label');
				}
			break;
			case 'delete':
				$label_id = $this->input->get('label_id');
				if($label_id>0){
					$this->taxonomy_model->delete(array('id'=>$label_id,'type'=>'label'));
				}
				redirect('admin/video/label');
			break;
			default:
				$this->datatables('video',$this->taxonomy_model,site_url('admin/video/label_json_data'));
				$this->view_data['datatables']['init_data'] = $this->taxonomy_model->init_data(site_url('admin/video/label/create'));
				return $this->twig->render("video/list",$this->view_data);
			break;
		}
	}

	public function list_content_ajax(){
		$param = array();
		$player_id = (int)$this->input->get('player_id');
		$player_video = array();
		$videos = $this->video_model->find();
		$this->twig->addFunction(array('in_array'));
		if($player_id>0){
			$this->load->model('refer_video_model');
			$refer_video_model = $this->refer_video_model->find(array(
				'select' => 'video_id',
				'where'	 => array(
					'ref_id' => $player_id,
					'type'	 => 'player'
				)
			));
			if($refer_video_model){
				foreach($refer_video_model as $pv){
					$player_video[] = $pv->video_id;
				}
			}
		}
		$this->view_data['videos'] = $videos;
		$this->view_data['player_video'] = $player_video;
		return $this->twig->render('video/content_ajax', $this->view_data);
	}

	public function player_html_ajax(){
		$video_id = $this->input->get('video_id');
		$player_ids = $this->input->get('player_ids');
		$htmlElm = "";
		if($video_id && $video_id>0){
			$this->view_data['players'] = $this->player_model->find(array(
				'select'=> "{$this->player_model->table_name}.*",
				'join'	=> array('refer_video pv'=>"pv.ref_id={$this->player_model->table_name}.id"),
				'where' => array(
					'pv.video_id' 	=> $video_id,
					'type'			=> 'player'
				)
			));
			return $this->twig->render('video/player_html_ajax', $this->view_data);
		} elseif(!empty($player_ids) and ($player_ids = @json_decode($player_ids,TRUE))){			
			$this->view_data['players'] = $this->player_model->find(array(
				'where_in' => array('id' => $player_ids)
			));
			return $this->twig->render('video/player_html_ajax', $this->view_data);
		}
		return $htmlElm;
	}

	public function team_html_ajax(){
		$video_id = $this->input->get('video_id');
		$team_ids = $this->input->get('team_ids');
		$htmlElm = "";
		if($video_id && $video_id>0){
			$this->view_data['teams'] = $this->team_model->find(array(
				'select'=> "{$this->team_model->table_name}.*",
				'join'	=> array('refer_video pv'=>"pv.ref_id={$this->team_model->table_name}.id"),
				'where' => array(
					'pv.video_id' 	=> $video_id,
					'type'			=> 'team'
				)
			));
			return $this->twig->render('video/team_html_ajax', $this->view_data);
		} elseif(!empty($team_ids) and ($team_ids = @json_decode($team_ids,TRUE))){			
			$this->view_data['teams'] = $this->team_model->find(array(
				'where_in' => array('id' => $team_ids)
			));
			return $this->twig->render('video/team_html_ajax', $this->view_data);
		}
		return $htmlElm;
	}

	private function video_update($video_id = 0){
		$video  = new stdClass();
		$video->id = $video_id;
		$this->video_id = $video->id;
		$video->title = $this->input->post('title');
		$video->introduction = $this->input->post('introduction');
		$video->content = $this->input->post('content');
		$video->label_id = $this->input->post('label_id');
		$video->video_url = $this->input->post('video_url');
		$video->tag = $this->input->post('tag');
		$video->status = $this->input->post('status');
		$video->league_id = $this->input->post('league_id');
		$player_ids = $this->input->post('player_ids');
		$team_ids = $this->input->post('team_ids');
		$this->image_upload_file = NULL;
		if($this->input->server('REQUEST_METHOD')=='POST'){
			$this->load->helper('form');
			$this->load->helper('string_helper');
			$this->load->library('form_validation');
			$this->twig->addFunction(array('form_error'));
			$this->form_validation->set_error_delimiters('<span id="name-error" class="help-block help-block-error">', '</span>');
			//require field and xss clean
			$rules = array(
				array(
					'field'   => 'title',
					'label'   => 'Tiêu đề',
					'rules'   => 'trim|required'
				),
				array(
					'field'   => 'image',
					'label'   => 'Thư điện tử',
					'rules'   => 'trim|callback_image_required'
				),
				array(
					'field'   => 'video_url',
					'label'   => 'Video url',
					'rules'   => 'trim|required'
				),
			);
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run()==TRUE){			
				/* Upload video image file */
				$video->image = $this->adminlib->upload_file($this->image_upload_file,"video_image");
				$alias_title = alias_name($video->title);
				$video->tag = hashtag_parser($video->tag);
				if($video_id>0){
					$old_video_data = $this->video_model->find(array(
						'where'=>array('id' => $video_id),
						'one'	=> TRUE
					));

					if(!$old_video_data){
						redirect('admin/video');
						$this->view_data['message']="Video không tồn tại";
					}

					if(strpos($old_video_data->code, $alias_title)===FALSE){
						$video->code = $alias_title.".{$this->video_model->code_id($alias_title)}";
					}

					if(empty($video->image)){
						unset($video->image);
					} else {
						if($old_video_data->image!==$video->image){
							$this->adminlib->remove_file($old_video_data->image, 'video_image');
						}
					}		
					$video->updated = date('Y-m-d H:i:s');	
					if(isset($video->id) && $video->id>0){
						unset($video->id);
					}		
					$this->video_model->eupdate($video, $old_video_data->id);
				} else {
					$video->code = $alias_title.".{$this->video_model->code_id($alias_title)}";
					$video->created = $video->updated = date('Y-m-d H:i:s');
					if(!($video_id = $this->video_model->create($video))>0){
						$this->view_data['message']="Hệ thống đang bận, vui lòng thử lại sau.";
						//Xóa file vừa up
						$this->adminlib->remove_file($user->image, 'video_image');
						redirect('admin/video');
					}
				}

				if($video_id>0){

					/* Xử lý player */
					$player_model=NULL;
					$player_video_data = array();
					if(!empty($player_ids)){
						$player_model=$this->player_model->find(array(
							'select' => 'id',
							'where_in' => array('id'=>$player_ids)
						));
					}
					if($player_model && !empty($player_model)){
						foreach($player_model as $player){
							$player_video_data[] = array(
								'ref_id' 	=> $player->id,
								'video_id' 	=> $video_id,
								'type'		=> 'player'
							);
						}
					}				
					$this->refer_video_model->delete(array(
						'video_id'	=> $video_id,
						'type'		=> 'player'
					));
					if(!empty($player_video_data)){
						$this->refer_video_model->create_multiple($player_video_data);
					}

					/* Xử lý Team */
					$team_model = NULL;
					$team_data = array();
					if(!empty($team_ids)){
						$team_model=$this->team_model->find(array(
							'select' => 'id',
							'where_in' => array('id'=>$team_ids)
						));
					}
					if($team_model && !empty($team_model)){
						foreach($team_model as $team){
							$team_data[] = array(
								'ref_id' 	=> $team->id,
								'video_id' 	=> $video_id,
								'type'		=> 'team'
							);
						}
					}				
					$this->refer_video_model->delete(array(
						'video_id'	=> $video_id,
						'type'		=> 'team'
					));
					if(!empty($team_data)){
						$this->refer_video_model->create_multiple($team_data);
					}
				}
				redirect(site_url('admin/video/index/update')."?video_id=".$video_id);
			}
		} else {
			if($video_id>0){
				$video = $this->video_model->find(array(
					'where'=>array('id' => $video_id),
					'one'	=> TRUE
				));
				if(!$video){
					redirect('admin/video');
					$this->view_data['message']="Video không tồn tại";
				}
			}
		}
		$this->view_data['taxo_label'] = $this->taxonomy_model->taxo_with_type('label');
		$this->view_data['leagues'] = $this->league_model->find();
		$this->view_data['video'] = $video;
		$this->twig->render("video/new",$this->view_data);
	}

	private function label_update($label_id = 0){
		$label  = new stdClass();
		$label->title = $this->input->post('title');
		if($this->input->server('REQUEST_METHOD')=='POST'){
			$this->load->helper('form');
			$this->load->helper('string_helper');
			$this->load->library('form_validation');
			$this->twig->addFunction(array('form_error'));
			$this->form_validation->set_error_delimiters('<span id="name-error" class="help-block help-block-error">', '</span>');
			//require field and xss clean
			$rules = array(
				array(
					'field'   => 'title',
					'label'   => 'Tiêu đề',
					'rules'   => 'trim|required'
				),
			);
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run()==TRUE){
				$alias_title = alias_name($label->title);
				$label->type = "label";
				if($label_id>0){
					$old_label_data = $this->taxonomy_model->find(array(
						'where'=>array('id' => $label_id),
						'one'	=> TRUE
					));

					if(!$old_label_data){
						redirect('admin/video/label');
						$this->view_data['message']="Label không tồn tại";
					}

					if(strpos($old_label_data->code, $alias_title)===FALSE){
						$label->code = $alias_title.".{$this->taxonomy_model->code_id($alias_title)}";
					}

					$this->taxonomy_model->eupdate($label, $label_id);
				} else {
					$label->code = $alias_title.".{$this->taxonomy_model->code_id($alias_title)}";

					if(!($label_id = $this->taxonomy_model->create($label))>0){

						$this->session->flashdata('message',"Hệ thống đang bận, vui lòng thử lại sau.");
					}
				}
				redirect(site_url('admin/video/label/update')."?label_id=".$label_id);
			}
		} else {
			if($label_id>0){
				$label = $this->taxonomy_model->find(array(
					'where'=>array('id' => $label_id),
					'one'	=> TRUE
				));
				if(!$label){
					redirect('admin/video/label');
					$this->view_data['message']="Label không tồn tại";
				}
			}
		}
		$this->view_data['label'] = $label;
		$this->twig->render("video/label",$this->view_data);
	}

	public function image_required(){
		try {    
		    // Undefined | Multiple Files | $_FILES Corruption Attack
		    // If this request falls under any of them, treat it invalid.
		    if (
		        !isset($_FILES['image']['error']) ||
		        is_array($_FILES['image']['error'])
		    ) {
		        throw new RuntimeException('Invalid parameters.');
		    }

			if($this->video_id>0 && $_FILES['image']['error']>0){
				return TRUE;
			}
		    // Check $_FILES['upfile']['error'] value.
		    switch ($_FILES['image']['error']) {
		        case UPLOAD_ERR_OK:
		            break;
		        case UPLOAD_ERR_NO_FILE:
		            throw new RuntimeException('Please select an image file.');
		        case UPLOAD_ERR_INI_SIZE:
		        case UPLOAD_ERR_FORM_SIZE:
		            throw new RuntimeException('Exceeded filesize limit.');
		        default:
		            throw new RuntimeException('Unknown errors.');
		    }

		    // You should also check filesize here. 
		    if ($_FILES['image']['size'] > 1000000) {
		        throw new RuntimeException('Exceeded filesize limit.');
		    }

		    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
		    // Check MIME Type by yourself.
		    $finfo = new finfo(FILEINFO_MIME_TYPE);
		    if (false === $ext = array_search(
		        $finfo->file($_FILES['image']['tmp_name']),
		        array(
		            'jpg' => 'image/jpeg',
		            'png' => 'image/png',
		        ),
		        true
		    )) {
		        throw new RuntimeException('Invalid file format.');
		    }

		    // You should name it uniquely.
		    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
		    // On this example, obtain safe unique name from its binary data.
		    
		    $this->image_upload_file = array(
		    	'extention' => $ext,
		    	'filesize' 	=> $_FILES['image']['size'],
		    	'tmp_name' 	=> $_FILES['image']['tmp_name']
		    );

		} catch (RuntimeException $e) {
		    $this->form_validation->set_message('image_required',$e->getMessage());
	    	return FALSE;
		}
	}

	public function json_data(){
		$this->get_json_data('video',$this->video_model);
	}

	public function label_json_data(){
		$this->get_json_data('video',$this->taxonomy_model);
	}
}