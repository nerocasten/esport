<?php

class Crypt {

	public function salt($number=5) {
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz@#$&=';
		$result = '';
		for ($i = 0; $i < $number; $i++){
			$result .= $characters[mt_rand(0, strlen($characters)-1)];
		}
		return $result;
	}

	public function encode($string, $salt, $crypt_salt=NULL){
		if($crypt_salt){
			return crypt(hash('sha256',$string,$salt),$crypt_salt);
		}
		return crypt(hash('sha256',$string,$salt));
	}

	public function equals($crypt,$string,$salt){
		return hash_equals($crypt, $this->encode($string,$salt,$crypt));
	}
}