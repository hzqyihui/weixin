<?php
/**
 * =====================================================================================
 *
 *        Filename: Lab_system_code.php
 *
 *     Description: 系统字典类库
 *
 *         Created: 2017-03-16 19:19:18
 *
 *          Author: huazhiqiang
 *
 * =====================================================================================
 */
namespace App\Libraries;

class Lab_system_code {

    /**
     * 角色：普通用户
     */
    const ROLE = "role";
    /**
     * 角色：管理员
     */
    const ROLE_ADMIN = 1;
    /**
     * 角色：教师
     */
    const ROLE_TEACHER = 2;
    /**
     * 角色：学生
     */
    const ROLE_STUDENT = 3;
    /**
     * 角色：外部成员
     */
    const ROLE_OTHERS = 4;
    /**
     * 角色：评委
     */
    const ROLE_JUDGMENT = 5;

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
     * 招新阶段
     */
    const STAGE = 'stage';
    /**
     * 招新阶段：报名
     */
    const STAGE_ENROLL = 1;
    /**
     * 招新阶段：初试
     */
    const STAGE_EXAMINE = 2;
    /**
     * 招新阶段：复试
     */
    const STAGE_REEXAMINE = 3;
    /**
     * 招新阶段：录取
     */
    const STAGE_PASS = 4;
    /**
     * 招新阶段：未录取
     */
    const STAGE_NOT_PASS = 5;

    /**
     * 状态类型
     */
    const STATUS = 'status';
    /**
     * 状态类型：招新
     */
    const STATUS_REEXAMINE = 3;
    /**
     * 状态类型：在册
     */
    const STATUS_IN_SCHOOL = 4;
    /**
     * 状态类型：开除
     */
    const STATUS_FIRE = 5;
    /**
     * 状态类型：注销
     */
    const STATUS_LOGOUT = 6;

    /**
     * 消息类型
     */
    const MESSAGE_TYPE = 'message_type';
    /**
     * 消息类型：招新
     */
    const MESSAGE_TYPE_RECRUIT = 1;
    /**
     * 消息类型：开会
     */
    const MESSAGE_TYPE_MEETING = 2;
    /**
     * 消息类型：讲座
     */
    const MESSAGE_TYPE_LECTURE = 3;

    /**
     * 钥匙类型
     */
    const KEY = 'key';
    /**
     * 钥匙类型：拥有钥匙
     */
    const KEY_HAVE_KEY = 1;
    /**
     * 钥匙类型：未拥有钥匙
     */
    const KEY_NOT_HAVE_KEY = 0;

    /**
     * 位置类型
     */
    const POSITION = 'position';
    /**
     * 位置类型：对内
     */
    const POSITION_INTERNAL = 1;
    /**
     * 位置类型：对外
     */
    const POSITION_EXTERNAL = 2;

    /**
     * 启用用户类型
     */
    const IS_SHOW = 'is_show';
    /**
     * 启用类型：停用
     */
    const IS_SHOW_NOT = 0;
    /**
     * 启用类型：启用
     */
    const IS_SHOW_SHOW = 1;

    /**
     * 启用招新类型
     */
    const IS_USE = 'is_use';
    /**
     * 启用类型：停用
     */
    const IS_USE_NOT = 0;
    /**
     * 启用类型：启用
     */
    const IS_USE_USE = 1;

    /**
     * 验证码用途
     */
    const VERIFICATION_CODE_PURPOSE = 'verification_code_purpose';
    /**
     * 验证码用途：报名
     */
    const VERIFICATION_CODE_PURPOSE_REGISTER = 1;
    /**
     * 验证码用途：邮件
     */
    const VERIFICATION_CODE_PURPOSE_EMAIL = 2;
    /**
     * 验证码用途：找回密码
     */
    const VERIFICATION_CODE_PURPOSE_FIND_PASSWORD = 3;

    /**
     * 验证码有效时间（分钟）
     */
    const VERIFICATION_CODE_VALID_TIME = 10;

    /**
     * 阶段状态
     */
    const STAGE_STATUS = 'stage_status';
    /**
     * 阶段状态：待审
     */
    const STAGE_STATUS_PENDING_TRIAL = 1;
    /**
     * 阶段状态：通过
     */
    const STAGE_STATUS_PASS = 2;
    /**
     * 阶段状态：淘汰
     */
    const STAGE_STATUS_NOT_PASS = 3;

    /**
     * 是否找回密码(邮件)
     */
    const FIND_BACK_PWD = 'find_back_pwd';
    /**
     * 是否找回密码：正在找回
     */
    const FIND_BACK_PWD_BEING_FIND = 1;
    /**
     * 是否找回密码：未找回
     */
    const FIND_BACK_PWD_NO_FIND = 0;


    /**
     * MD5加盐字符串
     */
    const MD5_SALT_STRING = 'JCVFIO3anvouae.[0359*haigovsh\.GXDUIWEIJOIRFVEW@#$^uB()JEROPVG';



}