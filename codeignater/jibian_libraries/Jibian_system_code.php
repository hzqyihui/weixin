<?php
if (!defined('BASEPATH'))
 exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Jibian_system_code.php
 *
 *   Description: 系统状态码
 *
 *       Created: 2016/7/20 13:18
 *
 *        Author: liuquanalways@163.com
 *
 * =========================================================
 */
 
class Jibian_system_code {

    private $_CI;

    public function __construct()
    {
        $this->_CI = &get_instance();
        $this->_CI->load->library('Jibian_db_helper');
    }


    /**
     * 获取系统字典码
     * @return mixed
     */
    public function get_system_code($type = [])
    {
    	return $this->_CI->db->select('value, name')->where($type)->get('system_code')->result_array();
        //return $this->_CI->jibian_db_helper->get('system_code', $type, ['value', 'name']);
    }

 
}