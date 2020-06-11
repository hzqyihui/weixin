<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_memcache.php
 *
 *     Description: Memcache类库
 *
 *         Created: 2017-06-19 16:58:58
 *
 *          Author: zourui
 *
 * =====================================================================================
 */
class Jys_memcache
{
    private $type = 'Memcache';
    private $m;
    private $time = 0;
    private $error;

    /**
     * 构造函数
     * Jys_memcache constructor.
     */
    public function __construct()
    {
        if (!class_exists($this->type)) {
            $this->error = 'NO '.$this->type;
            return false;
        }else{
            $this->m = new $this->type;
            $m->connect("localhost","11211");
        }
    }

    /**
     * 查询数据
     * @param  $key
     * @return data
     */
    public function get($key)
    {
        if (empty($key)){
            return FALSE;
        }
        $data = $this->m->get($key);
        return $data;
    }

    /**
     * 更新数据
     * @param null $key
     * @return bool
     */
    public function update($key, $value, $time=NULL)
    {
        if (empty($key) || empty($value)){
            return FALSE;
        } else if (empty($time)) {
            $time = $this->time;
        }
        $this->m->replace($key, $value, $time);
    }

    /**
     * 插入数据
     *
     * @param $key
     * @param array $value
     * @param null $time
     * @return bool
     */
    public function set($key, $value, $time=NULL)
    {
        if (empty($key) || empty($value)){
            return FALSE;
        } else if (empty($time)) {
            $time = $this->time;
        }
        $this->m->set($key, $value, $time);
    }

    /**
     * 删除数据
     *
     * @param null $key
     * @return bool
     */
    public function delete($key)
    {
        if (empty($key)){
            return FALSE;
        }
        $this->m->delete($key);
    }

    /**
     * 清除所有缓存数据
     * @return bool
     */
    public function flush()
    {
        $this->m->flush();
    }

    /**
     * 返回错误
     * @return data
     */
    public function getError()
    {
        if ($this->error) {
            return $this->error;
        } else {
            return $this->m->getResultMessage();
        }
    }
}