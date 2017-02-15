<?php
/**
 * Part of CodeIgniter3 Filename Checker
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/codeigniter3-filename-checker
 */

class Test extends MY_Controller
{
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		die('test');
	}

	public function acc(){
		$this->load->library('character');
		$acc = $this->character->info($this->input->get('username'),$this->input->get('server'));
		echo '<pre>';
		print_r($acc);
	}

	public function history(){
		$this->load->library('history_game');
		$this->history_game->set_history_server('hoanhoi4','S107','S108 - Lý Quyết');
	}

	public function send_item(){
		$this->load->library('ingame');
		$player = array(
			'server' => '145',
			'rolename' => 'S145.robjn'
		);
		$item = array(
			array(
				'type' => 5,
				'index' => '10098',
				'count' => 1
			)
		);
		$this->ingame->sendItemIngame($player,$item);
	}

	public function get_q(){
		error_reporting(-1);
		ini_set('display_errors', 1);
		$external_ip = exec('curl http://ipecho.net/plain; echo');

		echo 'My-IP: '.$external_ip.'</br>';
		$link = 'http://ab.gosu.vn/common/checkgamepasswordstatus?Username=llllll111&wsUserName=cuuam.web.ab.gosu.vn&Signature=03063569aa8ca13c8031e900116d7e63';
		echo $link.'</br>';
		// $result= file_get_contents('http://ab.gosu.vn/common/checkgamepasswordstatus?Username=llllll111&wsUserName=cuuam.web.ab.gosu.vn&Signature=03063569aa8ca13c8031e900116d7e63');
		$result= $this->do_get_request($link);

		var_dump($result);
	}


	public function do_get_request($url, $data=array()) {
	    $ch = curl_init(); // Init cURL

	    curl_setopt($ch, CURLOPT_URL, $url); // Post location
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 1 = Return data, 0 = No return
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ignore HTTPS
	    curl_setopt($ch, CURLOPT_POST, 0); // This is POST
	    if($data && is_array($data)){
	        $query = http_build_query($data);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $query); // Add the data to the request
	    }            

	    $response = curl_exec($ch); // Execute the request
	    curl_close($ch); // Finish the request

	    if ($response) {
	        return $response;
	    } else {
	        return NULL;
	    }
	}

}