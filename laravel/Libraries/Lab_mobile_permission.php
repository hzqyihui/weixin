<?php
/**
 *       Filename: Lab_log.php
 *
 *    Description: 手机解密获取数据类库
 *
 *        Created: 2017-04-06 18:38
 *
 *         Author: huazhiqiang
 */
namespace App\Libraries;

use App\Libraries\Lab_encrypt;

class Lab_mobile_permission {
    private $mobile;
    /*构造方法*/
    function __construct() {
        $this->mobile = new Lab_encrypt();
    }
    
    /**
     * 根据移动端发来的验证密钥返回用户ID，如果不对，则返回0
     */
    function get_customer_id($request){
        // 接收移动端发来的验证密钥
        if (!empty($request->input('customer_id'))){
            return 0;
        }
        $permission = $request->input('customer_id');
        $log_str = "移动端发来customer_id,原文:".$permission;
        if (preg_match('/%[a-zA-Z0-9]{2}/', $permission)){
            // 需要进行URL解码
            $permission = urldecode($permission);
            $log_str .= ",经过URL解码之后:".$permission;
        }
        $permission = $this->mobile->decrypt($permission);
        $log_str .= ",3DES解码之后:".$permission;
        $timeLenth = strlen(date("Y-m-d H:i:s"));
        // 取得用户名
        $user = substr($permission, 0, strlen($permission)-$timeLenth);
        $log_str .= ",用户名:".$user;
        // 取得时间
        $date = substr($permission, strlen($user));
        $login_date = strtotime($date);
        $log_str .= ",登录时间:".date("Y-m-d H:i:s", $login_date);
        $current_date = strtotime(date("Y-m-d H:i:s"));
        // 如果超过一周时间，那就要重新登录
        if ($current_date - $login_date > (60 * 60 * 24 * 7)){
            return 0;
        }
        // 取用户ID
        $this->mobile->db->select('id');
        $this->mobile->db->where("username",$user);
        $row = $this->mobile->db->get("customer");
        if ($row && $row->num_rows() > 0){
            $row = $row -> row_array();
            $log_str .= ",用户ID:".$row['id'];
            file_put_contents("permissionlog.txt", "执行日期：".strftime("%Y-%m-%d %H:%M:%S",time())."\n".$log_str."\n", FILE_APPEND|LOCK_EX);
            return $row['id'];
        }else {
            $log_str .= ",用户ID:0";
            file_put_contents("permissionlog.txt", "执行日期：".strftime("%Y-%m-%d %H:%M:%S",time())."\n".$log_str."\n", FILE_APPEND|LOCK_EX);
            return 0;
        }
    }
}

/* End of file myencryption.php */