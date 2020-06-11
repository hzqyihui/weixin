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
     * 商品状态：删除
     */
    const COMMODITY_STATUS_DELETE = 0;
    /**
     * 商品状态：上架
     */
    const COMMODITY_STATUS_PUTAWAY = 1;
    /**
     * 商品状态：下架
     */
    const COMMODITY_STATUS_SOLDOUT = 2;

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
     * Banner的位置：代理商主页
     */
    const BANNER_POSITION_AGENT_HOME = 6;

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
     * 支付方式：积分
     */
    const PAYMENT_POINTPAY = 4;
    /**
     * 支付方式：积分抽奖
     */
    const PAYMENT_INTEGRAL_SWEEPSTAKES = 5;
    /**
     * 支付方式：积分夺宝
     */
    const PAYMENT_INTEGRAL_INDIANA = 6;
    /**
     * 支付方式：线下支付
     */
    const PAYMENT_INTEGRAL_LINE = 7;

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
     * 终端类型：线下
     */
    const TERMINAL_TYPE_LINE = 3;

    /**
     * 商品类型
     */
    const COMMODITY_TYPE = "commodity_type";
    /**
     * 商品类型：基因商品
     */
    const COMMODITY_TYPE_GENE = 1;
    /**
     * 商品类型：实物商品
     */
    const COMMODITY_TYPE_ENTITY = 2;
    /**
     * 商品类型：会员商品
     */
    const COMMODITY_TYPE_MEMBER = 3;

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
     * 订单状态：已寄回
     */
    const ORDER_STATUS_SENT_BACK = 40;
    /**
     * 订单状态：正在检测
     */
    const ORDER_STATUS_ASSAYING = 50;
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
     * 订单状态：线下正式
     */
    const ORDER_STATUS_NORMAL= 110;

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
    const ROLE_ADMINISTRATOR = 20;
    /**
     * 角色：代理商
     */
    const ROLE_AGENT = 30;
    /**
     * 角色：代理商用户
     */
    const ROLE_AGENT_USER = 40;

    /**
     * 性别
     */
    const GENDER = 'gender';
    /**
     * 性别：女
     */
    const GENDER_FEMALE = 0;
    /**
     * 性别：男
     */
    const GENDER_MALE = 1;

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
     * 验证码用途：查询报告
     */
    const VERIFICATION_CODE_PURPOSE_SEARCH_REPORT = 4;
    /**
     * 验证码用途：找回密码
     */
    const VERIFICATION_CODE_PURPOSE_FIND_PASSWORD = 5;
    /**
     * 文章状态
     */
    const ARTICLE_STATUS = 'article_status';
    /**
     * 文章状态：发表
     */
    const ARTICLE_STATUS_PUBLISHED = 1;
    /**
     * 文章状态：未发表
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
     * 临床病史
     */
    const CHINICAL_HISTORY = "clinical_history";
    /**
     * 临床病史：手术
     */
    const CHINICAL_HISTORY_OPERATION = 10;
    /**
     * 临床病史：放疗
     */
    const CHINICAL_HISTORY_RADIONTHERAPY = 20;
    /**
     * 临床病史：化疗
     */
    const CHINICAL_HISTORY_CHEMOTHERAPY = 30;
    /**
     * 临床病史：靶向药物治疗
     */
    const CHINICAL_HISTORY_TARGETED_THERAPIES = 40;
    /**
     * 亲属关系
     */
    const RELATION = "relation";
    /**
     * 家属关系：父亲
     */
    const RELATION_FATHER = 10;
    /**
     * 家属关系：母亲
     */
    const RELATION_MOTHER = 20;
    /**
     * 家属关系：哥哥
     */
    const RELATION_OLD_BROTHER = 30;
    /**
     * 家属关系：弟弟
     */
    const RELATION_YOUNG_BROTHER = 40;
    /**
     * 家属关系：姐姐
     */
    const RELATION_OLD_SISTER = 50;
    /**
     * 家属关系：妹妹
     */
    const RELATION_YOUNG_SISTER = 60;
    /**
     * 家属关系：爷爷
     */
    const RELATION_GRANDFATHER = 70;
    /**
     * 家属关系：奶奶
     */
    const RELATION_GRANDMOTHER = 80;
    /**
     * 家属关系：舅舅
     */
    const RELATION_UNCLE = 90;
    /**
     * 家属关系：叔叔
     */
    const RELATION_NUNCLE = 100;
    /**
     * 家属关系：阿姨
     */
    const RELATION_AUNTY = 110;
    /**
     * 家属关系：姑姑
     */
    const RELATION_AUNT = 120;
    /**
     * 家属关系：本人
     */
    const RELATION_MYSELF = 130;
    /**
     * 家属关系：爱人
     */
    const RELATION_WIFE = 140;
    /**
     * 家属关系：孩子
     */
    const RELATION_CHILDREN = 150;
    /**
     * 家属关系：其他
     */
    const RELATION_OTHER = 160;
    /**
     * 健康状态
     */
    const HEALTH_STATUS = "health_status";
    /**
     * 健康状态：健康
     */
    const HEALTH_STATUS_HEALTH = 10;
    /**
     * 健康状态：亚健康
     */
    const HEALTH_STATUS_SUB_HEALTH = 20;
    /**
     * 健康状态：疾病
     */
    const HEALTH_STATUS_ILLNESS = 30;
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
     * 未领取
     */
    const SWEEPSTAKES_RESULT_STATUS_NOT_RECEIVE = 0;
    /**
     * 已领取
     */
    const SWEEPSTAKES_RESULT_STATUS_RECEIVED = 1;
    /**
     * 报告个人信息提交状态
     */
    const REPORT_STATUS = 'report_status';
    /**
     * 报告个人信息已提交
     */
    const REPORT_STATUS_COMMITTED = 1;
    /**
     * 报告个人信息未提交
     */
    const REPORT_STATUS_UNCOMMITTED =0;
    /**
     * 是否吸烟
     */
    const SMOKING = 'smoking';
    /**
     * 不吸烟
     */
    const SMOKING_NO = 0;
    /**
     * 吸烟
     */
    const SMOKING_YES =1;

    /**
     * ERP对接返回状态码
     */
    const ERP_STATUS = 'erp_status';
    /**
     * ERP对接返回状态码：成功
     */
    const ERP_STATUS_SUCCESS = 1;
    /**
     * ERP对接返回状态码：失败
     */
    const ERP_STATUS_FAIL =0;

    /**
     * ERP对接接口名称
     */
    const ERP_NAME = 'interface_name';
    /**
     * ERP对接接口名称：客户主数据新增启用、修改ERP-DS
     */
    const ERP_NAME_USER_INCREASE_ERP_DS = 1;
    /**
     * ERP对接接口名称：货品主数据新增启用ERP-DS
     */
    const ERP_NAME_GOODS_INCREASE_ERP_DS = 2;
    /**
     * ERP对接接口名称：销售订单新增ERP-DS
     */
    const ERP_NAME_ORDER_INCREASE_ERP_DS = 3;
    /**
     * ERP对接接口名称：销售订单作废ERP-DS
     */
    const ERP_NAME_FOUR_ORDER_CANCEL_ERP_DS = 4;
    /**
     * ERP对接接口名称：销售退货单ERP-DS
     */
    const ERP_NAME_RETURN_GOODS_ERP_DS = 5;
    /**
     * ERP对接接口名称：销售订单新增DS-ERP
     */
    const ERP_NAME_SIX_ORDER_INCREASE_DS_ERP = 6;
    /**
     * ERP对接接口名称：销售订单取消DS-ERP
     */
    const ERP_NAME_SEVEN_ORDER_CANCEL_DS_ERP = 7;
    /**
     * ERP对接接口名称：C端检测信息回传DS-ERP
     */
    const ERP_NAME_DETECTION_INFORMATION_DS_ERP = 8;
    /**
     * ERP对接接口名称：上传报告状态回传 DS-ERP
     */
    const ERP_NAME_REPORT_RETURN_DS_ERP = 9;
    /**
     * ERP对接接口名称：C端检测码回传DS-ERP
     */
    const ERP_NAME_DETECTION_CODE_DS_ERP = 10;

    /**
     * ERP对接接口代码
     */
    const ERP_CODE = 'erp_code';
    /**
     * ERP对接接口代码：BASE01
     */
    const ERP_CODE_BASE01 = 'BASE01';
    /**
     * ERP对接接口代码：BASE02
     */
    const ERP_CODE_BASE02 = 'BASE02';
    /**
     * ERP对接接口代码：SA01
     */
    const ERP_CODE_SA01 = 'SA01';
    /**
     * ERP对接接口代码：SA02
     */
    const ERP_CODE_SA02 = 'SA02';
    /**
     * ERP对接接口代码：SA03
     */
    const ERP_CODE_SA03 = 'SA03';
    /**
     * ERP对接接口代码：DS01
     */
    const ERP_CODE_DS01 = 'DS01';
    /**
     * ERP对接接口代码：DS02
     */
    const ERP_CODE_DS02 = 'DS02';
    /**
     * ERP对接接口代码：DS03
     */
    const ERP_CODE_DS03 = 'DS03';
    /**
     * ERP对接接口代码：DS04
     */
    const ERP_CODE_DS04 = 'DS04';

    /**
     * ERP返回异常状态
     */
    const ERP_RETURN_STATUS = 'erp_return_status';
    /**
     * ERP返回异常状态: 正常
     */
    const ERP_RETURN_STATUS_SUCCESS = 1;
    /**
     * ERP返回异常状态： 异常
     */
    const ERP_RETURN_STATUS_FAIL =2;

    /**
     * 商品评价审核状态
     */
    const COMMODITY_EVALUATION_STATUS = 'commodity_evaluation_status';
    /**
     * 商品评价审核状态：待审核
     */
    const COMMODITY_EVALUATION_STATUS_CHECK_PENDING = 0;
    /**
     * 商品评价审核状态：审核驳回
     */
    const COMMODITY_EVALUATION_STATUS_REJECT = 1;
    /**
     * 商品评价审核状态：审核通过
     */
    const COMMODITY_EVALUATION_STATUS_PASS = 2;

    /**
     * 商品规格状态
     */
    const COMMODITY_SPECIFICATION_STATUS = 'commodity_specification_status';
    /**
     * 商品规格状态：下架
     */
    const COMMODITY_SPECIFICATION_STATUS_DISABLED = 0;
    /**
     * 商品规格状态：上架
     */
    const COMMODITY_SPECIFICATION_STATUS_ENABLED = 1;
    /**
     * 商品规格状态：删除
     */
    const COMMODITY_SPECIFICATION_STATUS_DELETED = 2;

    /**
     * 线下订单用户id：550
     */
    const ERP_ORDER_USER_ID = 550;

    /**
     * 线下商品默认id：253
     */
    const ERP_ORDER_COMMODITY_ID = 253;

    /**
     * 子订单状态
     */
    const ORDER_COMMODITY_STATUS = 'order_commodity_status';
    /**
     * 子订单状态：正常
     */
    const ORDER_COMMODITY_STATUS_NORMAL = 1;
    /**
     * 子订单状态：作废
     */
    const ORDER_COMMODITY_STATUS_CANCEL = 0;

    /**
     * 商品规格包装类型
     */
    const COMMODITY_SPECIFICATION_PACKAGETYPE = 'packagetype';
    /**
     * 商品规格包装类型：精装
     */
    const COMMODITY_SPECIFICATION_PACKAGETYPE_HARDBACK = 1;
    /**
     * 商品规格包装类型：简装
     */
    const COMMODITY_SPECIFICATION_PACKAGETYPE_PAPERBACK = 2;
    /**
     * 代理商用户进入方式
     */
    const AGENT_ENTRANCE_METHOD = "agent_entrance_method";
    /**
     * 代理商用户进入方式：外部的
     */
    const AGENT_ENTRANCE_METHOD_EXTERNAL = 0;
    /**
     * 代理商用户进入方式：内部的
     */
    const AGENT_ENTRANCE_METHOD_INTERNAL = 1;
    /**
     * 包邮规则配置项类型
     */
    const FREIGHT_RULE_OPTION_TYPE = "freight_rule_option_type";
    /**
     * 包邮规则配置项类型：商品
     */
    const FREIGHT_RULE_OPTION_TYPE_COMMODITY = 1;
    /**
     * 包邮规则配置项类型：分类
     */
    const FREIGHT_RULE_OPTION_TYPE_CATEGORY = 2;
    /**
     * 包邮规则配置项类型：会员
     */
    const FREIGHT_RULE_OPTION_TYPE_LEVEL = 3;
    /**
     * 包邮规则配置项类型：终端
     */
    const FREIGHT_RULE_OPTION_TYPE_TERMINAL = 4;
}