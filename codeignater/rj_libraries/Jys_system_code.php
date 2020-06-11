<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename: Jys_system_code.php
 *
 *     Description: 系统字典类
 *
 *         Created: 2016-11-22 21:21:01
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
class Jys_system_code {

    /**
     * 商品状态
     */
    const COMMODITY_STATUS = "commodity_status";
    /**
     * 商品状态：上架
     */
    const COMMODITY_STATUS_PUTAWAY = 1;
    /**
     * 商品状态：下架
     */
    const COMMODITY_STATUS_SOLDOUT = 2;
    // /**
    //  * 商品状态：待上架
    //  */
    // const COMMODITY_STATUS_STAY_PUTAWAY = 3;
    // /**
    //  * 商品状态：暂不上架
    //  */
    // const COMMODITY_STATUS_WITHOUT_PUTAWAY = 4;
    /**
     * 商品状态：已删除
     */
    const COMMODITY_STATUS_DELETE = 3;

    /**
     * 优惠券状态
     */
    const DISCOUNT_COUPON_STATUS = "discount_coupon_status";
    /**
     * 优惠券状态：发布
     */
    const DISCOUNT_COUPON_STATUS_PULISHED = 1;
    /**
     * 优惠券状态：未发布
     */
    const DISCOUNT_COUPON_STATUS_UNPULISHED = 2;
    /**
     * 优惠券状态：已终止
     */
    const DISCOUNT_COUPON_STATUS_TERMINATED = 3;

    /**
     * 用户的惠券状态
     */
    const USER_DISCOUNT_COUPON_STATUS = "user_discount_coupon_status";
    /**
     * 用户的惠券状态：未使用
     */
    const USER_DISCOUNT_COUPON_STATUS_UNUSED = 1;
    /**
     * 用户的惠券状态：已使用
     */
    const USER_DISCOUNT_COUPON_STATUS_USED = 2;
    /**
     * 用户的惠券状态：已过期
     */
    const USER_DISCOUNT_COUPON_STATUS_EXPIRED = 3;

    /**
     * Banner的位置
     */
    const BANNER_POSITION = "banner_position";
    /**
     * Banner的位置：PC首页
     */
    const BANNER_POSITION_PC_HOME = 1;
    /**
     * Banner的位置：weixin首页
     */
    const BANNER_POSITION_WEIXIN_HOME = 2;
    /**
     * Banner的位置：weixin积分商城首页
     */
    const BANNER_POSITION_WEIXIN_INTEGRAL_HOME = 3;
    /**
     * Banner的位置：weixin积分商城兑换页
     */
    const BANNER_POSITION_WEIXIN_INTEGRAL_EXCHANGE = 4;
    /**
     * Banner的位置：weixin积分抽奖页
     */
    const BANNER_POSITION_WEIXIN_INTEGRAL_SWEEPSTAKES = 5;
    /**
     * Banner的位置：PC端首页
     */
    const BANNER_POSITION_PC_HEADER= 6;
    /**
     * Banner的位置：PC端首页顶部右侧
     */
    const BANNER_POSITION_PC_HOME_TOP_RIGHT = 7;
    /**
     * Banner的位置：PC促销专区
     */
    const BANNER_POSITION_PC_SALES = 8;
    /**
     * Banner的位置：积分兑换专区
     */
    const BANNER_POSITION_PC_EXCHANGE = 9;
    /**
     * Banner的位置：有效期品种
     */
    const BANNER_POSITION_PC_VALIDITY_VARIETIES = 10;
    /**
     * Banner的位置：PC端首页Banner下侧广告
     */
    const BANNER_POSITION_PC_HOME_BANNER_BELOW = 12;
    /**
     * Banner的位置：PC端新品推荐封面
     */
    const BANNER_POSITION_PC_HOME_NEW_PRODUCTS = 15;
    /**
     * Banner的位置：PC端畅销品种
     */
    const BANNER_POSITION_PC_SELL_WELL = 16;

    /**
     * 支付方式
     */
    const PAYMENT = "payment";
    /**
     * 支付方式：微信支付
     */
    const PAYMENT_WXPAY = 1;
    /**
     * 支付方式：支付宝
     */
    const PAYMENT_ALIPAY = 2;
    /**
     * 支付方式：中国银联
     */
    const PAYMENT_UNIONPAY = 3;
    /**
     * 支付方式：线下支付
     */
    const PAYMENT_OFFLINE_PAY= 4;
    /**
     * 支付方式：积分兑换
     */
    const PAYMENT_INTEGRAL_EXCHANGE = 5;
    /**
     * 支付方式：积分夺宝
     */
    const PAYMENT_INTEGRAL_INDIANA = 6;
    /**
     * 支付方式：抽奖
     */
    const PAYMENT_INTEGRAL_SWEEPSTAKES = 7;

