<?php

class Player extends BACKEND_Controller{

	protected $_role = "player";

	public function __construct(){
		parent::__construct(TRUE,FALSE);	
		$this->load->library('twig');
		$this->load->model('player_model');
		$this->load->model('refer_video_model');
		$this->load->model('team_model');
		$this->load->model('video_model');
		$this->load->model('taxonomy_model');
	}

	public function index($action=NULL){
		switch($action){
			case 'create':
				return $this->update();
			break;
			case 'update':
				$player_id = $this->input->get('player_id');
				if($player_id>0){
					return $this->update($player_id);
				} else {
					redirect('admin/player');
				}
			break;
			case 'delete':
				$player_id = $this->input->get('player_id');
				if($player_id>0){
					$this->player_model->delete(array('id'=>$player_id));
				}
				redirect('admin/player');
			break;
			default:
				$this->datatables('player',$this->player_model,site_url('admin/player/json_data'));
				$this->twig->render("player/list",$this->view_data);
			break;
		}		
	}

	private function update($player_id = 0){
		$player  = new stdClass();
		$this->player_id = $player_id;
		$player->fullname = $this->input->post('fullname');
		$player->biography = $this->input->post('biography');
		$player->character = $this->input->post('character');
		$player->position_id = $this->input->post('position_id');
		$player->team_id = $this->input->post('team_id');
		$player->image = $this->input->post('image');
		$player->tag = $this->input->post('tag');
		$video_player_ids = $this->input->post('video_player_ids');
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
					'field'   => 'fullname',
					'label'   => 'Tiêu đề',
					'rules'   => 'trim|required'
				),
				array(
					'field'   => 'character',
					'label'   => 'Tiêu đề',
					'rules'   => 'trim|required'
				),
				array(
					'field'   => 'image',
					'label'   => 'Thư điện tử',
					'rules'   => 'trim|callback_image_required'
				),
			);
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run()==TRUE){
				$alias_name = alias_name($player->fullname);
				$player->tag = hashtag_parser($player->tag);
				/* Upload video image file */
				$player->image = $this->adminlib->upload_file($this->image_upload_file,"player_image");
				if($player_id>0){
					$old_player_data = $this->player_model->find(array(
						'where'=>array('id' => $player_id),
						'one'	=> TRUE
					));

					if(!$old_player_data){
						redirect('admin/player');
						$this->view_data['message']="player không tồn tại";
					}

					if(strpos($old_player_data->code, $alias_name)===FALSE){
						$player->code = $alias_name.".{$this->player_model->code_id($alias_name)}";
					}

					if(empty($player->image)){
						unset($player->image);
					} elseif($old_player_data->image!==$player->image){
						$this->adminlib->remove_file($old_player_data->image, 'player_image');
					}
					$player->created = $player->updated = date('Y-m-d H:i:s');
					$this->player_model->eupdate($player, $player_id);
				} else {
					$player->code = $alias_name.".{$this->player_model->code_id($alias_name)}";
					$player->created = $player->updated = date('Y-m-d H:i:s');
					if(!($player_id = $this->player_model->create($player))>0){
						$this->session->flashdata('message',"Hệ thống đang bận, vui lòng thử lại sau.");
						//Xóa file vừa up
						if(!empty($player->image)){
							$this->adminlib->remove_file($player->image, 'player_image');
						}	
					}
				}
				if($player_id>0){
					$video_model=NULL;
					$video_player_data = array();
					if(!empty($video_player_ids)){
						$video_model=$this->video_model->find(array(
							'select' => 'id',
							'where_in' => array('id'=>$video_player_ids)
						));
					}
					if($video_model && !empty($video_model)){
						foreach($video_model as $video){
							$video_player_data[] = array(
								'video_id' 	=> $video->id,
								'ref_id' => $player_id,
								'type'		=> 'player'
							);
						}
					}				
					$this->refer_video_model->delete(array(
						'ref_id'	=> $player_id,
						'type'		=> 'player'
					));
					if(!empty($video_player_data)){
						$this->refer_video_model->create_multiple($video_player_data);
					}
				}
				redirect(site_url('admin/player/index/update')."?player_id=".$player_id);
			}
		} else {
			if($player_id>0){
				$player = $this->player_model->find(array(
					'where'=>array('id' => $player_id),
					'one'	=> TRUE
				));
				if(!$player){
					redirect('admin/player');
					$this->view_data['message']="Khu vực không tồn tại";
				}
			}
		}
		$this->view_data['player'] = $player;
		$this->view_data['team'] = $this->team_model->find();
		$this->view_data['positions'] = $this->taxonomy_model->find(array(
			'where' => array('type' => 'position')
		));
		$this->twig->render("player/edit",$this->view_data);
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

			if($this->player_id>0 && $_FILES['image']['error']>0){
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
		$this->get_json_data('player',$this->player_model);
	}

	public function video_html_ajax(){
		$video_ids = $this->input->get('video_ids');
		$player_id = $this->input->get('player_id');
		$htmlElm = "";
		$this->load->model('video_model');
		if($player_id && $player_id>0){
			$this->view_data['videos'] = $this->video_model->find(array(
				'select'=> "{$this->video_model->table_name}.*",
				'join'	=> array('refer_video pv'=>"pv.video_id={$this->video_model->table_name}.id"),
				'where' => array(
					'pv.ref_id' => $player_id,
					'type'		=> 'player'
				)
			));
			return $this->twig->render('player/video_html_ajax', $this->view_data);
		} elseif(!empty($video_ids) and ($video_ids = @json_decode($video_ids,TRUE))){			
			$this->view_data['videos'] = $this->video_model->find(array(
				'where_in' => array('id' => $video_ids)
			));
			return $this->twig->render('player/video_html_ajax', $this->view_data);
		}
		return $htmlElm;
	}

	public function list_player_ajax(){
		$video_id = $this->input->get('video_id');
		$htmlElm = "";
		$players = $this->player_model->find();
		$player_video = array();
		$this->twig->addFunction(array('in_array'));
		if($video_id>0){
			$this->load->model('refer_video_model');
			$refer_video_model = $this->refer_video_model->find(array(
				'select' => 'ref_id',
				'where'	 => array(
					'video_id' => $video_id,
					'type'		=> 'player'
				)
			));
			if($refer_video_model){
				foreach($refer_video_model as $pv){
					$player_video[] = $pv->ref_id;
				}
			}
		}
		/*echo '<pre>';
		print_r($players);die;*/
		$this->view_data['players'] = $players;
		$this->view_data['player_video'] = $player_video;
		return $this->twig->render('player/content_ajax', $this->view_data);
	}
}