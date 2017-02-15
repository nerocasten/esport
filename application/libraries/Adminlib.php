<?php
/**
 * Part of CodeIgniter Simple and Secure Twig
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/codeigniter-ss-twig
 */

class Adminlib {

	private $user_avatar="%sassets/static/uploads/%s";
	private $video_image="%sassets/static/uploads/%s";
	private $player_image="%sassets/static/uploads/%s";
	private $team_image="%sassets/static/uploads/%s";
	private $no_image="%sassets/static/templates/admin/common/img/no-image.png";

	public function user_avatar($avatar){
		$path=$this->user_avatar;
		$file = sprintf($this->no_image,base_url());
		if(!empty($avatar) && file_exists(sprintf($path,FCPATH,$avatar))){
			$file = sprintf($path,base_url(),$avatar);
		}
		return $file;
	}

	public function video_image($image){
		$path=$this->video_image;
		$file = sprintf($this->no_image,base_url());
		if(!empty($image) && file_exists(sprintf($path,FCPATH,$image))){
			$file = sprintf($path,base_url(),$image);
		}
		return $file;
	}

	public function player_image($image){
		$path=$this->player_image;
		$file = sprintf($this->no_image,base_url());
		if(!empty($image) && file_exists(sprintf($path,FCPATH,$image))){
			$file = sprintf($path,base_url(),$image);
		}
		return $file;
	}

	public function team_image($image){
		$path=$this->team_image;
		$file = sprintf($this->no_image,base_url());
		if(!empty($image) && file_exists(sprintf($path,FCPATH,$image))){
			$file = sprintf($path,base_url(),$image);
		}
		return $file;
	}

	public function remove_file($filename,$type){
		if(isset($this->{$type}) && !empty($filename)){
			$file = sprintf($this->{$type},FCPATH,$filename);
			if(is_file($file) && file_exists($file)){
				unlink($file);
			}			
		}
	}

	public function upload_file($file_data,$type){
		$filename = "";
		if(isset($this->{$type}) && $file_data && !empty($file_data)){
			$filename_binary=sha1_file($file_data['tmp_name'])."_".time();
			$ext=$file_data['extention'];

			move_uploaded_file(
				$file_data['tmp_name'],
				sprintf(
					$this->{$type},
					FCPATH,
					$filename = "{$filename_binary}.{$ext}"
			));
			if(!file_exists(sprintf(
					$this->{$type},
					FCPATH,
					$filename = "{$filename_binary}.{$ext}"
			))){
				$filename = "";
			}
		}
		return $filename;
	}
}