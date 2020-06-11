<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_production.php
 *
 *     Description:  作品类
 *
 *         Created:  2016-5-10 12:48:18
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
class Swcomp_production {
	private $_CI;
	private $_table;

	public function __construct(){
		$this->_CI =& get_instance();
		$this->_table = $this->_CI->db->dbprefix('production');
	}

	/**
	 * 通过id获取作品信息
	 * @param  integer $id 作品id
	 * @return 成功返回array数据，失败返回FALSE
	 */
	public function get_production($id = 0){
		if (empty($id) || intval($id) < 0){
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
	 * 增加作品
	 * @param  array  $array 作品信息
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
	 * 删除作品
	 * @param  integer $id 作品id
	 * @return [bool]      成功返回TRUE，失败返回FALSE
	 */
	public function delete_by_id($id = 0){
		if (empty($id) || intval($id) < 0){
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
	 * 更新作品信息
	 * @param  integer $id    作品id
	 * @param  array   $array 作品信息
	 * @return [bool]         成功返回TRUE，失败返回FALSE
	 */
	public function update($id = 0, $array = array()){
		if (empty($array) || !is_array($array) || empty($id) || intval($id) < 0){
			return FALSE;
		}

		$this->_CI->db->where('id', $id)->update($this->_table, $array);
		if ($this->_CI->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	/**
	 * 根据team_id获取作品信息
	 * @param int $team_id
	 * @param int $stage_id
	 * @return bool
	 */
	public function get_production_by_team_id_stage_id($team_id = 0, $stage_id = 0){
		if (empty($team_id) || intval($team_id) <= 0 || empty($stage_id) || intval($stage_id) <= 0){
		    return FALSE;
		}

		$result = $this->_CI->db->where(['team_id'=>$team_id, 'competition_stage_id'=>$stage_id])->get($this->_table);
		if ($result->num_rows() == 0){
			return FALSE;
		}else{
			return $result->row_array();
		}
	}
}