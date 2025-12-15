<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of the VisitorLog_Logger class
 *
 * @method public static function instance()
 * @method public        function __construct()
 * @method public        function debug()
 * @method public        function info()
 * @method public        function warning()
 * @method public        function log_action()
 * @method public        function log()
 * @method public        function who_is_user()
 * @method public        function log_backup()
 * @method public        function log_scan_files()
 * @method public        function log_update()
 * @method public        function prepare_log_info() 
 */
class VisitorLog_Logger
{
	const DISABLED = -1;
	const LOG      = 0;
	const WARNING  = 1;
	const INFO     = 2;
	const DEBUG    = 3;

	const LOG_COLOR     = '#999999';
	const DEBUG_COLOR   = '#666666';
	const INFO_COLOR    = '#276f86';
	const WARNING_COLOR = '#9f3a38;';

	private        $logPriority = self::INFO;   	// log file priotrity.
	private        $logSpecific = 0;				      // log Specific priotrity.
	private static $instance = null;		          // instance.

	public static function instance()
	{
		if ( null == self::$instance ) self::$instance = new self();
		return self::$instance;

	} // END func

	public function __construct()
	{
		if ( null == self::$instance ) self::$instance = $this;

	} // END func

	/**
	 * Grab debug.
	 *
	 * @param string $text Debug message text.
	 *
	 * @return string Log debug message.
	 */
	public function debug( $text )
	{
		return $this->log( $text, self::DEBUG );
		
	} // END func

	/**
	 * Grab info.
	 *
	 * @param string $text Info message text.
	 *
	 * @return string Log info message.
	 */
	public function info( $text )
	{
		return $this->log( $text, self::INFO );

	} // END func

	/**
	 * Grab warning information.
	 *
	 * @param string $text Warning message text.
	 *
	 * @return string Log warning message.
	 */
	public function warning( $text )
	{
		return $this->log( $text, self::WARNING );

	} // END func


	/**
	 * Grab actions information.
	 *
	 * @param string $text Warning message text.
	 * @param int    $priority priority message.
	 *
	 * @return string Log warning message.
	 */
	public function log_action( $text, $priority )
	{
		return $this->log( $text, $priority );

	} // END func

	/**
	 * Log to database.
	 *
	 * @param string $text Log record text.
	 * @param int    $priority Set priority.
	 *
	 * @return bool true|false Default is False.
	 *
	 * @uses VisitorLog_Utility::get_option_sl
	 */
	public function log( $text, $priority='' )
	{ 
		$text = $this->prepare_log_info( $text );
		$user = $this->who_is_user();
		$data = array();
		$data['log_content']   = $text;
		$data['log_user']      = $user;
		$data['log_timestamp'] = current_time( 'timestamp' );

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'action_log';
		$rows_limit = VisitorLog_Utility::get_option_sl( 'log_record_limit' );

		$result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
		if ( !$result ) return false;

		$number_rows = VisitorLog_DB_Base::$wpdb_vl->num_rows;

		if ( $number_rows > $rows_limit ) {
			$result = VisitorLog_DB_Base::$wpdb_vl->query( VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name LIMIT %d", 1));
			if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'log()-Error DELETE into table: ' . $table_name . ' - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning( $err );
                return false;
            }
		}
		return true;

	} // END func

	/**
	 * Who is user.
	 *
	 * @return text - user/admin/WP_CLI/DOING_CRON
	 */
	public function who_is_user()
	{
		$user = 'User';
		if ( current_user_can('manage_options') ) {
			$user = 'Admin';
			return $user;
		}	
		if ( defined( 'WP_CLI' ) ) {
		    $user = 'WP_CLI';
		    return $user;
		}    
		if ( defined( 'DOING_CRON' ) ) {
		    $user = 'CRON';
		    return $user;
		}    
		return $user;

	} // END func	

