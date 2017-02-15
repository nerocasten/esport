<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *  Xgo_datatables Library
 * @package XGO CMS v3.0
 * @subpackage xgo_datatables
 * @link http://sunsoft.vn
 */

require_once(APPPATH . 'libraries/Datatables.php');

class Xgo_datatables extends Datatables{
	
	protected $json_encode    = true;
	protected $produce_output = true;
	
	public function __construct(){
		parent::__construct();
	}

	/**
	 * Generates the LIKE portion of the query
	 *
	 * @return mixed
	 */
	protected function get_filtering()
	{
		if($this->check_mDataprop())
			$mColArray = $this->get_mDataprop();
		elseif($this->ci->input->post('sColumns'))
		$mColArray = explode(',', $this->ci->input->post('sColumns'));
		else
			$mColArray = $this->columns;

		$sWhere = '';
		$sSearch = $this->ci->input->post('sSearch');
		$mColArray = array_values(array_diff($mColArray, $this->unset_columns));
		$columns = array_values(array_diff($this->columns, $this->unset_columns));

		if($sSearch != '')
			for($i = 0; $i < count($mColArray); $i++)
			if($this->ci->input->post('bSearchable_' . $i) == 'true' && in_array($mColArray[$i], $columns))
			$sWhere .= $this->select[$mColArray[$i]] . " LIKE '%" . $sSearch . "%' OR ";

		$sWhere = substr_replace($sWhere, '', -3);

		if($sWhere != '')
			$this->ci->db->where('(' . $sWhere . ')');

		for($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++)
		{
			if(isset($_POST['sSearch_' . $i]) && $this->ci->input->post('sSearch_' . $i) != '' && in_array($mColArray[$i], $columns))
			{
				$miSearch = $this->ci->input->post('sSearch_' . $i);
				if(preg_match("/(in=)(\s*)(.+)/i", $miSearch, $matches)){
					$this->ci->db->where_in($this->select[$mColArray[$i]], array_values(json_decode($matches[3], true)));
				}else{
					$miSearch = explode(',', $this->ci->input->post('sSearch_' . $i));
					foreach($miSearch as $val)
					{
						if(preg_match("/(<=|>=|=|<|>)(\s*)(.+)/i", trim($val), $matches)){
							$this->ci->db->where($this->select[$mColArray[$i]].' '.$matches[1], $matches[3]);
						}else{
							$this->ci->db->where($this->select[$mColArray[$i]].' LIKE', '%'.$val.'%');
						}
					}
				}
			}
		}

		foreach($this->filter as $val)
			$this->ci->db->where($val[0], $val[1], $val[2]);
	}

	/**
	 * Builds a JSON encoded string data
	 *
	 * @param string charset
	 * @return string
	 */
	protected function produce_output($charset)
	{
		$aaData = array();
		$rResult = $this->get_display_result();
		$iTotal = $this->get_total_results();
		$iFilteredTotal = $this->get_total_results(TRUE);

		$sColumns = array_diff($this->columns, $this->unset_columns);
		$sColumns = array_merge_recursive($sColumns, array_keys($this->add_columns));

		if($this->produce_output==FALSE){
			$sOutput = array
			(
					'sEcho'                => intval($this->ci->input->post('sEcho')),
					'iTotalRecords'        => $iTotal,
					'iTotalDisplayRecords' => $iFilteredTotal,
					'aaData'               => $rResult->result_array(),
					'sColumns'             => implode(',', $sColumns)
			);

			return $sOutput;
		}

		foreach($rResult->result_array() as $row_key => $row_val)
		{
			$aaData[$row_key] = ($this->check_mDataprop())? $row_val : array_values($row_val);

			foreach($this->add_columns as $field => $val)
				if($this->check_mDataprop())
				$aaData[$row_key][$field] = $this->exec_replace($val, $aaData[$row_key]);
			else
				$aaData[$row_key][] = $this->exec_replace($val, $aaData[$row_key]);

			foreach($this->edit_columns as $modkey => $modval)
				foreach($modval as $val)
				$aaData[$row_key][($this->check_mDataprop())? $modkey : array_search($modkey, $this->columns)] = $this->exec_replace($val, $aaData[$row_key]);

			$aaData[$row_key] = array_diff_key($aaData[$row_key], ($this->check_mDataprop())? $this->unset_columns : array_intersect($this->columns, $this->unset_columns));

			if(!$this->check_mDataprop())
				$aaData[$row_key] = array_values($aaData[$row_key]);
		}

		$sOutput = array
		(
				'sEcho'                => intval($this->ci->input->post('sEcho')),
				'iTotalRecords'        => $iTotal,
				'iTotalDisplayRecords' => $iFilteredTotal,
				'aaData'               => $aaData,
				'sColumns'             => implode(',', $sColumns)
		);

		if($this->json_encode){
			if(strtolower($charset) == 'utf-8')
				return json_encode($sOutput);
			else
				return $this->jsonify($sOutput);
		}else{
			return $sOutput;
		}

	}

	public function set_json_encode($json_encode=true){
		$this->json_encode = $json_encode;
	}

	public function set_produce_output($produce_output=true){
		$this->produce_output = $produce_output;
	}

	public function values_encode($array){
		$dlist = array();
		foreach($array as $k=>$item){
			if($item!=NULL){
				$dlist[] = array(
						'value' => '='.$k,
						'label' => $item,
				);
			}
		}
		return json_encode($dlist);
	}
	
	/**
	 * Generates the ORDER BY portion of the query
	 *
	 * @return mixed
	 */
	protected function get_ordering()
	{
		if($this->check_mDataprop())
			$mColArray = $this->get_mDataprop();
		elseif($this->ci->input->post('sColumns'))
		$mColArray = explode(',', $this->ci->input->post('sColumns'));
		else
			$mColArray = $this->columns;
	
		$mColArray = array_values(array_diff($mColArray, $this->unset_columns));
		$columns = array_values(array_diff($this->columns, $this->unset_columns));
	
		for($i = 0; $i < intval($this->ci->input->post('iSortingCols')); $i++)
			if(isset($mColArray[intval($this->ci->input->post('iSortCol_' . $i))]) 
			&& in_array($mColArray[intval($this->ci->input->post('iSortCol_' . $i))], $columns)
			)
				$this->ci->db->order_by($mColArray[intval($this->ci->input->post('iSortCol_' . $i))], $this->ci->input->post('sSortDir_' . $i));
	}
}
/* End of file Datatables.php */
/* Location: ./application/libraries/Datatables.php */