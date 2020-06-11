<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_system_log.php
 *     Description: 系统日志类
 *         Created: 2017-6-30 17:41:02
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Jys_system_log
{
    private $_CI;

    /**
     * 构造函数
     */
    public function __construct(){
        $this->_CI =& get_instance();
        $this->_CI->load->library('session', 'jys_tool');
        $this->_CI->load->helper('url');
    }

    /**
     * 插入日志
     * @param $type 日志类型
     * @param null $content 日志内容
     * @return bool 插入结果
     */
    public function insert($type, $content = NULL) {
        if (intval($type) < 1) {
            return FALSE;
        }

        $data['id'] = $this->_CI->jys_tool->uuid();
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['protocol'] = $_SERVER['SERVER_PROTOCOL'];
        $url = $_SERVER['PHP_SELF'];
        if (preg_match('/^\/index\.php/', $url)) {
            $data['url'] = substr($url, 10);
        }else {
            $data['url'] = $url;
        }

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $params = "";
            if (!empty($_POST)) {
                $is_first = TRUE;
                foreach ($_POST as $key => $value) {
                    if ($is_first) {
                        $params = $key . "=" . $value;
                        $is_first = FALSE;
                    } else {
                        $params .= "&" . $key . "=" . $value;
                    }
                }
            }
            $data['params'] = $params;
        } else {
            $data['params'] = $_SERVER['QUERY_STRING'];
        }
        $data['params'] = $_SERVER['QUERY_STRING'];
        $user_id = $this->_CI->session->userdata('user_id');
        if (intval($user_id) > 0) {
            $data['user_id'] = $user_id;
        }
        $data['os'] = $this->_CI->jys_tool->get_os();
        $data['browser'] = $this->_CI->jys_tool->get_broswer();
        $data['method'] = $_SERVER['REQUEST_METHOD'];
        $data['type'] = $type;
        if (!empty($content)) {
            $data['content'] = $content;
        }
        $data['create_time'] = date("Y-m-d H:i:s");

        $this->_CI->db->insert('system_log', $data);
        return true;
    }

}