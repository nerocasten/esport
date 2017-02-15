<?php

class League_model extends MY_Model{

	public $table_name = "league";

	public function __construct(){
		parent::__construct();
	}

	public function init_data(){
		return array(
			array(
				'name' => 'name',
				'label' => "Tên đội",
				'width' => '30%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' => 'introduction',
				'label' => "Giới thiệu (lịch sử)",
				'width' => '60%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' 	=> 'id',
				'label' => '<a class="btn btn-primary" href="'.site_url('admin/league/index/create').'">New</a>',
				'width' => '10%',
				'sort'  => false
			),
		);
	}

	public function json_data($controller){
		$this->datatables
		->select('name,introduction,id')
		->from($this->table_name);
		$this->datatables->set_produce_output(false);
		$ouput = $datatables = $this->datatables->generate();
		unset($ouput['aaData']);
		$ouput['aaData'] = array();
		foreach($datatables['aaData'] as $item){
			$ouput['aaData'][] = array(
				"<a href='".site_url("admin/league/index/update")."?league_id={$item['id']}"."'>{$item['name']}</a>",
				$item['introduction'],
				"<a href='".site_url("admin/league/index/delete")."?league_id={$item['id']}"."'>Xóa</a>",
			);
		}
		return json_encode($ouput);
	}
}