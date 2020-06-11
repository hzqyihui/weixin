<?php
/**
 * =====================================================================================
 *
 *        Filename: Jys_soap.php
 *
 *     Description: SOAP接口
 *
 *         Created: 2017-09-05 10:46:46
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Jys_soap
{
    private $_CI;
    private $_url;
    private $_username = 'shsajy';
    private $_userpwd = 'inca1113';
    private $_client;

    public function __construct()
    {
        $this->_CI = &get_instance();
//        $this->_url = 'http://101.132.27.20:8080/shsajy/services/ERPServices?wsdl';   //测试环境
        $this->_url = 'http://101.132.27.20:9008/shsajy/services/ERPServices?wsdl';     //正式环境
        $this->_client = new SoapClient($this->_url);
    }

    /**
     * 客户主数据新增启用、修改
     * @param $start_time 开始时间
     * @param $end_time 结束时间
     * @return array|mixed
     */
    public function agent($start_time, $end_time)
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'BASE01';
            $array['startTime'] = $start_time;
            $array['endTime'] = $end_time;
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
           printf('Message = %s',$e->__toString());
            //添加日志
            $add = [
                'success' => Jys_system_code::ERP_STATUS_FAIL,
                'msg' => '同步客户数据出错('.$e->__toString().')',
                'interface_name' => jys_system_code::ERP_NAME_USER_INCREASE_ERP_DS,
                'code' => jys_system_code::ERP_CODE_BASE01,
                'create_time' => date("Y-m-d H:i:s"),
                'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
            ];
            $log_res = $this->jys_db_helper->add('log', $add);
            return;
       }
       $result = json_decode($response->return, TRUE);
       return $result;
    }

    /**
     * 货品主数据新增启用、修改
     * @param $start_time 开始时间
     * @param $end_time 结束时间
     * @return array|mixed
     */
    public function commodity($start_time, $end_time)
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'BASE02';
            $array['startTime'] = $start_time;
            $array['endTime'] = $end_time;
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
            printf('Message = %s',$e->__toString());
            //添加日志
            $add = [
                'success' => Jys_system_code::ERP_STATUS_FAIL,
                'msg' => '同步商品数据出错('.$e->__toString().')',
                'interface_name' => jys_system_code::ERP_NAME_GOODS_INCREASE_ERP_DS,
                'code' => jys_system_code::ERP_CODE_BASE02,
                'create_time' => date("Y-m-d H:i:s"),
                'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
            ];
            $log_res = $this->jys_db_helper->add('log', $add);
            return;
        }
        $result = json_decode($response->return, TRUE);
        return $result;
    }

    /**
     * 订单主数据新增
     * @param $start_time 开始时间
     * @param $end_time 结束时间
     * @return array|mixed
     */
    public function order_increase_to_ds ($start_time, $end_time)
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'SA01';
            $array['startTime'] = $start_time;
            $array['endTime'] = $end_time;
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
            printf('Message = %s',$e->__toString());
            //添加日志
            $add = [
                'success' => Jys_system_code::ERP_STATUS_FAIL,
                'msg' => '同步订单数据出错('.$e->__toString().')',
                'interface_name' => jys_system_code::ERP_NAME_ORDER_INCREASE_ERP_DS,
                'code' => jys_system_code::ERP_CODE_SA01,
                'create_time' => date("Y-m-d H:i:s"),
                'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
            ];
            $log_res = $this->jys_db_helper->add('log', $add);
            return;
        }
        $result = json_decode($response->return, TRUE);
        return $result;
    }

    /**
     * 订单主数据作废
     * @param $start_time 开始时间
     * @param $end_time 结束时间
     * @return array|mixed
     */
    public function order_cancel_to_ds ($start_time, $end_time)
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'SA02';
            $array['startTime'] = $start_time;
            $array['endTime'] = $end_time;
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
            printf('Message = %s',$e->__toString());
            //添加日志
            $add = [
                'success' => Jys_system_code::ERP_STATUS_FAIL,
                'msg' => '同步作废订单数据出错('.$e->__toString().')',
                'interface_name' => jys_system_code::ERP_NAME_FOUR_ORDER_CANCEL_ERP_DS,
                'code' => jys_system_code::ERP_CODE_SA02,
                'create_time' => date("Y-m-d H:i:s"),
                'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
            ];
            $log_res = $this->jys_db_helper->add('log', $add);
            return;
        }
        $result = json_decode($response->return, TRUE);
        return $result;
    }

    /**
     * 订单退货单
     * @param $start_time 开始时间
     * @param $end_time 结束时间
     * @return array|mixed
     */
    public function order_refund_to_ds ($start_time, $end_time)
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'SA03';
            $array['startTime'] = $start_time;
            $array['endTime'] = $end_time;
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
            printf('Message = %s',$e->__toString());
            //添加日志
            $add = [
                'success' => Jys_system_code::ERP_STATUS_FAIL,
                'msg' => '同步退货订单数据出错('.$e->__toString().')',
                'interface_name' => jys_system_code::ERP_NAME_RETURN_GOODS_ERP_DS,
                'code' => jys_system_code::ERP_CODE_SA03,
                'create_time' => date("Y-m-d H:i:s"),
                'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
            ];
            $log_res = $this->jys_db_helper->add('log', $add);
            return;
        }
        $result = json_decode($response->return, TRUE);
        return $result;
    }

    /**
     * 增加订单到ERP系统（传到ERP系统）
     * @param array $order_array 订单数据
     * @return array|mixed
     */
    public function order_increase_to_erp ($order_array = [], $number = '')
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'DS01';
            $array['dataList'] = $order_array;
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
            printf('Message = %s',$e->__toString());
            //添加日志
            $add = [
                'success' => Jys_system_code::ERP_STATUS_FAIL,
                'msg' => '订单编号为：'.$number.'的订单销新增(DS-ERP)失败。('.$e->__toString().')',
                'interface_name' => jys_system_code::ERP_NAME_SIX_ORDER_INCREASE_DS_ERP,
                'code' => jys_system_code::ERP_CODE_DS01,
                'create_time' => date("Y-m-d H:i:s"),
                'level' => jys_system_code::ERP_RETURN_STATUS_FAIL
            ];
            $log_res = $this->jys_db_helper->add('log', $add);
            return;
        }
        $result = json_decode($response->return, TRUE);
        return $result;
    }

    /**
     * 订单取消（传到ERP系统）
     * @param array $order_array 订单数据
     * @return array|mixed
     */
    public function order_cancel_to_erp ($order_array = [], $numbers = [])
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'DS02';
            $array['dataList'] = $order_array;
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
            printf('Message = %s',$e->__toString());
            //添加日志
            if (!empty($numbers)) {
                foreach ($numbers as $key => $value) {
                    $add[$key] = [
                        'success' => Jys_system_code::ERP_STATUS_FAIL,
                        'msg' => '订单编号为：'.$value['number'].'的订单取消(DS-ERP)失败。('.$e->__toString().')',
                        'interface_name' => jys_system_code::ERP_NAME_SEVEN_ORDER_CANCEL_DS_ERP,
                        'level' => jys_system_code::ERP_RETURN_STATUS_FAIL,
                        'code' => jys_system_code::ERP_CODE_DS02,
                        'create_time' => date("Y-m-d H:i:s")
                    ];       
                }
                $log_res = $this->jys_db_helper->add_batch('log', $add);
            }
            return;
        }
        $result = json_decode($response->return, TRUE);
        return $result;
    }

    /**
     * C端回传检测信息（传到ERP系统）
     * @param array $inspection_array 监测信息
     * @return array|mixed
     */
    public function inspection_information_to_erp ($inspection_array = [], $flag = 0)
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'DS03';
            $array['dataList'] = $inspection_array;
            $array['updateflag'] = intval($flag);
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
            printf('Message = %s',$e->__toString());
            //添加日志
            foreach ($inspection_array as $key => $value) {
                $add[$key] = [
                    'success' =>jys_system_code::ERP_STATUS_FAIL,
                    'msg' => '检测码为：'.$array['dataList'][0]['test_code'].'的报告信息(DS-ERP)添加失败。('.$e->__toString().')',
                    'interface_name' => jys_system_code::ERP_NAME_DETECTION_INFORMATION_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS03,
                    'level' => jys_system_code::ERP_RETURN_STATUS_FAIL,
                    'create_time' => date("Y-m-d H:i:s")
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
            return;
        }
        $result = json_decode($response->return, TRUE);
        return $result;
    }

    /**
     * 报告状态回传（传到ERP系统）
     * @param array $report_status_array 监测信息
     * @return array|mixed
     */
    public function report_status_to_erp ($report_status_array = [])
    {
        try {
            $array['username'] = $this->_username;
            $array['password'] = strtoupper(md5($this->_userpwd));
            $array['businessType'] = 'DS04';
            $array['dataList'] = $report_status_array;
            $parmIn = json_encode($array);
            $temp = ['paramIN' => $parmIn];
            $response = $this->_client->callErpBusiness($temp);
        }catch (SoapFault $e) {
            printf('Message = %s',$e->__toString());
            //添加日志
            foreach ($report_status_array as $key => $value) {
                $add[$key] = [
                    'success' => jys_system_code::ERP_STATUS_FAIL,
                    'msg' => '检测码为：'.$value['test_code'].'的上传报告人信息(DS-ERP)添加失败。('.$e->__toString().')',
                    'interface_name' => jys_system_code::ERP_NAME_REPORT_RETURN_DS_ERP,
                    'code' => jys_system_code::ERP_CODE_DS04,
                    'level' => jys_system_code::ERP_RETURN_STATUS_FAIL,
                    'create_time' => date("Y-m-d H:i:s")
                ];
            }
            $log_res = $this->jys_db_helper->add_batch('log', $add);
            return;
        }
        $result = json_decode($response->return, TRUE);
        return $result;
    }

}