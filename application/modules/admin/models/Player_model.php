<?php

class Player_model extends MY_Model{

	public $table_name = "player";

	public function __construct(){
		parent::__construct();
	}

	public function init_data(){
		return array(
			array(
				'name' => 'fullname',
				'label' => "Tên đội",
				'width' => '30%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' => 'biography',
				'label' => "Tiểu sử",
				'width' => '60%',
				'sort'  => false,
				'searchoptions' => array(
					'type' 	=> 'text',
				)
			),
			array(
				'name' 	=> 'id',
				'label' => '<a class="btn btn-primary" href="'.site_url('admin/player/index/create').'">New</a>',
				'width' => '10%',
				'sort'  => false
			),
		);
	}

	public function json_data($controller){
		$this->datatables
		->select('fullname,biography,id')
		->from($this->table_name);
		$this->datatables->set_produce_output(false);
		$ouput = $datatables = $this->datatables->generate();
		unset($ouput['aaData']);
		$ouput['aaData'] = array();
		foreach($datatables['aaData'] as $item){
			$ouput['aaData'][] = array(
				"<a href='".site_url("admin/player/index/update")."?player_id={$item['id']}"."'>{$item['fullname']}</a>",
				$item['biography'],
				"<a href='".site_url("admin/player/index/delete")."?player_id={$item['id']}"."'>Xóa</a>",
			);
		}
		return json_encode($ouput);
	}
}