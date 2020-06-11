<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename: Jys_message.php
 *
 *     Description: 短信发送类库
 *
 *         Created: 2017-1-17 11:20
 *
 *          Author: tangyu
 *
 * =====================================================================================
 */
class Jys_message
{
    private $_CI;
    private $_account;
    private $_password;

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_account = $this->_CI->config->item('253_account');
        $this->_password = $this->_CI->config->item('253_password');
    }

    /**
     * 短信发送接口
     * @param string $mobile 手机号码
     * @param string $msg 短信内容
     * @param string $needstatus 是否需要状态报告
     */
    public function send_message($phone, $message, $needstatus = 'true')
    {
        $url = "http://smssh1.253.com/msg/send/json";
        //创蓝接口参数
        $postArr = array(
            'account' => $this->_account,
            'password' => $this->_password,
            'msg' => urlencode($message),
            'phone' => $phone,
            'report' => $needstatus
        );

        $result = $this->_curlPost($url, $postArr);
        return $result;
    }

    /**
     * 发送变量短信
     *
     * @param string $msg 短信内容
     * @param string $params 最多不能超过1000个参数组
     */
    public function send_variable_message($msg, $params)
    {
        $url = "http://smssh1.253.com/msg/variable/json";

        //创蓝接口参数
        $postArr = array(
            'account' => $this->_account,
            'password' => $this->_password,
            'msg' => $msg,
            'params' => $params,
            'report' => 'true'
        );

        $result = $this->_curlPost($url, $postArr);
        return $result;
    }

    /**
     * 查询额度
     *
     *  查询地址
     */
    public function queryBalance()
    {
        $url = "http://smssh1.253.com/msg/balance/json";

        //查询参数
        $postArr = array(
            'account' => $this->_account,
            'password' => $this->_password
        );
        $result = $this->_curlPost($url, $postArr);
        return $result;
    }

    /**
     * 通过CURL发送HTTP请求
     * @param string $url //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     */
    private function _curlPost($url, $postFields)
    {
        $postFields = json_encode($postFields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec($ch);
        if (false == $ret) {
            $result = curl_error($ch);
        } else {
            $rsp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 " . $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close($ch);
        return $result;
    }
}