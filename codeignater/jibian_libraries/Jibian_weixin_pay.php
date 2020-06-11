<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Jibian_weixin_pay {
	private $CI;
	private $self_path;

	public function __construct() {
		$this -> CI = &get_instance();
		$this->self_path = dirname(__FILE__);
	}

	/**
	 * 组装订单信息
	 * @param $body 商品描述
	 * @param $detail 商品详情
	 * @param $out_trade_no 商户订单号
	 * @param $total_fee 总金额，单位：元
	 * @param $fee_type 货币类型，默认CNY人民币
	 * @param $trade_type 交易类型，默认JSAPI
	 */
	public function getOrderInfo($openid, $body, $detail = array(), $out_trade_no, $total_fee, $fee_type = "CNY", $trade_type = "JSAPI") {
		if (empty($openid) || empty($body) || empty($out_trade_no) || floatval($total_fee) <= 0) {
			return FALSE;
		}

		$info = array('appid' => $this -> CI -> config -> item('wx_appid'), 'mch_id' => $this -> CI -> config -> item('wx_partnerid'), 'device_info' => 'WEB', 'nonce_str' => $this -> getNonceStr(), 'body' => $body, 'out_trade_no' => $out_trade_no, 'total_fee' => floatval($total_fee) * 100, 'fee_type' => $fee_type, 'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], 'notify_url' => site_url('weixin/pay_notify'), 'trade_type' => $trade_type, 'openid' => $openid, 'time_start' => date("YmdHis"), 'time_expire' => date("YmdHis", time() + 1800));
		if (!empty($detail) && is_array($detail) && count($detail) > 0) {
			$info['detail'] = json_encode($detail);
		}
		$sign = $this -> _MakeSign($info);
		if ($sign) {
			$info['sign'] = $sign;
		} else {
			return FALSE;
		}
		return $info;
	}

	/**
	 * 
	 * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayUnifiedOrder $inputObj
	 * @param int $timeOut
	 * @return 成功时返回，其他返回FALSE
	 */
	public function unifiedOrder($order, $timeOut = 30) {
		if (empty($order) || !is_array($order) || count($order) < 1) {
			return FALSE;
		}
		$order_xml = $this -> ArrayToXml($order);
		$startTimeStamp = $this -> getMillisecond();
		//请求开始时间
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		$response = $this -> _postXmlCurl($order_xml, $url, FALSE, $timeOut);
		$result = $this -> Init($response);
		
		return $result;
	}

	/**
	 *
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws Exception
	 *
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function GetJsApiParameters($UnifiedOrderResult) {
		if (!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == "") {
			return FALSE;
		}
		$time = time();
		$value = array(
			'appId'=>$UnifiedOrderResult["appid"],
			'timeStamp'=> "$time",
			'nonceStr'=>$this->getNonceStr(),
			'package'=>"prepay_id=" . $UnifiedOrderResult['prepay_id'],
			'signType'=>"MD5"
		);
		$value['paySign'] = $this->_MakeSign($value);
		$parameters = json_encode($value);

		return $parameters;
	}

	/**
	 *
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public function getNonceStr($length = 32) {
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	/**
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	private function _MakeSign($value = array()) {
		if (empty($value) || !is_array($value) || count($value) < 1) {
			return FALSE;
		}
		//签名步骤一：按字典序排序参数
		ksort($value);
		$string = $this -> _ToUrlParams($value);
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=" . $this -> CI -> config -> item('wx_partnerkey');
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}

	/**
	 * 格式化参数格式化成url参数
	 */
	private function _ToUrlParams($value = array()) {
		if (empty($value) || !is_array($value) || count($value) < 1) {
			return FALSE;
		}
		$buff = "";
		foreach ($value as $k => $v) {
			if ($k != "sign" && $v != "" && !is_array($v)) {
				$buff .= $k . "=" . $v . "&";
			}
		}

		$buff = trim($buff, "&");
		return $buff;
	}

	/**
	 * 以post方式提交xml到对应的接口url
	 *
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws Exception
	 */
	private function _postXmlCurl($xml, $url, $useCert = false, $second = 30) {
		if (empty($xml) || empty($url)) {
			return FALSE;
		}
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);

		//如果有配置代理这里就设置代理
		//		if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
		//			&& WxPayConfig::CURL_PROXY_PORT != 0){
		//			curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
		//			curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
		//		}
		curl_setopt($ch, CURLOPT_URL, $url);
//		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
//		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		//严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		if ($useCert == true) {
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, $this->self_path."/apiclient_cert.pem");
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, $this->self_path."/apiclient_key.pem");
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if ($data) {
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
			throw new Exception("curl出错，错误码:$error");
		}
	}

	/**
	 *
	 * 检测签名
	 */
	private function _CheckSign($array) {
		//fix异常
		if (!array_key_exists('sign', $array)) {
			throw new Exception("签名错误！");
		}

		$sign = $this -> _MakeSign($array);
		if ($array['sign'] == $sign) {
			return true;
		}
		throw new Exception("签名错误！");
	}

	/**
	 * 将array转换成xml字符
	 **/
	public function ArrayToXml($value = array()) {
		if (empty($value) || !is_array($value) || count($value) < 1) {
			return FALSE;
		}
		$xml = "<xml>";
		foreach ($value as $key => $val) {
			if (is_numeric($val)) {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			} else {
				$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
			}
		}
		$xml .= "</xml>";
		return $xml;
	}

	/**
	 * 将xml转为array
	 * @param string $xml
	 * @throws Exception
	 */
	public function FromXml($xml) {
		if (!$xml) {
			throw new Exception("xml数据异常！");
		}
		//将XML转为array
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$value = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $value;
	}

	/**
	 * 将xml转为array，并验证签名
	 * @param string $xml
	 */
	public function Init($xml) {
		$array = $this -> FromXml($xml);
		//fix bug 2015-06-29
		if ($array['return_code'] != 'SUCCESS') {
			return $array;
		}
		try{
			$this -> _CheckSign($array);
			return $array;
		}catch(Exception $e) {
			return FALSE;
		}
		
		
	}

	/**
	 * 获取毫秒级别的时间戳
	 */
	public function getMillisecond() {
		//获取毫秒的时间戳
		$time = explode(" ", microtime());
		$time = $time[1] . ($time[0] * 1000);
		$time2 = explode(".", $time);
		$time = $time2[0];
		return $time;
	}
	
	/**
	 * 验证异步通知
	 */
	public function checkNotify($data = array()) {
		if (empty($data) || !is_array($data)) {
			return FALSE;
		}
		try{
			$this -> _CheckSign($data);
			return TRUE;
		}catch(Exception $e) {
			return FALSE;
		}
	}
	
	/**
	 * 向微信返回异步通知接口结果
	 * @param $return_code 返回状态码,SUCCESS/FAIL,SUCCESS表示商户接收通知成功并校验成功
	 * @param $return_msg 返回信息，如非空，为错误原因：签名失败,参数格式校验错误
	 */
	public function ReplyNotify($return_code, $return_msg) {
		$data = array('return_code'=>"{$return_code}", 'return_msg'=>"{$return_msg}");
		$xml = $this->ArrayToXml($data);
		echo $xml;
	}
	
	/**
	 * 调用微信退款接口
	 * @param $transaction_id 微信订单号
	 * @param $out_trade_no 商户订单号
	 * @param $total_fee 订单总额，单位：元
	 * @param $refund_fee 退款金额，单位：元
	 * @param $username 操作人员的username
	 * @param $role_id 角色ID，默认为管理员
	 */
	public function refund($transaction_id, $out_trade_no, $total_fee, $refund_fee, $username, $role_id = 1, $out_refund_no = "") {
		if (floatval($total_fee) <= 0 || floatval($refund_fee) <= 0 || empty($username)) {
			return FALSE;
		}
		
		$url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
		$total_fee = floatval($total_fee) * 100;
		$refund_fee = floatval($refund_fee) * 100;
        if (empty($out_refund_no)) {
            $out_refund_no = "JBR".$this->getMillisecond();
        }
		switch (intval($role_id)) {
			case 1:
				$op_user_id = "admin-".$username;
				break;
			case 2:
				$op_user_id = "company-".$username;
				break;
			default:
				$log_data = date("Y-m-d H:i:s")."\n用户角色不正确\n\n";
				file_put_contents($this->self_path."/../wxpay_refund_".date("Ymd"), $log_data, FILE_APPEND);
				return FALSE;
				break;
		}
		$data = array(
			"appid"=>$this -> CI -> config -> item('wx_appid'),
			"mch_id"=>$this -> CI -> config -> item('wx_partnerid'),
			"device_info"=>"WEB",
			"nonce_str"=>$this -> getNonceStr(),
			"out_refund_no"=>$out_refund_no,
			"total_fee"=>$total_fee, 
			"refund_fee"=>$refund_fee,
			"op_user_id"=>$op_user_id
		);
		if (!empty($transaction_id)) {
			$data['transaction_id'] = $transaction_id;
		}else if (!empty($out_trade_no)) {
			$data['out_trade_no'] = $out_trade_no;
		}else {
			$log_data = date("Y-m-d H:i:s")."\n缺少transaction_id和out_trade_no\n\n";
			file_put_contents($this->self_path."/../wxpay_refund_".date("Ymd"), $data, FILE_APPEND);
			return FALSE;
		}
		
		$sign = $this -> _MakeSign($data);
		if ($sign) {
			$data['sign'] = $sign;
		} else {
			$log_data = date("Y-m-d H:i:s")."\n签名失败\n\n";
			file_put_contents($this->self_path."/../wxpay_refund_".date("Ymd"), $data, FILE_APPEND);
			return FALSE;
		}
		$xml = $this->ArrayToXml($data);
		$result = $this->_postXmlCurl($xml, $url, TRUE, 30);
		$result  = $this->FromXml($result);
		if ($result['return_code'] == 'SUCCESS') {
			// 退款成功
			if ($this->checkNotify($result)) {
				// 验证成功
				return $result;
			}else {
				$log_data = date("Y-m-d H:i:s")."\n验证失败\n".json_encode($result)."\n\n";
				file_put_contents($this->self_path."/../wxpay_refund_".date("Ymd"), $data, FILE_APPEND);
				return FALSE;
			}
		}else {
			// 退款失败
			$log_data = date("Y-m-d H:i:s")."\n退款失败\n".json_encode($result)."\n\n";
			file_put_contents($this->self_path."/../wxpay_refund_".date("Ymd"), $data, FILE_APPEND);
			return FALSE;
		}
	}
}
