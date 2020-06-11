<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_kdniao.php
 *
 *     Description: 快递鸟公用类库
 *
 *         Created: 2016-12-27 14:14:12
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Jys_kdniao {
    private $_CI;
    private $_release_base_url;
    private $_debug_base_url;

    public function __construct() {
        $this->_CI = & get_instance();
        $this->_release_base_url = $this->_CI->config->item('kdn_release_base_url');
        $this->_debug_base_url = $this->_CI->config->item('kdn_debug_base_url');
    }

    /**
     * 物流轨迹（即时查询）
     * 物流查询API提供实时查询物流轨迹的服务，用户提供运单号和快递公司，即可查询当前时刻的最新物流轨迹。
     * @param $shipper_code 快递公司编号
     * @param $logistic_code 快递单号
     * @param $order_number 订单编号
     */
    public function ebusiness_order_handle($shipper_code, $logistic_code, $order_number = "") {
        if (empty($shipper_code) || empty($logistic_code)) {
            return FALSE;
        }
        $request_data = array();
        if (empty($order_number)) {
            $request_data['OrderCode'] = "";
        }else {
            $request_data['OrderCode'] = $order_number;
        }
        $request_data['ShipperCode'] = $shipper_code;
        $request_data['LogisticCode'] = $logistic_code;

        $url = "Ebusiness/EbusinessOrderHandle.aspx";
        $request_type = '1002';

        $result = $this->_request_kdniao($request_data, $request_type, $url);
        if (is_array($result) && !empty($result) && isset($result['State'])) {
            switch ($result['State']) {
                case '2':
                    // 在途中
                    $result['StateName'] = '在途中';
                    break;
                case '3':
                    // 已签收
                    $result['StateName'] = '已签收';
                    break;
                case '4':
                    // 问题件
                    $result['StateName'] = '问题件';
                    break;
                default:
                    $result['StateName'] = '';
                    break;
            }
        }

        return $result;
    }

    /**
     * 物流轨迹（订阅查询）
     * @param $shipper_code 快递公司编号
     * @param $logistic_code 快递单号
     * @param string $order_number 订单号
     * @param string $callback 用户自定义回调信息
     */
    public function dist($shipper_code, $logistic_code, $order_number = "", $callback = "") {
        if (empty($shipper_code) || empty($logistic_code)) {
            return FALSE;
        }

        $request_data = array();
        if (empty($order_number)) {
            $request_data['OrderCode'] = "";
        }else {
            $request_data['OrderCode'] = $order_number;
        }
        $request_data['ShipperCode'] = $shipper_code;
        $request_data['LogisticCode'] = $logistic_code;
        if (empty($callback)) {
            $request_data['CallBack'] = "";
        }else {
            $request_data['CallBack'] = $callback;
        }

        $url = "api/dist";
        $request_type = '1008';

        $result = $this->_request_kdniao($request_data, $request_type, $url);

        return $result;

    }

    /**
     * 将应用级参数放入系统级参数，并发起请求
     * @param $request_data 应用级参数
     * @param $request_type 请求类型
     * @param $url 基于基地址的url
     * @param $debug 是否调试，如果调试的话，调用调试接口
     */
    private function _request_kdniao($request_data, $request_type, $application_url, $debug = FALSE) {
        if (empty($request_data) || !is_array($request_data) || empty($request_type) || empty($application_url)) {
            return FALSE;
        }

        $request_data = $this->_json_encode_ex($request_data);

        $data = array(
            'EBusinessID'=>$this->_CI->config->item('kdn_ebusinessid'),
            'RequestType'=>$request_type,
            'RequestData'=>urlencode($request_data),
            'DataType'=>'2'
        );
        $data['DataSign'] = $this->_encrypt($request_data, $this->_CI->config->item('kdn_apikey'));

        if ($debug) {
            $url = $this->_debug_base_url.$application_url;
        }else {
            $url = $this->_release_base_url.$application_url;
        }

        $result = $this->_send_post($url, $data);
        return $result;
    }

    /**
     * Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    private function _encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

    /**
     *  post提交数据
     * @param  string $url 请求Url
     * @param  array $datas 提交的数据
     * @return url响应返回的html
     */
    private function _send_post($url, $datas) {
        $temps = array();
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);
        }
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(!isset($url_info['port']) || $url_info['port']=='') {
            $url_info['port']=80;
        }
        //echo $url_info['port'];
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);

        if (!empty($gets)) {
            $gets = json_decode($gets, TRUE);
        }

        return $gets;
    }

    /**
     * 对内容进行json编码，并且保持汉字不会被编码
     * @param $value 被编码的对象
     * @return 编码结果字符串
     */
    public function _json_encode_ex($value) {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $str = json_encode($value);
            $str = preg_replace_callback("#\\\u([0-9a-f]{4})#i", function($matchs) {
                return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
            }, $str);
            return $str;
        } else {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }
    }
}