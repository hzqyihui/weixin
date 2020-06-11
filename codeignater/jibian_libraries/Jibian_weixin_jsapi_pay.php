<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
require_once FCPATH."application/third_party/wxpaylib/WxPay.Api.php";

class Jibian_weixin_jsapi_pay {
	private $CI;

	public function __construct() {
		$this -> CI = &get_instance();
	}

	/**
	 *
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 *
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function GetJsApiParameters($UnifiedOrderResult) {
		if (!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == "") {
			throw new WxPayException("参数错误");
		}
		$jsapi = new WxPayJsApiPay();
		$jsapi -> SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();
		$jsapi -> SetTimeStamp("$timeStamp");
		$jsapi -> SetNonceStr(WxPayApi::getNonceStr());
		$jsapi -> SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi -> SetSignType("MD5");
		$jsapi -> SetPaySign($jsapi -> MakeSign());
		$parameters = json_encode($jsapi -> GetValues());
		return $parameters;
	}

}