    /**
     * 终端类型
     */
    const TERMINAL_TYPE = "terminal_type";
    /**
     * 终端类型：PC端
     */
    const TERMINAL_TYPE_PC = 1;
    /**
     * 终端类型：微信
     */
    const TERMINAL_TYPE_WEIXIN = 2;

    /**
     * 订单状态
     */
    const ORDER_STATUS = "order_status";
    /**
     * 订单状态：未付款
     */
    const ORDER_STATUS_NOT_PAID = 10;
    /**
     * 订单状态：已付款
     */
    const ORDER_STATUS_PAID = 20;
    /**
     * 订单状态：已发货
     */
    const ORDER_STATUS_DELIVERED = 30;
    /**
     * 订单状态：等待出库
     */
    const ORDER_STATUS_OUTING = 40;
    /**
     * 订单状态：已出库
     */
    const ORDER_STATUS_OUTED = 50;
    /**
     * 订单状态：已完成
     */
    const ORDER_STATUS_FINISHED = 60;
    /**
     * 订单状态：退款中
     */
    const ORDER_STATUS_REFUNDING = 70;
    /**
     * 订单状态：已退款
     */
    const ORDER_STATUS_REFUNDED = 80;
    /**
     * 订单状态：未退款
     */
    const ORDER_STATUS_UNREFUNDED = 90;
    /**
     * 订单状态：已取消
     */
    const ORDER_STATUS_CANCELED = 100;

    /**
     * 子订单状态
     */
    const ORDER_COMMODITY_STATUS = 'order_commodity_status';
    /**
     * 子订单状态：等待出库
     */
    const ORDER_COMMODITY_STATUS_OUTING = 10;
    /**
     * 子订单状态：已出库
     */
    const ORDER_COMMODITY_STATUS_OUTED = 20;
    /**
     * 子订单状态：已取消
     */
    const ORDER_COMMODITY_STATUS_CANCEL = 30;
    /**
     * 子订单状态：已退款
     */
    const ORDER_COMMODITY_STATUS_REFUNDED = 40;

    /**
     * 退款状态
     */
    const REFUND_STATUS = "refund_status";
    /**
     * 退款状态：退款中
     */
    const REFUND_STATUS_APPLYING = 10;
    /**
     * 退款状态：同意退款
     */
    const REFUND_STATUS_AGREED = 20;
    /**
     * 退款状态：拒绝退款
     */
    const REFUND_STATUS_REJECTED = 30;

    /**
     * 角色：普通用户
     */
    const ROLE = "role";
    /**
     * 角色：普通用户
     */
    const ROLE_USER = 10;
    /**
     * 角色：管理员
     */
    const ROLE_ADMIN = 20;
    /**
     * 角色：超级管理员
     */
    const ROLE_ADMINISTRATOR = 30;
    /**
     * 角色：商品管理
     */
    const ROLE_COMMODITY_MANAGE = 40;
    /**
     * 角色：客户服务
     */
    const ROLE_CUSTOMER_SERVICE = 50;
    /**
     * 角色：系统管理
     */
    const ROLE_SYSTEM_MANAGE = 60;
    /**
     * 角色：广告与资讯主管
     */
    const ROLE_ADVERTISEMENT_INFORMATION_MANAGE = 70;
    /**
     * 角色：客服主管
     */
    const ROLE_CUSTOMER_SERVICE_SUPERVISOR = 80;
    /**
     * 角色：业务员
     */
    const ROLE_SALESMAN = 90;
    /**
     * 角色：特价促销员
     */
    const ROLE_SPECIAL_SALES_MAN = 100;
    /**
     * 角色：资讯管理
     */
    const ROLE_INFORMATION_MANAGE = 110;
    /**
     * 角色：客服功能
     */
    const ROLE_CUSTOMER_SERVICE_FUNCTION = 120;
    /**
     * 角色：电商
     */
    const ROLE_E_COMMERCE = 130;
    /**
     * 角色：代理商
     */
    //const ROLE_AGENT = 30;


    /**
     * 验证码用途
     */
    const VERIFICATION_CODE_PURPOSE = 'verification_code_purpose';
    /**
     * 验证码用途：注册
     */
    const VERIFICATION_CODE_PURPOSE_REGISTER = 1;
    /**
     * 验证码用途：手机
     */
    const VERIFICATION_CODE_PURPOSE_PHONE = 2;
    /**
     * 验证码用途：邮件
     */
    const VERIFICATION_CODE_PURPOSE_EMAIL = 3;
    /**
     * 验证码用途：余额支付
     */
    const VERIFICATION_CODE_PURPOSE_BALANCE_PAY = 4;
    /**
     * 验证码用途：找回登录或支付密码
     */
    const VERIFICATION_CODE_PURPOSE_FIND_PASSWORD = 5;
    /**
     * 公告状态
     */
    const ARTICLE_STATUS = 'article_status';
    /**
     * 公告状态：发表
     */
    const ARTICLE_STATUS_PUBLISHED = 1;
    /**
     * 公告状态：未发表
     */
    const ARTICLE_STATUS_UNPUBLISHED = 2;

