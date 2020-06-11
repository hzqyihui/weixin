<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_attachment.php
 *
 *     Description:  作品上传类
 *
 *         Created:  2016-5-12 15:58:59
 *
 *          Author:  sunzuosheng
 *
 * =====================================================================================
 */
class Swcomp_attachment {
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
		$config['allowed_types'] = 'zip|tar|rar|7z|iso|cab|gz|gif|jpg|png|bmp|jpeg';
		$config['file_name'] = md5(time());
		$config['max_size'] = '0';
		$config['encrypt_name'] = 'TRUE';

		$this->_CI->load->library('upload', $config);
		if (!$this->_CI->upload->do_upload('file')){
			$info['error'] = array('error'=> $this->_CI->upload->display_errors());
		}else{
			$data = array('upload_data'=>$this->_CI->upload->data());
			//为附件处理数据
			$info['success'] = TRUE;
			$info['name'] = $data['upload_data']['raw_name'];
			$info['file_path'] = $config['upload_path'] . $data['upload_data']['file_name'];
			$info['md5_code'] = md5_file($info['file_path']);
		}

		return $info;
	}

    /**
     * 保存附件信息
     * @param  array  $array 附件信息
     * @return array         附件上传是否成功
     */
    public function insert_attachment($array = array(), $user_id = NULL, $competition_id = NULL){
        if (empty($array) || !is_array($array) || empty($user_id)){
            return FALSE;
        }
        //开始事务
        $this->_CI->db->trans_start();
        $this->_CI->db->where('md5_code', $array['md5_code']);
        $info = $this->_CI->db->get('swcomp_attachment');
        if (!empty($info) && $info->num_rows() > 0) {
            $info = $info -> row_array();
            $attachment_user['attachment_id'] = $info['id'];
        }else {
            $attachment['name'] = $array['name'];
            $attachment['path'] = $array['file_path'];
            $attachment['md5_code'] = $array['md5_code'];
            $this->_CI->db->insert('swcomp_attachment', $attachment);
            if ($this->_CI->db->affected_rows() > 0) {
                $attachment_user['attachment_id'] = intval($this->_CI->db->insert_id());
            }else {
                // 事务完成
                $this->_CI->db->trans_complete();
                return FALSE;
            }
        }
        $result = array('name'=>$array['name'], 'path'=>$array['file_path'], 'md5_code'=>$array['md5_code'], 'attachment_id'=>$attachment_user['attachment_id']);
		$attachment_user_id = $this->save_attachment_user($user_id, $attachment_user['attachment_id']);

        // 事务完成
        $this->_CI->db->trans_complete();
        if (intval($attachment_user_id) > 0) {
            $result['attachment_user_id'] = $attachment_user_id;
        }

        return $result;
    }

    /**
     * 保存附件用户信息
     * @param  array  $array 用户上传文件数据
     * @return boolean       成功返回attachment_user_id，失败返回FASLE
     */
    private function save_attachment_user($user_id = 0, $attachment_id = 0, $competition_id = 1){
        if (intval($user_id) < 1 || intval($attachment_id) < 1){
            return FALSE;
        }
        $attachment_user['attachment_id'] = $attachment_id;
        if (intval($competition_id) > 0) {
            $attachment_user['competition_id'] = $competition_id;
        }
        $attachment_user['user_id'] = $user_id;
        $attachment_user['time'] = date("Y-m-d H:i:s");

        $attachment_user_info = $this->get_attachment_user($user_id, $attachment_id);
        if (empty($attachment_user_info)){
            $this->_CI->db->insert('swcomp_attachment_user_r', $attachment_user);
            if ($this->_CI->db->affected_rows() > 0){
                return $this->_CI->db->insert_id();
            }
            return FALSE;
        }
        return $attachment_user_info['id'];
    }

	/**
	 * 检验目录是否存在，不存在则新建
	 * @param  string $dir 目录路径
	 */
	private function _make_dir($dir){
		if (!is_dir($dir)){
            $res = mkdir($dir, 0755, true);
		}
	}

	/**
	 * 根据附件id获取附件路径
	 * @param integer	$attachment_id	附件id
	 * @return array					成功返回附件路径,失败返回FALSE
	 */
	public function get_path($attachment_id){
	    $result = $this->_CI->db->where('id', $attachment_id)->get($this->_table);
		if ($result->num_rows() == 0){
		    return FALSE;
		}
		$attachment = $result->row_array();
		return $attachment['path'];
	}

	/**
	 *  获取阶段附件表信息
	 * @param  integer $team_id 团队id
	 * @return array            成功返回阶段附件信息，失败返回FALSE
	 */
	public function get_stage_attachment($team_id = 0, $stage_id = 0){
		if (empty($team_id) || intval($team_id) < 1 || empty($stage_id) || intval($stage_id) < 1){
			return FALSE;
		}

        $this->_CI->db->where('team_id', $team_id);
        $this->_CI->db->where('competition_stage_id', $stage_id);
		$result = $this->_CI->db->get('swcomp_stage_attachment');

		if ($result->num_rows() == 0){
		    return FALSE;
		}
		return $result->row_array();
	}

	/**
	 * 根据user_id获取附件用户表信息
	 * @param  integer $user_id 用户id
     * @param  integer $attachment_id 附件ID
	 * @return array            成功返回阶段附件信息，失败返回FALSE
	 */
	public function get_attachment_user($user_id = 0, $attachment_id = 0){
		if (empty($user_id) || intval($user_id) < 0 || empty($attachment_id) || intval($attachment_id) < 1){
			return FALSE;
		}
        $this->_CI->db->where('user_id', $user_id);
        $this->_CI->db->where('attachment_id', $attachment_id);
		$result = $this->_CI->db->get('swcomp_attachment_user_r');

		if ($result->num_rows() == 0){
		    return FALSE;
		}
		return $result->row_array();
	}
}