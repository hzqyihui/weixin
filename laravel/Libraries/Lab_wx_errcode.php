<?php
/**
 *       Filename: Lab_wx_errcode.php
 *
 *    Description: 微信错误码
 *
 *        Created: 2017-05-31 19:56
 *
 *         Author: huazhiqiang
 */
namespace App\Libraries;

use Illuminate\Http\Request;

class Lab_wx_errcode{
    /**
     * error code 说明.
     * <ul>
     *    <li>-40001: 签名验证错误</li>
     *    <li>-40002: xml解析失败</li>
     *    <li>-40003: sha加密生成签名失败</li>
     *    <li>-40004: encodingAesKey 非法</li>
     *    <li>-40005: corpid 校验错误</li>
     *    <li>-40006: aes 加密失败</li>
     *    <li>-40007: aes 解密失败</li>
     *    <li>-40008: 解密后得到的buffer非法</li>
     *    <li>-40009: base64加密失败</li>
     *    <li>-40010: base64解密失败</li>
     *    <li>-40011: 生成xml失败</li>
     * </ul>
     */
    public static $OK = 0;
    public static $ValidateSignatureError = -40001;
    public static $ParseXmlError = -40002;
    public static $ComputeSignatureError = -40003;
    public static $IllegalAesKey = -40004;
    public static $ValidateCorpidError = -40005;
    public static $EncryptAESError = -40006;
    public static $DecryptAESError = -40007;
    public static $IllegalBuffer = -40008;
    public static $EncodeBase64Error = -40009;
    public static $DecodeBase64Error = -40010;
    public static $GenReturnXmlError = -40011;
}