    /**
     * 商品推荐类型
     */
    const RECOMMEND_COMMODITY_STATUS = "recommend_commodity_status";
    /**
     * 商品推荐类型：热卖商品
     */
    const RECOMMEND_COMMODITY_STATUS_HOT_SALE = 1;
    /**
     * 商品推荐类型：热换商品
     */
    const RECOMMEND_COMMODITY_STATUS_HOT_EXCHANGE = 2;
    /**
     * 商品推荐类型：新品推荐
     */
    const RECOMMEND_COMMODITY_STATUS_NEW_COMMODITY = 3;
    /**
     * 商品推荐类型：畅销推荐
     */
    const RECOMMEND_COMMODITY_STATUS_SELL_WELL = 4;

    /**
     * 帖子状态
     */
    const POST_STATUS = 'post_status';
    /**
     * 草稿
     */
    const POST_STATUS_DRAFT = 1;
    /**
     * 已发表
     */
    const POST_STATUS_PUBLISHED = 2;
    /**
     * 已删除
     */
    const POST_STATUS_DELETED = 3;
    /**
     * 评论状态
     */
    const COMMENT_STATUS = 'comment_status';
    /**
     * 已发表
     */
    const COMMENT_STATUS_PUBLISHED = 1;
    /**
     * 已删除（管理员）
     */
    const COMMENT_STATUS_MANAGER_DELETED = 2;
    /**
     * 已删除（楼主）
     */
    const COMMENT_STATUS_LANDLORD_DELETED = 3;
    /**
     * 已删除（本人）
     */
    const COMMENT_STATUS_OWNER_DELETED = 4;
    /**
     * 站内信信息状态
     */
    const MESSAGE_STATUS = 'message_status';
    /**
     * 草稿
     */
    const MESSAGE_STATUS_UNREAD = 0;
    /**
     * 已发表
     */
    const MESSAGE_STATUS_READ = 1;
    /**
     * 积分夺宝活动状态
     */
    const INTEGRAL_INDIANA_STATUS = 'integral_indiana_status';
    /**
     * 进行中
     */
    const INTEGRAL_INDIANA_STATUS_DOING = 1;
    /**
     * 已结束
     */
    const INTEGRAL_INDIANA_STATUS_DONE = 2;
    /**
     * 已删除
     */
    const INTEGRAL_INDIANA_STATUS_DELETED = 3;
    /**
     * 积分夺宝结果状态
     */
    const INTEGRAL_INDIANA_RESULT_STATUS = 'integral_indiana_result_status';
    /**
     * 未操作
     */
    const INTEGRAL_INDIANA_RESULT_STATUS_SYSTEM_EXTRACTION = 0;
    /**
     * 审核通过
     */
    const INTEGRAL_INDIANA_RESULT_STATUS_PASS = 1;
    /**
     * 已领取
     */
    const INTEGRAL_INDIANA_RESULT_STATUS_RECEIVED = 2;
    /**
     * 积分抽奖结果状态
     */
    const SWEEPSTAKES_RESULT_STATUS = 'sweepstakes_result_status';
    /**
     * 未发货
     */
    const SWEEPSTAKES_RESULT_STATUS_NOT_RECEIVE = 1;
    /**
     * 已发货
     */
    const SWEEPSTAKES_RESULT_STATUS_RECEIVED = 2;
    /*
     * 报告个人信息提交状态
     */
    const REPORT_STATUS = 'report_status';
    /*
     * 报告个人信息已提交
     */
    const REPORT_STATUS_COMMITTED = 1;
    /*
     * 报告个人信息未提交
     */
    const REPORT_STATUS_UNCOMMITTED =0;

    /********************************************************************/
    /*
     * 企业类型
     */
    const ENTERPRISE_TYPE = 'enterprise_type';
    /*
     * 企业类型：药品零售连锁企业
     */
    const ENTERPRISE_TYPE_RETAIL_CHAIN_ENTERPRISE = 10;
    /*
    * 企业类型：药品零售连锁加盟店
    */
    const ENTERPRISE_TYPE_RETAIL_CHAIN_FRANCHISEE = 20;
    /*
    * 企业类型：单体药店
    */
    const ENTERPRISE_TYPE_SINGLE_DRUGSTORE = 30;
    /*
    * 企业类型：诊所、卫生站、社区服务中心
    */
    const ENTERPRISE_TYPE_CLINIC = 40;
    /*
    * 企业类型：民营医院
    */
    const ENTERPRISE_TYPE_PRIVATE_HOSPITAL = 50;
    /*
    * 企业类型：内部药店
    */
    const ENTERPRISE_TYPE_INTERNAL_DRUGSTORE = 60;

