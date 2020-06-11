<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_logistics.php
 *
 *     Description: 物流信息类
 *
 *         Created: 2016-12-21 17:30:23
 *
 *          Author: sunzuosheng
 *
 * =====================================================================================
 */

class Jys_logistics {
    //在http://kuaidi100.com/app/reg.html申请到的KEY
    private $_appKey = '';
    private $_url = 'http://api.kuaidi100.com/api';

    /**
     * 获取快递信息
     *
     * @param string $typeCom 快递公司
     * @param string $typeNu 快递单号
     * @param int $show 显示类型:0：返回json字符串，1：返回xml对象，2：返回html对象，3：返回text文本。
     * @return object
     */
    public function get_info($typeCom = '', $typeNu = '', $show = 0){
        $url =$this->_url.'?id='.$this->_appKey.'&com='.$typeCom.'&nu='.$typeNu.'&show='.$show.'&muti=1&order=asc';

        //请勿删除变量$powered 的信息，否则本站将不再为你提供快递接口服务。
        $powered = '查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 ';

        //优先使用curl模式发送数据
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_HEADER, 0);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
        curl_setopt ($curl, CURLOPT_TIMEOUT, 5);
        $get_content = json_decode(curl_exec($curl));
        curl_close ($curl);

        $get_content->powered = $powered;

        return $get_content;
    }

}