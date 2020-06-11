<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  mp_weixin.php
 *
 *     Description:  微信SDK类库
 *
 *         Created:  2016-05-09 15:46:32
 *
 *          Author:  huazhiqiang
 *
 * =====================================================================================
 */

/**
 * 微信SDK接口
 */
Class Swcomp_mp_weixin {

	//消息类型常量
	const MSG_TYPE_TEXT       	 = 'text';
    const MSG_TYPE_IMAGE      	 = 'image';
    const MSG_TYPE_VOICE     	 = 'voice';
    const MSG_TYPE_VIDEO     	 = 'video';
	const MSG_TYPE_SHORT_VIDEO   = 'shortvideo';
	const MSG_TYPE_LOCATION    	 = 'location';
	const MSG_TYPE_LINK		     = 'link';
    const MSG_TYPE_EVENT      	 = 'event';

    //事件类型常量
    const MSG_EVENT_SUBSCRIBE   = 'subscribe';
    const MSG_EVENT_CLICK       = 'CLICK';
    const MSG_EVENT_VIEW        = 'VIEW';
	const MSG_EVENT_SCAN        = 'SCAN';
	const MSG_EVENT_UNSUBSCRIBE = 'unsubscribe';
	

	private $_CI;

	/**
	 * 构造函数
	 */
	public function __construct(){
		$this->_CI = &get_instance();
	}

	/**
	 * 微信接入验证
	 */
	public function valid(){
		$signature = $this->_CI->input->get('signature');
		$timestamp = $this->_CI->input->get('timestamp');
		$nonce = $this->_CI->input->get('nonce');
		
		$token = $this->_CI->config->item('wx_token');
		$tmp_arr = array($token, $timestamp, $nonce);

		//按字典序排序
		sort($tmp_arr, SORT_STRING);

		//拼接成字符串并sha1加密
		$str = sha1(implode($tmp_arr));

		//进行signature校验
		if ($str == $signature){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * 接受微信端的信息
	 * @return object 微信接口对象
	 */
	public function receive_message(){
		$post = file_get_contents('php://input');

		if (!$post){
			return FALSE;
		}

		return $post;
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
	 * 对用户发送的消息进行处理
	 * @param $postarr微信用户发送的数据经转换得到的数组
	 */
	public function response($postarr){
		//防止xml注入
        libxml_disable_entity_loader(true);
		$result = simplexml_load_string($postarr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$msg_type = trim($result->MsgType);
		switch($msg_type){
			case self::MSG_TYPE_EVENT:  
				//用户发生事件
				$result = $this->_deal_event($postarr); 
				break;   
			case self::MSG_TYPE_TEXT: 
				//用户发送文本  
				$result = $this->_get_text($postarr); 
				break;	 
			case self::MSG_TYPE_IMAGE:  
				//用户发送图片
				$result = $this->_get_image($postarr); 
				break;   
			case self::MSG_TYPE_VOICE:  
				//用户发送声音
				$result = $this->_get_image($postarr); 
				break;   
			case self::MSG_TYPE_LOCATION:
				//用户发送位置
				break;
			case self::MSG_TYPE_LINK:
				//用户发送链接
				break;
			default :
				echo "";
				break;
		}
		echo $result;
	}
	
	/**
	 * 用户发生事件(订阅,取消订阅,点击)
	 * @param  $postobj公众号接收到的用户的xml数据转换得到的数组
	 * @param  $postobj公众号接收到的用户的xml数据转换得到的数组
	 */
	 private function _deal_event($postarr){
	 	$content = "";             
		//防止xml注入
        libxml_disable_entity_loader(true);
		$result = simplexml_load_string($postarr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$event = $result->Event;
		switch($event){
			case self::MSG_EVENT_SUBSCRIBE: {
				//这里可以随调用者改变关注后回复的内容
				$content = "欢迎关注成都启程卓越科技有限公司"; 
				$result = $this->reply_text($postarr,$content);
				break;  
			}	
			case self::MSG_EVENT_UNSUBSCRIBE: 
				// 取消关注微信号
				break;
			case self::MSG_EVENT_SCAN: 
				// 扫描带参数二维码事件，用户已关注时的事件推送
				// 如果用户已经关注公众号，则微信会将带场景值扫描事件推送给开发者。
				break;
			case self::MSG_EVENT_VIEW: 
				// 自定义菜单事件，点击菜单跳转链接时的事件推送
				break;
			case self::MSG_EVENT_CLICK: 
				// 自定义菜单事件，点击菜单时的事件推送
				$result = $this->_event_click($postarr);
				break;
			default :
				echo "";
				break;
		}
		
		return $result;
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
		$access_token = $this->get_access_token();
		if (empty($access_token)) {
			$result['msg'] = '获取access_token失败，无法发送模板消息！';
			return $result;
		}
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token['access_token']}";
		$send_result = $this->_http_post_request($url, $post_data);
		if (empty($send_result) || (isset($send_result['errcode']) && intval($send_result['errcode']) != 0)) {
			$result['msg'] = $send_result['errmsg'];
			$result['errcode'] = $send_result['errcode'];
		}else {
			$result['success'] = TRUE;
		}
		return $result;
	}
	
	/**
	 * 用户发送文本消息,系统被动回应
	 * @param  $postobj公众号接收到的用户的xml数据
	 * @return xml数据,为新用户可看
	 */
	 private function _get_text($postarr){
	 	//防止xml注入
	 	$content = "您的留言我们已收到，非常感谢您对我们的关注，祝您生活愉快。";             
		$result = $this->reply_text($postarr,$content);
		return $result;
	 }
	 
	/**
	 * 用户发送图片消息,系统回应
	 * @param  $postobj公众号接收到的用户的xml数据
	 * @return xml数据,为新用户可看
	 */
	 private function _get_image($postarr){
	 	//防止xml注入
        libxml_disable_entity_loader(true);
		$result = simplexml_load_string($postarr, 'SimpleXMLElement', LIBXML_NOCDATA);
	 	//$content ="";
		//$user_image_id = $postobj->MediaId;        //获取图片id
		//switch(){
		//	case '' :  $content = "" ; break;     //这里暂时不写,具体情况未知
		//}
	 }
	 
	/**
	 * 用户发送语音消息,系统回应
	 * @param  $postobj公众号接收到的用户的xml数据
	 * @return xml数据,为新用户可看
	 */
	 private function _get_voice($postarr){
	 	//防止xml注入
        libxml_disable_entity_loader(true);
		$result = simplexml_load_string($postarr, 'SimpleXMLElement', LIBXML_NOCDATA);
	 	//$content ="";
		//$user_image_id = $postobj->MediaId;        //获取图片id
		//switch(){
		//	case '' :  $content = "" ; break;     //这里暂时不写,具体情况未知
		//}
	 }
	 
	 
	 
	/**
	 * 公众号被动回复文本消息
	 * @param  $content 用户自定义的回复内容
	 * @return $resultStr 公众号接收到用户数据后返回给用户的xml数据
	 */
	public function reply_text($postarr,$content){
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
	 * 公众号回复图文消息
	 * @param  $postarr 接收到的xml数据
	 * @param  $content 管理员自定义的回复内容(多维数组)
	 * @return $resultStr 公众号接收到用户数据后返回给用户的xml数据
	 */
	public function reply_news($postarr,$content){
		libxml_disable_entity_loader(true);
		$result = simplexml_load_string($postarr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$createtime = time();
		$textTpl .= "<xml>
					 <ToUserName><![CDATA[%s]]></ToUserName>
					 <FromUserName><![CDATA[%s]]></FromUserName>
					 <CreateTime>%s</CreateTime>
					 <MsgType><![CDATA[news]]></MsgType>
					 <ArticleCount>".count($content)."</ArticleCount>
					 <Articles>";
		foreach($content as $key => $value){
			$textTpl .= "<item>
						 <Title><![CDATA[".$value['title']."]]></Title> 
						 <Description><![CDATA[".$value['description']."]]></Description>
						 <PicUrl><![CDATA[".$value['picurl']."]]></PicUrl>
						 <Url><![CDATA[".$value['url']."]]></Url>
						 </item>";
		}
		$textTpl .= "</Articles>
					 </xml>";
					
		$resultstr = sprintf($textTpl, $result->FromUserName, $result->ToUserName, $createtime);
		return $resultstr;
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
		$token = $this->get_access_token();

		if (empty($token) || (isset($token['errcode']) && intval($token['errcode']) != 0)) {
			$result['msg'] = '获取access_token失败';
			return $result;
		}
		
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$token['access_token']}";
		$menu_result = $this->_http_post_request($url, $menus);
		if (empty($menu_result) || (isset($menu_result['errcode']) && intval($menu_result['errcode']) != 0)) {
			$result['msg'] = $menu_result['errmsg'];
			$result['errcode'] = $menu_result['errcode'];
		}else {
			$result['success'] = TRUE;
		}
		return $result;
	}
	
	/**
	 * 自定义菜单事件，点击菜单拉取消息时的事件推送处理逻辑
	 */
	private function _event_click($postarr) {
		if (empty($postarr)) {
			echo "";
			exit ;
		}

		// 防止xml注入
		libxml_disable_entity_loader(true);
		$postobj = simplexml_load_string($postarr, 'SimpleXMLElement', LIBXML_NOCDATA);
		
		$event_key = $postobj -> EventKey;

		switch ($event_key) {
			case 'library_introduce':
				$result = $this ->reply_text($postarr, "移动互联网研发联合实验室成立于2016年4月，是成都信息工程大学软件工程学院与成都启程卓越科技有限公司联合创立的校企共建实验室。实验室面向全校学生开放，将定期举行内部技术交流，不定期邀请企业技术人员、管理人员进行技术分享及交流沙龙。为同学们提供真实商业项目进行锻炼、指导，在实验室内部提供项目孵化及知识产权申请等帮助，同时为表现优秀的同学提供实习或工作岗位。");
				break;
			default :
				$result = $this ->reply_text($postarr, "该功能正在加紧建设中，感谢您对本公众号的关注！");
				break;
		}
		return $result;
	}

	/**
	 * 获取自定义菜单
	 * @return 返回执行结果数组，其中包括success,msg,errcode（当success为FALSE时）,data(自定义菜单对应的数组)等参数，失败时返回FALSE
	 */
	public function get_menu() {
		$token = $this->get_access_token();
		$result = array('success'=>FALSE, 'msg'=>'');
		if (empty($token) || (isset($token['errcode']) && intval($token['errcode']) != 0)) {
			$result['msg'] = '获取access_token失败';
			return $result;
		}
		
		$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token={$token['access_token']}";
		$menus = $this->_http_get_request($url);
		if (isset($menus['errcode']) && intval($menus['errcode']) != 0) {
			$result['errcode'] = $menus['errcode'];
			$result['msg'] = $menus['errmsg'];
		}else {
			$result['data'] = $menus;
		}
		return $result;
	}
	
	/**
	 * 获取永久素材列表
	 * @return 返回执行结果数组，其中包括success,msg,errcode（当success为FALSE时）,data(素材列表对应的数组)等参数，失败时返回FALSE
	 */
	public function get_material_list() {
		$token = $this->get_access_token();
		$result = array('success'=>FALSE, 'msg'=>'');
		if (empty($token) || (isset($token['errcode']) && intval($token['errcode']) != 0)) {
			$result['msg'] = '获取access_token失败';
			return $result;
		}
		
		$url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token={$token['access_token']}";
		//这里可以更改想要获取素材的类型和偏移量和数量
		$data = array("type"=>"news", "offset"=>0, "count"=>20);          
		$materials = $this->_http_post_request($url, $data);
		if (isset($materials['errcode']) && intval($materials['errcode']) != 0) {
			$result['errcode'] = $materials['errcode'];
			$result['msg'] = $materials['errmsg'];
		}else {
			$result['data'] = $materials;
		}
		return $result;
	}
	
	/**
	 * 获取普通access_token
	 * @return 获取成功返回消息数组，其中包括access_token,expires_in,update_time，获取失败返回FALSE
	 */
	public function get_access_token() {
		$access_log_file_path = APPPATH."libraries/access_token.log";
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
		$appid = $this->_CI->config->item('wx_appid');
		$secret = $this->_CI->config->item('wx_secret');
			
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
		$result = $this->_http_get_request($url);
			
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
	 * 发起httpGET请求
	 * @param $url 请求的URL
	 * @param $parameters 请求的参数，以数组形式传递
	 */
	private function _http_get_request($url, $parameters = NULL) {
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
	private function _http_post_request($url, $parameters = array()) {
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
	 * 将XML解析后的对象的元素转为string类型
	 * * @param $item xml对象数据
	 * 这里需要注意，经过xml_to_object转换得到的object的属性，虽然打印时是字符串，但其并非string类型，如果存数据库的话，必须进行转换
	 */
	public function object_item_to_string($item) {
		$item = json_encode($item);
		$item = json_decode($item, TRUE);
		return $item[0];
	}
}