    /*
     * gsp状态
     */
    const GSP_STATUS = 'gsp_status';
    /*
    * gsp状态：未审核
    */
    const GSP_STATUS_UNCHECKED = 10;
    /*
    * gsp状态：已审核
    */
    const GSP_STATUS_CHECKED = 20;
    /*
    * gsp状态：已冻结
    */
    const GSP_STATUS_DELETE = 30;

    /*
     * 用户状态
     */
    const USER_STATUS = 'user_status';
    /*
     * 用户状态：未审核
     */
    const USER_STATUS_UNCHECKED = 10;
    /*
     * 用户状态：已审核
     */
    const USER_STATUS_CHECKED = 20;
    /*
     * 用户状态：已冻结
     */
    const USER_STATUS_FREEZED = 30;

    /**
     * 商品出售方式
     */
    const SALES_METHOD = 'sales_method';
    /**
     * 商品出售方式：无限制
     */
    const SALES_METHOD_UNRESTRICTED = 1;
    /**
     * 商品出售方式：中包装
     */
    const SALES_METHOD_MEDIUM_PACKAGE = 2;
    /**
     * 商品出售方式：整件
     */
    const SALES_METHOD_WHOLE_PIECE = 3;

    /**
     * 客户交易意向状态
     */
    const TRANSACTION_INTENTION_STATUS = 'transaction_intention_status';
    /**
     * 客户交易意向状态：待处理
     */
    const TRANSACTION_INTENTION_STATUS_PENDING = 1;
    /**
     * 客户交易意向状态：已处理
     */
    const TRANSACTION_INTENTION_STATUS_PENDED = 2;
    /**
     * 客户交易意向状态：已取消
     */
    const TRANSACTION_INTENTION_STATUS_CANCELED = 3;

    /**
     * 客户交易意向可接受到货周期
     */
    const ACCEPT_ARRIVAL_DATE = 'accept_arrival_date';
    /**
     * 客户交易意向可接受到货周期：7天
     */
    const ACCEPT_ARRIVAL_DATE_WEEK = 1;
    /**
     * 客户交易意向可接受到货周期：半个月
     */
    const ACCEPT_ARRIVAL_DATE_HALF_MONTH = 2;
    /**
     * 客户交易意向可接受到货周期：一个月
     */
    const ACCEPT_ARRIVAL_DATE_ONE_MONTH = 3;
    /**
     * 客户交易意向可接受到货周期：一个月以上
     */
    const ACCEPT_ARRIVAL_DATE_MORE_THAN_ONE_MONTH = 4;

    /**
     * 买家类型
     */
    const BUYER_TYPE = 'buyer_type';
    /**
     * 买家类型：无
     */
    const BUYER_TYPE_NONE = 1;
    /**
     * 买家类型：OTC拆零客户
     */
    const BUYER_TYPE_OTC_CUSTOMER = 2;
    /**
     * 买家类型：批发客户
     */
    const BUYER_TYPE_WHOLESALE = 3;

    /**
     * 加价换购活动状态
     */
    const REDEMPTION_STATUS = 'redemption_status';
    /**
     * 加价换购活动状态：已启用
     */
    const REDEMPTION_STATUS_START = 1;
    /**
     * 加价换购活动状态：已停用
     */
    const REDEMPTION_STATUS_END = 2;
    /**
     * 加价换购活动状态：已删除
     */
    const REDEMPTION_STATUS_CANCEL = 3;

    /**
     * 满减满赠活动类型
     */
    const GRANT_REDUCE_TYPE = 'grant_reduce_type';
    /**
     * 满减满赠活动类型：满减
     */
    const GRANT_REDUCE_TYPE_REDUCE = 1;
    /**
     * 满减满赠活动类型：满赠
     */
    const GRANT_REDUCE_TYPE_GRANT = 2;

    /**
     * 满减满赠活动状态
     */
    const GRANT_REDUCE_STATUS = 'grant_reduce_status';
    /**
     * 满减满赠活动状态：已启用
     */
    const GRANT_REDUCE_STATUS_START = 1;
    /**
     * 满减满赠活动状态：已停用
     */
    const GRANT_REDUCE_STATUS_END = 2;
    /**
     * 满减满赠活动状态：已删除
     */
    const GRANT_REDUCE_STATUS_CANCEL = 3;

    /**
     * 满减满赠活动类型享受范围
     */
    const GRAN_REDUCE_RANGE = 'grant_reduce_range';
    /**
     * 满减满赠活动类型享受范围：分类
     */
    const GRAN_REDUCE_RANGE_CATEGORY = 10;
    /**
     * 满减满赠活动类型享受范围：品牌
     */
    const GRAN_REDUCE_RANGE_BRAND = 20;
    /**
     * 满减满赠活动类型享受范围：商品
     */
    const GRAN_REDUCE_RANGE_COMMODITY = 30;

