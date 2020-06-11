<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_user_library.php
 *
 *     Description:  用户抽象类，参赛人员、管理员、评委等
 *
 *         Created:  2016-05-09 10:57:37
 *
 *          Author:  wuhaohua
 *
 * =====================================================================================
 */
abstract class Swcomp_user_library {
	public $CI;
	public $content;
	public $table;
	
	public function __construct($arg = '') {
		$this->CI =& get_instance();
		if($arg) {
			if(is_string($arg)) {
				$this->con_by_account($arg);
			} elseif(is_int($arg)) {
				$this->con_by_id($arg);
			}elseif (is_array($arg)) {
				$this->update($arg);
			}
		}
	}
	
	/**
	 * 使用用户名来构造对象（参赛选手的用户名为phone，裁判和管理员的用户名为number）
	 */
	abstract protected function con_by_account($account = '');
	
	/**
	 * 使用用户ID来构造对象
	 */
	abstract protected function con_by_id($id = 0);
	
	/**
	 * 更新用户信息
	 */
	abstract protected function update($array = array());
	
	/**
	 * 增加用户
	 */
	public function insert($array = array()) {
		if (empty($array) || !is_array($array)) {
			return FALSE;
		}
		
		$this->CI->db->insert($this->table, $array);
		if ($this->CI->db->affected_rows() > 0) {
			return TRUE;
		}else {
			return FALSE;
		}
	}
	
	/**
	 * 删除用户
	 */
	public function delete_by_id($id = 0) {
		if (empty($id) || intval($id) < 1) {
			return FALSE;
		}
		
		$this->CI->db->where('id', $id)->delete($this->table);
		if ($this->CI->db->affected_rows() > 0) {
			return TRUE;
		}else {
			return FALSE;
		}
	}
}
