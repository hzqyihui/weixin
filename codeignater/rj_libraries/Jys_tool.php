<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_tool.php
 *
 *     Description: 工具类
 *
 *         Created: 2017-1-19 10:58:51
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Jys_tool{
    
    public function unicode_encode($name){
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2)
        {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0)
            {    // 两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
            }
            else
            {
                $str .= $c2;
            }
        }
        return $str;
    }

    /**
     * 对发给太平的数据进行签名
     * @param $data 要签名的json字符串
     * @return string
     */
    public function taiping_sign($data) {
        $taiping_key_path = APPPATH.'libraries/taiping_private_key.pem';

        $key = openssl_pkey_get_private(file_get_contents($taiping_key_path));

        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $sign = "";
        $res = openssl_get_privatekey($key);
        openssl_sign(base64_encode($data), $sign, $res, OPENSSL_ALGO_SHA1);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 发起httpPOST请求
     * @param $url 请求的URL
     * @param $parameters 请求的参数，以数组形式传递
     */
    public function http_post_request($url, $parameters = array()) {
        if (empty($url)) {
            return FALSE;
        }
        // 初始化CURL
        $ch = curl_init();
        // 设置要请求的URL
        curl_setopt($ch, CURLOPT_URL, $url);
        // 设置不显示头部信息
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        // 设置不将请求结果直接输出在标准输出里，而是返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // 设置本地不检测SSL证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置post方式提交
        curl_setopt($ch, CURLOPT_POST, TRUE);
        // 设置请求参数
        if (!empty($parameters)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        }
        // 执行请求动作，并获取结果
        $result = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        // 关闭CURL
        curl_close($ch);
        return $result;
    }

    /**
     * 对内容进行json编码，并且保持汉字不会被编码
     * @param $value 被编码的对象
     * @return 编码结果字符串
     */
    public function json_encode_ex($value) {
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

    /**
     * 写日志函数
     * @param $content
     * @param $filename
     */
    public function write_log($content, $filename) {
        $filepath = APPPATH."/logs/".date('Y-m-d').$filename;

        file_put_contents($filepath, $content."\n", FILE_APPEND);
    }

    /**
     * Generates an UUID
     *
     * @author     Anis uddin Ahmad
     * @param      string  an optional prefix
     * @return     string  the formatted uuid
     */
    public function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr($chars, 0, 8) . '-';
        $uuid .= substr($chars, 8, 4) . '-';
        $uuid .= substr($chars, 12, 4) . '-';
        $uuid .= substr($chars, 16, 4) . '-';
        $uuid .= substr($chars, 20, 12);
        return $prefix . $uuid;
    }

    /**
     * 获取客户端浏览器信息 添加win10 edge浏览器判断
     * @param  null
     * @author  Jea杨
     * @return string
     */
    public function get_broswer()
    {
        $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];  //获取火狐浏览器的版本号
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];  //获取IE的版本号
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif (stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];  //获取google chrome的版本号
        } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
            preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        } else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        return $exp[0] . '(' . $exp[1] . ')';
    }

    /**
     * 获取客户端操作系统信息包括win10
     * @param  null
     * @author  Jea杨
     * @return string
     */
    public function get_os()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $os = false;

        if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
            $os = 'Windows 95';
        } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
            $os = 'Windows ME';
        } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
            $os = 'Windows 98';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
            $os = 'Windows Vista';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
            $os = 'Windows 7';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
            $os = 'Windows 8';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
            $os = 'Windows 10';#添加win10判断
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
            $os = 'Windows XP';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
            $os = 'Windows 2000';
        } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
            $os = 'Windows NT';
        } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
            $os = 'Windows 32';
        } else if (preg_match('/linux/i', $agent)) {
            $os = 'Linux';
        } else if (preg_match('/unix/i', $agent)) {
            $os = 'Unix';
        } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'SunOS';
        } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
            $os = 'IBM OS/2';
        } else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
            $os = 'Macintosh';
        } else if (preg_match('/PowerPC/i', $agent)) {
            $os = 'PowerPC';
        } else if (preg_match('/AIX/i', $agent)) {
            $os = 'AIX';
        } else if (preg_match('/HPUX/i', $agent)) {
            $os = 'HPUX';
        } else if (preg_match('/NetBSD/i', $agent)) {
            $os = 'NetBSD';
        } else if (preg_match('/BSD/i', $agent)) {
            $os = 'BSD';
        } else if (preg_match('/OSF1/i', $agent)) {
            $os = 'OSF1';
        } else if (preg_match('/IRIX/i', $agent)) {
            $os = 'IRIX';
        } else if (preg_match('/FreeBSD/i', $agent)) {
            $os = 'FreeBSD';
        } else if (preg_match('/teleport/i', $agent)) {
            $os = 'teleport';
        } else if (preg_match('/flashget/i', $agent)) {
            $os = 'flashget';
        } else if (preg_match('/webzip/i', $agent)) {
            $os = 'webzip';
        } else if (preg_match('/offline/i', $agent)) {
            $os = 'offline';
        } else {
            $os = '未知操作系统';
        }
        return $os;
    }

    /**
     * 递归处理从中间库接受到的数据
     */
    public function deal_center_data($array = []) 
    {
        if (empty($array)) {
            return FALSE;
        }
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->deal_center_data($value);
            }else{
                $array[$key] = trim($value);
            }
        }
        return $array;
    }
}