    /**
     * 抽奖活动状态
     */
    const SWEEPSTAKES_STATUS = 'sweepstakes_status';
    /**
     * 抽奖活动状态：已启用
     */
    const SWEEPSTAKES_STATUS_START = 1;
    /**
     * 抽奖活动状态：已停用
     */
    const SWEEPSTAKES_STATUS_END = 2;
    /**
     * 抽奖活动状态：已删除
     */
    const SWEEPSTAKES_STATUS_CANCEL = 3;

    /**
     * 奖品类型 
     */
    const PRIZE_TYPE = 'prize_type';
    /**
     * 奖品类型:满赠
     */
    const PRIZE_TYPE_FULL_GRANT_REDUCE = 1;
    /**
     * 奖品类型：抽奖
     */
    const PRIZE_TYPE_SWEEPSTAKES = 2;
    /**
     * 奖品类型：积分兑换
     */
    const PRIZE_TYPE_INDIANA_EXCHANGE= 3;

    /**
     * 优惠券发布类型
     */
    const DISCOUNT_COUPON_PUBLISH_TYPE = 'discount_coupon_publish_type';
    /**
     * 优惠券发布类型：会员领取
     */
    const DISCOUNT_COUPON_PUBLISH_TYPE_RECEIVE = 1;
    /**
     * 优惠券发布类型：后台自动发放
     */
    const DISCOUNT_COUPON_PUBLISH_TYPE_AUTO = 2;

    /**
     * 企业资质审核状态
     */
    const ENTERPRISE_QUALIFICATION_STATUS = 'enterprise_qualification_status';
    /**
     * 企业资质审核状态：未审核
     */
    const ENTERPRISE_QUALIFICATION_STATUS_UNAUDIT = 1;
    /**
     * 企业资质审核状态：已审核
     */
    const ENTERPRISE_QUALIFICATION_STATUS_AUDITED = 2;

    /**
     * 优惠券明细状态
     */
    const DISCOUNT_COUPON_DETAIL_TYPE = 'discount_coupon_detail_type';
    /**
     * 优惠券明细状态：领取
     */
    const DISCOUNT_COUPON_DETAIL_TYPE_RECEIVE = 1;
    /**
     * 优惠券明细状态：使用
     */
    const DISCOUNT_COUPON_DETAIL_TYPE_USE = 2;
    /**
     * 优惠券明细状态：过期
     */
    const DISCOUNT_COUPON_DETAIL_TYPE_EXPIRED = 3;

    /**
     * 购物车促销活动类型
     */
    const DISCOUNT_TYPE = 'discount_type';
    /**
     * 购物车促销活动类型：限时折扣
     */
    const DISCOUNT_TYPE_FLASH_SALE = 1;
    /**
     * 购物车促销活动类型：折扣活动
     */
    const DISCOUNT_TYPE_DISCOUNT_ACTIVITY = 2;
    /**
     * 购物车促销活动类型：满减、满赠
     */
    const DISCOUNT_TYPE_FULL_GRANT_REDUCE = 3;
    /**
     * 购物车促销活动类型：套餐
     */
    const DISCOUNT_TYPE_PACKAGES = 4;
    /**
     * 购物车促销活动类型：加价换购
     */
    const DISCOUNT_TYPE_REDEMPTION= 5;

    /**
     * 子订单类型
     */
    const ORDER_COMMODITY_TYPE = 'order_commodity_type';
    /**
     * 子订单类型：商品订单
     */
    const ORDER_COMMODITY_TYPE_COMMODITY = 1;
    /**
     * 子订单类型：奖品订单
     */
    const ORDER_COMMODITY_TYPE_PRIZE = 2;

    /**
     * 用户积分明细类型
     */
    const USER_POINT_DETAIL_TYPE = 'user_point_detail_type';
    /**
     * 用户积分明细类型：增加
     */
    const USER_POINT_DETAIL_TYPE_ADD = 1;
    /**
     * 用户积分明细类型：使用
     */
    const USER_POINT_DETAIL_TYPE_USE = 2;

