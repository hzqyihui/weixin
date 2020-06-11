<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_team.php
 *
 *     Description:  团队类
 *
 *         Created:  2016-05-09 22:43:11
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
class Swcomp_team {
	private $_CI;
	private $_table;

	public function __construct(){
		$this->_CI =& get_instance();
		$this->_table = $this->_CI->db->dbprefix('team');
	}

	/**
	 * 使用id获取团队信息
	 */
	public function get_team($id = 0){
		if (empty($id) || intval($id) < 1){
			return FALSE;
		}

		$result = $this->_CI->db->where('id', $id)->get($this->_table);
		if ($result->num_rows() == 0){
			return FALSE;
		}else{
			return $result->row_array();
		}
	}

	/**
	 * 增加团队
	 */
	public function insert($array = array()){
		if (empty($array) || !is_array($array)){
			return FALSE;
		}
		$this->_CI->db->insert($this->_table, $array);
		if ($this->_CI->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * 删除团队
	 */
	public function delete_by_id($id = 0){
		if (empty($id) || intval($id) < 1){
			return FALSE;
		}

		$this->_CI->db->where('id', $id)->delete($this->_table);
		if ($this->_CI->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * 更新团队信息
	 */
	public function update($id = 0, $array = array()){
		if (empty($array) || !is_array($array) || empty($id) || intval($id) < 0){
			return FALSE;
		}

		$this->_CI->db->where('id', $id);
		$this->_CI->db->update($this->_table, $array);
		if ($this->_CI->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * 根据用户id查询团队信息
	 * @param  integer $user_id 用户id
	 * @return array            成功则返回团队信息，失败返回FALSE
	 */
	public function get_team_by_user_id($user_id = 0){
		if(empty($user_id) || intval($user_id) < 0){
			return FALSE;
		}

		$result = $this->_CI->db->where('user_id', $user_id)->get($this->_table);
		if ($result->num_rows() == 0){
			return FALSE;
		}

		return $result->row_array();
	}

}