<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once FCPATH."application/third_party/phpqrcode/qrlib.php";

class Jys_qrcode {
	
	public function get_qrcode($content) {
		QRcode::png($content, FALSE, "H", 7, 3, FALSE);
	}

	/**
	 * 直接根据微信code_url生成二维码
	 * @param $code_url
	 * @return bool || 图片
	 */
	public function create_qrcode($code_url){
		if(empty($code_url) || is_null($code_url)){
			return FALSE;
		}
		$this->get_qrcode($code_url);
	}
}