    /**
     * 地址状态
     */
    const ADDRESS_STATUS = 'address_status';
    /**
     * 地址状态：未审核
     */
    const ADDRESS_STATUS_UNAUDIT = 1;
    /**
     * 地址状态：已审核
     */
    const ADDRESS_STATUS_AUDITED = 2;
    /********************************************************************/
    /**
     * 账户金额变动记录类型
     */
    const EXPENSES_RECORD_TYPE = 'expenses_record_type';
    /**
     * 账户金额变动记录类型：充值待确认
     */
    const EXPENSES_RECORD_TYPE_RECHARGING = 10;
    /**
     * 账户金额变动记录类型：消费
     */
    const EXPENSES_RECORD_TYPE_CONSUME = 20;
    /**
     * 账户金额变动记录类型：退款
     */
    const EXPENSES_RECORD_TYPE_REFUND = 30;
    /**
     * 账户金额变动记录类型：充值
     */
    const EXPENSES_RECORD_TYPE_RECHARGE = 40;
    /**
     * 账户金额变动记录类型：管理员充值
     */
    const EXPENSES_RECORD_TYPE_ADMIN_RECHARGE = 50;
    /**
     * 账户金额变动记录类型：管理员扣除
     */
    const EXPENSES_RECORD_TYPE_ADMIN_DEDUCTION= 60;
    /**
     * 系统日志类型
     */
    const SYSTEM_LOG_TYPE = 'system_log_type';
    /**
     * 系统日志类型：访问记录
     */
    const SYSTEM_LOG_TYPE_ACCESS = 10;
    /**
     * 系统日志类型：买家操作
     */
    const SYSTEM_LOG_TYPE_BUYER_OPERATE = 20;


    /**
     * 中间库操作码：删除
     */
    const CENTER_LIBRARY_OPERATION_DELETE = 0;
    /**
     * 中间库操作码：增加
     */
    const CENTER_LIBRARY_OPERATION_INCREMENT = 1;
    /**
     * 中间库操作码：修改
     */
    const CENTER_LIBRARY_OPERATION_UPDATE = 2;


    /**
     * 中间库处理码：未处理
     */
    const CENTER_LIBRARY_HANDLE_NO= 0;
    /**
     * 中间库处理码：已处理
     */
    const CENTER_LIBRARY_HANDLE_BEEN_HANDLE = 1;
    /**
     * 中间库处理码：处理失败
     */
    const CENTER_LIBRARY_HANDLE_FAIL = 2;
    /**
     * 中间库处理码：回写
     */
    const CENTER_LIBRARY_HANDLE_BACK_WRITE = 101;


    /**
     * 中间库订单商品状态：已取消
     */
    const CENTER_LIBRARY_ORDER_COMMODITY_STATUS_CANCEL= 0;
    /**
     * 中间库订单商品状态：货物已发出
     */
    const CENTER_LIBRARY_ORDER_COMMODITY_STATUS_SENT = 1;


    /**
     * 中间库支付类型
     */
    const CENTER_LIBRARY_PAYMENT_TYPE = 'PaymentType';
    /**
     * 中间库支付类型：在线支付
     */
    const CENTER_LIBRARY_PAYMENT_TYPE_ONLINE = 1;
    /**
     * 中间库支付类型：银行转账
     */
    const CENTER_LIBRARY_PAYMENT_TYPE_BANK_TRANSFER = 2;


    /**
     * 中间库付款方式
     */
    const CENTER_LIBRARY_PAYMENT = 'Payment';
    /**
     * 中间库付款方式：货到付款
     */
    const CENTER_LIBRARY_PAYMENT_CASH_ON_DELIVER = 1;
    /**
     * 中间库付款方式：款到发货
     */
    const CENTER_LIBRARY_PAYMENT_BEFORE_DELIVER = 2;
    /**
     * 中间库付款方式：账期结算-月结
     */
    const CENTER_LIBRARY_PAYMENT_MONTHLY_STATEMENT = 3;


    /**
     * 中间库订单进展状态
     */
    const CENTER_LIBRARY_ORDER_STATUS = 'OrderStatus';
    /**
     * 中间库订单进展状态：已提交
     */
    const CENTER_LIBRARY_ORDER_STATUS_SUBMITTED = 1;
    /**
     * 中间库订单进展状态：已审核
     */
    const CENTER_LIBRARY_ORDER_STATUS_CHECKED = 2;
    /**
     * 中间库订单进展状态：已支付
     */
    const CENTER_LIBRARY_ORDER_STATUS_PAID = 3;
    /**
     * 中间库订单进展状态：已完成
     */
    const CENTER_LIBRARY_ORDER_STATUS_FINISHED = 4;
    /**
     * 中间库订单进展状态：已取消
     */
    const CENTER_LIBRARY_ORDER_STATUS_CANCELED = -1;
    /**
     * 中间库订单进展状态：已作废
     */
    const CENTER_LIBRARY_ORDER_STATUS_INVALID = -2;


    /**
     * 中间库订单付款情况
     */
    const CENTER_LIBRARY_PAYMENT_STATUS = 'PaymentStatus';
    /**
     * 中间库订单付款情况：未付款
     */
    const CENTER_LIBRARY_PAYMENT_STATUS_NOT_PAID = 0;
    /**
     * 中间库订单付款情况：已付款
     */
    const CENTER_LIBRARY_PAYMENT_STATUS_PAID = 1;


