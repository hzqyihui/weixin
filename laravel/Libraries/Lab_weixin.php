<?php
/**
 *       Filename: Lab_weixin.php
 *
 *    Description: 微信类库
 *
 *        Created: 2017-04-29 20:16
 *
 *         Author: huazhiqiang
 */
namespace App\Libraries;

use Illuminate\Http\Request;

class Lab_weixin{
    private $key = '';
    private $pc = '';
    private $corpid = '';
    private $aesKey = '';
    private $token = '';
    private $corpSecret = '';
    private $accessToken = '';
    public function __construct()
    {
        $this->corpid = config('app.wx_corpid');
        $this->aesKey = config('app.wx_aeskey');
        $this->token = config('app.wx_token');
        $this->corpSecret = config('app.wx_secret');
        $this->pc = new Lab_wx_encrypt($this->aesKey);
        $this->accessToken = $this->getAccessToken($this->corpid,$this->corpSecret);
    }

    /*
	*验证URL
    *@param sMsgSignature: 签名串，对应URL参数的msg_signature
    *@param sTimeStamp: 时间戳，对应URL参数的timestamp
    *@param sNonce: 随机串，对应URL参数的nonce
    *@param sEchoStr: 随机串，对应URL参数的echostr
    *@param sReplyEchoStr: 解密之后的echostr，当return返回0时有效
    *@return：成功0，失败返回对应的错误码
	*/
    public function VerifyURL($sMsgSignature, $sTimeStamp, $sNonce, $sEchoStr, &$sReplyEchoStr)
    {
        if (strlen($this->aesKey) != 43) {
            return Lab_wx_errcode::$IllegalAesKey;
        }

        //验证msg_signature
        $array = $this->pc->getSHA1($this->token, $sTimeStamp, $sNonce, $sEchoStr);
        $ret = $array[0];

        if ($ret != 0) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature != $sMsgSignature) {
            return Lab_wx_errcode::$ValidateSignatureError;
        }

        $result = $this->pc->decrypt($sEchoStr, $this->corpid);
        if ($result[0] != 0) {
            return $result[0];
        }
        $sReplyEchoStr = $result[1];

