<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_admin.php
 *
 *     Description:  管理人员类
 *
 *         Created:  2016-05-09 18:06:21
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
class Swcomp_admin extends Swcomp_user_library {
	public function __construct($arg = ''){
		$this->CI =& get_instance();
		$this->table = $this->CI->db->dbprefix('user');
		parent::__construct($arg);
	}

	/**
	 * 使用用户名来构造对象
	 */
	protected function con_by_account($account = ''){
		if (empty($account)){
			$this->content['content'] = FALSE;
		}

		$result = $this->CI->db->where(array('number'=>$account, 'role_id'=>2))->get($this->table);
		if ($result->num_rows() == 0){
			$this->content['content'] = FALSE;
		}else{
			$this->content['content'] = $result->row_array();
		}
	}

	/**
	 * 使用id来构造对象
	 */
	protected function con_by_id($id = 0) {
		if (empty($id)) {
			$this->content['content'] = FALSE;
		}
		
		$result = $this->CI->db->where(array('id'=>$id, 'role_id'=>2))->get($this->table);
		if($result->num_rows() == 0) {
			$this->content['content'] = FALSE;
		} else {
			$this->content['content'] = $result->row_array();
		}
	}
	
	/**
	 * 更新用户信息
	 */
	protected function update($array = array()) {
		if (empty($array) || !is_array($array)) {
			return FALSE;
		}
		
		$this->CI->db->where('id', $array['id']);
		$this->CI->db->update($this->table, $array);
		return TRUE;
	}
}