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
}