    /**
     * 中间库送货状态
     */
    const CENTER_LIBRARY_OGISTICS_STATUS = 'OgisticsStatus';
    /**
     * 中间库送货状态：未发货
     */
    const CENTER_LIBRARY_OGISTICS_STATUS_SUBMITTED = 0;
    /**
     * 中间库送货状态：未确认货源
     */
    const CENTER_LIBRARY_OGISTICS_STATUS_CHECKED = -1;
    /**
     * 中间库送货状态：已确认货源
     */
    const CENTER_LIBRARY_OGISTICS_STATUS_PAID = -2;
    /**
     * 中间库送货状态：多次发货中
     */
    const CENTER_LIBRARY_OGISTICS_STATUS_FINISHED = -3;
    /**
     * 中间库送货状态：已发货
     */
    const CENTER_LIBRARY_OGISTICS_STATUS_CANCELED = 1;
    /**
     * 中间库送货状态：已收货
     */
    const CENTER_LIBRARY_OGISTICS_STATUS_INVALID = 2;


    /**
     * 中间库配送方式
     */
    const CENTER_LIBRARY_CARRIAGE = 'Carriage';
    /**
     * 中间库配送方式：未选择配送
     */
    const CENTER_LIBRARY_CARRIAGE_NO = 0;
    /**
     * 中间库配送方式：送货上门
     */
    const CENTER_LIBRARY_CARRIAGE_HOME_DELIVERY = -1;
    /**
     * 中间库配送方式：其它
     */
    const CENTER_LIBRARY_CARRIAGE_OTHERS = -2;
    /**
     * 中间库配送方式：自提
     */
    const CENTER_LIBRARY_CARRIAGE_TAKE_THEIR = -1;
    /**
     * 中间库配送方式：第三方物流
     */
    const CENTER_LIBRARY_CARRIAGE_THIRD_PART = -2;


    /**
     * 中间库订单商品状态
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS = 'order_product_status';
    /**
     * 中间库订单商品状态：已提交
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_SUBMITTED = 1;
    /**
     * 中间库订单商品状态：确认供货
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_CONFIRM_DELIVER = 2;
    /**
     * 中间库订单商品状态：确认缺货
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_CONFIRM_OUT = 3;
    /**
     * 中间库订单商品状态：已预购
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_PRE_ORDER = 4;
    /**
     * 中间库订单商品状态：无货
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_OUT = 5;
    /**
     * 中间库订单商品状态：已取消
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_CANCEL = 6;
    /**
     * 中间库订单商品状态：已申请出库
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_APPLIED_WARE_HOUSING = 7;
    /**
     * 中间库订单商品状态：已出库待发运
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_BEEN_CONSIGNMENT = 8;
    /**
     * 中间库订单商品状态：货物已发出
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_DELIVERED = 9;
    /**
     * 中间库订单商品状态：已收货
     */
    const CENTER_LIBRARY_ORDER_PRODUCT_STATUS_GET = 10;
    
    /**
     * 商品类型
     */
    const COMMODITY_TYPE = "commodity_type";
    /**
     * 商品类型：QS食品
     */
    const COMMODITY_TYPE_SQ_FOOD = 1;
    /**
     * 商品类型：中成药
     */
    const COMMODITY_TYPE_CHINESS_PATENT_MEDICINE = 2;
    /**
     * 商品类型：保健食品
     */
    const COMMODITY_TYPE_HEALTH_FOOD = 3;
    /**
     * 商品类型：化学药制剂
     */
    const COMMODITY_TYPE_CHEMICAL_MEDICINE_PAREPARATIONS = 4;
    /**
     * 商品类型：抗生素制剂
     */
    const COMMODITY_TYPE_ANTIBIOTIC_PREPARATION = 5;
    /**
     * 商品类型：生物制品
     */
    const COMMODITY_TYPE_BIOLOGICAL_PRODUCT = 6;
    /**
     * 商品类型：第一类医疗器械
     */
    const COMMODITY_TYPE_FIRST_MEDICAL_EQUIPMENT = 7;
    /**
     * 商品类型：第三类医疗器械
     */
    const COMMODITY_TYPE_THIRD_MEDICAL_EQUIPMENT = 8;
    /**
     * 商品类型：非药品外
     */
    const COMMODITY_TYPE_NON_MEDICINE = 9;
    /**
     * 商品类型：第二类医疗器械
     */
    const COMMODITY_TYPE_SECOND_MEDICAL_EQUIPMENT = 10;
    /**
     * 商品类型：中药饮片
     */
    const COMMODITY_TYPE_CHINESE_HERBAL_PIECES = 11;
    /**
     * 商品类型：食品
     */
    const COMMODITY_TYPE_FOOD = 12;
    /**
     * 商品类型：非药品
     */
    const COMMODITY_TYPE_NON_DRUG = 13;
    /**
     * 商品类型：计生用品
     */
    const COMMODITY_TYPE_CONTRACEPTIVE = 14;

