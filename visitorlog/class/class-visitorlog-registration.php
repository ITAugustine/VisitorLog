<?php
/**
 * VisitorLog Registration
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of the VisitorLog_Registration class 
 * 
 * @method public static function instance()
 * @method public        function __construct()
 * @method public        function start registration()
 * @method public        function registration()
 */
class VisitorLog_Registration
{
    private static $instance = null;    

    public static function instance()
    {
        if ( null == self::$instance ) self::$instance = new self;
        return self::$instance;
    }

    public function __construct()
    {
        $this->start_registration();

    } // END __construct    

    public function start_registration()
    {
        global $visitorlog_users_resident_parameters;

        $url      = $visitorlog_users_resident_parameters['url'];
        $category = $visitorlog_users_resident_parameters['category'];
        $event    = $visitorlog_users_resident_parameters['event'];

        if ( '' != $event ) return;
        if ( is_404() )     return;
        if ( 'CRON'      == $category ||
             'WP'        == $category ||
             'BotLogin'  == $category ||
             'BotDdos'   == $category ||
             'BotDdosIP' == $category ||
             'Bot404'    == $category ) return;

        if ( false !== strpos( $url, 'wp-login.php' ) ||
             false !== strpos( $url, 'xmlrpc.php' )   ||
             false !== strpos( $url, 'wp-cron.php' )  ||
             false !== strpos( $url, 'favicon.ico' )  ||
             false !== strpos( $url, 'admin-ajax' )   ||
             false !== strpos( $url, 'assets' ) )  return;

        $this->registration();

    } // END func

	/**
	 * Registration of visitor
	 * 
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 * @uses VisitorLog_Logger::instance()->warning()
	 * @uses VisitorLog_Utility::get_option_sl()
	 * @return true/false
	 */
	public function registration()
	{
		global $visitorlog_users_resident_parameters;

        $event = $visitorlog_users_resident_parameters['event'];

        if ( '' != $event ) return;

		$timestamp    = $visitorlog_users_resident_parameters['timestamp']; 
		$timerelease  = $visitorlog_users_resident_parameters['timerelease'];
		$ip           = $visitorlog_users_resident_parameters['ip'];
		$canal        = $visitorlog_users_resident_parameters['canal'];
		$category     = $visitorlog_users_resident_parameters['category'];
		$user_agent   = $visitorlog_users_resident_parameters['username'];
		$url          = $visitorlog_users_resident_parameters['url'];
		$referer_info = $visitorlog_users_resident_parameters['refinfo'];
		
		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'users';

        $tstamp_session = $timestamp - 3600; // 60 minutes - session

        $query = "SELECT * FROM `$table_name` WHERE `user_ip` = %s";
        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM `$table_name` WHERE `user_ip` = %s", $ip));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'registration()-Error select into <users>: ' . VisitorLog_DB_Base::$wpdb_vl->last_error; 
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        } 

        $time_session = 0;
        $page_session = 0;
        $allpage = 0;
        $session = 0;

        if ( !empty($array) ) {

        	foreach ($array as $arr) {
    			$page_session = $arr->pages;
    			$session      = $arr->session;
    			$allpage      = $arr->allpages;

        		if ( $arr->timestamp > $tstamp_session ) {
        			$time_session = 1;
        		}
        	}
        } 
		$allpage++;

       	if ( 0 == $time_session ) {
       		$session++;
       		$page_session = 1;
       	} else {
	       	$page_session++;
       	}
       	$data = array(
			'timestamp'    => $timestamp,
			'timerelease'  => $timerelease,
			'user_ip'      => $ip,
			'pages'        => $page_session,
			'allpages'     => $allpage,
			'session'      => $session,
			'category'     => $category,
			'canal'        => $canal,
			'user_agent'   => $user_agent,
			'url'          => esc_html($url),
			'referer_info' => esc_html($referer_info)
       	);
		$result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$err = 'registration()-Error insert data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error ;
			VisitorLog_Logger::instance()->warning( $err );
			return false;
		}		

		$array  = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT COUNT(id) FROM $table_name WHERE `id` != %d", 0), ARRAY_A);
		$c_rows = $array[0]['COUNT(id)'];
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$err = 'registration()-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error ;
			VisitorLog_Logger::instance()->warning( $err );
			return false;
		}

		// cleanup  ip list
		$record_limit = VisitorLog_Utility::get_option_sl( '#_record_limit' );

		if ( '' == $record_limit || 0 == $record_limit || '' == $c_rows ) return true;

		if ( $c_rows > $record_limit ) {
			$delete = $c_rows - $record_limit;
			$result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name ORDER BY `id` ASC LIMIT %d", $delete));

			if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
				$err = 'registration()-Error delete data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error ;
				VisitorLog_Logger::instance()->warning( $err );
				return false;
			}
		}
		return true; 	

	} // END func


} // END class