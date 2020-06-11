<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_email.php
 *
 *     Description: 邮件发送类库
 *
 *         Created: 2017-3-13 14:23:50
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

require_once FCPATH."application/third_party/phpmailer/class.phpmailer.php";

class Jys_email
{
    private $_CI;
    private $_host;
    private $_username;
    private $_password;
    private $_port;
    private $_form;
    private $_form_name;

    /**
     * 构造函数
     */
    public function __construct() {
        $this->_CI = & get_instance();
        $this->_host = $this->_CI->config->item('mail_smtp_host');
        $this->_username = $this->_CI->config->item('mail_smtp_username');
        $this->_password = $this->_CI->config->item('mail_smtp_password');
        $this->_port = $this->_CI->config->item('mail_smtp_port');
        $this->_form = $this->_CI->config->item('mail_smtp_form');
        $this->_form_name = $this->_CI->config->item('mail_smtp_form_name');
    }

    /**
     * 发送邮件
     *
     * @param null $to_mail 收件人邮箱
     * @param null $subject 邮件标题
     * @param null $content 邮件内容
     * @param null $reply_to 回复人邮箱
     * @param null $reply_to_name 回复人邢敏
     * @return bool
     * @throws phpmailerException
     */
    public function send_email($to_mail = NULL, $subject = NULL, $content = NULL, $reply_to = NULL, $reply_to_name = NULL){
        $mail = new PHPMailer();
        //配置参数
        $mail->isSMTP();
        $mail->Host = $this->_host;
        $mail->SMTPAuth = TRUE;
        $mail->Username = $this->_username;
        $mail->Password = $this->_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = $this->_port;

        //配置信息
        $mail->setFrom($this->_form, $this->_form_name);
        $mail->addAddress($to_mail);
        $mail->addReplyTo($reply_to, $reply_to_name);
        $mail->isHTML(TRUE);

        $mail->Subject = $subject;
        $mail->Body = $content;

        //发送邮件
        if (!$mail->send()) {
            return FALSE;
        }else{
            return TRUE;
        }
    }

}