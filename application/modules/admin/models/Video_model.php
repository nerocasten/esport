<?php

class Video_model extends MY_Model{

	public $table_name = "video";

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function init_data(){
		return array(
			array(
				'name' => 'title',
				'label' => "Tiêu đề",
				'width' => '25%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' 	=> 'introduction',
				'label' => 'Giới thiệu',
				'width' => '45%',
				'sort'  => false
			),
			array(
				'name' 	=> 'video_url',
				'label' => 'Đường dẫn',
				'width' => '20%',
				'sort'  => false
			),
			array(
				'name' 	=> 'id',
				'label' => '<a class="btn btn-primary" href="'.site_url('admin/video/index/create').'">New</a>',
				'width' => '10%',
				'sort'  => false
			),
		);
	}

	public function json_data($controller){
		$this->datatables
		->select('title,introduction,video_url,id')
		->from($this->table_name);
		$this->datatables->set_produce_output(false);
		$ouput = $datatables = $this->datatables->generate();
		unset($ouput['aaData']);
		$ouput['aaData'] = array();
		foreach($datatables['aaData'] as $item){
			$ouput['aaData'][] = array(
				"<a href='".site_url("admin/video/index/update")."?video_id={$item['id']}"."'>{$item['title']}</a>",
				$item['introduction'],
				$item['video_url'],
				"<a href='".site_url("admin/video/index/delete")."?video_id={$item['id']}"."'>Xóa</a>",
			);
		}
		return json_encode($ouput);
	}
}