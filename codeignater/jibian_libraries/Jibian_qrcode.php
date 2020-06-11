<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once FCPATH."application/third_party/phpqrcode/qrlib.php";

class Jibian_qrcode {
	
	public function get_qrcode($content) {
		QRcode::png($content, FALSE, "H", 7, 3, FALSE);
	}
}