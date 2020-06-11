<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename: Jys_sql_server.php
 *     Description: SQL Server数据库增删查改类
 *         Created: 2017-7-3 18:08:14
 *          Author: wuhaohua
 *
 * =====================================================================================
 */
class Jys_sql_server
{
    private $_CI;
    public $config;
    public static $connection;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->_CI =& get_instance();
        $this->_CI->config->load('sql_server');
        $this->config = $this->_CI->config->item('sql_server');
        // $this->init();
    }

    private function init()
    {
        if (empty(self::$connection)) {
            try {
                self::$connection = new PDO("sqlsrv:server={$this->config['host']};database={$this->config['dbname']}", $this->config['username'], $this->config['password']);
            }catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        }
    }

    /**
     * 根据条件获取数据
     *
     * @param $table 表名
     * @param string $column 需要展示的列明
     * @param null $where 条件
     * @param null $data 条件中的参数
     * @param null $join join条件
     * @param null $group_by group_by的条件
     * @return bool 查询结果，失败时返回FALSE
     */
    public function get_where($table, $page, $pagesize, $column = '*', $id = NULL, $where = NULL, $data = NULL, $join = NULL, $group_by = NULL) {
        if (empty($table) || empty($column)) {
            return FALSE;
        }

        if(!empty($page) && !empty($pagesize)){
            if($page == 1){    //如果当前显示第一页
                $sql = "SELECT TOP {$pagesize} {$column} FROM {$table}";
            }else{
                $position = $pagesize*($page-1);
                $sql = "SELECT TOP {$pagesize} {$column} FROM (SELECT ROW_NUMBER () OVER (ORDER BY {$id} ASC) RowNumber ,* FROM {$table}) A  WHERE A.RowNumber > $position";
            }

        }else{
            $sql = "SELECT {$column} FROM {$table}";
        }
        if (!empty($join)) {
            $sql .= " {$join}";
        }
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        if (!empty($group_by)) {
            $sql .= " {$group_by}";
        }
        $sth = self::$connection->prepare($sql);
        if (!empty($data) && is_array($data)) {
            $sth->execute($data);
        }else {
            $sth->execute();
        }
        // echo $sth->queryString;exit;
        $res = $sth->fetchAll(PDO::FETCH_ASSOC);  //以索引数组的形式返回结果集
        return $res;
    }


    /**
     * 根据表名获取表中数据总量
     *
     * @param string $table 表名
     * @param string $column 需要展示的列明
     * @param null $where 条件
     * @return bool 查询结果，失败时返回FALSE
     */
    public function get_total_count($table,$column = '*', $where = NULL) {
        if (empty($table) || empty($column)) {
            return FALSE;
        }
        $sql = "SELECT COUNT({$column}) FROM {$table}";

        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        $sth = self::$connection->prepare($sql);
        if (!empty($data) && is_array($data)) {
            $sth->execute($data);
        }else {
            $sth->execute();
        }
        $res = $sth->fetch(PDO::FETCH_COLUMN);  //以对象的形式返回结果集

        return $res;
    }

    /**
     * 插入操作
     * @param $sql SQL语句
     * @param $data SQL语句中的参数
     * @param $return_insert_id 是否要返回insert_id
     * @param $table 表名，当需要返回insert_id时，该字段不能为空
     */
    public function insert($sql, $data = NULL, $return_insert_id = FALSE, $table = "") {
        if (empty($sql)) {
            return FALSE;
        }
        if ($return_insert_id && empty($table)) {
            return FALSE;
        }
        try {
            if ($return_insert_id) {
                self::$connection->beginTransaction();
            }
            $sth = self::$connection->prepare($sql);
            if (!empty($data) && is_array($data)) {
                $sth->execute($data);
            }else {
                $sth->execute();
            }
            if ($return_insert_id) {
                $insert_id = self::$connection->query("SELECT IDENT_CURRENT('{$table}')");
                $insert_id = $insert_id->fetch();
                self::$connection->rollBack();
                return $insert_id[0];
            }
            return TRUE;
        }catch (PDOException $e) {
            return FALSE;
        }
    }

    /**
     * 更新数据
     * @param $sql SQL语句
     * @param null $data SQL语句中的参数
     * @return bool 成功是返回TRUE，失败时返回FALSE
     */
    public function update($sql, $data = NULL) {
        if (empty($sql)) {
            return FALSE;
        }

        try {
            $sth = self::$connection->prepare($sql);
            if (!empty($data) && is_array($data)) {
                $sth->execute($data);
            }else {
                $sth->execute();
            }
            return TRUE;
        }catch (PDOException $e) {
            return FALSE;
        }
    }

    /**
     * 删除操作
     * @param $sql SQL语句
     * @param null $data SQL语句中的参数
     * @return bool 成功是返回TRUE，失败时返回FALSE
     */
    public function delete($sql, $data = NULL) {
        if (empty($sql)) {
            return FALSE;
        }

        try {
            $sth = self::$connection->prepare($sql);
            if (!empty($data) && is_array($data)) {
                $sth->execute($data);
            }else {
                $sth->execute();
            }
            return TRUE;
        }catch (PDOException $e) {
            return FALSE;
        }
    }

}