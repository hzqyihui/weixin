<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Jibian_common.php
 *
 *     Description:  增删查改类
 *
 *         Created:  2016-06-13 10:57:08
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
class Jibian_common {
    private $_CI;

    //表名
    private $_table;

    //分页数据条数,默认5条
    private $_offset = 5;

    /**
     * 构造函数
     * @param $params 数组参数,包含表名等
     */
    public function __construct($params = ['table'=> '']){
        $this->_CI =& get_instance();
        if($params['table'] != ''){
            $this->_table = $this->_CI->db->dbprefix($params['table']);
        }
    }

    /**
     * 根据数据id获取信息,成功返回信息,错误返回FALSE
     */
    public function get($id = 0, $table = ''){
        if (empty($id) || intval($id) < 1){
            return FALSE;
        }
        if($table != ''){
            $temporary_table = $table;
        }else{
            $temporary_table = $this->_table;
        }
        
        $result = $this->_CI->db->where('id', $id)->get($temporary_table);
        if ($result->num_rows() == 0){
            return FALSE;
        }else{
            return $result->row_array();
        }
    }

    /**
     * 根据数据库中的键值对获取数据,成功返回信息,错误返回FALSE
     */
    public function get_where($condition = [], $table = ''){
        if (empty($condition) || !is_array($condition)){
            return FALSE;
        }
        if($table != ''){
            $temporary_table = $table;
        }else{
            $temporary_table = $this->_table;
        }

        $result = $this->_CI->db->where($condition)->get($temporary_table);
        if ($result->num_rows() == 0){
            return FALSE;
        }else{
            return $result->row_array();
        }

    }

    /**
     * 根据数据库中的键值对获取数据,成功返回多条信息,错误返回FALSE
     */
    public function get_where_multi($condition = [], $table = ''){
        if (empty($condition) || !is_array($condition)){
            return FALSE;
        }
        if($table != ''){
            $temporary_table = $table;
        }else{
            $temporary_table = $this->_table;
        }
        
        $result = $this->_CI->db->where($condition)->get($temporary_table);
        if ($result->num_rows() == 0){
            return FALSE;
        }else{
            return $result->result_array();
        }

    }

    /**
     * 获取全部信息,成功返回信息,错误返回FALSE
     */
    public function all(){
        $result = $this->_CI->db->get($this->_table);
        if ($result->num_rows() == 0){
            return FALSE;
        }else{
            return $result->result_array();
        }
    }

    /**
     * 设置数据,成功返回TRUE,失败返回FALSE
     */
    public function set($array = array()){
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
     * 更新数据,成功返回TRUE,失败返回FALSE
     */
    public function update($id = 0, $array = array()){
        if (empty($array) || !is_array($array) || empty($id) || intval($id) < 1){
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
     * 删除数据,成功返回TRUE,失败返回FALSE
     */
    public function delete($id){
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
     * 自定义设置数据更新
     */
    public function set_update($id = 0, $array = array(), $escape = TRUE){
        if (empty($array) || !is_array($array) || empty($id) || intval($id) < 1){
            return FALSE;
        }

        $this->_CI->db->where('id', $id);

        foreach ($array as $key => $value) {
            $this->_CI->db->set($key, $value, $escape);
        }

        $this->_CI->db->update($this->_table);

        if ($this->_CI->db->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }

    }

    /**
     * 根据传入页数page获取分页数据
     *
     * @param int $page 页数
     */
    public function get_page($page = 1){
        if (empty($page) || intval($page) < 1 ){
            return FALSE;
        }

        $result = $this->_CI->db->get($this->_table, $this->_offset, ($page - 1) * $this->_offset);
        if ($result->num_rows() == 0){
            return FALSE;
        }else{
            return $result->result_array();
        }
    }

    /**
     * 获取总页数
     */
    public function get_total_page(){
        $result = $this->_CI->db->get($this->_table);
        if ($result->num_rows() == 0){
            return FALSE;
        }else{
            $total_page = ceil(count($result->result_array())/$this->_offset);

            return $total_page;
        }
    }

    /**
     * 设置分页数据量
     *
     * @param int $set_offset_nums 设置的offset
     * @return bool
     */
    public function set_offset($set_offset_nums = 0){
        if (empty($set_offset_nums) || intval($set_offset_nums) < 1 ){
            return FALSE;
        }else{
            $this->_offset = $set_offset_nums;
            return TRUE;
        }
    }

    /**
     * 设置表名
     *
     * @param string $table
     * @return bool
     */
    public function set_table($table = ''){
        if(empty($table)){
            return FALSE;
        }else{
            $this->_table = $this->_CI->db->dbprefix($table);
            return TRUE;
        }
    }
    
    public function get_info_by_field($id = 0, $field = '', $table = ''){
        if (empty($id) || intval($id) < 1 || empty($field)){
            return FALSE;
        }

        if($table != ''){
            $this->_table = $table;
        }
    }

    /**
     * 根据用户名获取用户信息
     * 
     * @param  [type] $username [description]
     * @return [type]           [description]
     */
    public function get_userinfo_by_username($username){
        $userinfo = $this->_CI->db->where('username', $username)->get('admin');

        return $userinfo->row_array();
    }

    /**
     * 根据字段更新数据,成功返回TRUE,失败返回FALSE
     */
    public function update_by_condition($condition = [], $array = array()){
        if (empty($array) || !is_array($array) || empty($condition) || !is_array($condition)){
            return FALSE;
        }

        $this->_CI->db->where($condition);
        $this->_CI->db->update($this->_table, $array);
        if ($this->_CI->db->affected_rows() > 0){
            return TRUE;
        }else{
            return FALSE;
        }
    }


}
