<?php

class Leagueoflegend extends FRONTEND_Controller{
	
	public function __construct(){
		parent::__construct();
		$this->view_data = array_merge($this->view_data,array(
			
		));
	}

	public function _($type = NULL, $params = NULL){
		$frontend = $this->view_data['frontend'];
		$controll = 'league-of-legends/';
		switch($type){
			case 'homepage':
				$frontend .= $controll.'homepage/';
				$frontend = base_url().$frontend;
				if($params){
					$frontend .= $params;
				}
			break;
		}
		return $frontend;
	}

	public function index(){
		$this->load->model('homepage_model');
		$video_category = $this->homepage_model->get_home_video();
		$this->view_data['video_category'] = $video_category;
		$this->twig->render('home',$this->view_data);
	}
}