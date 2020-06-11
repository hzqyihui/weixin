<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_attachment.php
 *     Description: 附件类
 *         Created: 2016-11-12 21:21:02
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Jys_attachment {
    private $_CI;
    private $_table;

    public function __construct(){
        $this->_CI =& get_instance();
        $this->_table = "attachment";
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

        // 允许上传的文件类型
        $allowed_types = array('.png', '.jpg', '.jpeg', '.gif', '.pdf');
        $this->_make_dir($dir);
        $file_arr = explode('.', $_FILES['file']['name']);
        $file_ext = $file_arr[count($file_arr) - 1];
        if (in_array(strtolower($file_ext), $allowed_types)){
            $config['file_name'] = md5(time()).'.jpg';
        }else{
            $config['file_name'] = md5(time());
        }

        $config['upload_path'] = $dir;
        $config['allowed_types'] = '*';
        $config['max_size'] = '5120';
        //$config['max_width'] = '1024';
        //$config['max_height'] = '768';
        $config['encrypt_name'] = 'TRUE';

        $this->_CI->load->library('upload', $config);
        $this->_CI->upload->initialize($config);

        if (!$this->_CI->upload->do_upload('file') && !$this->_CI->upload->do_upload('imgFile')){
            $info['error'] = array('error'=> $this->_CI->upload->display_errors());
        }else{
            $data = array('upload_data'=>$this->_CI->upload->data());

            if ($data['upload_data']['file_ext'] == ''){
                $data['upload_data']['file_ext'] = $file_arr[count($file_arr) - 1];
                $data['upload_data']['file_name'] .= '.jpg';
            }

            // 校验文件后缀名
            if (!in_array(strtolower($data['upload_data']['file_ext']), $allowed_types)) {
                // 删除不符合上传类型的文件
                unlink($data['upload_data']['full_path']);
            }
            //为附件处理数据
            if ($data['upload_data']['file_ext'] == ''){
                $info['success'] = FALSE;
                $info['md5'] = '';
                $info['msg'] = '文件格式不正确，请选择其他文件';
            }else{
                $info['success'] = TRUE;
                $info['path']   = $config['upload_path'] . $data['upload_data']['file_name'];
                $info['md5']    = md5_file($info['path']);
            }
        }

        return $info;
    }

    /**
     * 上传excel模版文件处理
     * @param  string $dir 文件上传路径
     * @return array       失败则返回失败信息，成功则返回文件信息
     */
    public function upload_excel_attachment($dir = 'source/excel/'){
        $info['success'] = FALSE;
        if (empty($dir)){
            return $info;
        }
        $this->_make_dir($dir);
        $config['upload_path'] = $dir;
        $config['allowed_types'] = 'xlsx|xls';
        $config['file_name'] = md5(time());
        $config['max_size'] = '5120';
        $config['encrypt_name'] = 'TRUE';

        $this->_CI->load->library('upload', $config);
        $this->_CI->upload->initialize($config);

        if (!$this->_CI->upload->do_upload('file')){
            $info['error'] = array('error'=> $this->_CI->upload->display_errors());
        }else{
            $data = array('upload_data'=>$this->_CI->upload->data());
            //为附件处理数据
            $info['success'] = TRUE;
            $info['path']   = $config['upload_path'] . $data['upload_data']['file_name'];
        }
        return $info;
    }

    /**
     * 保存附件信息
     * @param  array  $array 附件信息
     * @return array         附件上传是否成功
     */
    public function save_attachment($array = array()){
        $data['success'] = FALSE;
        $data['msg'] = '文件上传失败';

        if (empty($array) || !is_array($array)){
            return $data;
        }

        $attachment['path'] = $array['path'];
        $attachment['md5'] = $array['md5'];
        $attachment['create_time'] = date('Y-m-d H:i:s');
        $this->_CI->db->insert('attachment', $attachment);
        if ($this->_CI->db->affected_rows() > 0) {
            $data['success'] = TRUE;
            $data['path'] = $attachment['path'];
            $data['msg'] = '文件上传成功';
        }
        return $data;
    }

    /**
     * 保存附件用户信息
     * @param  int	$attachment_id 附件ID
     * @return bool                附件用户信息是否保存成功,成功为TRUE，失败为FALSE
     */
    public function save_attachment_user($attachment_id, $user_id){
        if (empty($attachment_id) || is_null($attachment_id) || intval($user_id) < 1){
            return FALSE;
        }
        $attachment_user['attachment_id'] = $attachment_id;
        $attachment_user['user_id'] = $user_id;
        $attachment_user['create_time'] = date('Y-m-d H:i:s');
        $this->_CI->db->insert('attachment_user', $attachment_user);
        if ($this->_CI->db->affected_rows() > 0) {
            return TRUE;
        }
        return FALSE;
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
     * 校验文件MD5是否存在
     *
     * @param null $md5_code 文件MD5码
     * @return array|bool|null
     */
    public function check_md5_code($md5_code = NULL){
        if (empty($md5_code)){
            return ['exist' => FALSE];
        }

        if (is_array($md5_code)){
            foreach ($md5_code as $key => $md5){
                $result = $this->_CI->db->where('md5', $md5)->get($this->_table);
                if ($result->num_rows() > 0){
                    $md5_code[$key]['exist'] = TRUE;
                    $md5_code[$key]['attachment_id'] = $result->row_array()['id'];
                    $md5_code[$key]['path'] = $result->row_array()['path'];
                }else{
                    $md5_code[$key]['exist'] = FALSE;
                }
            }

            return $md5_code;
        }else{
            $result = $this->_CI->db->where('md5', $md5_code)->get($this->_table);
            if ($result->num_rows() > 0){
                return [
                    'exist' => TRUE,
                    'attachment_id' => $result->row_array()['id'],
                    'path' => $result->row_array()['path']
                ];
            }

            return ['exist' => FALSE];
        }
    }

    /**
     * 上传剪切的头像
     *
     * @param string $dataUrl 头像URL
     * @param string $dir 保存路径
     * @return mixed
     */
    public function upload_clip_avatar($user_id, $dataUrl = '', $dir = 'source/uploads/'){
        $data['msg'] = "上传头像失败";
        $data['success'] = FALSE;

        if (empty($dataUrl) || empty($dir)){
            return $data;
        }

        $this->_make_dir($dir);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $dataUrl, $result)){
            $new_name = 'u'.$user_id.md5(time()).'.'.$result[2];
            if (file_put_contents($dir.$new_name, base64_decode(str_replace($result[1], '', $dataUrl)))){
                $data['success'] = TRUE;
                $data['path'] = $dir.$new_name;
                $data['md5'] = md5_file($data['path']);
            }
        }

        return $data;
    }

}