<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_weixin_jssdk.php
 *
 *     Description: 微信jssdk
 *
 *         Created: 2016-11-23 13:13:10
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Jys_weixin_jssdk {
    private $CI;
    private $appId;
    private $appSecret;

    public function __construct() {
        $this -> CI = &get_instance();
        $this -> appId = $this -> CI -> config -> item('wx_appid');
        $this -> appSecret = $this -> CI -> config -> item('wx_secret');
    }

    public function getSignPackage() {
        $jsapiTicket = $this -> getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this -> createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array("appId" => $this -> appId, "nonceStr" => $nonceStr, "timestamp" => $timestamp, "url" => $url, "signature" => $signature, "rawString" => $string);
        return $signPackage;
    }

    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $jsapi_ticket_file_path = APPPATH."/libraries/jsapi_ticket.log";
        $data = json_decode($this -> get_php_file($jsapi_ticket_file_path));
        if ($data -> expire_time < time()) {
            $accessToken = $this -> getAccessToken();
            // 如果是企业号用以下 URL 获取 ticket
            // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode(json_encode($this -> httpGetRequest($url)));
            $ticket = $res -> ticket;
            if ($ticket) {
                $data -> expire_time = time() + 7000;
                $data -> jsapi_ticket = $ticket;
                $this -> set_php_file($jsapi_ticket_file_path, json_encode($data));
            }
        } else {
            $ticket = $data -> jsapi_ticket;
        }

        return $ticket;
    }

    private function getAccessToken() {
        $access_log_file_path = APPPATH."/libraries/access_token.log";
        $result = FALSE;
        try {
            // 防止因为没有write权限报错而使程序停止不前
            if (!file_exists($access_log_file_path)) {
                file_put_contents($access_log_file_path, "");
            }
            // 检查本地缓存的access_token是否过期
            $result = json_decode(file_get_contents($access_log_file_path), TRUE);
            if (!is_array($result) || isset($result['access_token'])) {
                $now = time();
                if (!empty($result['access_token']) && $now - intval($result['update_time']) < intval($result['expires_in'])) {
                    return $result['access_token'];
                }
            }
        }catch (Exception $e) {
            // 出现异常就说明本地文件读写出现问题，直接请求网络，获取最新的access_token
        }

        $appid = $this->CI->config->item('wx_appid');
        $secret = $this->CI->config->item('wx_secret');

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
        $result = $this->httpGetRequest($url);

        if (isset($result['errcode']) && intval($result['errcode']) != 0) {
            return FALSE;
        }else {
            $result['update_time'] = time();
            try{
                // 本地缓存获得的access_token
                file_put_contents($access_log_file_path, json_encode(array('access_token'=>$result['access_token'], 'expires_in'=>$result['expires_in'], 'update_time'=>$result['update_time'])));
            }catch(Exception $e) {
                // 出现异常就说明本地文件write出现问题
            }
            return $result['access_token'];
        }
    }

    /**
     * 发起httpGET请求
     * @param $url 请求的URL
     * @param $parameters 请求的参数，以数组形式传递
     */
    private function httpGetRequest($url, $parameters = NULL) {
        if (empty($url)) {
            return FALSE;
        }
        // 将请求参数追加在url后面
        if (!empty($parameters) && is_array($parameters) && count($parameters)) {
            $is_first = TRUE;
            foreach ($parameters as $key => $value) {
                if ($is_first) {
                    $url .= "?" . $key . "=" . urlencode($value);
                    $is_first = FALSE;
                } else {
                    $url .= "&" . $key . "=" . urlencode($value);
                }
            }
        }
        //初始化CURL
        $ch = curl_init();
        // 设置要请求的URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置不显示头部信息
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // 设置本地不检测SSL证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // 执行请求动作，并获取结果
        $result = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        // 关闭CURL
        curl_close($ch);
        return json_decode($result, TRUE);
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    private function get_php_file($filename) {
        try {
            // 防止因为没有write权限报错而使程序停止不前
            if (!file_exists($filename)) {
                file_put_contents($filename, "<?php exit();?>\n{\"jsapi_ticket\":\"\",\"expire_time\":0}");
            }
            return trim(substr(file_get_contents($filename), 15));
        }catch (Exception $e) {

        }

        return trim(substr(file_get_contents($filename), 15));
    }

    private function set_php_file($filename, $content) {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }

}
