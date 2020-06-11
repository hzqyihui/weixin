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
require_once FCPATH . "application/third_party/aliyunsms/vendor/autoload.php";

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();

class Jys_message
{
    private $_CI;
    private $_access_key;
    private $_access_key_secret;
    private $_signature;
    public function __construct()
    {
        $this->_CI = & get_instance();
        $this->_access_key = $this->_CI->config->item('aliyun_sms_access_key');
        $this->_access_key_secret = $this->_CI->config->item('aliyun_sms_access_key_secret');
        $this->_signature = $this->_CI->config->item('aliyun_sms_signature');

        // 短信API产品名
        $product = "Dysmsapi";
        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        // 暂时不支持多Region
        $region = "cn-hangzhou";
        // 服务结点
        $endPointName = "cn-hangzhou";
        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $this->_access_key, $this->_access_key_secret);
        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
        // 初始化AcsClient用于发起请求
        $this->acsClient = new DefaultAcsClient($profile);
    }

    /**
     * 短信发送接口
     * @param $phone
     * @param $message
     * @return mixed
     */
    public function send_message($phoneNumbers, $templateCode, $templateParam = null)
    {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置雉短信接收号码
        $request->setPhoneNumbers($phoneNumbers);

        // 必填，设置签名名称
        $request->setSignName($this->_signature);

        // 必填，设置模板CODE
        $request->setTemplateCode($templateCode);

        // 可选，设置模板参数
        if($templateParam) {
            $request->setTemplateParam(json_encode($templateParam));
        }

        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);

        // 打印请求结果
//         var_dump($acsResponse);

        $result = array('success'=>FALSE, 'error'=>"");
        if ($acsResponse->Code == "OK") {
            $result['success'] = TRUE;
            $result['error'] = "";
            $result['RequestId'] = $acsResponse->RequestId;
            $result['Message'] = $acsResponse->Message;
            $result['BizId'] = $acsResponse->BizId;
            $result['Code'] = $acsResponse->Code;
        }else {
            $result['success'] = FALSE;
            $result['error'] = $acsResponse->Message;
        }

        return $result;
    }
}