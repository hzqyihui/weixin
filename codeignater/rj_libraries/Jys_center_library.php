<?php
/**
 * =====================================================================================
 *
 *        Filename: Jys_center_library.php
 *
 *     Description: 中间库类库
 *
 *         Created: 2017-07-10 14:50:50
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Jys_center_library{
    private $_CI;

    /**
     * 构造函数
     */
    public function __construct(){
        $this->_CI =& get_instance();
        $this->_CI->load->library(['Jys_sql_server']);
    }

    /**
     * 分页获取中间库商品表中商品基本信息
     * @param int $page 当前页数
     * @param int $pagesize 每页显示数量
     * @return mixed 从中间库中查出的商品基本信息
     */
    public function get_commodity_info_from_center_library($page = 1, $pagesize = 10)
    {
        $data = $this->_CI->jys_sql_server->get_where('product', $page, $pagesize,'spid as erp_id, spbh as number, 欠款价 as arrears_price,现金价 as cash_price, 超市价 as supermarket_price,
                                                                                                基药价 as basic_medicine, 库存 as store, 批号 as batch_number, 效期 as expiry_date,
                                                                                                商品名称 as name, 批准文号 as approval_number, 生产厂家 as manufacturer,
                                                                                                通用名 as common_name, 剂型 as dosage_form, 件装 as piece_pack,
                                                                                                中包装 as middle_pack, 是否控销 as control_sales, 
                                                                                                销售方式 as sales_method, 采购员 as buyer, 包装单位 as pack_units,
                                                                                                生产日期 as production_date, 商品类别 as type,
                                                                                                拼音简码 as PY_brevity_code, 规格 as specification', 'spid');
        return $data;
    }

    /**
     * 根据Id批量获取中间库商品表中商品基本信息
     * @param array $condition 每页显示数量
     * @return mixed 从中间库中查出的商品基本信息
     */
    public function get_single_commodity_info_from_center_library($condition = [])
    {
        //拼接IN_WHERE语句，拼接为('sph21312', 2, 23)
        $in_where = "(";
        for ($i = 0; $i < count($condition); $i++){
            if ($i == (count($condition) - 1)){
                if (is_string($condition[$i])) {
                    //数组value为字符串时，拼接''
                    $in_where .= "'".$condition[$i]."')";
                }elseif (is_int($condition[$i])) {
                    dd($condition[$i]);
                    //数组value为整形时
                    $in_where .= $condition[$i].")";
                }
            }else{
                if (is_string($condition[$i])) {
                    //数组value为字符串时，拼接''
                    $in_where .= "'".$condition[$i]."',";
                }elseif (is_int($condition[$i])) {
                    dd($condition[$i]);
                    //数组value为整形时
                    $in_where .= $condition[$i].",";
                }
            }
        }
    
        $data = $this->_CI->jys_sql_server->get_where('product', NULL, NULL,'spid as erp_id, spbh as number, 欠款价 as arrears_price,现金价 as cash_price, 超市价 as supermarket_price,
                                                                                                基药价 as basic_medicine, 库存 as store, 批号 as batch_number, 效期 as expiry_date,
                                                                                                商品名称 as name, 批准文号 as approval_number, 生产厂家 as manufacturer,
                                                                                                通用名 as common_name, 剂型 as dosage_form, 件装 as piece_pack,
                                                                                                中包装 as middle_pack, 是否控销 as control_sales, 包装单位 as pack_units, 
                                                                                                生产日期 as production_date, 商品类别 as type,
                                                                                                拼音简码 as PY_brevity_code, 规格 as specification','spid IN '.$in_where
                                                                                                );
        return $data;
    }


    /**
     * 获取商品表总页数
     * @param int $pagesize 每页显示数量
     * @return mixed 总页数
     */
    public function get_commodity_info_total_page_from_center_library($pagesize = 10)
    {
        $total_count = $this->_CI->jys_sql_server->get_total_count('product', 'spid');
        $total_page = ceil($total_count/$pagesize * 1.0);
        return $total_page;
    }

    /**
     * 根据状态获取中间库商品消息表商品状态消息
     * @param int $action_code 操作 0 删除 1 增加  2 修改
     * @param int $handleStatus 是否处理 0未处理 1已经处理 2 处理失败(默认为0) 101回写
     * @return mixed
     */
    public function get_commodity_note_from_center_library($action_code = jys_system_code::CENTER_LIBRARY_OPERATION_INCREMENT, $handleStatus = jys_system_code::CENTER_LIBRARY_HANDLE_NO)
    {
        $data = $this->_CI->jys_sql_server->get_where('I_ERP_Product_NOTE',NULL, NULL,'*','actionCode = '.$action_code.' AND handleStatus = '.$handleStatus);
        return $data;
    }

    /**
     * 更新中间库商品消息表
     * @param int $action_code 事件代码 1 增加 2 修改 0 删除
     * @param int $handle_status 处理状态 0 － 未处理 ，1－处理成功，2－处理失败
     * @param string $notes 如果处理失败则填写处理失败原因
     * @param int $id
     * @return mixed
     */
    public function update_commodity_note_from_center_library($action_code = 2, $handle_status = 1, $notes = '', $id = 1)
    {
        $update_sql = 'update I_ERP_Product_NOTE set handleTime = ?, actionCode = ?, handleStatus = ?, notes = ? where id = ?';
        $data[0] = date("Y-m-d H:i:s",time());   //处理时间
        $data[1] = $action_code;
        $data[2] = $handle_status;
        $data[3] = $notes;
        $data[4] = $id;
        $data = $this->_CI->jys_sql_server->update($update_sql,$data);
        return $data;
    }

    /**
     * 根据状态获取中间库订单消息表商品状态消息
     * @param int $action_code 操作 0 删除 1 增加  2 修改
     * @param int $handleStatus 是否处理 0未处理 1已经处理 2 处理失败(默认为0) 101回写
     * @return mixed
     */
    public function get_order_note_from_center_library($action_code = jys_system_code::CENTER_LIBRARY_OPERATION_INCREMENT, $handleStatus = jys_system_code::CENTER_LIBRARY_HANDLE_NO)
    {
        $data = $this->_CI->jys_sql_server->get_where('I_ERP_Order_NOTE',NULL, NULL, '*', 'id', 'actionCode = '.$action_code.' AND handleStatus = '.$handleStatus);
        return $data;
    }

    /**
     * 根据状态获取中间库订单明细表订单状态信息
     * @param int $status 操作状态 订单商品现在的状态（对应 6＝已取消 9＝货物已发出）
     * @param int $handleStatus  处理状态 是否处理 0未处理 1已经处理 2 处理失败
     * @return mixed
     */
    public function get_order_status_note_from_center_library($status = 6, $handleStatus = 0)
    {
        $data = $this->_CI->jys_sql_server->get_where('I_ERP_OrderStatus_NOTE',NULL, NULL,'*','id','Status = '.$status.' AND handleStatus = '.$handleStatus);
        return $data;
    }

    /**
     * 获取会员表总页数
     * @param int $pagesize 每页显示数量
     * @return mixed 总页数
     */
    public function get_member_info_total_page_from_center_library($pagesize = 10)
    {
        $total_count = $this->_CI->jys_sql_server->get_total_count('memberinfo', '会员编号');
        $total_page = ceil($total_count/$pagesize * 1.0);
        return $total_page;
    }

    /**
     * 分页获取中间库会员表中会员基本信息
     * @param int $page 当前页数
     * @param int $pagesize 每页显示数量
     * @return mixed 从中间库中查出的会员基本信息
     */
    public function get_member_info_from_center_library($page = 1, $pagesize = 10)
    {
        $data = $this->_CI->jys_sql_server->get_where('memberinfo', $page, $pagesize,'会员编号 as number, 联系手机 as phone, 联系人名 as contacts,省 as province, 市 as city,
                                                                                                区 as district, 地址 as address, 坐机 as business_phone, 公司名称 as erp_name,
                                                                                                交易员 as salesman, 是否建档 as gsp_status, 未通过原因 as reason,
                                                                                                公司编号 as business_number, 企业类型 as type, 经营范围 as business_scope','会员编号');
        return $data;
    }

    /**
     * 根据Id批量获取中间库会员表中会员基本信息
     * @param array $condition 每页显示数量
     * @return mixed 从中间库中查出的会员基本信息
     */
    public function get_single_member_info_from_center_library($condition = [])
    {
        //拼接IN_WHERE语句，拼接为('sph21312', 2, 23)
        $in_where = "(";
        for ($i = 0; $i < count($condition); $i++){
            if ($i == (count($condition) - 1)){
                if (is_string($condition[$i])) {
                    //数组value为字符串时，拼接''
                    $in_where .= "'".$condition[$i]."')";
                }elseif (is_int($condition[$i])) {
                    dd($condition[$i]);
                    //数组value为整形时
                    $in_where .= $condition[$i].")";
                }
            }else{
                if (is_string($condition[$i])) {
                    //数组value为字符串时，拼接''
                    $in_where .= "'".$condition[$i]."',";
                }elseif (is_int($condition[$i])) {
                    dd($condition[$i]);
                    //数组value为整形时
                    $in_where .= $condition[$i].",";
                }
            }
        }
        $data = $this->_CI->jys_sql_server->get_where('memberinfo', NULL, NULL,'会员编号 as number, 联系手机 as phone, 联系人名 as contacts,省 as province, 市 as city,
                                                                                                区 as district, 地址 as address, 坐机 as business_phone, 公司名称 as erp_name,
                                                                                                交易员 as salesman, 是否建档 as gsp_status, 未通过原因 as reason,
                                                                                                公司编号 as business_number, 企业类型 as type, 经营范围 as business_scope', '会员编号', '会员编号 IN '.$in_where
                                                                                                );
        return $data;
    }

    /**
     * 插入中间库会员表
     * @param array $data 订单明细信息
     * @param bool $return_insert_id 是否需要返回插入的Id
     * @return mixed
     */
    public function insert_member_info_to_center_library($result = [], $return_insert_id = FALSE)
    {
        $table = 'memberinfo';
        $insert_sql = 'insert into memberinfo(会员编号, 联系手机, 联系人名, 省, 市, 区, 地址, 坐机, 公司名称, 交易员, 是否建档, 未通过原因, 公司编号, 企业类型, 经营范围) values (:会员编号, :联系手机, :联系人名, :省, :市, :区, :地址, :坐机, :公司名称, :交易员, :是否建档, :未通过原因, :公司编号, :企业类型, :经营范围)';
        $data['会员编号'] = $result['number'];
        $data['联系手机'] = $result['phone'];
        $data['联系人名'] = $result['contacts'];
        $data['省'] = $result['province'];
        $data['市'] = '-';
        $data['区'] = '-';
        $data['地址'] = $result['address'];
        $data['坐机'] = '-';
        $data['公司名称'] = $result['name'];
        $data['交易员'] = $result['salesman'];
        $data['是否建档'] = 1; //1.建档
        $data['未通过原因'] = $result['reason'];
        $data['公司编号'] = $result['business_number'];
        $data['企业类型'] = $result['type'];
        $data['经营范围'] = $result['business_scope'];
        $data = $this->_CI->jys_sql_server->insert($insert_sql, $data, $return_insert_id, $table);
        return $data;
    }

    /**
     * 根据状态获取中间库会员消息表会员状态信息
     * @param int $action_code 操作状态 订单商品现在的状态（对应 6＝已取消 9＝货物已发出）
     * @param int $handleStatus  处理状态 是否处理 0未处理 1已经处理 2 处理失败
     * @return mixed
     */
    public function get_member_info_note_from_center_library($action_code = 6, $handleStatus = 1)
    {
        $data = $this->_CI->jys_sql_server->get_where('I_ERP_memberinfo_NOTE',NULL, NULL,'*', '会员编号', 'actionCode = '.$action_code.' AND handleStatus = '.$handleStatus);
        return $data;
    }

    //由于会员视图无法访问，此处暂时搁置。
    /**
     * 分页获取中间库会员表中会员基本信息
     * @param int $page 当前页数
     * @param int $pagesize 每页显示数量
     * @return mixed 从中间库中查出的会员基本信息
     */
    public function get_user_info_from_center_library($page = 1, $pagesize = 10)
    {
        $data = $this->_CI->jys_sql_server->get_where('Product_test', $page, $pagesize);
        return $data;
    }

    /**
     * 更新中间库会员消息表
     * @param int $action_code 事件代码 1 增加 2 修改 0 删除
     * @param int $handle_status 处理状态 0 － 未处理 ，1－处理成功，2－处理失败
     * @param string $notes 如果处理失败则填写处理失败原因
     * @param int $id
     * @return mixed
     */
    public function update_member_note_from_center_library($action_code = 2, $handle_status = 1, $notes = '', $id = 1)
    {
        $update_sql = 'update I_ERP_memberinfo_NOTE set handleTime = ?, actionCode = ?, handleStatus = ?, notes = ? where id = ?';
        $data[0] = date("Y-m-d H:i:s",time());   //处理时间
        $data[1] = $action_code;
        $data[2] = $handle_status;
        $data[3] = $notes;
        $data[4] = $id;
        $data = $this->_CI->jys_sql_server->update($update_sql,$data);
        return $data;
    }


    /**
     * 插入中间库消息表信息（由于商品消息表和会员消息表的结构一样，故此处用同一函数）
     * @param string $table 消息表名
     * @param int $handle_status 处理状态 0 － 未处理 ，1－处理成功，2－处理失败
     * @param string $notes 如果处理失败则填写处理失败原因
     * @param int $id 消息表Id
     * @param bool $return_insert_id 是否需要返回插入的Id
     * @return mixed
     */
    public function update_note_to_center_library($table, $handle_status = 0, $notes = '', $id = 1, $return_insert_id = FALSE)
    {
        $insert_sql = 'update '.$table.'set handleTime = ?, handleStatus = ?, notes = ? where id = ?';
        $data[0] = date("Y-m-d H:i:s",time());   //添加时间
        $data[1] = $handle_status;
        $data[2] = $notes;
        $data[3] = $id;
        $data = $this->_CI->jys_sql_server->insert($insert_sql, $data, $return_insert_id, $table);
        return $data;
    }

    /**
     * 根据Id批量获取中间库订单表中订单基本信息
     * @param array $condition 每页显示数量
     * @return mixed 从中间库中查出的商品基本信息
     */
    public function get_single_order_info_from_center_library($condition = [])
    {
        //拼接IN_WHERE语句，拼接为('sph21312', 2, 23)
        // dd($condition);
        $in_where = "(";
        for ($i = 0; $i < count($condition); $i++){
            if ($i == (count($condition) - 1)){
                if (is_string($condition[$i])) {
                    //数组value为字符串时，拼接''
                    $in_where .= "'".$condition[$i]."')";
                }elseif (is_int($condition[$i])) {
                    dd($condition[$i]);
                    //数组value为整形时
                    $in_where .= $condition[$i].")";
                }
            }else{
                if (is_string($condition[$i])) {
                    //数组value为字符串时，拼接''
                    $in_where .= "'".$condition[$i]."',";
                }elseif (is_int($condition[$i])) {
                    dd($condition[$i]);
                    //数组value为整形时
                    $in_where .= $condition[$i].",";
                }
            }
        }
        $data = $this->_CI->jys_sql_server->get_where('Orders', NULL, NULL, 'OrderId, UserName, ReceiverId, ShopDate, OrderDate, ConsigneeRealName, ConsigneeName, ConsigneePhone,
                                           ConsigneeProvince, ConsigneeAddress, ConsigneeZip, ConsigneeTel, ConsigneeFax, ConsigneeEmail, PaymentType,
                                           Payment, TotalPrice, Fees, OtherFees, Invoice, Remark, OrderStatus, PaymentStatus, OgisticsStatus, BusinessmanID,
                                           BusinessmanName, Carriage, OrderType, ContractNo, ConsigneeCity, ConsigneeBorough, ConsigneeConstructionSigns,
                                           ConsignesTime, TradeFees, TradeFeesPay, Editer, parentid, parentCorpName, BillingCorp, BillingCorpName, IsBusinessCheck,
                                           isFinancialReview, BusinessCheckDate, FinancialCheckDate, LogisticsName, dwid, SpmzStatus, MzlpffStatus, SpmzDjbh,
                                           SptjStatus, salesman', 'OrderId', 'OrderId IN '.$in_where);
        return $data;
    }

    /**
     * 根据Id获取中间库订单表中订单基本信息
     * @param array $condition 每页显示数量
     * @return mixed 从中间库中查出的商品基本信息
     */
    public function get_single_order_info_by_id($condition = '')
    {
        $data = $this->_CI->jys_sql_server->get_where('Orders', NULL, NULL, 'OrderId, UserName, ReceiverId, ShopDate, OrderDate, ConsigneeRealName, ConsigneeName, ConsigneePhone,
                                           ConsigneeProvince, ConsigneeAddress, ConsigneeZip, ConsigneeTel, ConsigneeFax, ConsigneeEmail, PaymentType,
                                           Payment, TotalPrice, Fees, OtherFees, Invoice, Remark, OrderStatus, PaymentStatus, OgisticsStatus, BusinessmanID,
                                           BusinessmanName, Carriage, OrderType, ContractNo, ConsigneeCity, ConsigneeBorough, ConsigneeConstructionSigns,
                                           ConsignesTime, TradeFees, TradeFeesPay, Editer, parentid, parentCorpName, BillingCorp, BillingCorpName, IsBusinessCheck,
                                           isFinancialReview, BusinessCheckDate, FinancialCheckDate, LogisticsName, dwid, SpmzStatus, MzlpffStatus, SpmzDjbh,
                                           SptjStatus, salesman', 'OrderId', "OrderId = '{$condition}'");
        return $data;
    }

    /**
     * 根据Id批量获取中间库子订单表中商品信息
     * @param array $condition 每页显示数量
     * @return mixed 从中间库中查出的商品基本信息
     */
    public function get_order_commodity_info_from_center_library($condition = [])
    {
        //拼接IN_WHERE语句，拼接为('sph21312', 2, 23)
        // dd($condition);
        $in_where = "(";
        for ($i = 0; $i < count($condition); $i++){
            if ($i == (count($condition) - 1)){
                if (is_string($condition[$i])) {
                    //数组value为字符串时，拼接''
                    $in_where .= "'".$condition[$i]."')";
                }elseif (is_int($condition[$i])) {
                    dd($condition[$i]);
                    //数组value为整形时
                    $in_where .= $condition[$i].")";
                }
            }else{
                if (is_string($condition[$i])) {
                    //数组value为字符串时，拼接''
                    $in_where .= "'".$condition[$i]."',";
                }elseif (is_int($condition[$i])) {
                    dd($condition[$i]);
                    //数组value为整形时
                    $in_where .= $condition[$i].",";
                }
            }
        }
        $data = $this->_CI->jys_sql_server->get_where('OrderProduct ', NULL, NULL, 'OrderId, ProId, spid, ProName, ProPrice, ProNum, AddTime,
                                            Status', 'OrderId IN '.$in_where);
        return $data;
    }

    /**
     * 插入中间库订单表
     * @param array $result 商城订单信息
     * @param bool $return_insert_id 是否需要返回插入的Id
     * @return mixed
     */
    public function insert_order_to_center_library($result = [], $return_insert_id = FALSE)
    {
        $table = 'Orders';
        $insert_sql = 'insert into Orders(OrderId, UserName, ReceiverId, ShopDate, OrderDate, ConsigneeRealName, ConsigneeName, ConsigneePhone,
                                           ConsigneeProvince, ConsigneeAddress, ConsigneeZip, ConsigneeTel, ConsigneeFax, ConsigneeEmail, PaymentType,
                                           Payment, TotalPrice, Fees, OtherFees, Invoice, Remark, OrderStatus, PaymentStatus, OgisticsStatus, BusinessmanID,
                                           BusinessmanName, Carriage, OrderType, ContractNo, ConsigneeCity, ConsigneeBorough, ConsigneeConstructionSigns,
                                           ConsignesTime, TradeFees, TradeFeesPay, Editer, parentid, parentCorpName, BillingCorp, BillingCorpName, IsBusinessCheck,
                                           isFinancialReview, BusinessCheckDate, FinancialCheckDate, LogisticsName, dwid, SpmzStatus, MzlpffStatus, SpmzDjbh,
                                           SptjStatus, Cid, salesname) values (:OrderId, :UserName, :ReceiverId, :ShopDate, :OrderDate, :ConsigneeRealName, :ConsigneeName,
                                           :ConsigneePhone,:ConsigneeProvince,:ConsigneeAddress, :ConsigneeZip, :ConsigneeTel, :ConsigneeFax, :ConsigneeEmail, :PaymentType,
                                           :Payment, :TotalPrice, :Fees, :OtherFees, :Invoice, :Remark, :OrderStatus, :PaymentStatus, :OgisticsStatus, :BusinessmanID,
                                           :BusinessmanName, :Carriage, :OrderType, :ContractNo, :ConsigneeCity, :ConsigneeBorough, :ConsigneeConstructionSigns,
                                           :ConsignesTime, :TradeFees, :TradeFeesPay, :Editer, :parentid, :parentCorpName, :BillingCorp, :BillingCorpName, :IsBusinessCheck,
                                           :isFinancialReview, :BusinessCheckDate, :FinancialCheckDate, :LogisticsName, :dwid, :SpmzStatus, :MzlpffStatus, :SpmzDjbh,
                                           :SptjStatus, :salesname)';
        $data['OrderId'] = $result['number'];
        $data['UserName'] = $result['username'];
        $data['ReceiverId'] = $result['user_id'];
        $data['ShopDate'] = $result['ShopDate'];
        $data['OrderDate'] = $result['finnished_time'];
        $data['ConsigneeRealName'] = $result['username'];
        $data['ConsigneeName'] = $result['username'];
        $data['ConsigneePhone'] = $result['phone'];
        $data['ConsigneeProvince'] = $result['province'];
        $data['ConsigneeAddress'] = $result['address'];
        $data['ConsigneeZip'] = NULL;
        $data['ConsigneeTel'] = $result['phone'];
        $data['ConsigneeFax'] = NULL;
        $data['ConsigneeEmail'] = $result['email'];
        $data['PaymentType'] = 1; //1-在线支付,2-银行转帐
        $data['Payment'] = 2; //1：货到付款,2：款到发货，3,账期结算-月结等
        $data['TotalPrice'] = $result['total_price'];
        $data['Fees'] = .00;
        $data['OtherFees'] = .00;
        $data['Invoice'] = 1;
        $data['Remark'] = $result['message'];
        $data['OrderStatus'] = 1; //1＝已提交,2＝已审核,3＝已支付,4＝已完成,-1＝已取消,-2＝已作废
        $data['PaymentStatus'] = 0; //0：未付款,1：买家已付款
        $data['OgisticsStatus'] = 0; //未发货 = 0,未确认货源 = -1,已确认货源 = -2,多次发货中 = -3,已发货 = 1, 已收货（签收） = 2
        $data['BusinessmanID'] = NULL;
        $data['BusinessmanName'] = NULL;
        $data['Carriage'] = 4; //0：未选择配送； 1：送货上门；2：其它；3：自提,4 第三方物流
        $data['OrderType'] = 1;
        $data['ContractNo'] = NULL;
        $data['ConsigneeCity'] = $result['city'];
        $data['ConsigneeBorough'] = $result['district'];
        $data['ConsigneeConstructionSigns'] = $result['IP'];
        $data['ConsignesTime'] = 1;
        $data['TradeFees'] = $result['freight'];
        $data['TradeFeesPay'] = 0;
        $data['Editer'] = 0;
        $data['parentid'] = $result['enterprise_id'];
        $data['parentCorpName'] = $result['parentCorpName'];
        $data['BillingCorp'] = 0;
        $data['BillingCorpName'] = NULL;
        $data['IsBusinessCheck'] = 0;
        $data['isFinancialReview'] = 0;
        $data['BusinessCheckDate'] = $result['ShopDate'];
        $data['FinancialCheckDate'] = $result['ShopDate'];
        $data['LogisticsName'] = NULL;
        $data['dwid'] = $result['dwid'];
        $data['SpmzStatus'] = NULL;
        $data['MzlpffStatus'] = NULL;
        $data['SpmzDjbh'] = NULL;
        $data['SptjStatus'] = NULL;
        $data['salesman'] = $result['salesman'];
        $return_insert_id = TRUE;
        $data = $this->_CI->jys_sql_server->insert($insert_sql, $data, $return_insert_id, $table);
        return $data;
    }


    /**
     * 插入中间库订单明细表
     * @param array $data 订单明细信息
     * @param bool $return_insert_id 是否需要返回插入的Id
     * @return mixed
     */
    public function insert_order_product_to_center_library($result = [], $return_insert_id = FALSE)
    {
        $table = 'OrderProduct';
        $insert_sql = 'insert into OrderProduct(OrderId, ProId, spid, ProName, ProPrice, ProNum, AddTime, Status,
                                           SpmzStatus, MzlpffStatus,SpmzDjbh, SptjStatus) values (:OrderId, :ProId, :spid, :ProName, :ProPrice, :ProNum, :AddTime,
                                           :Status,:SpmzStatus,:MzlpffStatus, :SpmzDjbh, :SptjStatus)';
        $data['OrderId'] = $result['number'];
        $data['ProId'] = $result['commodity_id'];
        $data['spid'] = $result['erp_id'];
        $data['ProName'] = $result['name'];
        $data['ProPrice'] = $result['price'];
        $data['ProNum'] = $result['amount'];
        $data['AddTime'] = $result['create_time'];
        $data['Status'] = 1;
        $data['SpmzStatus'] = NULL;
        $data['MzlpffStatus'] = NULL;
        $data['SpmzDjbh'] = NULL;
        $data['SptjStatus'] = NULL;
        $return_insert_id = TRUE;
        $data = $this->_CI->jys_sql_server->insert($insert_sql, $data, $return_insert_id, $table);
        return $data;
    }

    /**
     * 插入中间库订单消息表信息
     * @param int $action_code 事件代码 1 增加 2 修改 0 删除
     * @param int $orderId 订单Id
     * @param int $handle_status 处理状态 0 － 未处理 ，1－处理成功，2－处理失败
     * @param string $notes 如果处理失败则填写处理失败原因
     * @param bool $return_insert_id 是否需要返回插入的Id
     * @return mixed
     */
    public function insert_order_note_to_center_library($action_code = 0, $orderId = 1, $handle_status = 0, $notes = '', $return_insert_id = FALSE)
    {
        $table = 'I_ERP_Order_NOTE';
        $insert_sql = 'insert into I_ERP_Order_NOTE(noteTime, actionCode, OrderId, handleStatus, notes, handleTime) values(?,?,?,?,?)';
        $data[0] = date("Y-m-d H:i:s",time());   //添加时间
        $data[1] = $action_code;    //操作1 增加 2 修改 0 删除
        $data[2] = $orderId;        // 订单Id
        $data[3] = $handle_status;  //handleStatus
        $data[4] = '';
        $data[5] = '';
        $return_insert_id = TRUE;
        $data = $this->_CI->jys_sql_server->insert($insert_sql, $data, $return_insert_id, $table);
        return $data;
    }

    /**
     * 更新中间库订单消息表信息
     * @param int $action_code 事件代码 1 增加 2 修改 0 删除
     * @param int $orderId 订单Id
     * @param int $handle_status 处理状态 0 － 未处理 ，1－处理成功，2－处理失败
     * @param string $notes 如果处理失败则填写处理失败原因
     * @param bool $return_insert_id 是否需要返回插入的Id
     * @return mixed
     */
    public function update_order_note_to_center_library($action_code = 0, $orderId = 1, $handle_status = 0, $notes = '')
    {
        $update_sql = 'update I_ERP_Order_NOTE set noteTime = ?, actionCode = ?, OrderId = ?, handleStatus = ?, notes = ?, handleTime = ? where orderId = ?';
        $data[0] = date("Y-m-d H:i:s",time());   //修改时间
        $data[1] = $action_code;    //操作1 增加 2 修改 0 删除
        $data[2] = $orderId;        // 订单Id
        $data[3] = $handle_status;  //handleStatus
        $data[4] = '';
        $data[5] = '';
        $data = $this->_CI->jys_sql_server->update($update_sql, $data);
        return $data;
    }


    /**
     * 更新中间库订单表
     * @param int $action_code 事件代码 1 增加 2 修改 0 删除
     * @param int $handle_status 处理状态 0 － 未处理 ，1－处理成功，2－处理失败
     * @param string $notes 如果处理失败则填写处理失败原因
     * @param int $id
     * @return mixed
     */
    public function update_order_from_center_library($array = [], $id = '')
    {
        $update_sql = 'update Orders set OrderStatus = ?, OgisticsStatus = ?, PaymentStatus = ?, Carriage = ? where OrderId = ?';  
        $data[0] = $array['OrderStatus'];
        $data[1] = $array['OgisticsStatus'];
        $data[2] = $array['PaymentStatus'];
        $data[3] = $array['Carriage']; 
        $data[4] = $id;
        $data = $this->_CI->jys_sql_server->update($update_sql,$data);
        return $data;
    }

    /**
     * 更新中间库订单明细状态信息
     * @param string $table 消息表名
     * @param int $handle_status 处理状态 0 － 未处理 ，1－处理成功，2－处理失败
     * @param string $notes 如果处理失败则填写处理失败原因
     * @param int $id 消息表Id
     * @param bool $return_insert_id 是否需要返回插入的Id
     * @return mixed
     */
    public function update_order_status_note_to_center_library($handle_status = 0, $id = 1, $notes = '')
    {
        $insert_sql = 'update I_ERP_OrderStatus_NOTE set handleTime = ?, handleStatus = ?, notes = ? where id = ?';
        $data[0] = date("Y-m-d H:i:s",time());   //添加时间
        $data[1] = $handle_status;
        $data[2] = $notes;
        $data[3] = $id;
        $data = $this->_CI->jys_sql_server->update($insert_sql, $data);
        return $data;
    }

    //获取sql server数据库商品图片
    public function get_commodity_id_from_number($number = []){
        $commodity_info = $this->_CI->jys_sql_server->get_where('Product', NULL, NULL, 'Goods_Image.Original', 'Product.Product_ID', "Product.spid =?", $number, "left join Goods_Image on Goods_Image.Goods_ID = Product.Goods_ID");
        if (!empty($commodity_info)) {
            $data = $commodity_info;
        }else{
            $data = '';
        }
        return $data;
    }


}