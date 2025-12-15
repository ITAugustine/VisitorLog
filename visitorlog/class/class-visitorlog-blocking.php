<?php
/**
 * VisitorLog Blocking
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of the VisitorLog_Blocking class 
 * 
 * @method public        function __construct()
 * @method public static function checking_for_blocks()
 * @method public static function checking_blocking_time_login()
 * @method public static function checking_blocking_time_404()
 */

class VisitorLog_Blocking
{
	public function __construct()
    {
        self::checking_for_blocks();

	} // END  __construct()

	/**
	 *  Checking for blocks
	 * 
	 * @uses VisitorLog_Utility::get_option_sl()
	 * @uses VisitorLog_Registration::instance()->registration()
	 * @uses VisitorLog_Statistics_Utility::instance()->statistics_handler()
	 * @uses VisitorLog_Security_Utility::redirect_to_url()
	 */
	public static function checking_for_blocks()
    {
        global $visitorlog_users_resident_parameters;

		$category = $visitorlog_users_resident_parameters['category'];
		$url      = $visitorlog_users_resident_parameters['url'];
        $event    = $visitorlog_users_resident_parameters['event'];

        if ( '' != $event )      return;
        if ( 'WP' == $category ) return;

		/* --- LOGIN --- */
        if ( false !== stripos($url, 'wp-login.php') || 
             false !== stripos($url, 'xmlrpc.php') ) {

	        $block = self::checking_blocking_time_login();
	    	if ( '' == $block['category'] ) return;

	        $visitorlog_users_resident_parameters['timerelease'] = $block['timerelease'];

			if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) &&
			     'on' == VisitorLog_Utility::get_option_sl( 'on_login_lockdown' )         &&
			     'temp' == $block['block']                                                &&
			     'blocklogin' == $block['category'] ) {

				$visitorlog_users_resident_parameters['category'] = 'blocklogin';
	    		$visitorlog_users_resident_parameters['block'] = 'temp';
	    		VisitorLog_Registration::instance()->registration();
				VisitorLog_Statistics_Utility::instance()->statistics_handler();
				$visitorlog_users_resident_parameters['event'] = 'eventlogin';

		        $redirect_url = VisitorLog_Utility::get_option_sl( 'redirect_url_login' );
	        	VisitorLog_Security_Utility::redirect_to_url( $redirect_url );
		    	exit;
			}

