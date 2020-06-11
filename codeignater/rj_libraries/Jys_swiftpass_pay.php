<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once FCPATH . "application/third_party/swiftpass/Utils.class.php";
require_once FCPATH . "application/third_party/swiftpass/RequestHandler.class.php";
require_once FCPATH . "application/third_party/swiftpass/ClientResponseHandler.class.php";
require_once FCPATH . "application/third_party/swiftpass/PayHttpClient.class.php";

/**
 * =====================================================================================
 *
 *        Filename: Jys_swiftpass_pay.php
 *
 *     Description: 威富通支付类库
 *
 *         Created: 2017-8-18 13:40:32
 *
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Jys_swiftpass_pay
{
    private $_CI;
    private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->request();
    }

    public function request()
    {
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();

        $this->reqHandler->setGateUrl($this->_CI->config->item('sp_url'));
        $this->reqHandler->setKey($this->_CI->config->item('sp_key'));
    }

    /**
     * 统一下单接口
     * @param $out_trade_no 订单编号
     * @param $total_fee 订单金额（单位：元）
     * @param $body 商品描述
     * @param $attach 附加信息
     */
    public function submitOrderInfo($out_trade_no, $total_fee, $body = "", $attach = "", $payment_id = jys_system_code::PAYMENT_WXPAY)
    {
        $result = array('success' => FALSE, 'msg' => '创建支付信息失败', 'data' => array());

        $this->reqHandler->setParameter('out_trade_no', $out_trade_no);
        if (round(floatval($total_fee), 2) < 0.01) {
            $result['msg'] = '订单总金额不得小于0.01元';
            return $result;
        }
        $total_fee = intval(round(floatval($total_fee), 2) * 100);
        $this->reqHandler->setParameter('total_fee', $total_fee);//总金额，以分为单位
        if (empty($body)) {
            $body = "蓉锦医药网".$out_trade_no;
        }
        $this->reqHandler->setParameter('body', $body);//商品描述
        if (!empty($attach)) {
            $this->reqHandler->setParameter('attach', $attach);//附加信息
        }
        $this->reqHandler->setParameter('time_start', date('YmdHis'));//订单生成时间，格式为yyyyMMddHHmmss
        $this->reqHandler->setParameter('time_expire', date('YmdHis', strtotime('+60 minutes')));//订单失效时间，格式为yyyyMMddHHmmss

        if (intval($payment_id) == jys_system_code::PAYMENT_WXPAY) {
            $this->reqHandler->setParameter('service', 'pay.weixin.native');//接口类型
        }else if (intval($payment_id) == jys_system_code::PAYMENT_ALIPAY) {
            $this->reqHandler->setParameter('service', 'pay.alipay.native');//接口类型
        }else {
            $result['msg'] = '支付方式不正确';
            return $result;
        }

        $this->reqHandler->setParameter('mch_id', $this->_CI->config->item('sp_mchid'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('version', $this->_CI->config->item('sp_version'));//版本号
        $this->reqHandler->setParameter('device_info', '蓉锦医药网');//终端设备号
        $this->reqHandler->setParameter('mch_create_ip', $this->_get_ip());//订单生成的机器 IP

        //通知地址，必填项，接收平台通知的URL，需给绝对路径，255字符内格式如:http://wap.tenpay.com/tenpay.asp
        $this->reqHandler->setParameter('notify_url', site_url('/swiftpass/notify').'/'.$payment_id);
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名

        $data = Utils::toXml($this->reqHandler->getAllParameters());
//        dd($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                    $result['success'] = TRUE;
                    $result['msg'] = '创建支付信息成功';
                    $result['data'] = array(
                        'code_img_url' => $this->resHandler->getParameter('code_img_url'),
                        'code_url' => $this->resHandler->getParameter('code_url'),
                        'code_status' => $this->resHandler->getParameter('code_status')
                    );
                } else {
                    $result['data'] = array(
                        'status' => 500,
                        'msg' => 'Error Code:' . $this->resHandler->getParameter('err_code') . ' Error Message:' . $this->resHandler->getParameter('err_msg')
                    );
                }
            } else {
                $result['data'] = array(
                    'status' => 500,
                    'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message')
                );
            }
        } else {
            $result['data'] = array(
                'status' => 500,
                'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()
            );
        }

        return $result;
    }

    /**
     * 查询订单
     * @param null $out_trade_no 商户端订单号
     * @param null $transaction_id 支付平台生成的订单号
     */
    public function queryOrder($out_trade_no = NULL, $transaction_id = NULL)
    {
        $result = array('success'=>FALSE, 'msg'=>'查询订单信息失败', 'data'=>array());

        if (empty($out_trade_no) && empty($transaction_id)) {
            $result['msg'] = '请输入商户订单号或平台订单号';
            return $result;
        }

        if (!empty($out_trade_no)) {
            $this->reqHandler->setParameter('out_trade_no', $out_trade_no);//商户系统内部的订单号
        }
        if (!empty($transaction_id)) {
            $this->reqHandler->setParameter('transaction_id', $transaction_id);//平台交易号
        }

        $this->reqHandler->setParameter('version', $this->_CI->config->item('sp_version'));//版本号
        $this->reqHandler->setParameter('service', 'unified.trade.query');//接口类型：

        $this->reqHandler->setParameter('mch_id', $this->_CI->config->item('sp_mchid'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            $res = $this->resHandler->getAllParameters();
//            if ($this->resHandler->isTenpaySign()) {
                $res = $this->resHandler->getAllParameters();
                $result['success'] = TRUE;
                $result['msg'] = '查询订单信息成功';
                $result['data'] = $res;
//                $result['debug'] = $this->resHandler->getDebugInfo();
//            }else {
//                $result['data'] = array(
//                    'status' => 500,
//                    'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message') . 'Debug info:' . $this->resHandler->getDebugInfo()
//                );
//            }
        } else {
            $result['data'] = array(
                'status' => 500,
                'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()
            );
        }

        return $result;
    }

    /**
     * 申请退款接口
     * @param $out_refund_no 退款单号
     * @param $total_fee 订单总额（单位：元）
     * @param $refund_fee 退款金额（单位：元）
     * @param null $out_trade_no 商户端订单号
     * @param null $transaction_id 支付平台生成的订单号
     * @param string $refund_channel 退款渠道
     */
    public function submitRefund($out_refund_no, $total_fee, $refund_fee, $out_trade_no = NULL, $transaction_id = NULL, $refund_channel = 'ORIGINAL')
    {
        $result = array('success'=>TRUE, 'msg'=>'退款失败', 'data'=>array());
        if (floatval($total_fee) < floatval($refund_fee)) {
            $result['msg'] = '退款金额不得大于总金额';
            return $result;
        }

        if (empty($out_trade_no) && empty($transaction_id)) {
            $result['msg'] = '请输入商户订单号或平台订单号';
            return $result;
        }

        if (!empty($out_trade_no)) {
            $this->reqHandler->setParameter('out_trade_no', $out_trade_no);//商户系统内部的订单号
        }
        if (!empty($transaction_id)) {
            $this->reqHandler->setParameter('transaction_id', $transaction_id);//平台订单号
        }
        $this->reqHandler->setParameter('out_refund_no', $out_refund_no);//商户退款单号
        if (round(floatval($total_fee), 2) < 0.01) {
            $result['msg'] = '总金额不得小于0.01元';
            return $result;
        }
        $total_fee = intval(round(floatval($total_fee), 2) * 100);
        $this->reqHandler->setParameter('total_fee', $total_fee);//订单总金额，单位为分
        if (round(floatval($refund_fee), 2) < 0.01) {
            $result['msg'] = '退款金额不得小于0.01元';
            return $result;
        }
        $refund_fee = intval(round(floatval($refund_fee), 2) * 100);
        $this->reqHandler->setParameter('refund_fee', $refund_fee);//退款总金额,单位为分,可以做部分退款
        $this->reqHandler->setParameter('refund_channel', $refund_channel);//退款渠道，ORIGINAL-原路退款，默认

        $this->reqHandler->setParameter('version', $this->_CI->config->item('sp_version'));//版本号
        $this->reqHandler->setParameter('service', 'unified.trade.refund');//接口类型
        $this->reqHandler->setParameter('mch_id', $this->_CI->config->item('sp_mchid'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->setParameter('op_user_id', $this->_CI->config->item('sp_mchid'));//必填项，操作员帐号,默认为商户号

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                 'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                 'out_refund_no'=>$this->resHandler->getParameter('out_refund_no'),
                                 'refund_id'=>$this->resHandler->getParameter('refund_id'),
                                 'refund_channel'=>$this->resHandler->getParameter('refund_channel'),
                                 'refund_fee'=>$this->resHandler->getParameter('refund_fee'),
                                 'coupon_refund_fee'=>$this->resHandler->getParameter('coupon_refund_fee'));*/
                    $res = $this->resHandler->getAllParameters();
                    $result['success'] = TRUE;
                    $result['msg'] = '退款成功';
                    $result['data'] = $res;
                } else {
                    $result['data'] = array(
                        'status' => 500,
                        'msg' => 'Error Code:' . $this->resHandler->getParameter('err_code') . ' Error Message:' . $this->resHandler->getParameter('err_msg')
                    );
                }
            }else {
                $result['data'] = array(
                    'status' => 500,
                    'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message')
                );
            }
        } else {
            $result['data'] = array(
                'status' => 500,
                'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo());
        }

        return $result;
    }

    /**
     * 查询退款信息
     * @param $refund_id 平台退款单号
     * @param $out_refund_no 商户退款单号
     * @param $transaction_id 平台订单号
     * @param $out_trade_no 商户订单号
     */
    public function queryRefund($refund_id, $out_refund_no, $transaction_id, $out_trade_no)
    {
        $result = array('success'=>FALSE, 'msg'=>'查询退款信息失败', 'data'=>array());

        if (empty($refund_id) && empty($out_refund_no) && empty($transaction_id) && empty($out_trade_no)) {
            $result['msg'] = '请输入商户订单号，平台订单号，商户退款单号，平台退款单号';
        }

        if (!empty($refund_id)) {
            $this->reqHandler->setParameter('refund_id', $refund_id);//平台退款单号
        }
        if (!empty($out_refund_no)) {
            $this->reqHandler->setParameter('out_refund_no', $out_refund_no);//商户退款单号
        }
        if (!empty($transaction_id)) {
            $this->reqHandler->setParameter('transaction_id', $transaction_id);//平台订单号
        }
        if (!empty($out_trade_no)) {
            $this->reqHandler->setParameter('out_trade_no', $out_trade_no);//商户系统内部的订单号
        }


        $this->reqHandler->setParameter('version', $this->_CI->config->item('sp_version'));//版本号
        $this->reqHandler->setParameter('service', 'unified.trade.refundquery');//接口类型
        $this->reqHandler->setParameter('mch_id', $this->_CI->config->item('sp_mchid'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);//设置请求地址与请求参数
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                //当返回状态与业务结果都为0时才返回，其它结果请查看接口文档
                if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                  'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                  'refund_count'=>$this->resHandler->getParameter('refund_count'));
                    for($i=0; $i<$res['refund_count']; $i++){
                        $res['out_refund_no_'.$i] = $this->resHandler->getParameter('out_refund_no_'.$i);
                        $res['refund_id_'.$i] = $this->resHandler->getParameter('refund_id_'.$i);
                        $res['refund_channel_'.$i] = $this->resHandler->getParameter('refund_channel_'.$i);
                        $res['refund_fee_'.$i] = $this->resHandler->getParameter('refund_fee_'.$i);
                        $res['coupon_refund_fee_'.$i] = $this->resHandler->getParameter('coupon_refund_fee_'.$i);
                        $res['refund_status_'.$i] = $this->resHandler->getParameter('refund_status_'.$i);
                    }*/
                    $res = $this->resHandler->getAllParameters();
                    $result['success'] = TRUE;
                    $result['msg'] = '查询退款信息成功';
                    $result['data'] = $res;
                } else {
                    $result['data'] = array(
                        'status' => 500,
                        'msg' => 'Error Code:' . $this->resHandler->getParameter('err_code')
                    );
                }
            }else {
                $result['data'] = array(
                    'status' => 500,
                    'msg' => $this->resHandler->getContent()
                );
            }
        } else {
            $result['data'] = array(
                'status' => 500,
                'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()
            );
        }

        return $result;
    }

    /**
     * 关闭订单接口
     * @param $out_trade_no 商户端订单编号
     */
    public function closeOrder($out_trade_no)
    {
        $result = array('success'=>TRUE, 'msg'=>'关闭订单失败', 'data'=>array());

        if (empty($out_trade_no)) {
            $result['msg'] = '请输入商户订单号';
            return $result;
        }

        $this->reqHandler->setParameter('out_trade_no', $out_trade_no);//商户订单号

        $this->reqHandler->setParameter('version', $this->_CI->config->item('sp_version'));//版本号
        $this->reqHandler->setParameter('service', 'unified.trade.close');//接口类型
        $this->reqHandler->setParameter('mch_id', $this->_CI->config->item('sp_mchid'));//必填项，商户号，由平台分配
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(), time() + rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if ($this->resHandler->isTenpaySign()) {
                $res = $this->resHandler->getAllParameters();
                $result['success'] = TRUE;
                $result['msg'] = '订单关闭成功';
                $result['data'] = $res;
            }else {
                $result['data'] = array(
                    'status' => 500,
                    'msg' => 'Error Code:' . $this->resHandler->getParameter('status') . ' Error Message:' . $this->resHandler->getParameter('message')
                );
            }
        } else {
            $result['data'] = array(
                'status' => 500,
                'msg' => 'Response Code:' . $this->pay->getResponseCode() . ' Error Info:' . $this->pay->getErrInfo()
            );
        }

        return $result;
    }

    /**
     * 后台异步通知处理
     */
    public function callback()
    {
        $result = array('success'=>FALSE, 'msg'=>'验签失败', 'data'=>array());

        $xml = file_get_contents('php://input');

//        file_put_contents('1.txt', $xml);//检测是否执行callback方法，如果执行，会生成1.txt文件，且文件中的内容就是通知参数
        $this->resHandler->setContent($xml);
        $this->resHandler->setKey($this->_CI->config->item('sp_key'));
        if ($this->resHandler->isTenpaySign()) {
            if ($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0) {
                //校验单号和金额是否一致，更改订单状态等业务处理
                $result['success'] = TRUE;
                $result['msg'] = '验签成功';
                $result['data'] = $this->resHandler->getAllParameters();
            } else {
                $result['msg'] = '订单状态不正确';
            }
        }

        return $result;
    }

    /**
     * 微信端统一下单，原生js支付api
     * @param int $openid  微信openid
     * @param int $out_trade_no  订单编号
     * @param int $total_fee  订单金额
     * @param string $callback_url  交易返回地址
     * @param string $attach  附加信息
     * @return array
     */
    public function jspaySubmitOrderInfo($openid = 0, $out_trade_no = 0, $total_fee = 0, $callback_url = '', $attach = '')
    {
        $result = array('success' => FALSE, 'msg' => '微信下单失败', 'data' => array());
        //接口类型（必填）
        $this->reqHandler->setParameter('service', 'pay.weixin.jspay');
        //版本号
        $this->reqHandler->setParameter('version', $this->_CI->config->item('sp_version'));
        //商户号（必填）
        $this->reqHandler->setParameter('mch_id', $this->_CI->config->item('sp_mchid'));
        //是否为原生态JS支付
        $this->reqHandler->setParameter('is_raw', '1');
        //订单编号（必填）
        $this->reqHandler->setParameter('out_trade_no', $out_trade_no);
        //商品描述（必填）
        $this->reqHandler->setParameter('body', "蓉锦医药网".$out_trade_no);
        //附加信息
        if (!empty($attach)) {
            $this->reqHandler->setParameter('attach', $attach);
        }
        //用户openid（必填）
        $this->reqHandler->setParameter('sub_openid', $openid);
        //公众号ID（必填）
        $this->reqHandler->setParameter('sub_appid', $this->_CI->config->item('wx_appid'));
        if ($total_fee < 0.01) {
            $result['msg'] = '订单金额最小为0.01';
            return $result;
        }
        //总金额（单位：分）（必填）
        $this->reqHandler->setParameter('total_fee', $total_fee * 100);
        //下单IP地址（必填）
        $this->reqHandler->setParameter('mch_create_ip', $this->_get_ip());
        //通知地址（必填）
        $this->reqHandler->setParameter('notify_url', 'http://www.sailwish.com/');
        //交易完成返回地址
        if (!empty($callback_url)) {
            $this->reqHandler->setParameter('callback_url', $callback_url);
        }
        //随机字符串（必填）
        $this->reqHandler->setParameter('nonce_str', mt_rand(time(),time()+rand()));
        //生成签名字符串
        $this->reqHandler->createSign();

        //将下单数据转换为xml格式
        $data = Utils::toXml($this->reqHandler->getAllParameters());
        //设置请求数据内容
        $this->pay->setReqContent($this->reqHandler->getGateURL(), $data);
        if ($this->pay->call()) {
            //获取请求结果数据
            $this->resHandler->setContent($this->pay->getResContent());
            //获取密钥
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    $result['success'] = TRUE;
                    $result['msg'] = '创建支付信息成功';
                    //原生JS支付返回参数
                    $result['data'] = array(
                        'token_id' => $this->resHandler->getParameter('token_id'),
                        'pay_info' => $this->resHandler->getParameter('pay_info')
                    );
                } else {
                    $result['data'] = array(
                        'status' => 500,
                        'msg' => 'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')
                    );
                }
                return $result;
            }
            $result['data'] = array(
                'status' => 500,
                'msg' => 'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')
            );
        } else {
            $result['data'] = array(
                'status' => 500,
                'msg' => 'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()
            );
        }

        return $result;
    }

    /**
     * 获取IP地址
     */
    private function _get_ip()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}