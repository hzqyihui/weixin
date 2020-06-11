<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_weixin.php
 *
 *     Description: 微信公用类库
 *
 *         Created: 2016-11-23 13:13:05
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */

class Jys_weixin {
    private $CI;

    public function __construct() {
        $this->CI = & get_instance();
    }

    /**
     * 在网页授权获取用户基本信息接口中
     * 根据code获取服务器信息包括网页授权accessToken和openID
     * @param $code 用户同意授权后，从微信服务器发来的CODE
     * @return 成功时返回结果数组，包括access_token,expires_in,refresh_token,openid,scope，失败时返回FALSE
     */
    public function get_oauth_access($code) {
        if (empty($code)) {
            return FALSE;
        }
        $appid = $this->CI->config->item('wx_appid');
        $secret = $this->CI->config->item('wx_secret');
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
        $result = $this->httpGetRequest($url);

        if (isset($result['errcode']) && intval($result['errcode']) != 0) {
            return FALSE;
        }else {
            return $result;
        }
    }

    /**
     * 在网页授权获取用户基本信息接口中
     * 根据网页授权access_token和openid获取用户信息
     * @param $access_token 网页授权access_token
     * @param $openid 用户openid
     * @return 成功时返回结果数组，其中包括openid,nickname,sex,province,city,country,headimgurl,privilege，失败时返回FALSE
     */
    public function get_user_info($access_token, $openid) {
        if (empty($access_token) || empty($openid)) {
            return FALSE;
        }
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $result = $this->httpGetRequest($url);

        if (isset($result['errcode']) && intval($result['errcode']) != 0) {
            return FALSE;
        }else {
            return $result;
        }
    }

    /**
     * 验证微信消息的合法性
     * @param $signature
     * @param $timestamp
     * @param $nonce
     * @return 成功时返回TRUE，失败时返回FALSE
     */
    public function check_signature ($signature, $timestamp, $nonce) {
        if (empty($signature) || empty($timestamp) || empty($nonce)) {
            return FALSE;
        }

        $token = $this->CI->config->item('wx_token');;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * 获取普通access_token
     * @return 获取成功返回消息数组，其中包括access_token,expires_in,update_time，获取失败返回FALSE
     */
    public function get_access() {
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
                    return $result;
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
            return $result;
        }
    }

    /**
     * 创建自定义菜单
     * @param $menus 以多维数组形式传入菜单的格式，具体参看微信文档
     * @return 返回执行结果数组，其中包括success,msg,errcode（当success为FALSE时）等参数
     */
    public function create_menu($menus = array()) {
        $result = array('success'=>FALSE, 'msg'=>'');
        if (!is_array($menus) || count($menus) < 1) {
            $result['msg'] = '请填写要创建的菜单';
            return $result;
        }
        $token = $this->get_access();

        if (empty($token) || (isset($token['errcode']) && intval($token['errcode']) != 0)) {
            $result['msg'] = '获取access_token失败';
            return $result;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token['access_token']}";
        $menu_result = $this->httpPostRequest($url, $menus);
        if (empty($menu_result) || (isset($menu_result['errcode']) && intval($menu_result['errcode']) != 0)) {
            $result['msg'] = $menu_result['errmsg'];
            $result['errcode'] = $menu_result['errcode'];
        }else {
            $result['success'] = TRUE;
        }
        return $result;
    }

    /**
     * 获取自定义菜单
     * @return 返回执行结果数组，其中包括success,msg,errcode（当success为FALSE时）,data(自定义菜单对应的数组)等参数，失败时返回FALSE
     */
    public function get_menu() {
        $token = $this->get_access();
        $result = array('success'=>FALSE, 'msg'=>'');
        if (empty($token) || (isset($token['errcode']) && intval($token['errcode']) != 0)) {
            $result['msg'] = '获取access_token失败';
            return $result;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$token['access_token']}";
        $menus = $this->httpGetRequest($url);
        if (isset($menus['errcode']) && intval($menus['errcode']) != 0) {
            $result['errcode'] = $menus['errcode'];
            $result['msg'] = $menus['errmsg'];
        }else {
            $result['success'] = TRUE;
            $result['data'] = $menus;
        }
        return $result;
    }

    /**
     * 被动回复用户消息，回复文本消息
     * @param $to_user_openid 接收方帐号（收到的OpenID）
     * @param $from_user_name 开发者微信号
     * @param $create 消息创建时间 （整型）
     * @param $content 回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
     * @return void
     */
    public function reply_text_message($to_user_openid, $from_user_name, $content) {
        if (empty($to_user_openid) || empty($from_user_name) || empty($content)) {
            return FALSE;
        }

        $create = time();
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
        $resultStr = sprintf($textTpl, $to_user_openid, $from_user_name, $create, $content);
        echo $resultStr;
    }

