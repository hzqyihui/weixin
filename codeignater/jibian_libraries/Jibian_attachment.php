<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Jibian_attachment.php
 *
 *     Description:  上传类
 *
 *         Created:  2016-7-17 20:24:15
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
class Jibian_attachment {
	private $_CI;
	private $_table;

	public function __construct(){
		$this->_CI =& get_instance();
		$this->_table = $this->_CI->db->dbprefix('attachment');
	}

	/**
	 * 上传文件处理
	 * @param  string $dir 文件上传路径
	 * @return array       失败则返回失败信息，成功则返回文件信息
	 */
	public function upload($dir = 'source/uploads/'){
		$info['success'] = FALSE;
		if (empty($dir)){
			return $info;
		}

		$this->_make_dir($dir);

		$config['upload_path'] = $dir;
        $config['allowed_types'] = '*';
		$config['file_name'] = md5(time());
		$config['max_size'] = '5120';
		//$config['max_width'] = '1024';
		//$config['max_height'] = '768';
		$config['encrypt_name'] = 'TRUE';

		$this->_CI->load->library('upload', $config);
		$this->_CI->upload->initialize($config);

        // 允许上传的文件类型
        $allowed_types = array('.png', '.jpg', '.jpeg', '.gif');

		if (!$this->_CI->upload->do_upload('file')){
			$info['error'] = array('error'=> $this->_CI->upload->display_errors());
		}else{
			$data = array('upload_data'=>$this->_CI->upload->data());
            // 校验文件后缀名
            if (!in_array(strtolower($data['upload_data']['file_ext']), $allowed_types)) {
                // 删除不符合上传类型的文件
                unlink($data['upload_data']['full_path']);
            }
			//为附件处理数据
			$info['success'] = TRUE;
			$info['name'] = $data['upload_data']['raw_name'];
			$info['file_path'] = $config['upload_path'] . $data['upload_data']['file_name'];
			$info['md5'] = md5_file($info['file_path']);
		}
		return $info;
	}

	/**
	 * 检验目录是否存在，不存在则新建
	 * @param  string $dir 目录路径
	 */
	private function _make_dir($dir){
		$dir = FCPATH.$dir;
		if (!is_dir($dir)){
            $res = mkdir($dir, 0755, true);
		}
	}

	/**
	 * 根据文件md5码获取相关信息
	 * @param string $file_md5 根据文件md5码获取相关信息
	 * @param string $type 想查询的字段,默认为id
	 * @return 成功返回图片路径id,失败返回false
	 */
	 public function query_by_type($file_md5, $type = 'id'){
	 	if(empty($file_md5)){
	 		return FALSE;
	 	}
		$result = $this->_CI->db->select($type)->where('md5', $file_md5)->get($this->_table);
		if($result->num_rows() <= 0){
			return '不存在';
		}else{
			$result = $result->result_array();
			return $result[$type];
		}
	 }

	/**
	 * 获取商品轮播图
	 */
    public function get_commodity_pics($commodity_id, $commodity_type, $limit = 5) {
        // todo maybe we can set the default image for commodity
        $path = NULL;
        if ($commodity_id && $commodity_type) {

			$this->_CI->db->select("Jibian_attachment.id, Jibian_attachment.name, Jibian_attachment.path");
            $this->_CI->db->from('Jibian_attachment');
			$this->_CI->db->join('Jibian_commodity_pic', "Jibian_commodity_pic.attachment_id = Jibian_attachment.id");
			$this->_CI->db->where('Jibian_commodity_pic.commodity_id', $commodity_id);
			$this->_CI->db->where('Jibian_commodity_pic.commodity_type', $commodity_type);
			$this->_CI->db->limit($limit);
			$res = $this->_CI->db->get();
            if ($res->num_rows() > 0) {
            	if ($limit == 1) {
            		$path = $res->row_array();
					$path = $path['path'];
            	} else if ($limit > 1) {
            		$path = $res->result_array();
            	}
            }
        }

        return $path;
    }

}
