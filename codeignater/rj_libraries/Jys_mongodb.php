<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_mongodb.php
 *
 *     Description: MongoDB类库
 *
 *         Created: 2017-1-10 10:29:46
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Jys_mongodb{
    private $_manager;
    private $_host = 'localhost';
    private $_port = '27017';
    private $_db = NULL;

    /**
     * 构造函数
     *
     * Jys_mongodb constructor.
     * @param null $host
     * @param null $port
     * @param null $db_name
     * @param null $port
     */
    public function __construct($host = NULL, $port = NULL, $db_name = NULL){
        $url = "mongodb://".($host ? $host : $this->_host).':'.($port ? $port : $this->_port);
//        $this->_manager = new MongoDB\Driver\Manager($url);

        if (!empty($db_name)){
            $this->select_db($db_name);
        }
    }

    /**
     * 选择数据库
     *
     * @param $db_name
     */
    public function select_db($db_name){
        $this->_db = $db_name;
    }

    /**
     * 查询
     *
     * @param null $collection
     * @param array $filter
     * @param array $options
     * @param null $db_name
     * @return bool|\MongoDB\Driver\Cursor
     */
    public function find($collection = NULL, $filter = [], $options = [], $db_name = NULL){
        if (empty($collection)){
            return FALSE;
        }

        $query = new MongoDB\Driver\Query($filter, $options);

        if (empty($db_name) && empty($this->_db)){
            return FALSE;
        }else if (empty($db_name)){
            $db_name = $this->_db;
        }
        return $this->_manager->executeQuery($this->_name_space($db_name, $collection), $query);

    }

    /**
     * 插入数据（单条）
     *
     * @param null $collection
     * @param array $document
     * @param null $db_name
     * @return bool|\MongoDB\Driver\WriteResult
     */
    public function insert($collection = NULL, $document = [], $db_name = NULL){
        if (empty($collection) || empty($document)){
            return FALSE;
        }

        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($document);

        return $this->_write($db_name, $collection, $bulk);
    }

    /**
     * 插入数据（多条）
     *
     * @param null $collection
     * @param array $documents
     * @param null $db_name
     * @return bool|\MongoDB\Driver\WriteResult
     */
    public function insert_multi($collection = NULL, $documents = [], $db_name = NULL){
        if (empty($collection) || empty($documents)){
            return FALSE;
        }

        $bulk = new MongoDB\Driver\BulkWrite;
        foreach ($documents as $document){
            if (is_array($document)){
                $bulk->insert($document);
            }
        }

        return $this->_write($db_name, $collection, $bulk);
    }

    /**
     * 更新数据
     *
     * @param null $collection
     * @param array $condition
     * @param array $document
     * @param bool $multi
     * @param null $db_name
     * @return bool|\MongoDB\Driver\WriteResult
     */
    public function update($collection = NULL, $condition = [], $document = [], $multi = FALSE, $db_name = NULL){
        if (empty($collection) || empty($documents)){
            return FALSE;
        }

        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update($condition, $document, ['multi' => $multi, 'upsert' => FALSE]);

        return $this->_write($db_name, $collection, $bulk);
    }

    /**
     * 删除数据（匹配到的第一条）
     *
     * @param null $collection
     * @param array $condition
     * @param null $db_name
     * @return \MongoDB\Driver\WriteResult
     */
    public function delete($collection = NULL, $condition = [], $db_name = NULL){
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete($condition, ['limit'=>1]);

        return $this->_write($db_name, $collection, $bulk);
    }

    /**
     * 删除数据（匹配到的所有）
     *
     * @param null $collection
     * @param array $condition
     * @param null $db_name
     * @return \MongoDB\Driver\WriteResult
     */
    public function delete_all($collection = NULL, $condition = [], $db_name = NULL){
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete($condition, ['limit'=>0]);

        return $this->_write($db_name, $collection, $bulk);
    }


    /**
     * 改变数据
     *
     * @param $db_name
     * @param $collection
     * @param $bulk
     * @return \MongoDB\Driver\WriteResult
     */
    private function _write($db_name, $collection, $bulk){
        if (empty($db_name) && empty($this->_db)){
            return FALSE;
        }else if (empty($db_name)){
            $db_name = $this->_db;
        }

        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        return $this->_manager->executeBulkWrite($this->_name_space($db_name, $collection), $bulk, $writeConcern);
    }

    /**
     * 数据集合名称
     *
     * @param $db_name
     * @param $collection
     * @return string
     */
    private function _name_space($db_name, $collection){
        return "{$db_name}.{$collection}";
    }

    /**
     * 判断该条数据是否存在
     *
     * @param null $collection
     * @param array $condition
     * @param null $db_name
     * @return bool
     */
    public function is_exist($collection = NULL, $condition = [], $db_name = NULL){
        $result = $this->find($collection, $condition, [], $db_name);

        foreach ($result as $row){
            if ($row){
                return TRUE;
            }
        }

        return FALSE;
    }
}