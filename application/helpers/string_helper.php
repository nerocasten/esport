<?php

if (!function_exists('alias_name') ){
	function alias_name($str){
		$array_search = array("á","à","ả","ã","ạ","ắ","ằ","ẳ","ẵ","ặ","ấ","ầ","ẩ","ẫ","ậ","é","è","ẻ","ẽ","ẹ","ế","ề","ể","ễ","ệ","í","ì","ỉ","ĩ","ị","ó","ò","ỏ","õ","ọ","ố","ồ","ổ","ỗ","ộ","ớ","ờ","ở","ỡ","ợ","ú","ù","ủ","ũ","ụ","ứ","ừ","ử","ữ","ự","ý","ỳ","ỷ","ỹ","ỵ","ă","â","đ","ê","ô","ơ","ư","Á","À","Ả","Ã","Ạ","Ắ","Ằ","Ẳ","Ẵ","Ặ","Ấ","Ầ","Ẩ","Ẫ","Ậ","É","È","Ẻ","Ẽ","Ẹ","Ế","Ề","Ể","Ễ","Ệ","Í","Ì","Ỉ","Ĩ","Ị","Ó","Ò","Ỏ","Õ","Ọ","Ố","Ồ","Ổ","Ỗ","Ộ","Ớ","Ờ","Ở","Ỡ","Ợ","Ú","Ù","Ủ","Ũ","Ụ","Ứ","Ừ","Ử","Ữ","Ự","Ý","Ỳ","Ỷ","Ỹ","Ỵ","Ă","Â","Đ","Ê","Ô","Ơ","Ư"," ", "--", ":", "%20");
		$array_replace = array("a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","e","e","e","e","e","e","e","e","e","e","i","i","i","i","i","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","u","u","u","u","u","u","u","u","u","u","y","y","y","y","y","a","a","d","e","o","o","u","a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","e","e","e","e","e","e","e","e","e","e","i","i","i","i","i","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","u","u","u","u","u","u","u","u","u","u","y","y","y","y","y","a","a","d","e","o","o","u","-", "-", "-", "-");
		$str = strtolower(str_replace($array_search,$array_replace,$str));
		$str = preg_replace('/[^a-z 0-9~%.:_\-]+/', '', $str);
		return str_replace('--','-',$str);
	}
}

if (!function_exists('hashtag_parser') ){
	function hashtag_parser($str_tag){
		if(!empty($str_tag)){
			$tag_arr = explode("#", $str_tag);
			$tag_tmp = array();
			for($i=0;$i<count($tag_arr);$i++){
				$tag = trim($tag_arr[$i]);
				if(!empty($tag)){
					$tag_space_arr = explode(" ", $tag);
					foreach($tag_space_arr as $vl){
						$tag_tmp[] = "#".$vl;
					}					
				}
			}
			$str_tag = implode(" ",$tag_tmp);
		}
		return $str_tag;
	}
}