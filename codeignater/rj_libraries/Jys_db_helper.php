<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_db_helper.php
 *     Description: 数据库增删查改类(只适合字段少的表)
 *         Created: 2016-11-12 21:21:09
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Jys_db_helper {
    private $_CI;

    /**
     * 构造函数
     */
    public function __construct(){
        $this->_CI =& get_instance();
    }

    /**
     * 根据数据id获取信息,成功返回信息,错误返回FALSE
     */
    public function get($table = NULL, $id = 0){
        if (empty($id) || intval($id) < 1 || empty($table)){
            return FALSE;
        }

        $result = $this->_CI->db->where('id', $id)->get($table);
        if ($result && $result->num_rows() > 0){
            return $result->row_array();
        }else{
            return FALSE;
        }
    }

    /**
     * 根据数据库中的键值对获取数据,成功返回信息,错误返回FALSE
     */
    public function get_where($table = NULL, $condition = []){
        if (empty($condition) || !is_array($condition) || empty($table)){
            return FALSE;
        }

        $result = $this->_CI->db->where($condition)->get($table);
        if ($result && $result->num_rows() > 0){
            return $result->row_array();
        }else{
            return FALSE;
        }

    }

    /**
     * 根据数据库中的键值对获取数据,成功返回多条信息,错误返回FALSE
     */
    public function get_where_multi($table = NULL, $condition = NULL){
        if (empty($condition) || empty($table)){
            return FALSE;
        }

        $result = $this->_CI->db->where($condition)->get($table);
        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return FALSE;
        }

    }

    /**
     * 获取全部信息,成功返回信息,错误返回FALSE
     */
    public function all($table = NULL, $condition = []){
        if (empty($table)) {
            return FALSE;
        }

        if (!empty($condition) && is_array($condition)){
            $select = implode(',', $condition);
            $this->_CI->db->select($select);
        }

        $result = $this->_CI->db->get($table);
        if ($result && $result->num_rows() > 0){
            return $result = [
                'success' => TRUE,
                'msg' => '获取成功',
                'data' => $result->result_array()
            ];
        }else{
            return $result = [
                'success' => FALSE,
                'msg' => '获取失败',
                'data' => NULL
            ];
        }
    }

    /**
     * 设置数据,成功返回TRUE,失败返回FALSE
     */
    public function set($table = NULL, $array = array()){
        if (empty($array) || !is_array($array) || empty($table)){
            return FALSE;
        }

        $this->_CI->db->insert($table, $array);

        return TRUE;
    }

    /**
     * 更新数据,成功返回TRUE,失败返回FALSE
     */
    public function update($table = NULL, $id = 0, $array = array()){
        if (empty($array) || !is_array($array) || empty($id) || intval($id) < 1 || empty($table)){
            return FALSE;
        }

        $this->_CI->db->where('id', $id);
        $this->_CI->db->update($table, $array);
        return TRUE;
    }

    /**
     * 删除数据,成功返回TRUE,失败返回FALSE
     */
    public function delete($table = NULL, $id = 0){
        if (empty($id) || empty($table)){
            return FALSE;
        }

        if (is_array($id)){
            foreach ($id as $key => $row){
                if ($key == 0){
                    $this->_CI->db->where('id', $row);
                }else{
                    $this->_CI->db->or_where('id', $row);
                }
            }
        }else{
            $this->_CI->db->where('id', $id);
        }

        $this->_CI->db->delete($table);

        return TRUE;
    }

    /**
     * 根据条件删除数据
     *
     * @param array $condition 对应数据库键值对
     * @param string $table 表名
     * @return bool 成功返回true，失败返回false
     */
    public function delete_by_condition($table = NULL, $condition = []){
        if (empty($condition) || empty($table)){
            return FALSE;
        }

        $this->_CI->db->where($condition)->delete($table);

        return TRUE;
    }

    /**
     * 软删除
     * @param $table string 表名
     * @param $condition array 对应数据库键值对
     * @return boolean
     */
    public function soft_delete($table = NULL, $condition = [], $modify = [])
    {
        $response = ['success'=>FALSE, 'msg'=>'修改失败'];

        if (!is_null($table) && !empty($condition) && !empty($modify)) {
            $this->_CI->db->where($condition)->update($table, $modify);
            $response['success']    = TRUE;
            $response['msg']        = '修改成功';
        }

        return $response;
    }

    /**
     * 自定义设置数据更新
     */
    public function set_update($table = NULL, $id = 0, $array = array(), $escape = TRUE){
        if (empty($array) || !is_array($array) || empty($id) || intval($id) < 1 || empty($table)){
            return FALSE;
        }

        $this->_CI->db->where('id', $id);

        foreach ($array as $key => $value) {
            if (is_null($value) || $value == "null" || $value == "NULL"){
                $this->_CI->db->set($key, $value, $escape);
            }else{
                $this->_CI->db->set($key, $value, $escape);
            }

        }

        $this->_CI->db->update($table);
        return TRUE;
    }

    /**
     * 根据传入页数page获取分页数据
     * @param null $table
     * @param int $page
     * @param int $page_size
     * @param array $condition
     * @return bool
     */
    public function get_page($table = NULL, $page = 1, $page_size = 10, $condition = array()){
        if (intval($page) < 1 || intval($page_size) < 1 || empty($table)){
            return FALSE;
        }

        if (!empty($condition) && is_array($condition) && count($condition) > 0) {
            $this->_CI->db->where($condition);
        }
        $result = $this->_CI->db->get($table, $page_size, ($page - 1) * $page_size);
        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return FALSE;
        }
    }

    /**
     * 获取总页数
     */
    public function get_total_page($table = NULL, $page_size = 10, $condition = NULL){
        if(empty($table) || intval($page_size) < 1){
            return FALSE;
        }

        if (!empty($condition)){
            $this->_CI->db->where($condition);
        }

        $result = $this->_CI->db->get($table);
        if ($result && $result->num_rows() > 0){
            $total_page = ceil($result->num_rows() / $page_size * 1.0);
            return $total_page;
        }else{
            return FALSE;
        }
    }

    /**
     * 根据条件获取总页数
     */
    public function get_total_page_by_where($table = NULL, $condition=[],$page_size = 10){
        if(empty($table) || intval($page_size) < 1){
            return FALSE;
        }

        $result = $this->_CI->db->where($condition)->get($table);
        if ($result && $result->num_rows() > 0){
            $total_page = ceil($result->num_rows() / $page_size * 1.0);
            return $total_page;
        }else{
            return FALSE;
        }
    }

    /**
     * 根据字段更新数据,成功返回TRUE,失败返回FALSE
     */
    public function update_by_condition($table = NULL, $condition = [], $array = array()){
        if (empty($array) || !is_array($array) || empty($condition) || !is_array($condition) || empty($table)){
            return FALSE;
        }

        $this->_CI->db->where($condition);
        $this->_CI->db->update($table, $array);
        return TRUE;
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
        $result = ['success'=>FALSE, 'msg'=>'添加失败'];

        if ($table && $insert) {
            if ($this->_CI->db->insert($table, $insert)) {
                if ($flag) {
                    $result['insert_id'] = $this->_CI->db->insert_id();
                }
                $result['success']    = TRUE;
                $result['msg']        = '添加成功';
            }else{
                $result['success']    = FALSE;
            }
        }

        return $result;
    }

    /**
     * 批量插入数据
     *
     * @param null $table 表名
     * @param array $batch 数据（二维数组）
     * @return array
     */
    public function add_batch($table = NULL, $batch = []){
        $result = [
            'success' => FALSE,
            'msg' => '添加失败'
        ];
        if ($table && $batch){
            if ($this->_CI->db->insert_batch($table, $batch) == count($batch)){
                $result['success'] = TRUE;
                $result['msg'] = '添加成功';
            }
        }

        return $result;
    }

    /**
     * 批量更新数据
     * @param null $table 表名
     * @param array $batch 数据（二维数组）
     * @param string $title 批量更新所用where字段
     * @return array
     */
    public function update_batch($table = NULL, $batch = [], $title = ''){
        $result = [
            'success' => FALSE,
            'msg' => '更新失败'
        ];
        if ($table && $batch){
            if ($this->_CI->db->update_batch($table, $batch, $title)){
                $result['success'] = TRUE;
                $result['msg'] = '更新成功';
            }
        }

        return $result;
    }

    /**
     * 批量删除数据
     *
     * @param null $table 表名
     * @param string $filed 字段名
     * @param array $batch 数据（二维数组）
     * @return array
     */
    public function del_batch($table = NULL, $filed= '',$batch = []){
        $result = [
            'success' => FALSE,
            'msg' => '删除失败'
        ];
        if ($table && $batch){
            if ($this->_CI->db->where_in($filed,$batch)->delete($table)){
                $result['success'] = TRUE;
                $result['msg'] = '删除成功';
            }
        }

        return $result;
    }

    /**
     * 获取总数据条数
     *
     * @param null $table 表名
     * @param array $condition 条件
     * @return bool|int
     */
    public function get_total_num($table = NULL, $condition = []){
        if (empty($table)){
            return FALSE;
        }

        if (!empty($condition)){
            $this->_CI->db->where($condition);
        }

        $result = $this->_CI->db->get($table);

        if ($result && $result->num_rows() > 0){
            return $result->num_rows();
        }else{
            return 0;
        }
    }

    /**
     * 检验数据是否存在
     *
     * @param null $table 表名
     * @param array $condition 条件
     * @return bool
     */
    public function is_exist($table = NULL, $condition = []){
        if (empty($table)){
            return FALSE;
        }

        if (!empty($condition)){
            $this->_CI->db->where($condition);
        }

        $result = $this->_CI->db->get($table);

        if ($result && $result->num_rows() > 0){
            return $result->row_array()['id'];
        }else{
            return FALSE;
        }
    }

    /**
     * 根据id数组获取某表中的数据（限时折扣表、折扣活动表、满减满赠表）
     * @param string $table
     * @param string $where_field
     * @param array $condition
     * @return array
     */
    public function get_where_in($table = '', $where_field = '', $condition = array())
    {
        if (empty($table) || empty($where_field) || empty($condition) || !is_array($condition)){
            return array();
        }
        $date = date('Y-m-d H:i:s');

        $this->_CI->db->where_in($where_field, $condition);
        $this->_CI->db->where('start_time <= ', $date);
        $this->_CI->db->where('end_time >= ', $date);
        $result = $this->_CI->db->get($table);

        if ($result && $result->num_rows() > 0){
            return $result->result_array();
        }else{
            return array();
        }
    }
}