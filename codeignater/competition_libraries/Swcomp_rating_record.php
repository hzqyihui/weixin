<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_rating_record.php
 *
 *     Description:  竞赛评分记录类
 *
 *         Created:  2016-8-17 13:14:45
 *
 *          Author:  huazhiqiang
 *
 * =====================================================================================
 */
class Swcomp_rating_record {
	private $_CI;
	private $_table;

	public function __construct(){
		$this->_CI =& get_instance();
		$this->_table = $this->_CI->db->dbprefix('rating_record');
	}
	
	/**
	 * 获取某一个角色的评分记录总数
	 * @param $role 身份
	 * @param $role_id 角色的id
	 * @return 成功时返回评分记录总数，失败时返回0
	 */
	 public function count_record_by_role($role, $role_id){
	 	$this->_CI->db->select('COUNT(*) as count');
		$this->_CI->db->where($role,$role_id);
		$result = $this->_CI->db->get($this->_table);
		if ($result) {
			$result = $result->row_array();
			return intval($result['count']);
		}else {
			return 0;
		}
	 }
	 
	
}