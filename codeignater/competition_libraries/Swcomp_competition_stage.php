<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_competition_stage.php
 *
 *     Description:  竞赛阶段类
 *
 *         Created:  2016-8-11 14:54:41
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
class Swcomp_competition_stage {
	private $_CI;
	private $_table;

	public function __construct(){
		$this->_CI =& get_instance();
		$this->_table = $this->_CI->db->dbprefix('competition_stage');
	}

	/**
	 * 通过id获取竞赛阶段信息
	 * @param  integer $id 竞赛阶段id
	 * @return 成功返回array数据，失败返回FALSE
	 */
	public function get_competition_stage($id = 0){
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
	 * 增加竞赛阶段
	 * @param  array  $array 竞赛阶段信息
	 * @return [bool]        成功返回TRUE，失败返回FALSE
	 */
	public function insert($array = array()){
		if (empty($array) || !is_array($array)){
			$data['success'] = FALSE;
			return $data;
		}
		$this->_CI->db->trans_start();
		$this->_CI->db->insert($this->_table, $array);
		$data['id'] = $this->_CI->db->insert_id();
		if ($this->_CI->db->trans_status() === TRUE){
			$this->_CI->db->trans_complete();
			$result = $this->_CI->db->select_sum('scale')->where('competition_id',$array['competition_id'])->get($this->_table)->row_array();
			if($result['scale'] > 1){
				$this->_CI->db->trans_rollback();
				$data['success'] = FALSE;
				return $data;
			}else{
				$data['success'] = TRUE;
				return $data;
			}
		}else{
			$data['success'] = FALSE;
			return $data;
		}
	}

	/**
	 * 删除竞赛阶段
	 * @param  integer $id 竞赛阶段id
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
	 * 更新竞赛阶段信息
	 * @param  integer $id    竞赛阶段id
	 * @param  array   $array 竞赛阶段信息
	 * @return [bool]         成功返回TRUE，失败返回FALSE
	 */
	public function update($id = 0, $array = array()){
		if (empty($array) || !is_array($array) || empty($id) || intval($id) <= 0){
			return FALSE;
		}
		
		$this->_CI->db->trans_start();
		$this->_CI->db->where('id', $id)->update($this->_table, $array);
		if ($this->_CI->db->trans_status() === TRUE){

			$result = $this->_CI->db->select_sum('scale')->where('competition_id',$array['competition_id'])->get($this->_table)->row_array();

			if($result['scale'] > 1){
				$this->_CI->db->trans_rollback();
				return FALSE;
			}else{
				$this->_CI->db->trans_complete();
				return TRUE;
			}
		}else{
			return FALSE;
		}
	}		
}