     /**
     * 商品剂型
     */
    const DOSAGE_FORM = "dosage_form";
    /**
     * 商品剂型:外用剂
     */
    const DOSAGE_FORM_EXTERNAL_APPLICATION = 1;
    /**
     * 商品剂型：干混悬剂
     */
    const DOSAGE_FORM_DRY_SUSPENSION = 2;
    /**
     * 商品剂型：散剂
     */
    const DOSAGE_FORM_POWDER = 3;
    /**
     * 商品剂型：栓剂
     */
    const DOSAGE_FORM_SUPPOSITORY = 4;
    /**
     * 商品剂型：橡胶膏剂
     */
    const DOSAGE_FORM_ADHESIVE_PLASTER = 5;
    /**
     * 商品剂型：气雾剂
     */
    const DOSAGE_FORM_AEROSOL = 6;
    /**
     * 商品剂型：注射剂
     */
    const DOSAGE_FORM_INJECTION = 7;
    /**
     * 商品剂型：流浸膏剂
     */
    const DOSAGE_FORM_LIQUID_EXTRACT = 8;
    /**
     * 商品剂型：滴眼剂
     */
    const DOSAGE_FORM_EYE_DROPS = 9;
    /**
     * 商品剂型：煎膏剂              
     */
    const DOSAGE_FORM_ELECTUARY = 10;
    /**
     * 商品剂型：片剂                
     */
    const DOSAGE_FORM_TABLET = 11;
    /**
     * 商品剂型：眼膏剂
     */
    const DOSAGE_FORM_EYE_OINTMENT = 12;
    /**
     * 商品剂型：糖浆剂
     */
    const DOSAGE_FORM_SYRUPS = 13;
    /**
     * 商品剂型：胶囊剂
     */
    const DOSAGE_FORM_CAPSULE = 14;
    /**
     * 商品剂型：软膏剂
     */
    const DOSAGE_FORM_OINTMENT = 15;
    /**
     * 商品剂型：非药品内
     */
    const DOSAGE_FORM_NON_DRUG_INTERNAL = 16;
    /**
     * 商品剂型：非药品外
     */
    const DOSAGE_FORM_NON_DRUG_FREE = 17;
    /**
     * 商品剂型：颗粒剂
     */
    const DOSAGE_FORM_GRANULE = 18;
    /**
     * 商品剂型：丸剂
     */
    const DOSAGE_FORM_PILL = 19;
    /**
     * 商品剂型：乳膏剂
     */
    const DOSAGE_FORM_CREAMS = 20;
    /**
     * 商品剂型：保健食品
     */
    const DOSAGE_FORM_HEALTH_FOOD = 21;
    /**
     * 商品剂型：医疗器械
     */
    const DOSAGE_FORM_MEDICAL_APPARATUS = 22;
    /**
     * 商品剂型：口服溶液剂
     */
    const DOSAGE_FORM_ORAL_SOLUTION = 23;
    /**
     * 用户留言类型
     */
    const USER_MESSAGE_TYPE = "user_message_type";
    /**
     * 套餐商品状态
     */
    const PACKAGES_STATUS = "packages_status";
    /**
     * 套餐商品状态：已启用
     */
    const PACKAGES_STATUS_START = 1;
    /**
     * 套餐商品状态：已停用
     */
    const PACKAGES_STATUS_STOP = 2;
    /**
     * 套餐商品状态：已删除
     */
    const PACKAGES_STATUS_DELETE = 3;
    /**
     * 折扣商品状态
     */
    const DISCOUNT_ACTIVITY_STATUS = "discount_activity_status";
    /**
     * 折扣商品状态：已启用
     */
    const DISCOUNT_ACTIVITY_STATUS_START = 1;
    /**
     * 折扣商品状态：已停用
     */
    const DISCOUNT_ACTIVITY_STATUS_STOP = 2;
    /**
     * 折扣商品状态：已删除
     */
    const DISCOUNT_ACTIVITY_STATUS_DELETE = 3;
    /**
     * 评价状态
     */
    const EVALUATION_STATUS = "evaluation_status";
    /**
     * 评价状态：未审核
     */
    const EVALUATION_STATUS_UNAUDIT = 1;
    /**
     * 评价状态：已审核
     */
    const EVALUATION_STATUS_AUDITED = 2;

    /**
     * 优惠券发放码状态
     */
    const COUPON_CODE_STATUS = "coupon_code_status";
    /**
     * 评价状态：未兑换
     */
    const COUPON_CODE_STATUS_UNEXCHANGE = 1;
    /**
     * 评价状态：已兑换
     */
    const COUPON_CODE_STATUS_EXCHANGED = 2;
}