    /**
     * 发送模板消息
     * @param $touser 接受消息的用户的openid
     * @param $template_id 模板ID
     * @param $data 以键值对数组形式传入模板的数据
     * @param $url 模板详情页的URL
     * @return 返回执行结果数组，其中包括success,msg,errcode（当success为FALSE时）等参数
     */
    public function send_template_message($touser, $template_id, $data, $url = NULL) {
        $result = array('success'=>FALSE, 'msg'=>'', 'errcode'=>-2);
        if (empty($touser) || empty($template_id) || empty($data)) {
            return $result;
        }

        $post_data = array('touser'=>$touser, 'template_id'=>$template_id, 'data'=>$data);
        if (!empty($url)) {
            $post_data['url'] = $url;
        }
        $access_token = $this->get_access();
        if (empty($access_token)) {
            $result['msg'] = '获取access_token失败，无法发送模板消息！';
            return $result;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token['access_token']}";
        $send_result = $this->httpPostRequest($url, $post_data);
        if (empty($send_result) || (isset($send_result['errcode']) && intval($send_result['errcode']) != 0)) {
            $result['msg'] = $send_result['errmsg'];
            $result['errcode'] = $send_result['errcode'];
        }else {
            $result['success'] = TRUE;
        }
        return $result;
    }

    /**
     * 获取永久素材列表
     * @return 返回执行结果数组，其中包括success,msg,errcode（当success为FALSE时）,data(素材列表对应的数组)等参数，失败时返回FALSE
     */
    public function get_material_list() {
        $token = $this->get_access();
        $result = array('success'=>FALSE, 'msg'=>'');
        if (empty($token) || (isset($token['errcode']) && intval($token['errcode']) != 0)) {
            $result['msg'] = '获取access_token失败';
            return $result;
        }

        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$token['access_token']}";
        $data = array("type"=>"news", "offset"=>0, "count"=>20);
        $materials = $this->httpPostRequest($url, $data);
        if (isset($materials['errcode']) && intval($materials['errcode']) != 0) {
            $result['errcode'] = $materials['errcode'];
            $result['msg'] = $materials['errmsg'];
        }else {
            $result['data'] = $materials;
        }
        return $result;
    }

    /**
     * 将XML解析后的对象的元素转为string类型
     * 这里需要注意，经过xml_to_object转换得到的object的属性，虽然打印时是字符串，但其并非string类型，如果存数据库的话，必须进行转换
     */
    public function object_item_to_string($item) {
        $item = json_encode($item);
        $item = json_decode($item, TRUE);
        return $item[0];
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

    /**
     * 发起httpPOST请求
     * @param $url 请求的URL
     * @param $parameters 请求的参数，以数组形式传递
     */
    private function httpPostRequest($url, $parameters = array()) {
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this -> json_encode_ex($parameters));
        }
        // 执行请求动作，并获取结果
        $result = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        // 关闭CURL
        curl_close($ch);
        return json_decode($result, TRUE);
    }

    /**
     * 使用CURL从网络上下载文件
     * @param $filepath 文件路径，其中包括目录和文件名称
     * @param $url 要下载的文件的url
     * @return 成功返回文件保存的绝对路径，失败返回FALSE
     */
    public function curl_download($filepath, $url) {
        $path_parts = pathinfo($filepath);

        if (!file_exists($path_parts['dirname'])) {
            if (!mkdir($path_parts['dirname'], 0755, TRUE)) {

                return FALSE;
            }
        }

        // 获取文件绝对路径


        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);	// 设置资源URL
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);	// 设置支持重定向
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);	// 将curl_exec()获取的信息以文件流的形式返回，而不是直接输出
        //curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);	// 在发起连接前等待的时间，如果设置为0，则无限等待
        $file_content=curl_exec($ch);
        curl_close($ch);
        try {
            $fp=@fopen($filepath,'w');
            fwrite($fp,$file_content);
            fclose($fp);
        }catch (Exception $e) {
            file_put_contents(APPPATH."/logs/exception".date('Y-m-d').".log", date('Y-m-d H:i:s') . "日志信息：保存已下载的文件时出现异常，异常信息：{$e}\n", FILE_APPEND);
            return FALSE;
        }
        $filepath = realpath($filepath);
        return $filepath;
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
}
