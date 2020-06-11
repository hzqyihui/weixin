<?php
/**
 *       Filename: Lab_message.php
 *
 *    Description: 短信类库
 *
 *        Created: 2017-04-27 19:16
 *
 *         Author: huazhiqiang
 */
namespace App\Libraries;

class Lab_message{
    /**
     * 发送短信
     * @param $mobile
     * @param $valid_code
     * @param int $tag
     * @return mixed
     */
    public function sendTextMessages($mobile, $content, $tag = 2){
        if (empty($content)){
            return FALSE;
        }

        $content = urlencode($content);

        $ch = curl_init();
        $url = 'http://apis.baidu.com/kingtto_media/106sms/106sms?mobile='.$mobile.'&content='.$content.'&tag='.$tag;
        $header = array(
            'apikey: d7fa7fa440daa6f92e3cded9e072b8a3',
        );
        // 添加apikey到header
        curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 执行HTTP请求
        curl_setopt($ch , CURLOPT_URL , $url);
        $res = curl_exec($ch);

        $res = json_decode($res);
        return $res;
    }
}

