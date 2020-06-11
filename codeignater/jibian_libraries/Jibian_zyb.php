<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Jibian_zyb {
	private $CI;
	/**
	 * 网关地址
	 */
	private $url;
	/**
	 * 账号
	 */
	private $username;
	/**
	 * 企业码
	 */
	private $corpcode;
	/**
	 * 私钥
	 */
	private $privatekey;
	
	private $xml;
	
	public function __construct() {
		$this->CI = & get_instance();
		$this->url = $this->CI->config->item('zyb_url');
		$this->username = $this->CI->config->item('zyb_username');
		$this->corpcode = $this->CI->config->item('zyb_corpcode');
		$this->privatekey = $this->CI->config->item('zyb_privatekey');
		$this->xml = new DOMDocument();
	}
	
	/**
	 * 创建订单
	 * @param $certificate_no 身份证号
	 * @param $name 联系人姓名
	 * @param $mobile 接收彩信的手机号（电信手机无法收到信息）
	 * @param $order_code 订单编码
	 * @param $price 单价
	 * @param $quantity 数量
	 * @param $occ_date 使用时间
	 * @param $commodity_name 商品名称
	 * @param $ticket_code 票型编码
	 */
	public function createOrder($certificate_no, $name, $mobile, $order_code, $price, $quantity, $occ_date, $commodity_name, $ticket_code) {
		if(empty($certificate_no) || empty($name) || empty($mobile) || empty($order_code) || empty($price) || empty($quantity) || empty($commodity_name) || empty($ticket_code)) {
			return FALSE;
		}
		
		$date = date('Y-m-d');
		$pay_method = "sign_bill";
		$total_price = floatval($price) * intval($quantity);
		$xml_msg = "<PWBRequest><transactionName>SEND_CODE_REQ</transactionName><header><application>SendCode</application><requestTime>{$date}</requestTime></header><identityInfo><corpCode>{$this->corpcode}</corpCode><userName>{$this->username}</userName></identityInfo><orderRequest><order><certificateNo>{$certificate_no}</certificateNo><linkName>{$name}</linkName><linkMobile>{$mobile}</linkMobile><orderCode>{$order_code}</orderCode><groupNo/><payMethod>{$pay_method}</payMethod><orderPrice>{$total_price}</orderPrice><ticketOrders><ticketOrder><orderCode>{$order_code}</orderCode><price>{$price}</price><quantity>{$quantity}</quantity><occDate>{$occ_date}</occDate><goodsCode>{$ticket_code}</goodsCode><goodsName>{$commodity_name}</goodsName><totalPrice>{$total_price}</totalPrice></ticketOrder></ticketOrders></order></orderRequest></PWBRequest>";
		
		$sign = $this->getSign($xml_msg);
		$info = "xmlMsg=".$xml_msg."&sign=".$sign;
		$result = $this->Sendxml($this->url, $info);
		
		$result = simplexml_load_string($result);
		$result = json_encode($result);
		$result = json_decode($result,TRUE);
		if (intval($result['code']) == 0) {
			return array('success'=>TRUE, 'data'=>$result);
		}else {
			return array('success'=>FALSE, 'data'=>$result);
		}
	}

    /**
     * 发送短信
     * @param $order_code 订单编号
     * @return array|bool 发送失败返回FALSE，发送成功返回数组信息
     */
	public function sendTextMessage($order_code) {
		if (empty($order_code)) {
			return FALSE;
		}
		
		$date = date('Y-m-d');
		$xml_msg = "<PWBRequest><transactionName>SEND_SM_REQ</transactionName><header><application>SendCode</application><requestTime>{$date}</requestTime></header><identityInfo><corpCode>{$this->corpcode}</corpCode><userName>{$this->username}</userName></identityInfo><orderRequest><order><orderCode>{$order_code}</orderCode><tplCode></tplCode></order></orderRequest></PWBRequest>";
		
		$sign = $this->getSign($xml_msg);
		$info = "xmlMsg=".$xml_msg."&sign=".$sign;
		$result = $this->Sendxml($this->url, $info);
		
		$result = simplexml_load_string($result);
		$result = json_encode($result);
		$result = json_decode($result,TRUE);
		if (intval($result['code']) == 0) {
			return array('success'=>TRUE, 'data'=>$result);
		}else {
			return array('success'=>FALSE, 'data'=>$result);
		}
	}

    /**
     * 部分退票
     * @param $order_code 订单号
     * @param $number 退票数量
     * @param $refund_code 退款单号
     * @return array|bool 发送失败返回FALSE，发送成功返回数组信息
     */
	public function segment_refund($order_code, $number, $refund_code) {
        if (empty($order_code) || empty($refund_code) || intval($number) < 1) {
            return FALSE;
        }

        $date = date('Y-m-d');
        $xml_msg = "<PWBRequest><transactionName>RETURN_TICKET_NUM_REQ</transactionName><header><application>SendCode</application><requestTime>{$date}</requestTime></header><identityInfo><corpCode>{$this->corpcode}</corpCode><userName>{$this->username}</userName></identityInfo><orderRequest><returnTicket><orderCode>{$order_code}</orderCode><returnNum>{$number}</returnNum><thirdReturnCode>{$refund_code}</thirdReturnCode></returnTicket></orderRequest></PWBRequest>";

        $sign = $this->getSign($xml_msg);
        $info = "xmlMsg=".$xml_msg."&sign=".$sign;
        $result = $this->Sendxml($this->url, $info);

        $result = simplexml_load_string($result);
        $result = json_encode($result);
        $result = json_decode($result,TRUE);
        if (intval($result['code']) == 0) {
            return array('success'=>TRUE, 'data'=>$result);
        }else {
            return array('success'=>FALSE, 'data'=>$result);
        }
    }

    /**
     * 订单完结通知
     * @oaram $order_no 订单编号
     * @param $status 状态，cancel/success状态:取消/完成
     * @param $checkNum 已检票数量
     * @param $returnNum 退票数量
     * @param $total 总数量
     * @return bool 数据正确返回TRUE，数据错误返回FALSE
     */
    public function complete_order_notice($order_no, $status, $checkNum, $returnNum, $total) {
        if (empty($order_no) || intval($total) < 1){
            return FALSE;
        }

        if (intval($checkNum) < 1 && intval($returnNum) < 1) {
            return FALSE;
        }

        if (intval($checkNum) + intval($returnNum) != intval($total)) {
            return FALSE;
        }

        if ($status != "cancel" && $status != "success") {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 核销订单通知
     * @param $order_no 订单编号
     * @param $status 订单状态，check状态:检票
     * @param $checkNum 已检票数量
     * @param $returnNum 退票数量
     * @param $total 总数量
     * @return bool 数据正确返回TRUE，数据错误返回FALSE
     */
    public function check_oder_notice($order_no, $status, $checkNum, $returnNum, $total) {
        if (empty($order_no) || intval($total) < 1){
            return FALSE;
        }

        if (intval($checkNum) < 1 && intval($returnNum) < 1) {
            return FALSE;
        }

        if (intval($checkNum) + intval($returnNum) != intval($total)) {
            return FALSE;
        }

        if ($status != "check") {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 退票审核通知
     * @param $return_code 退票单号
     * @param $status 状态，fail/success审核状态:失败/完成
     * @param $returnNum 退票数量
     * @return bool 数据正确返回TRUE，数据错误返回FALSE
     */
    public function refund_order_notice($return_code, $status, $returnNum) {
        if (empty($return_code)){
            return FALSE;
        }

        if (intval($returnNum) < 1) {
            return FALSE;
        }

        if ($status != "fail" && $status != "success") {
            return FALSE;
        }

        return TRUE;
    }

	/**
	 * 数组转XML
	 */
	private function arrayToXML($parent_node, $array) {
		if (empty($parent_node) || empty($array) || !is_array($array) || count($array) < 1) {
			return FALSE;
		}
		
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$node = $this->xml->createElement($key);
				$this->arrayToXML($node, $value);
				$parent_node->appendchild($node);
			}else {
				$parent_node->appendchild($this->xml->createElement($key, $value));
			}
		}
	}
	
	/**
	 * 获取签名信息
	 */
	private function getSign($xml_msg) {
		if (empty($xml_msg)) {
			return FALSE;
		}
		return md5("xmlMsg=".$xml_msg.$this->privatekey);
	}
	
	/*发送xml*/
	private function Sendxml($url,$new)
	{
		$ch = curl_init();
		$header = "Content-type: text/xml; charset=utf-8";
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$new);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, $header);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}