	/**
	 * Log to database backup.
	 *
	 * @param int    $res_file  result backup.
	 * @param int    $res_mail  result email.
	 * @param string $mode      auto/manual.
	 *
   * @uses VisitorLog_DB_Base::$table_prefix_sl
   * @uses VisitorLog_Utility::get_option_sl()
   * @uses VisitorLog_System_View::show_message()
	 */
	public function log_backup( $res_file, $res_mail, $mode )
	{ 
		global $current_user;

        $current_time = current_time( 'timestamp' ); 
		$success_file = '';
		$success_mail = '';
		$user = '';
		$data = array();

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'backups';
		$rows_limit = VisitorLog_Utility::get_option_sl( 'log_record_limit' );

		if ( is_admin() ) 
			$user = 'Admin';
		elseif ( ! empty( $current_user ) && ! empty( $current_user->user_login ) )
			$user = $current_user->user_login;
		elseif ( defined( 'WP_CLI' ) ) 
			$user = 'WP_CLI';
		elseif ( defined( 'DOING_CRON' ) ) 
			$user = 'CRON';

		if ( $res_file > 0 ) $success_file = 'success';
		if ( $res_file < 0 ) $success_file = 'fail';
		if ( $res_mail > 0 ) $success_mail = 'success';
		if ( $res_mail < 0 ) $success_mail = 'fail';

		$data['initiator']    = $user;
		$data['mode']         = $mode;
		$data['success']      = $success_file;
		$data['success_mail'] = $success_mail;
		$data['date_time']    = $current_time;

		$result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$msg = __( 'Error writing to the Backups table!', 'visitorlog' );
			VisitorLog_System_View::show_message( 'orange', $msg, $msg );
			return false;
		}

		$number_rows = VisitorLog_DB_Base::$wpdb_vl->num_rows;

