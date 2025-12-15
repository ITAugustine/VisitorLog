<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Class VisitorLog_System_Cron_Jobs
 *
 * @method public static function  instance()
 * @method public        function  __construct()
 * @method public static function  get_cron_schedules()
 * @method public static function  cron_backups()
 * @method public static function  login_captcha()
 * @method public static function  cron_activity_test()
 */
class VisitorLog_System_Cron_Jobs
{
	private static $on_auto_backups  = null;
	private static $on_login_captcha = null;
	private static $Enable_auto_stat = null;

	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Construct.
	 *
	 * @uses VisitorLog_Utility::get_option_sl()
	 */
    public function __construct()
    {
		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_auto_backups' ) &&
			 'on' == VisitorLog_Utility::get_option_sl( 'on_db_protection' )
		   ) {
		    self::$on_auto_backups = 1;
		} else
			self::$on_auto_backups = null;

		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_login_captcha' ) &&
			 'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' )
		   ) 
			self::$on_login_captcha = 1;
		else
			self::$on_login_captcha = null;

		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_auto_send_stat' ) ) 
			self::$Enable_auto_stat = 1;
		else
			self::$Enable_auto_stat = null;

		add_action( 'visitorlog_cron_backups',  array( self::get_class_name(), 'cron_backups' ) );
		add_action( 'visitorlog_login_captcha', array( self::get_class_name(), 'login_captcha' ) );
		add_action( 'visitorlog_check_system',  array( self::get_class_name(), 'cron_check_system' ) );
		add_action( 'visitorlog_activity_test', array( self::get_class_name(), 'cron_activity_test' ) );
		add_filter( 'cron_schedules',           array( self::get_class_name(), 'get_cron_schedules' ) ); 

		// Cron jobs.
		// ---------- task #1 
  		if ( self::$on_auto_backups ) {
  			// Set Cron Schedules.
			$sched1 = wp_next_scheduled( 'visitorlog_cron_backups' );
			if ( false == $sched1 ) {
				$time = current_time( 'timestamp' );
				$backups_period = VisitorLog_Utility::get_option_sl( 'backups_period' );
			    switch( $backups_period ) { 
			        case '1': 
			        	$interval = 'monthly';   break;
			        case '2': 
			        	$interval = 'weekly';    break;
			        case '3': 
			        	$interval = 'daily';     break; 
			        case '4': 
			        	$interval = '5minutely'; break; 
			        case '' : 
			        	$interval = 'monthly';   break; 
			    }
				wp_schedule_event( $time, $interval, 'visitorlog_cron_backups' );
			}
		} else {
			// Unset Cron Schedules.
			$sched1 = wp_next_scheduled( 'visitorlog_cron_backups' );
			if ( $sched1 ) wp_unschedule_event( $sched1, 'visitorlog_cron_backups' );
		}

		// ---------- task #2
  		if ( self::$on_login_captcha ) {
  			// Set Cron Schedules.
			$sched2 = wp_next_scheduled( 'visitorlog_login_captcha' );
			if ( false == $sched2 ) {
				$time = current_time( 'timestamp' );
				wp_schedule_event( $time, 'daily', 'visitorlog_login_captcha' );
			}	
		} else {
			// Unset Cron Schedules.
			$sched2 = wp_next_scheduled( 'visitorlog_login_captcha' );
			if ( $sched2 ) wp_unschedule_event( $sched2, 'visitorlog_login_captcha' );
		}

		// ---------- task #3
		// Set Cron Schedules.
		$sched3 = wp_next_scheduled( 'visitorlog_check_system' );
		if ( false == $sched3 ) {
			$time = current_time( 'timestamp' ); 
			wp_schedule_event( $time, 'daily', 'visitorlog_check_system' );
		}	

    } // END of __construct()

	/**
	 * Get current Cron Schedual.
	 *
	 * @param  array $schedules   Array of currently set scheduals.
	 * @return array $scheduales.
	 */
	public static function get_cron_schedules( $schedules )
	{
		$schedules['monthly'] = array( 
			'interval' => 30 * 24 * 60 * 60,
			'display'  => esc_html__( 'Monthly', 'visitorlog' ),
		);
		$schedules['weekly'] = array(
			'interval' => 7 * 24 * 60 * 60,
			'display'  => esc_html__( 'Weekly', 'visitorlog' ),
		);
		$schedules['daily'] = array(
			'interval' => 24 * 60 * 60,
			'display'  => esc_html__( 'Daily', 'visitorlog' ),
		);
		$schedules['hourly'] = array(
			'interval' => 60 * 60,
			'display'  => esc_html__( 'Once an hour', 'visitorlog' ),
		);
		$schedules['5minutely'] = array(
			'interval' => 5 * 60,
			'display'  => esc_html__( 'Once every 5 minutes', 'visitorlog' ),
		);
		return $schedules; 

	} // END func

	/**
	 * Execute Backup Tasks.
 	 *
	 * @uses VisitorLog_Backup_Handler::scheduled_backup_handler()
	 */
	public static function cron_backups()
	{
		if ( !self::$on_auto_backups ) return;

		VisitorLog_Backup_Handler::instance()->scheduled_backup_handler();

	} // END func

	/**
	 * Execute login captcha Tasks.
	 *
	 * @uses VisitorLog_Security_Utility::delete_expired_captcha_transients()
	 */
	public static function login_captcha()
	{
		if ( !self::$on_login_captcha ) return;

        VisitorLog_Security_Utility::delete_expired_captcha_transients();
        
	} // END func

	/**
	 * Execute scheduled check system.
	 *
	 * @uses VisitorLog_System_Check::handler_system_check_common()
	 */
	public static function cron_check_system()
	{
        VisitorLog_System_Check::handler_system_check_common();

	} // END func

	/**
	 * Execute Cron test Tasks.
	 *
	 * @uses VisitorLog_Utility::update_option_sl()
	 */
	public static function cron_activity_test()
	{
		// Unset Cron Schedules.
		$sched = wp_next_scheduled( 'visitorlog_activity_test' );
		if ( $sched ) wp_unschedule_event( $sched, 'visitorlog_activity_test' );

		$cron_test_time = current_time('timestamp');
        VisitorLog_Utility::update_option_sl( 'cron_test_time', $cron_test_time );

		$msg = __( 'Cron WP - successful activity test', 'visitorlog' );
        $date_time = gmdate( 'd-m-Y H:i:s', $cron_test_time );
        $msg .= ',  Time: ' . $date_time;
        VisitorLog_Logger::instance()->warning($msg);

	} // END func	

} // END class