        return Lab_wx_errcode::$OK;
    }

    /**
     * 创建成员
     * @param $userid 成员UserID。对应管理端的帐号，企业内必须唯一。长度为1~64个字节，必填
     * @param $name 成员名称。长度为1~64个字节，必填
     * @param $departmentid 成员所属部门id列表。注意，每个部门的直属成员上限为1000个
     * @param $mobile 手机号码。企业内必须唯一，mobile/weixinid/email三者不能同时为空
     * @param $email 邮箱。长度为0~64个字节。企业内必须唯一，mobile/weixinid/email三者不能同时为空
     * @param $weixinid 微信号。企业内必须唯一。（注意：是微信号，不是微信的名字）
     * @param $position 职位信息。长度为0~64个字节
     * @param $gender 性别。1表示男性，2表示女性
     * @param $avatar_mediaid 成员头像的mediaid，通过多媒体接口上传图片获得的mediaid
     * @param $extattr 扩展属性。扩展属性需要在WEB管理端创建后才生效，否则忽略未知属性的赋值，以数组形式传入
     */
    public function createUser($userid, $name, $departmentid, $mobile = FALSE, $email = FALSE, $weixinid = FALSE, $position = FALSE,
                               $gender = FALSE, $avatar_mediaid = FALSE, $extattr = array()) {
        if (empty($userid) || empty($name) || empty($departmentid)) {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Userid or name or departmentid is empty!', 'errcode'=>-2));
        }

        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token={$this->accessToken}";
        $data = array('userid'=>$userid, 'name'=>$name, 'department'=>$departmentid);
        if (!empty($mobile)) {
            $data['mobile'] = $mobile;
        }
        if (!empty($email)) {
            $data['email'] = $email;
        }
        if (!empty($weixinid)) {
            $data['weixinid'] = $weixinid;
        }
        if (!empty($position)) {
            $data['position'] = $position;
        }
        if (!empty($gender)) {
            $data['gender'] = $gender;
        }
        if (!empty($avatar_mediaid)) {
            $data['avatar_mediaid'] = $avatar_mediaid;
        }
        if (!empty($extattr) && is_array($extattr)) {
            $temp =  array();
            foreach ($extattr as $key => $value) {
                $temp[] = array('name'=>$key, 'value'=>$value);
            }
            $data['extattr'] = array('attrs'=>$temp);
        }

        $result = $this->httpRequest($url, $data, 'post');
        if ($result) {
            if ($result['errcode'] == 0) {
                $result['success'] = TRUE;
                $result['userid'] = $userid;
                $result['name'] = $name;
                //$this->inviteConcern($userid);
                return json_encode($result);
            }else {
                $result['success'] = FALSE;
                return json_encode($result);
            }
        }else {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Create a user fails!', 'errcode'=>-2));
        }
    }

    /**
     * 批量删除成员
     * @param $useridlist 成员UserID列表。  形如["zhangsan", "lisi"]的一维数组
     */
    public function batchDeleteUsers($useridlist) {
        if (empty($useridlist)) {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Useridlist is empty!', 'errcode'=>-2));
        }else if (!is_array($useridlist)) {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Useridlist not an array!', 'errcode'=>-2));
        }

        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/batchdelete?access_token={$this->accessToken}";
        $data = array('useridlist'=>$useridlist);

        $result = $this->httpRequest($url, $data, 'post');
        if ($result) {
            if ($result['errcode'] == 0) {
                $result['success'] = TRUE;
                return json_encode($result);
            }else {
                $result['success'] = FALSE;
                return json_encode($result);
            }
        }else {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Batch delete users fails!', 'errcode'=>-2));
        }
    }

    /**
     * 删除成员
     * @param $userid 成员UserID。对应管理端的帐号，企业内必须唯一。长度为1~64个字节，必填
     */
    public function deleteUser($userid) {
        if (empty($userid)) {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Userid is empty!', 'errcode'=>-2));
        }

        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/delete";
        $data = array('access_token'=>$this->accessToken, 'userid'=>$userid);

        $result = $this->httpRequest($url, $data);
        if ($result) {
            if ($result['errcode'] == 0) {
                $result['success'] = TRUE;
                $result['userid'] = $userid;
                return json_encode($result);
            }else {
                $result['success'] = FALSE;
                return json_encode($result);
            }
        }else {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Delete a user fails!', 'errcode'=>-2));
        }
    }

    /**
     * 邀请成员关注企业号
     * 认证号优先使用微信推送邀请关注，如果没有weixinid字段则依次对手机号，邮箱绑定的微信进行推送，全部没有匹配则通过邮件邀请关注。 邮箱字段无效则邀请失败。 非认证号只通过邮件邀请关注。邮箱字段无效则邀请失败。 已关注以及被禁用成员不允许发起邀请关注请求。
     * 为避免骚扰成员，企业应遵守以下邀请规则：
     * 每月邀请的总人次不超过成员上限的2倍；每7天对同一个成员只能邀请一次。
     * @param $userid 成员UserID。对应管理端的帐号
     */
    public function inviteConcern($userid) {
        if (empty($userid)) {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Invite concern failure!', 'errcode'=>-2));
        }

        $url = "https://qyapi.weixin.qq.com/cgi-bin/invite/send?access_token={$this->accessToken}";
        $data = array('userid'=>$userid);

        $result = $this->httpRequest($url, $data, 'post');
        if ($result) {
            if ($result['errcode'] == 0) {
                $result['success'] = TRUE;
                $result['userid'] = $userid;
                if ($result['type'] == 1) {
                    $result['result'] = '已发出微信邀请';
                }else if ($result['type'] == 2) {
                    $result['result'] = '已发出邮件邀请';
                }else {
                    $result['result'] = '已发出邀请';
                }
                return json_encode($result);
            }else {
                $result['success'] = FALSE;
                return json_encode($result);
            }
        }else {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Invite concern failure!', 'errcode'=>-2));
        }
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
     * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
     * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
     *                      当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function encryptMsg($sReplyMsg, $sTimeStamp, $sNonce, &$sEncryptMsg)
    {
        //加密
        $array = $this->pc->encrypt($sReplyMsg, $this->corpid);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }

        if ($sTimeStamp == null) {
            $sTimeStamp = time();
        }
        $encrypt = $array[1];

        //生成安全签名
        $array = $this->pc->getSHA1($this->token, $sTimeStamp, $sNonce, $encrypt);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }
        $signature = $array[1];

        //生成发送的xml
        $sEncryptMsg = $this->pc->generate($encrypt, $signature, $sTimeStamp, $sNonce);
        return Lab_wx_errcode::$OK;
    }


    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *    <li>利用收到的密文生成安全签名，进行签名验证</li>
     *    <li>若验证通过，则提取xml中的加密消息</li>
     *    <li>对消息进行解密</li>
     * </ol>
     *
     * @param $msgSignature string 签名串，对应URL参数的msg_signature
     * @param $timestamp string 时间戳 对应URL参数的timestamp
     * @param $nonce string 随机串，对应URL参数的nonce
     * @param $postData string 密文，对应POST请求的数据
     * @param &$msg string 解密后的原文，当return返回0时有效
     *
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptMsg($sMsgSignature, $sTimeStamp = null, $sNonce, $sPostData, &$sMsg)
    {
        if (strlen($this->aesKey) != 43) {
            return Lab_wx_errcode::$IllegalAesKey;
        }

        //提取密文
        $array = $this->pc->extract($sPostData);
        $ret = $array[0];

        if ($ret != 0) {
            return $ret;
        }

        if ($sTimeStamp == null) {
            $sTimeStamp = time();
        }

        $encrypt = $array[1];
        $touser_name = $array[2];

        //验证安全签名
        $array = $this->pc->getSHA1($this->token, $sTimeStamp, $sNonce, $encrypt);
        $ret = $array[0];

        if ($ret != 0) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature != $sMsgSignature) {
            return Lab_wx_errcode::$ValidateSignatureError;
        }

        $result = $this->pc->decrypt($encrypt, $this->corpid);
        if ($result[0] != 0) {
            return $result[0];
        }
        $sMsg = $result[1];

        return Lab_wx_errcode::$OK;
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
     * 主动发送文字消息
     * @param $agent_id 企业应用的id，整型。可在应用的设置页面查看
     * @param $content 消息内容
     * @param $to_user 成员ID列表，一维数组传递['1', '2', ...]，最多1000个，默认发送给全部成员
     * @param $to_party 部门ID列表，一维数组传递['1', '2', ...]，最多1000个
     * @param $to_tag 标签ID列表，一维数组传递['1', '2', ...]，最多1000个
     * @param $safe 表示是否是保密消息，0表示否，1表示是，默认0
     */
    public function sendText($agent_id, $content, $to_user = "@all", $to_party = array(), $to_tag = array(), $safe = 0) {
        if (intval($agent_id) < 0 || empty($content)) {
            return json_encode(array('success' => FALSE, 'errmsg' => '应用ID或发送内容为空', 'errcode' => -2));
        }
        $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$this->accessToken}";
        $data = array('agentid' => $agent_id, 'msgtype' => "text", 'text' => array('content' => $content));
        $data['touser'] = $this->getToUserList($to_user);
        if (($tmp = $this->getToList($to_party)) != FALSE) {
            $data['toparty'] = $tmp;
        }
        if (($tmp = $this->getToList($to_tag)) != FALSE) {
            $data['totag'] = $tmp;
        }
        if (intval($safe) == 1) {
            $data['safe'] = 1;
        }

        $result = $this -> httpRequest($url, $data, 'post');
        if ($result) {
            if ($result['errcode'] == 0) {
                $result['success'] = TRUE;
                return json_encode($result);
            } else {
                $result['success'] = FALSE;
                return json_encode($result);
            }
        } else {
            return json_encode(array('success' => FALSE, 'errmsg' => '发送失败!', 'errcode' => -2));
        }
    }

    /**
     * 公众号被动回复文本消息
     * @param  $content string 用户自定义的回复内容
     * @return $resultStr 公众号接收到用户数据后返回给用户的xml数据
     */
    public function replyText($postarr,$content){
        libxml_disable_entity_loader(true);
        $result = simplexml_load_string($postarr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $createtime = time();
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					</xml>";
        $resultstr = sprintf($textTpl, $result->FromUserName, $result->ToUserName, $createtime, $content);
        return $resultstr;
    }

    /**
     * 获取AccessToken
     * @param $corpid 企业ID
     * @param $corpsecret 管理组的凭证密钥
     */
    public function getAccessToken($corpid = FALSE, $corpsecret = FALSE) {
        if (empty($corpid) || empty($corpsecret)) {
            return FALSE;
        }

        $result = $this->getLocalAccessToken($corpid, $corpsecret);
        if (empty($result)) {
            $result = $this->getRemoteAccessToken($corpid, $corpsecret);
            if (empty($result)) {
                return FALSE;
            }else {
                return $result;
            }
        }else {
            return $result;
        }
    }

    /**
     * 获取存储在本地的AccessToken
     * @param $corpid 企业ID
     * @param $corpsecret 管理组的凭证密钥
     */
    private function getLocalAccessToken($corpid = FALSE, $corpsecret = FALSE) {
        if (empty($corpid) || empty($corpsecret)) {
            return FALSE;
        }

        return $this->getAccessTokenByLocal($corpid, $corpsecret);
    }

    /**
     * 从服务器上获取AccessToken
     * @param $corpid 企业ID
     * @param $corpsecret 管理组的凭证密钥
     */
    private function getRemoteAccessToken($corpid = FALSE, $corpsecret = FALSE) {
        if (empty($corpid) || empty($corpsecret)) {
            return FALSE;
        }

        $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken";
        $data = array('corpid'=>$corpid, 'corpsecret'=>$corpsecret);

        $result = $this->httpRequest($url, $data);
        if (isset($result['access_token'])) {
            $this->saveAccessToken($corpid, $corpsecret, $result['access_token']);
            return $result['access_token'];
        }else {
            //echo $result['errcode'].":".$result['errmsg'];
            return FALSE;
        }
    }

    /**
     * 发起http请求
     * @param $url 请求的URL
     * @param $parameters 请求的参数
     * @param $method 请求的方法，只能是get或post
     */
    public function httpRequest($url, $parameters = NULL, $method = 'get') {
        $method = strtolower($method);
        switch ($method) {
            case 'get' :
                return $this -> httpGetRequest($url, $parameters);
                break;
            case 'post' :
                return $this -> httpPostRequest($url, $parameters);
                break;
            default :
                return FALSE;
                break;
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->json_encode_ex($parameters));
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
     * 使用POST请求上传文件
     */
    public function uploadFileByPost($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        if ($error = curl_error($ch)) {
            die($error);
        }
        curl_close($ch);

        return json_decode($result, TRUE);
    }

    /**
     * 用CURL发起一个HTTP请求
     * @param $url 访问的URL
     * @param $post post数据(不填则为GET)
     * @param $cookie 提交的$cookies
     * @param $returnCookie 是否返回$cookies
     */
    public function curlRequest($url, $post = '', $cookie = '', $returnCookie = 0) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if ($returnCookie) {
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
            $info['cookie'] = substr($matches[1][0], 1);
            $info['content'] = $body;
            return $info;
        } else {
            return $data;
        }
    }

    /**
     * 保存从网络上获取到的AccessToken
     * @param $corpid 企业ID
     * @param $corpsecret 管理组的凭证密钥
     * @param $token 从网络上获取到的AccessToken
     */
    public function saveAccessToken($corpid, $corpsecret, $token) {
        if (empty($corpid) || empty($corpsecret) || empty($token)) {
            return FALSE;
        }
        if (!file_exists(dirname(__FILE__) . '/token.bin')) {
            file_put_contents(dirname(__FILE__) . '/token.bin', "");
        }

        $result = file_get_contents(dirname(__FILE__) . '/token.bin');

        $result = json_decode($result, TRUE);
        $key = $corpid . $corpsecret;
        if (empty($result)) {
            $result = array($key => array($token, time()));
        } else {
            $result[] = array($key => array($token, time()));
        }

        if (file_put_contents(dirname(__FILE__) . '/token.bin', json_encode($result))) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 获取从网络上获取到的AccessToken
     * @param $corpid 企业ID
     * @param $corpsecret 管理组的凭证密钥
     * @return bool 当前企业ID和管理组的凭证密钥对应的AccessToken，没有则返回false
     */
    public function getAccessTokenByLocal($corpid, $corpsecret) {
        if (empty($corpid) || empty($corpsecret)) {
            return FALSE;
        }

        if (!file_exists(dirname(__FILE__) . '/token.bin')) {
            return FALSE;
        }

        $result = file_get_contents(dirname(__FILE__) . '/token.bin');
        if (empty($result)) {
            return FALSE;
        }

        $result = json_decode($result, TRUE);
        $key = $corpid . $corpsecret;
        if (isset($result[$key])) {
            if (time() - 7200 > $result[$key][1]) {
                // token已超时
                return FALSE;
            } else {
                // token未超时
                return $result[$key][0];
            }
        } else {
            return FALSE;
        }
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
     * 根据传入的数组，返回微信需要的列表格式字符串
     * @param $to_list 一维数组传递['1', '2', ...]，最多1000个
     */
    private function getToList($to_list = array()) {
        $data = "";
        if (!empty($to_list) && is_array($to_list)) {
            $first = TRUE;
            foreach ($to_list as $value) {
                if ($first) {
                    $data = $value;
                    $first = FALSE;
                } else {
                    $data .= "|" . $value;
                }
            }
        }else {
            $data = FALSE;
        }
        return $data;
    }

    /**
     * 根据成员ID列表返回微信需要的成员列表格式字符串
     * @param $to_user 成员ID列表，一维数组传递['1', '2', ...]，最多1000个，默认发送给全部成员
     */
    private function getToUserList($to_user = "@all") {
        $data = "";
        if (is_array($to_user)) {
            $first = TRUE;
            foreach ($to_user as $value) {
                if ($first) {
                    $data = $value;
                    $first = FALSE;
                } else {
                    $data .= "|" . $value;
                }
            }
        } else {
            $data = $to_user;
        }
        return $data;
    }

    /**
     * 根据部门ID获取用户列表
     * @param $token 微信AccessToken
     * @param $department_id 部门ID
     * @param $fetch_child 1/0：是否递归获取子部门下面的成员
     * @param $status 0获取全部成员，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加
     */
    public function getUserList($token, $department_id = 1, $fetch_child = 0, $status = 0) {
        if (intval($department_id) < 1) {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Department_id must be greater than zero!', 'errcode'=>-2, 'userlist'=>array()));
        }

        $url = "https://qyapi.weixin.qq.com/cgi-bin/user/simplelist";
        $data = array('access_token'=>$token, 'department_id'=>$department_id);
        if (intval($fetch_child) > -1) {
            $data['fetch_child'] = $fetch_child;
        }
        if (intval($status) > -1) {
            $data['status'] = $status;
        }

        $result = $this->httpRequest($url, $data);
        if ($result) {
            if ($result['errcode'] == 0) {
                $result['success'] = TRUE;
                return json_encode($result);
            }else {
                $result['success'] = FALSE;
                return json_encode($result);
            }
        }else {
            return json_encode(array('success'=>FALSE, 'errmsg'=>'Query fails!', 'errcode'=>-2, 'userlist'=>array()));
        }
    }
}