			$visitorlog_users_resident_parameters['category'] = 'BotLogin';
    		$visitorlog_users_resident_parameters['block'] = $block['block'];
    		VisitorLog_Registration::instance()->registration();
			VisitorLog_Statistics_Utility::instance()->statistics_handler();
			$visitorlog_users_resident_parameters['event'] = 'eventlogin';
			exit;
		}

		/* --- Bot404 --- */
	    $check = self::checking_blocking_time_404();

	    if ( 'Admin404' == $check['category'] || 'block404' == $check['category'] ) {

	        $visitorlog_users_resident_parameters['timerelease'] = $check['timerelease'];

            if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) &&
                 'on' == VisitorLog_Utility::get_option_sl( 'on_block_ip_temp' )         &&
                 'on' == VisitorLog_Utility::get_option_sl( 'on_404_logging' )           &&
			     'temp' == $check['block'] ) {

	        	$visitorlog_users_resident_parameters['category'] = 'block404';
	        	$visitorlog_users_resident_parameters['block'] = 'temp';
	    		VisitorLog_Registration::instance()->registration();
				VisitorLog_Statistics_Utility::instance()->statistics_handler();
				$visitorlog_users_resident_parameters['event'] = 'event404';

		        $redirect_url = VisitorLog_Utility::get_option_sl( 'ddos_redirect_url' );
	        	VisitorLog_Security_Utility::redirect_to_url( $redirect_url );
		    	exit;
	    	}

			$visitorlog_users_resident_parameters['category'] = 'Bot404';
    		$visitorlog_users_resident_parameters['block'] = '';
    		VisitorLog_Registration::instance()->registration();
			VisitorLog_Statistics_Utility::instance()->statistics_handler();
			$visitorlog_users_resident_parameters['event'] = 'event404';
    		exit;
	    }

	} // END func


    /**
     * Checking the lock time when logging in
     *
     * @return false/array $block
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function checking_blocking_time_login()
    {
        global $visitorlog_users_resident_parameters;

        $ip        = $visitorlog_users_resident_parameters['ip'];
        $timestamp = $visitorlog_users_resident_parameters['timestamp'];

        $block['block'] = '';
        $block['timerelease'] = 0;
        $block['category'] = '';

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'events_failed';

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_ip` = %s AND `timerelease` > $timestamp ORDER BY `id` DESC", $ip));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'checking_blocking_time_login()-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error ;
            VisitorLog_Logger::instance()->warning( $err );
            return $block;
        }

        if ( empty($array) ) return $block;

        $category  = $visitorlog_users_resident_parameters['category'];
        $user_name = $visitorlog_users_resident_parameters['username'];
        $canal     = $visitorlog_users_resident_parameters['canal'];
        $url       = $visitorlog_users_resident_parameters['url'];
        $refinfo   = $visitorlog_users_resident_parameters['refinfo'];

        $login_sanitize    = 'not';
        $password_sanitize = 'not';

        if ( 'Admin' == $category ) {
	        $block['block'] = '';
	        $block['timerelease'] = 0;
	        $block['category'] = 'AdminLogin';
        	$count = 0;
        } else {
	        $block['block'] = 'temp';
	        $block['timerelease'] = $array[0]->timerelease;
	        $block['category'] = 'blocklogin';
	        $count = $array[0]->count;
        }
        $data = array(
            'timestamp'    => $timestamp,
            'timerelease'  => $block['timerelease'],
            'user_name'    => $user_name,
            'user_ip'      => $ip,
            'category'     => $block['category'],
            'canal'        => $canal,
            'count'        => $count,
            'block'        => $block['block'],
            'login'        => $login_sanitize,
            'pswd'         => $password_sanitize,
            'url'          => $url,
            'referer_info' => $refinfo
        );
        $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'checking_blocking_time_login()-Error insert data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        }  

        $array  = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT COUNT(id) FROM $table_name WHERE `id` != %d", 0), ARRAY_A);
        $c_rows = $array[0]['COUNT(id)'];
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'checking_blocking_time_login(2)-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        }  

        $rows_record_limit = (int)VisitorLog_Utility::get_option_sl( '#_record_limit' );
        if ( $c_rows > $rows_record_limit ) {
            $delete = $c_rows - $rows_record_limit;
            $res = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE `id` != %d LIMIT $delete", 0));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'checking_blocking_time_login()-Error delete rows: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return $block;
            } 
        }
        return $block;

    } // END func

    /**
     * Checking the lock time when 404 event
     *
     * @return false/array block
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function checking_blocking_time_404()
    {
        global $visitorlog_users_resident_parameters;

        $ip        = $visitorlog_users_resident_parameters['ip'];
        $timestamp = $visitorlog_users_resident_parameters['timestamp'];
        $category  = $visitorlog_users_resident_parameters['category'];

        $block['block'] = '';
        $block['timerelease'] = 0;
        $block['category'] = '';

		if ( 'WP' == $category ) return $block;

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'event_404';

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_ip` = %s AND `release_tstmp` > $timestamp ORDER BY `id` DESC", $ip ));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'checking_blocking_time_404()-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error ;
            VisitorLog_Logger::instance()->warning( $err );
            return $block;
        }

		if ( empty($array) ) return $block;

        $useragent = $visitorlog_users_resident_parameters['username'];
        $canal     = $visitorlog_users_resident_parameters['canal'];
        $url       = $visitorlog_users_resident_parameters['url'];
        $refinfo   = $visitorlog_users_resident_parameters['refinfo'];

        if ( 'Admin' == $category ) {
	        $block['block'] = '';
	        $block['timerelease'] = 0;
	        $block['category'] = 'Admin404';
        	$count = 0;
        } else {
	        $block['block'] = 'temp';
	        $block['timerelease'] = $array[0]->release_tstmp;
	        $block['category'] = 'block404';
	        $count = $array[0]->count;
        }
        $data = array(
           'timestamp'     => $timestamp, 
           'release_tstmp' => $block['timerelease'],
           'user_ip'       => $ip,
           'count'         => $count,
           'block'         => $block['block'],
           'canal'         => $canal,
           'category'      => $block['category'],
           'user_agent'    => $useragent,
           'url'           => $url,
           'refinfo'       => $refinfo
        );
        $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'checking_blocking_time_404()-Error insert data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        } 

        $array  = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT COUNT(id) FROM $table_name WHERE `id` != %d", 0), ARRAY_A);
        $c_rows = $array[0]['COUNT(id)'];
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'checking_blocking_time_404(2)-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        }  

        $rows_record_limit = (int)VisitorLog_Utility::get_option_sl( '#_record_limit' );

        if ( $c_rows > $rows_record_limit ) {

            $delete = $c_rows - $rows_record_limit;
            $res = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE `id` != %d LIMIT $delete", 0));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'checking_blocking_time_404()-Error delete rows: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return $block;
            } 
        }
        return $block;

    } // END func


} // END class