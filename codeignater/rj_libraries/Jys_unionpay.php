<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once FCPATH . "application/third_party/unionpay/acp_service.php";

/**
 * =====================================================================================
 *
 *        Filename: Jys_unionpay.php
 *
 *     Description: 银联支付类库
 *
 *         Created: 2017-7-4 10:28:10
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Jys_unionpay
{
    private $_CI;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->_CI =& get_instance();
    }

    /**
     * 前台跳转银联支付页面
     * @param $order_number 商户订单号，8-32位数字字母，不能含“-”或“_”
     * @param $total_price 订单总价，单位为元
     * @param $frontUrl 前台通知地址
     * @param bool $is_mobile 是否移动端，默认为FALSE
     */
    public function front_consume($order_number, $total_price, $frontUrl, $backUrl = NULL,  $is_mobile = FALSE)
    {
        if (empty($backUrl)) {
            $backUrl = site_url("/unionpay/back_url");
        }
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,                 //版本号
            'encoding' => 'utf-8',                  //编码方式
            'txnType' => '01',                      //交易类型
            'txnSubType' => '01',                  //交易子类
            'bizType' => '000201',                  //业务类型
            'frontUrl' => $frontUrl,  //前台通知地址
            'backUrl' => $backUrl,      //后台通知地址
            'signMethod' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod,                  //签名方法
            'channelType' => '07',                  //渠道类型，07-PC，08-手机
            'accessType' => '0',                  //接入类型
            'currencyCode' => '156',              //交易币种，境内商户固定156

            //TODO 以下信息需要填写
            'merId' => $this->_CI->config->item('un_merid'),        //商户代码，请改自己的测试商户号
            'orderId' => $order_number,    //商户订单号，8-32位数字字母
            'txnTime' => date('YmdHis'),//$_POST["txnTime"],	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间
            'txnAmt' => $total_price * 100,    //交易金额，单位分

            // 订单超时时间。
            // 超过此时间后，除网银交易外，其他交易银联系统会拒绝受理，提示超时。 跳转银行网银交易如果超时后交易成功，会自动退款，大约5个工作日金额返还到持卡人账户。
            // 此时间建议取支付时的北京时间加15分钟。
            // 超过超时时间调查询接口应答origRespCode不是A6或者00的就可以判断为失败。
            'payTimeout' => date('YmdHis', strtotime('+60 minutes')),

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),
        );
        if ($is_mobile) {
            $params['channelType'] = '08';
        }

        com\unionpay\acp\sdk\AcpService::sign($params);
        $uri = com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->frontTransUrl;
        $html_form = com\unionpay\acp\sdk\AcpService::createAutoFormHtml($params, $uri);

        echo $html_form;
    }

    /**
     * 撤销交易
     * @param $order_number 商户订单号，8-32位数字字母，不能含“-”或“_”
     * @param $total_price 订单总价，单位为元
     * @param $transaction_id 交易单号，银联支付返回的交易单号
     * @param bool $is_mobile 是否移动端，默认为FALSE
     */
    public function consume_undo($order_number, $total_price, $transaction_id, $is_mobile = FALSE)
    {
        $result = array('success'=>FALSE, 'msg'=>'撤销交易失败', 'data'=>array());
        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,              //版本号
            'encoding' => 'utf-8',              //编码方式
            'signMethod' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod,              //签名方法
            'txnType' => '31',                  //交易类型
            'txnSubType' => '00',              //交易子类
            'bizType' => '000201',              //业务类型
            'accessType' => '0',              //接入类型
            'channelType' => '07',              //渠道类型
            'backUrl' => site_url("/unionpay/back_url"), //后台通知地址

            //TODO 以下信息需要填写
            'orderId' => $order_number,        //商户订单号，8-32位数字字母，不能含“-”或“_”，可以自行定制规则，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'merId' => $this->_CI->config->item('un_merid'),            //商户代码，请改成自己的测试商户号，此处默认取demo演示页面传递的参数
            'origQryId' => $transaction_id, //原消费的queryId，可以从查询接口或者通知接口中获取，此处默认取demo演示页面传递的参数
            'txnTime' => date('YmdHis'),        //订单发送时间，格式为YYYYMMDDhhmmss，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'txnAmt' => $total_price * 100,       //交易金额，消费撤销时需和原消费一致，此处默认取demo演示页面传递的参数

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),
        );
        if ($is_mobile) {
            $params['channelType'] = '08';
        }

        com\unionpay\acp\sdk\AcpService::sign($params); // 签名
        $url = com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->backTransUrl;

        $result_arr = com\unionpay\acp\sdk\AcpService::post($params, $url);
        if (count($result_arr) <= 0) {
            //没收到200应答的情况
            $result['msg'] = '网络请求失败';
            return $result;
        }
        if (!com\unionpay\acp\sdk\AcpService::validate($result_arr)) {
            //echo "应答报文验签失败<br>\n";
            $result['msg'] = '应答报文验签失败';
            $result['data'] = $result_arr;
            return $result;
        }
        if ($result_arr["respCode"] == "00") {
            //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
            //TODO
            $result['success'] = TRUE;
            $result['msg'] = '撤销交易成功';
            $result['data'] = $result_arr;
            return $result;
        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05"
        ) {
            //后续需发起交易状态查询交易确定交易状态
            //TODO
            $result['msg'] = '后续需发起交易状态查询交易确定交易状态';
            $result['data'] = $result_arr;
            return $result;
        } else {
            //其他应答码做以失败处理
            //TODO
            $result['msg'] = '其他应答码做以失败处理';
            $result['data'] = $result_arr;
            return $result;
        }
    }

    /**
     * 退款
     * @param $order_number 商户订单号，8-32位数字字母，不能含“-”或“_”
     * @param $total_price 订单总价，单位为元
     * @param $transaction_id 交易单号，银联支付返回的交易单号
     * @param bool $is_mobile 是否移动端，默认为FALSE
     */
    public function refund($order_number, $total_price, $transaction_id, $is_mobile = FALSE)
    {
        $result = array('success'=>FALSE, 'msg'=>'退款交易失败', 'data'=>array());
        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,              //版本号
            'encoding' => 'utf-8',              //编码方式
            'signMethod' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod,              //签名方法
            'txnType' => '04',                  //交易类型
            'txnSubType' => '00',              //交易子类
            'bizType' => '000201',              //业务类型
            'accessType' => '0',              //接入类型
            'channelType' => '07',              //渠道类型
            'backUrl' => site_url("/unionpay/back_url"), //后台通知地址

            //TODO 以下信息需要填写
            'orderId' => $order_number,        //商户订单号，8-32位数字字母，不能含“-”或“_”，可以自行定制规则，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'merId' => $this->_CI->config->item('un_merid'),            //商户代码，请改成自己的测试商户号，此处默认取demo演示页面传递的参数
            'origQryId' => $transaction_id, //原消费的queryId，可以从查询接口或者通知接口中获取，此处默认取demo演示页面传递的参数
            'txnTime' => date('YmdHis'),        //订单发送时间，格式为YYYYMMDDhhmmss，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
            'txnAmt' => $total_price * 100,       //交易金额，退货总金额需要小于等于原消费

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),
        );
        if ($is_mobile) {
            $params['channelType'] = '08';
        }

        com\unionpay\acp\sdk\AcpService::sign($params); // 签名
        $url = com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->backTransUrl;

        $result_arr = com\unionpay\acp\sdk\AcpService::post($params, $url);
        if (count($result_arr) <= 0) {
            //没收到200应答的情况
            $result['msg'] = '网络请求失败';
            return $result;
        }
        if (!com\unionpay\acp\sdk\AcpService::validate($result_arr)) {
            //echo "应答报文验签失败<br>\n";
            $result['msg'] = '应答报文验签失败';
            $result['data'] = $result_arr;
            return $result;
        }

        if ($result_arr["respCode"] == "00") {
            //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
            //TODO
            $result['success'] = TRUE;
            $result['msg'] = '退款交易成功';
            $result['data'] = $result_arr;
            return $result;
        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05"
        ) {
            //后续需发起交易状态查询交易确定交易状态
            //TODO
            $result['msg'] = '后续需发起交易状态查询交易确定交易状态';
            $result['data'] = $result_arr;
            return $result;
        } else {
            //其他应答码做以失败处理
            //TODO
            $result['msg'] = '其他应答码做以失败处理';
            $result['data'] = $result_arr;
            return $result;
        }
    }

    /**
     * 查询订单信息
     * @param $order_number 商户订单号，8-32位数字字母，不能含“-”或“_”
     * @param bool $is_mobile 是否移动端，默认为FALSE
     */
    public function query($order_number, $txn_time, $is_mobile = FALSE)
    {
        $result = array('success'=>FALSE, 'msg'=>'查询交易失败', 'data'=>array());
        $params = array(
            //以下信息非特殊情况不需要改动
            'version' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,          //版本号
            'encoding' => 'utf-8',          //编码方式
            'signMethod' => com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod,          //签名方法
            'txnType' => '00',              //交易类型
            'txnSubType' => '00',          //交易子类
            'bizType' => '000000',          //业务类型
            'accessType' => '0',          //接入类型
            'channelType' => '07',          //渠道类型

            //TODO 以下信息需要填写
            'orderId' => $order_number,    //请修改被查询的交易的订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数
            'merId' => $this->_CI->config->item('un_merid'),        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'txnTime' => $txn_time,    //请修改被查询的交易的订单发送时间，格式为YYYYMMDDhhmmss，此处默认取demo演示页面传递的参数
        );
        if ($is_mobile) {
            $params['channelType'] = '08';
        }

        com\unionpay\acp\sdk\AcpService::sign($params); // 签名
        $url = com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->singleQueryUrl;

        $result_arr = com\unionpay\acp\sdk\AcpService::post($params, $url);
        if (count($result_arr) <= 0) {
            //没收到200应答的情况
            $result['msg'] = '网络请求失败';
            return $result;
        }

        if (!com\unionpay\acp\sdk\AcpService::validate($result_arr)) {
            //echo "应答报文验签失败<br>\n";
            $result['msg'] = '应答报文验签失败';
            $result['data'] = $result_arr;
            return $result;
        }

        if ($result_arr["respCode"] == "00") {
            if ($result_arr["origRespCode"] == "00") {
                //交易成功
                //TODO
                $result['success'] = TRUE;
                $result['msg'] = '查询交易成功';
                $result['data'] = $result_arr;
                return $result;
            } else if ($result_arr["origRespCode"] == "03"
                || $result_arr["origRespCode"] == "04"
                || $result_arr["origRespCode"] == "05"
            ) {
                //后续需发起交易状态查询交易确定交易状态
                //TODO
                $result['msg'] = '后续需发起交易状态查询交易确定交易状态';
                $result['data'] = $result_arr;
                return $result;
            } else {
                //其他应答码做以失败处理
                //TODO
                $result['msg'] = '其他应答码做以失败处理';
                $result['data'] = $result_arr;
                return $result;
            }
        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05"
        ) {
            //后续需发起交易状态查询交易确定交易状态
            //TODO
            $result['msg'] = '请稍后再查询交易状态';
            $result['data'] = $result_arr;
            return $result;
        } else {
            //其他应答码做以失败处理
            //TODO
            $result['msg'] = '其他应答码做以失败处理';
            $result['data'] = $result_arr;
            return $result;
        }
    }

    /**
     * 验证签名
     * @param $data 待验签的数组
     * @return 验签成功返回TRUE，失败返回FALSE
     */
    public function validate($data)
    {
        if (isset($data['signature'])) {
            if (com\unionpay\acp\sdk\AcpService::validate($data)) {
                // 验签成功
                return TRUE;
            }else {
                // 验签失败
                return FALSE;
            }
        } else {
            // 签名为空
            return FALSE;
        }
    }
}