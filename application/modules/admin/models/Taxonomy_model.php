<?php

class Taxonomy_model extends MY_Model{

	public $table_name = "taxonomy";

	public function __construct(){
		parent::__construct();
	}

	public function taxo_with_type($type){
		return $this->find(array(
			'where' => array('type' => $type)
		));
	}

	public function init_data($taxo_url_button = NULL){
		$taxo_button = "";
		if($taxo_url_button && !empty($taxo_url_button)){
			$taxo_button = $taxo_url_button;
		}
		return array(
			array(
				'name' => 'title',
				'label' => "Tiêu đề",
				'width' => '90%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' 	=> 'id',
				'label' => '<a class="btn btn-primary" href="'.$taxo_button.'">New</a>',
				'width' => '10%',
				'sort'  => false
			),
		);
	}

	public function json_data($controller){
		$this->datatables
		->select('title,id')
		->from($this->table_name);
		$this->datatables->set_produce_output(false);
		$ouput = $datatables = $this->datatables->generate();
		unset($ouput['aaData']);
		$ouput['aaData'] = array();
		foreach($datatables['aaData'] as $item){
			$ouput['aaData'][] = array(
				"<a href='".site_url("admin/video/label/update")."?label_id={$item['id']}"."'>{$item['title']}</a>",
				"<a href='".site_url("admin/video/label/delete")."?label_id={$item['id']}"."'>Xóa</a>",
			);
		}
		return json_encode($ouput);
	}
}