		if ( $number_rows > $rows_limit ) {
			$result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name LIMIT %d", 1));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
              $err = 'log_backup()-Error DELETE into table: ' . $table_name . ' - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
              VisitorLog_Logger::instance()->warning( $err );
            }
		}
		return true;

	} // END func

	/**
	 * File scan log.
	 *
	 * @param int    $res_file  result backup.
	 * @param int    $res_mail  result email.
	 * @param string $text_in   text of message.
	 * @param string $mode      auto/manual.
	 *
   * @uses VisitorLog_DB_Base::$table_prefix_sl
   * @uses VisitorLog_Utility::get_option_sl()
   * @uses VisitorLog_System_View::show_message()
	 */
	public function File_scan_log( $res_file, $res_mail, $text_in, $mode )
	{ 
		global $current_user;

    $current_time = current_time( 'timestamp' ); 
		$success_file = '';
		$success_mail = '';
		$user = '';
		$data = array();
		$text = $this->prepare_log_info( $text_in );

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'fcd_scan';
		$rows_limit = VisitorLog_Utility::get_option_sl( 'log_record_limit' );

		if ( is_admin() ) 
			$user = 'Admin';
		elseif ( ! empty( $current_user ) && ! empty( $current_user->user_login ) )
			$user = $current_user->user_login;
		elseif ( defined( 'WP_CLI' ) ) 
			$user = 'WP_CLI';
		elseif ( defined( 'DOING_CRON' ) ) 
			$user = 'CRON';

		if ( $res_file > 0 ) $success_file = 'success';
		if ( $res_file < 0 ) $success_file = 'fail';
		if ( $res_mail > 0 ) $success_mail = 'success';
		if ( $res_mail < 0 ) $success_mail = 'fail';

		$data['initiator']    = $user;
		$data['mode']         = $mode;
		$data['success']      = $success_file;
		$data['text']         = $text;
		$data['success_mail'] = $success_mail;
		$data['date_time']    = $current_time;

		$result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$msg = __( 'Error writing to the Scan table', 'visitorlog' );
			VisitorLog_System_View::show_message( 'orange', $msg, $msg );
			return false;
		}

		$number_rows = VisitorLog_DB_Base::$wpdb_vl->num_rows;
		
		if ( $number_rows > $rows_limit ) {
			$result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name LIMIT %d", 1));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
              $err = 'File_scan_log()-Error delete into table fcd_scan - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
              VisitorLog_Logger::instance()->warning( $err );
            }
		}
		return true;

	} // END func

	/**
	 * Log to scan files.
	 *
	 * @param int    $arg0      result scan.
	 * @param string $arg1      text.
	 * @param string $mode      auto/manual.
	 *
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_System_View::show_message()
	 */
	public function log_scan_files( $arg0, $arg1, $mode )
	{ 
		global $current_user;

		$text = $this->prepare_log_info( $arg1 );
    $current_time = current_time( 'timestamp', 0 ); 
		$data = array();
		$user = '';
		$success_mail = '';
		$success = '';
		$mail = '';

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'fcd_scan';
		$rows_limit = VisitorLog_Utility::get_option_sl( 'log_record_limit' );

		if ( 5 == $arg0  ) {
			$success_mail = 'success';
			$success = 'success';
			$mail = VisitorLog_Utility::get_option_sl( 'FCD_scan_email_address' );
		} elseif ( 4 == $arg0 || 6 == $arg0 ) {
			$success = 'success';
		} elseif ( -4 == $arg0 ) {
			$success = 'success';
			$success_mail = 'fail';
			$mail = VisitorLog_Utility::get_option_sl( 'FCD_scan_email_address' );
    } elseif ( -3 == $arg0 || -2 == $arg0 || -2 == $arg0 ) {
			$success = 'fail';
    }

		if ( is_admin() ) { 
			$user = 'ADMIN';
		} else {
			if ( ! empty( $current_user ) && ! empty( $current_user->user_login ) ) {
				$user = $current_user->user_login;
			} elseif ( defined( 'WP_CLI' ) ) {
					$user = 'WP_CLI';
			} elseif ( defined( 'DOING_CRON' ) ) {
				$user = 'CRON';
			}
		}		

		$data['initiator']    = $user;
		$data['mode']         = $mode;
		$data['success']      = $success;
		$data['text']         = $text;
		$data['mail']         = $mail;
		$data['success_mail'] = $success_mail;
		$data['date_time']    = $current_time;

		$result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error) {
			$msg = __( 'Error writing to the files scan table!', 'visitorlog' );
			VisitorLog_System_View::show_message( 'orange', $msg, $msg );
			return;
		}

		if ( VisitorLog_DB_Base::$wpdb_vl->num_rows > $rows_limit ) {
			$result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name LIMIT %d", 1));
			if ( VisitorLog_DB_Base::$wpdb_vl->last_error) {
				$err = 'log_scan_files()-Error deleting into table fcd_scan - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
				VisitorLog_Logger::instance()->warning( $err );
			}
		}

	} // END func

	/**
	 * Set update statistics.
	 *
	 * @param string $mode -> install
	 *						  activated
	 *						  deactivated
	 *						  updated
	 *
   * @uses VisitorLog_DB_Base::instance()->insert_update_log()
   * @uses VisitorLog_Utility::get_option_sl()
	 */
	public function log_update( $mode )
	{
		$data['category']       = $mode;
		$data['plugin_version'] = VISITORLOG_PLUGIN_VERSION;
		$data['db_version']     = VisitorLog_Utility::get_option_sl('db_version');
		$data['date_time']      = current_time( 'timestamp' ); 

		$result = VisitorLog_DB_Base::instance()->insert_update_log( $data );
        if ( false === $result ) {
            $err = 'log_update()-Error INSERT into table update - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning( $err );
        }

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'update';
		$rows_limit = VisitorLog_Utility::get_option_sl( 'log_record_limit' );

		VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE id != %d", 0));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'log_update()-Error SELECT into table update - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning( $err );
        }

		if ( VisitorLog_DB_Base::$wpdb_vl->num_rows > $rows_limit ) {
			VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name LIMIT %d", 1));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
              $err = 'log_update()-Error DELETE into table update - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
              VisitorLog_Logger::instance()->warning( $err );
            }
		}

	} // END func	

	/**
	 * Prepare log data.
	 *
	 * @param mixed $data Log data.
	 * @return mixed $data filtered data.
	 */
	public function prepare_log_info( $data )
	{
		$patterns[0]    = '/user=([^\&]+)\&/';
		$replacement[0] = 'user=xxxxxx&';
		$patterns[1]    = '/alt_user=([^\&]+)\&/';
		$replacement[1] = 'alt_user=xxxxxx&';
		$patterns[2]    = '/\&server=([^\&]+)\&/';
		$replacement[2] = '&server=xxxxxx&';
		$data           = preg_replace( $patterns, $replacement, $data );
		return $data;

	} // END func


} // END func