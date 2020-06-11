<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_competition.php
 *
 *     Description:  竞赛类
 *
 *         Created:  2016-5-10 12:23:51
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
class Swcomp_competition {
	private $_CI;
	private $_table;

	public function __construct(){
		$this->_CI =& get_instance();
		$this->_table = $this->_CI->db->dbprefix('competition');
	}

	/**
	 * 通过id获取竞赛信息
	 * @param  integer $id 竞赛id
	 * @return 成功返回array数据，失败返回FALSE
	 */
	public function get_competition($id = 0){
		if (empty($id) || intval($id) <= 0){
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
	 * 增加竞赛
	 * @param  array  $array 竞赛信息
	 * @return [bool]        成功返回TRUE，失败返回FALSE
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
	 * 删除竞赛
	 * @param  integer $id 竞赛id
	 * @return [bool]      成功返回TRUE，失败返回FALSE
	 */
	public function delete_by_id($id = 0){
		if (empty($id) || intval($id) <= 0){
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
	 * 更新竞赛信息
	 * @param  integer $id    竞赛id
	 * @param  array   $array 竞赛信息
	 * @return [bool]         成功返回TRUE，失败返回FALSE
	 */
	public function update($id = 0, $array = array()){
		if (empty($array) || !is_array($array) || empty($id) || intval($id) <= 0){
			return FALSE;
		}

		$this->_CI->db->where('id', $id)->update($this->_table, $array);
		if ($this->_CI->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}