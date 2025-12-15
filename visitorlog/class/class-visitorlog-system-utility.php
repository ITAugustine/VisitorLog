<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_System_Utility class.
 *
 * @method	public static function get_class_name()
 * @method	public static function is_admin()
 */
class VisitorLog_System_Utility
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	public static function is_admin()
	{
		global $current_user;

		if ( 0 === $current_user->ID ) {
			return false;
		}
		if ( 10 == $current_user->wp_user_level || ( isset( $current_user->user_level ) && 10 == $current_user->user_level ) || current_user_can( 'level_10' ) ) {
			return true;
		}
		return false;

	}  // END func


} // END class