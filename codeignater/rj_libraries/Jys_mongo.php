<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * =====================================================================================
 *
 *        Filename: Jys_mongo.php
 *
 *     Description: MongoDB(old)类库
 *
 *         Created: 2017-1-13 15:53:16
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */
class Jys_mongo
{
    private $_CI;
    private $_manager;
    private $_host = '120.27.54.13';
    private $_port = '17017';
    private $_user = '';
    private $_pwd = '';
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
    public function __construct($host = NULL, $port = NULL, $db_name = NULL)
    {
        $this->_CI =& get_instance();
        $this->_host = $this->_CI->config->item('mongodb_host');
        $this->_port = $this->_CI->config->item('mongodb_port');
        $this->_db = $this->_CI->config->item('mongodb_name');
        $this->_user = $this->_CI->config->item('mongodb_user');
        $this->_pwd = $this->_CI->config->item('mongodb_pwd');

        if (empty($this->_user) || empty($this->_pwd)) {
            $url = "mongodb://" . ($host ? $host : $this->_host) . ':' . ($port ? $port : $this->_port);
        }else {
            $url = "mongodb://{$this->_user}:{$this->_pwd}@" . ($host ? $host : $this->_host) . ':' . ($port ? $port : $this->_port).'/'.$this->_db;
        }

        $this->_manager = new MongoClient($url);

        if (!empty($db_name)) {
            $this->select_db($db_name);
        } else if (!empty($this->_db)) {
            $this->select_db($this->_db);
        }
    }

    /**
     * 选择数据库
     *
     * @param $db_name
     */
    public function select_db($db_name)
    {
        $this->_db = $db_name;
    }

    /**
     * 创建集合
     *
     * @param null $collection
     * @param null $db_name
     * @return bool
     */
    public function create_collection($collection = NULL, $db_name = NULL)
    {
        if (empty($collection)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $db = $this->_manager->selectDB($db_name);
        $db->createCollection($collection);

        return TRUE;
    }

    /**
     * 查询
     *
     * @param null $collection
     * @param array $filter
     * @param null $db_name
     * @return bool|\MongoDB\Driver\Cursor
     */
    public function find($collection = NULL, $filter = [], $db_name = NULL)
    {
        if (empty($collection)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->find($filter);

        $result = [];
        foreach ($cursor as $document) {
            $result[] = $document;
        }

        return $result;

    }

    /**
     * 查询数据，获取一条
     *
     * @param null $collection
     * @param array $filter
     * @param null $db_name
     * @return array|bool
     */
    public function find_one($collection = NULL, $filter = [], $db_name = NULL)
    {
        if (empty($collection)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->findOne($filter);

        return $cursor;

    }

    /**
     * 插入数据（单条）
     *
     * @param null $collection
     * @param array $document
     * @param null $db_name
     * @return bool
     */
    public function insert($collection = NULL, $document = [], $db_name = NULL)
    {
        if (empty($collection) || empty($document)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->insert($document);

        if ($cursor['err'] === NULL) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 插入数据（多条）
     *
     * @param null $collection
     * @param array $documents
     * @param null $db_name
     * @return bool
     */
    public function insert_multi($collection = NULL, $documents = [], $db_name = NULL)
    {
        if (empty($collection) || empty($documents)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->batchInsert($documents);

        if ($cursor['err'] === NULL) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 更新数据
     *
     * @param null $collection
     * @param array $condition
     * @param array $document
     * @param null $db_name
     * @return bool
     */
    public function update($collection = NULL, $condition = [], $document = [], $db_name = NULL)
    {
        if (empty($collection) || empty($documents)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->update($condition, ['$set' => $document], ['multiple' => FALSE, 'upsert' => FALSE]);

        if ($cursor['err'] === NULL) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 更新数据（多条）
     *
     * @param null $collection
     * @param array $condition
     * @param array $document
     * @param null $db_name
     * @return bool
     */
    public function update_multi($collection = NULL, $condition = [], $document = [], $db_name = NULL)
    {
        if (empty($collection) || empty($documents)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->update($condition, ['$set' => $document], ['multiple' => TRUE, 'upsert' => FALSE]);

        if ($cursor['err'] === NULL) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 更新数据，若匹配不到数据则新增
     *
     * @param null $collection
     * @param array $condition
     * @param array $document
     * @param null $db_name
     * @return bool
     */
    public function upsert($collection = NULL, $condition = [], $document = [], $db_name = NULL)
    {
        if (empty($collection) || empty($document)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->update($condition, ['$set' => $document], ['multiple' => FALSE, 'upsert' => TRUE]);

        if ($cursor['err'] === NULL) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 删除数据（匹配到的第一条）
     *
     * @param null $collection
     * @param array $condition
     * @param null $db_name
     * @return bool
     */
    public function delete($collection = NULL, $condition = [], $db_name = NULL)
    {
        if (empty($collection) || empty($condition)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->remove($condition, ['justOne' => TRUE]);

        if ($cursor['err'] === NULL) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 删除数据（匹配到的所有）
     *
     * @param null $collection
     * @param array $condition
     * @param null $db_name
     * @return bool
     */
    public function delete_all($collection = NULL, $condition = [], $db_name = NULL)
    {
        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->remove($condition);

        if ($cursor['err'] === NULL) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 自增或自减(increment中正数自增，负数自减)
     *
     * @param null $collection
     * @param array $condition
     * @param array $increment
     * @param null $db_name
     * @return bool
     */
    public function increment($collection = NULL, $condition = [], $increment = [], $db_name = NULL)
    {
        if (empty($collection) || empty($increment)) {
            return FALSE;
        }

        if (empty($db_name) && empty($this->_db)) {
            return FALSE;
        } else if (empty($db_name)) {
            $db_name = $this->_db;
        }

        $cursor = $this->_manager->selectCollection($db_name, $collection)->update($condition, ['$inc' => $increment], ['multiple' => FALSE, 'upsert' => FALSE]);

        if ($cursor['err'] === NULL) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * 判断该条数据是否存在
     *
     * @param null $collection
     * @param array $condition
     * @param null $db_name
     * @return bool
     */
    public function is_exist($collection = NULL, $condition = [], $db_name = NULL)
    {
        $result = $this->find($collection, $condition, $db_name);

        if (!empty($result)) {
            return TRUE;
        }

        return FALSE;
    }
}