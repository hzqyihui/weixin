<?php
if (!defined('BASEPATH'))
 exit('No direct script access allowed');
/**
 * =========================================================
 *
 *      Filename: Jibian_db_helper.php
 *
 *   Description: 数据库辅助类
 *
 *       Created: 2016/7/20 11:30
 *
 *        Author: liuquanalways@163.com
 *
 * =========================================================
 */
 
class Jibian_db_helper {
	private $_CI;

    //表名
    private $_table;

    //分页数据条数,默认5条
    private $_offset = 5;

    public function __construct(){
        $this->_CI = &get_instance();
    }

    /**
     * 查询sql总行数
     * @param  string $sql sql语句
     * @return int      总行数
     */
    public function get_lines($sql)
    {
        return $this->_CI->db->query($sql)->num_rows();
    }

    /**
     * 添加数据
     * @param $table string 表名
     * @param $insert array 对应数据库键值对
     * @param $flag boolean 是否返回添加的id
     * @return array
     */
    public function add($table = NULL, $insert = [], $flag = false)
    {

        $response = ['success'=>false, 'msg'=>'添加失败'];

        if ($table && $insert) {
            if ($this->_CI->db->insert($table, $insert)) {
                if ($flag) {
                    $response['data']['id'] = $this->_CI->db->insert_id();
                }
                $response['success']    = true;
                $response['msg']        = '添加成功';
            }
        }

        return $response;
    }

    /**
     * 批量添加数据
     * @param $table string 表名
     * @param $insert_arr array 对应数组数据
     * @return array
     */

    public function add_batch($table = NULL, $insert_arr = []){
        $response = ['success'=>false, 'msg'=>'添加失败'];

        if ($table && $insert_arr) {
            if ($this->_CI->db->insert_batch($table, $insert_arr)) {
                $response['success']    = true;
                $response['msg']        = '添加成功';
            }
        }

        return $response;
    }

    /**
     * 删除数据
     * @param $table string 表名
     * @param $condition array 对应数据库键值对
     * @return boolean
     */
    public function delete($table = NULL, $condition = [])
    {
        $response = ['success'=>false, 'msg'=>'删除失败'];

        if ($table && $condition) {
            if ($this->_CI->db->delete($table, $condition)) {
                $response['success']    = true;
                $response['msg']        = '删除成功';
            }
        }

        return $response;
    }

    /**
     * 修改数据
     * @param $table string 表名
     * @param $condition array 对应数据库键值对
     * @param $modify array 对应数据库键值对
     * @return boolean
     */
    public function update($table = NULL, $condition = [], $modify = [])
    {
        $response = ['success'=>false, 'msg'=>'修改失败'];
		
        if ($table && $condition && $modify) {
            if ($this->_CI->db->update($table, $modify, $condition)) {
                $response['success']    = true;
                $response['msg']        = '修改成功';
            }
        }

        return $response;
    }

    /**
     * 批量更新
     * @param string $table 数据表
     * @param array $modify 更新数据  二维数组
     * @param string $key 更新语句的 where 条件的 key
     */
    public function update_batch($table = NULL, $modify = [], $key='') {
        $response = ['success'=>false, 'msg'=>'修改失败'];

        if ($table && $modify && $key) {
            if ($this->_CI->db->update_batch($table, $modify, $key)) {
                $response['success']    = true;
                $response['msg']        = '修改成功';
            }
        }

        return $response;
    }

    /**
     * 软删除
     * @param $table string 表名
     * @param $condition array 对应数据库键值对
     * @return boolean
     */
    public function soft_delete($table = NULL, $condition = [], $modify = [])
    {
        $response = ['success'=>false, 'msg'=>'修改失败'];

        if (!is_null($table) && !empty($condition) && !empty($modify)) {
        	$this->_CI->db->where($condition)->update($table, $modify);
        	//$this->_CI->db->update($table, $modify, $condition);
            if ($this->_CI->db->affected_rows() > 0) {
                $response['success']    = true;
                $response['msg']        = '修改成功';
            }
        }
		
        return $response;
    }
	
	

    /**
     * 查询数据(单表查询)
     * @param $table string 表名
     * @param $condition array 对应数据库键值对
     * @param $field mixed 返回数据项(为空返回全部)
     * @return array 全部记录
     */
    public function get($table = NULL, $condition = [], $field = [])
    {
        $response = ['success'=>false, 'msg'=>'查询失败'];
        $select = '*';

        if ($table && $condition) {
            if ($field) {
                $res = $this->array_to_string($field, ', ');
                if ($res) {
                    $select = $res;
                }
            }

            $this->_CI->db->select($select);
            $res = $this->_CI->db->get_where($table, $condition);

            if ($res->num_rows() > 0) {
                foreach ($res->result_array() as $row) {
                    $response['data'][] = $row;
                }

                $response['success']    = true;
                $response['msg']        = '查询成功';
            }
        }

        return $response;
    }

    /**
     * set one col filed
     * @param  string  $table     [description]
     * @param  array   $condition [description]
     * @param  array   $set       what filed you want modify. eg: set[key] = key + 1
     * @return array              [description]
     */
    public function set($table = null, $condition = [], $set = [])
    {
        $response = ['success'=>false, 'msg'=>'修改失败'];

        if ($table && $condition) {

            if (is_array($set) && key($set)) {
                $this->_CI->db->set(key($set), $set[key($set)], FALSE);
                $this->_CI->db->where($condition);
                if ($this->_CI->db->update($table)) {
                    $response['success']    = true;
                    $response['msg']        = '修改成功';
                }
            }
        }

        return $response;
    }


    /**
     * figure out the unique data is exist 
     * @param  string  $table     [description]
     * @param  array   $condition [description]
     * @return boolean            [description]
     */
    public function is_exist($table = NULL, $condition = []) 
    {
        $exist = false;

        if ($table && $condition) {
            if ($this->_CI->db->get_where($table, $condition)->num_rows() > 0) {
                $exist = true;
            }
        }

        return $exist;
    }
	
	/**
	 * 将数组转化成字符串
	 */
	public function array_to_string($arr, $str) {
		if (empty($arr) || count($arr) < 1) {
			return FALSE;
		}
		$result = "";
		if (is_array($arr)) {
			$first = TRUE;
			foreach ($arr as $key => $value) {
				if ($first) {
					$result = $value;
				}else {
					$result .= $str.$value;
				}
			}
		}else {
			$result = $arr;
		}
	}
}