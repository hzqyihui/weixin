<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_player.php
 *
 *     Description:  参赛人员类
 *
 *         Created:  2016-05-09 10:57:37
 *
 *          Author:  wuhaohua
 *
 * =====================================================================================
 */
class Swcomp_player extends Swcomp_user_library {
	public function __construct($arg = '') {
		$this->CI =& get_instance();
		$this->table = $this->CI->db->dbprefix('user');
		parent::__construct($arg);
	}
	
	/**
	 * 使用手机号来构造对象
	 */
	protected function con_by_account($account = '') {
		if (empty($account)) {
			$this->content['content'] = FALSE;
		}

        $this->CI->db->select("swcomp_user.*, swcomp_attachment.path");
		$this->CI->db->where(array('swcomp_user.phone'=>$account, 'swcomp_user.role_id'=>1));
        $this->CI->db->join("swcomp_attachment", "swcomp_attachment.id = swcomp_user.avatar_id", "left");
		$result = $this->CI->db->get($this->table);
		if($result->num_rows() == 0) {
			$this->content['content'] = FALSE;
		} else {
			$this->content['content'] = $result->row_array();
		}
	}
	
	/**
	 * 使用用户id来构造对象
	 */
	protected function con_by_id($id = 0) {
		if (empty($id)) {
			$this->content['content'] = FALSE;
		}

		$this->CI->db->select("swcomp_user.*, swcomp_attachment.path");
        $this->CI->db->where(array('swcomp_user.id'=>$id, 'swcomp_user.role_id'=>1));
        $this->CI->db->join("swcomp_attachment", "swcomp_attachment.id = swcomp_user.avatar_id", "left");
        $result = $this->CI->db->get($this->table);
		if($result->num_rows() == 0) {
			$this->content['content'] = FALSE;
		} else {
			$this->content['content'] = $result->row_array();
		}
	}
	
	/**
	 * 更新用户信息
	 */
	public function update($array = array()) {
		if (empty($array) || !is_array($array)) {
			return FALSE;
		}
		
		$this->CI->db->where('number', $array['number']);
		$this->CI->db->update($this->table, $array);
		return TRUE;
	}
}