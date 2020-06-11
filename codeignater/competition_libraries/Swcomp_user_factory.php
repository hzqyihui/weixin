<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * =====================================================================================
 *
 *        Filename:  Swcomp_user_factory.php
 *
 *     Description:  用户工厂类，根据参数返回相应的用户数组
 *
 *         Created:  2016-05-09 14:58:52
 *
 *          Author:  wuhaohua
 *
 * =====================================================================================
 */
class Swcomp_user_factory {
	public static function get_user($mode, $arg = '') {
		switch ($mode) {
			case 1:
				return new Swcomp_player($arg);
				break;
			case 2:
				return new Swcomp_admin($arg);
				break;
			case 3:
				return new Swcomp_judgment($arg);
				break;
			default:
				return FALSE;
		}
	}
}
