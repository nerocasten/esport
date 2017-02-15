<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model{
	public $table_name;
		
	private $user_info = FALSE;
	
	public function __construct(){
		parent::__construct();
	}

	/**
	*	Hàm find chỉ có 1 tham số duy nhất, tham số sẽ chứa các statements cần sử dụng
	* Ex: array( 
	*		'select' => 'news.id, news.title, category.title as cat_title',
	*		'where'	 => array('key1'=>'value1', 'key2'=>'value2'),
	*		'where_in' => array('news.id'=>array(22,23,24)),
	*		'or_where'	=> array('news.id' => 25),
	*		//'limit'	=> 5,
	*		//'offset' => 2,
	*		'join'	=> array('category' => 'news.category_id=category.id'),
	*		//'count'	=> TRUE,
	*		'order_by' => array('news.id' => 'desc')
	*	);
	**/

	public function find($params = array()){
		$_sql_params = array(
			'where' 		=> NULL,
			'where_in'		=> NULL,
			'or_where'		=> NULL,
			'or_where_in'	=> NULL,
			'where_not_in'	=> NULL,
			'like'			=> NULL,
			'or_like'		=> NULL,
			'not_like'		=> NULL,
			'select'		=> '*',
			'order_by'		=> NULL,
			'limit'			=> NULL,
			'offset'		=> NULL,
			'join'			=> NULL,
			'join_ext'		=> NULL,
			'group_by'		=> NULL,
			'having'		=> NULL,
			'count'			=> FALSE,
			'one'			=> FALSE,
			'all'			=> TRUE,
			'to_array'		=> FALSE,
			'compiled'		=> FALSE,
			'from'			=> "",
			'group'			=> NULL,
		);
		
		$_sql_params = array_merge($_sql_params,$params);
		
		foreach($_sql_params as $key=>$_item){
			$$key = $_item;
		}

		//SELECT
		if(isset($select)){ 
			$this->db->select($select); 
		}

		//FROM
		if(empty($from)){
			$from = $this->table_name;
		}
		$this->db->from($from);

		//JOIN
		if(isset($join) && is_array($join)){
			foreach ($join as $key => $value) {
				$this->db->join($key, $value);
			}
		}	

		//JOIN EXTENTION
		if(isset($join_ext) && is_array($join_ext)){
			foreach ($join_ext as $key => $value) {
				$this->db->join($key, $value[0],$value[1]);
			}
		}	

		//WHERE (STRING/ARRAY)
		if(isset($where)){
			if(is_string($where)){
				$this->db->where($where);
			} else {
				foreach ($where as $key => $value) {
					if(is_numeric($key)){
						$this->db->where($value);
					} else {
						$this->db->where($key, $value);
					}					
				}
			}			
		}

		//WHERE IN
		if(isset($where_in)){
			foreach ($where_in as $key => $value) {
				$this->db->where_in($key, $value);
			}			
		}

		//OR WHERE
		if(isset($or_where)){
			foreach ($or_where as $key => $value) {
				$this->db->or_where($key, $value);
			}			
		}

		//OR WHERE IN
		if(isset($or_where_in)){
			foreach ($or_where_in as $key => $value) {
				$this->db->or_where_in($key, $value);
			}			
		}

		//where_not_in
		if(isset($where_not_in)){
			foreach ($where_not_in as $key => $value) {
				$this->db->where_not_in($key, $value);
			}			
		}

		//LIKE
		if(isset($like)){
			foreach ($like as $key => $value) {
				if(is_numeric($key)){
					$this->db->like($value);
				} else {
					$this->db->like($key, $value);
				}				
			}			
		}

		//OR_LIKE
		if(isset($or_like)){
			foreach ($or_like as $key => $value) {
				$this->db->or_like($key, $value);
			}			
		}

		//NOT_LIKE
		if(isset($not_like)){
			foreach ($not_like as $key => $value) {
				$this->db->not_like($key, $value);
			}			
		}

		//GROUP
		if(isset($group)){
			$this->group_query($group);
		}

		//OR GROUP
		if(isset($or_group)){
			$this->group_query($or_group,'OR');
		}

		//GROUP BY
		if(isset($group_by)){
			$this->db->group_by($group_by);
		}

		//HAVING (STRING/ARRAY)
		if(isset($having)){
			if(is_string($having)){
				$this->db->having($having);
			} else {
				foreach ($having as $key => $value) {
					$this->db->having($key, $value);
				}
			}			
		}

		//ORDER BY (STRING/ARRAY)
		if(isset($order_by)){
			if(is_string($order_by)){
				$this->db->order_by($order_by);
			} else {
				foreach($order_by as $key=>$value){
					$this->db->order_by($key, $value);
				}
			}
		}

		//LIMIT/OFFSET
		if(isset($limit)){
			if(isset($offset)){
				$this->db->limit($limit, $offset);
			} else {
				$this->db->limit($limit);
			}
		}

		//RESULT
		if($count == TRUE){
			$result = $this->db->count_all_results();
		} else {			
			if($one == TRUE){
				$this->db->limit(1);
				if( $compiled == TRUE) {
					$result = $this->db->get_compiled_select();
				} else {
					$query = $this->db->get();
					$result = $query->row();
				}				
			} elseif($all == TRUE) {
				if( $compiled == TRUE) {
					$result = $this->db->get_compiled_select();
				} else {
					$query = $this->db->get();
					if($to_array == TRUE){
						$result = $query->result_array();
					} else {
						$result = $query->result();
					}
				}								
			} else {
				if( $compiled == TRUE) {
					$result = $this->db->get_compiled_select();
				} else {
					$query = $this->db->get();
					$result = $query;
				}				
			}
		}
		
		return $result;
	}

	private function group_query($params,$type = 'AND'){
		$_sql_params = array(
			'where' 		=> NULL,
			'where_in'		=> NULL,
			'or_where'		=> NULL,
			'or_where_in'	=> NULL,
			'where_not_in'	=> NULL,
			'like'			=> NULL,
			'or_like'		=> NULL,
			'not_like'		=> NULL,
			'group'			=> NULL,
			'or_group'			=> NULL,
		);
		
		$_sql_params = array_merge($_sql_params,$params);
		
		foreach($_sql_params as $key=>$_item){
			$$key = $_item;
		}
		if(strtoupper($type)=='OR'){
			$this->db->or_group_start();
		} else {
			$this->db->group_start();
		}
		
		//WHERE (STRING/ARRAY)
		if(isset($where)){
			if(is_string($where)){
				$this->db->where($where);
			} else {
				foreach ($where as $key => $value) {
					if(is_numeric($key)){
						$this->db->where($value);
					} else {
						$this->db->where($key, $value);
					}					
				}
			}			
		}

		//WHERE IN
		if(isset($where_in)){
			foreach ($where_in as $key => $value) {
				$this->db->where_in($key, $value);
			}			
		}

		//OR WHERE
		if(isset($or_where)){
			foreach ($or_where as $key => $value) {
				$this->db->or_where($key, $value);
			}			
		}

		//OR WHERE IN
		if(isset($or_where_in)){
			foreach ($or_where_in as $key => $value) {
				$this->db->or_where_in($key, $value);
			}			
		}

		//where_not_in
		if(isset($where_not_in)){
			foreach ($where_not_in as $key => $value) {
				$this->db->where_not_in($key, $value);
			}			
		}

		//LIKE
		if(isset($like)){
			foreach ($like as $key => $value) {
				if(is_numeric($key)){
					$this->db->like($value);
				} else {
					$this->db->like($key, $value);
				}				
			}			
		}

		//OR_LIKE
		if(isset($or_like)){
			foreach ($or_like as $key => $value) {
				$this->db->or_like($key, $value);
			}			
		}

		//NOT_LIKE
		if(isset($not_like)){
			foreach ($not_like as $key => $value) {
				$this->db->not_like($key, $value);
			}			
		}

		//GROUP
		if(isset($group)){
			$this->group_query($group);
		}

		//OR GROUP
		if(isset($or_group)){
			$this->group_query($or_group,'OR');
		}

		$this->db->group_end();
	}

	/**
	 * Abstract record creation.
	 *
	 * @param array $data
	 * @return type
	 */
	public function create($data)
	{
		$this->db->insert($this->table_name, $data);
		return $this->db->insert_id();
	}

	/**
	 * Abstract record creation.
	 *
	 * @param array $data
	 * @return type
	 */
	public function create_multiple($data)
	{
		$this->db->insert_batch($this->table_name, $data);
	}

	/**
	 * Abstract recort update.
	 *
	 * @param array $data
	 * @param type $id
	 */
	public function eupdate($data, $id)
	{
		$this->db->update($this->table_name, $data, array('id' => $id));
	}

	public function update($data, $params)
	{
		$this->db->update($this->table_name, $data, $params);
	}

	/**
	 * Abstract recort update.
	 *
	 * @param array $data
	 * @param type $id
	 */
	public function update_where($data, $where = array())
	{
		$this->db->update($this->table_name, $data, $where);
	}

	/**
	 * Abstract record deletion.
	 *
	 * @param type $id
	 */
	public function delete($data)
	{
		$this->db->delete($this->table_name, $data);
		//     echo $this->db->last_query();
		//     exit();
	}

	/**
	 * Utiltiy method to create a UUID.
	 *
	 * @return type
	 */
	protected function create_uuid()
	{
		$uuid_query = $this->db->query('SELECT UUID()');
		$uuid_rs = $uuid_query->result_array();
		return $uuid_rs[0]['UUID()'];
	}

	/**
	 * check code exists and rebuild code
	 * return string
	 */
	public function code_id($beforecode){
		$code_id = base64_encode($beforecode.time().rand(1,1000));
		$code_id = substr($code_id,strlen($code_id)-7,strlen($code_id)-2);
		$obj_query = $this->find(array(
			'select' 	=> 'id',
			'where' 		=> array(
				'code' 	=> $beforecode.".{$code_id}"
			),
			'one'	=> TRUE
		));
		if($obj_query){
			$this->code_id($beforecode);
		}
		return $code_id;
	}

	public function last_id() {
		return $this->db->query("SELECT LAST_INSERT_ID() AS ID")->row();
	}	
	
	/**
	 * Get user session data.
	 *
	 * @return type
	 */
	public function get_user_data(){
		return $this->session->all_userdata();
	}

	/**
	 * Get logged in user id.
	 *
	 * @return type
	 */
	public function get_user_id(){
		$session_data = $this->session->all_userdata();
		return $session_data['user_id'];
	}	

	protected function object_to_array($stdclass){
		$array = array();
		if(is_array($stdclass)){
			return $stdclass;
		}
		if(is_object($stdclass)){
			foreach($stdclass as $k=>$item){
				$array[$k] = $item;
			}
		}
		return $array;
	}

	public function selectDb($db){
		$this->db = $this->load->database($db, TRUE);
	}
}