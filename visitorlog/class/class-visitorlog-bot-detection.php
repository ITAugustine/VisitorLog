<?php
/**
 * VisitorLog Bot Detection
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of the VisitorLog_Bot_Detection class 
 * 
 * @method public function __construct()
 * @method public function bot_detection()
 * @method public function handler_bot_detection()
 */

class VisitorLog_Bot_Detection
{
	public function __construct()
	{
		global $visitorlog_users_resident_parameters;

		$category = $this->bot_detection();
		$visitorlog_users_resident_parameters['category'] = $category;

	} // END  __construct()

	/**
	 * Detection of bot
	 *
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 * @uses VisitorLog_Logger::instance()->debug()
	 */
	public function bot_detection()
	{

		global $visitorlog_users_resident_parameters;

		$url        = $visitorlog_users_resident_parameters['url'];
		$canal      = $visitorlog_users_resident_parameters['canal'];
		$user_agent = $visitorlog_users_resident_parameters['username'];

        $symbols = array(
            array('item'=>'admin-'.'ajax'),
            array('item'=>'WordPress'),
            array('item'=>'wp-'.'cron'),
            array('item'=>'favicon.ico'),
            array('item'=>'assets'),
        );
		if ( VisitorLog_Utility::is_admin_ip() ) return 'Admin';

        foreach ( $symbols as $item ) {
        	if ( false !== strpos($url, $item['item']) ) return 'WP';
        }
        if ( false !== strpos($user_agent, 'WordPress') ) return 'WP';

        if ( defined('DOING_CRON') ) return 'CRON';        
        if ( defined('WP_CLI') )     return 'WP_CLI';

		if ( 'auth' == $canal )                            return 'BotLogin';
		if ( false !== stripos($url, 'wp-login') )         return 'BotLogin';
		if ( false !== stripos($url, 'wp-comments-post') ) return 'botspamco';
		if ( false !== stripos($url, 'admin-post') )       return 'botspamcf';

        $bot = $this->handler_bot_detection();
        if ( '' != $bot ) return $bot;

        return 'User';

	} // END func

    /**
     * Bot detection
     *
	 * @uses VisitorLog_Utility::get_data_select()
	 *
     * @param  string $user_agent
     * @param  string $ip
     */
    public function handler_bot_detection()
    {

		global $visitorlog_users_resident_parameters;

		$ip         = $visitorlog_users_resident_parameters['ip'];
		$user_agent = $visitorlog_users_resident_parameters['username'];

        if ( ($user_agent == '' ) && ($ip == '' ) ) return ''; 

		$result = VisitorLog_Utility::get_data_select( 'bots_table', 'viewall', $n_rows, $err );
		if ( $err ) return '';

	    foreach ($result as $res) {
	        if ( false !== stripos($user_agent, $res->bot_name) ) {

	        	$visitorlog_users_resident_parameters['bot']      = $res->bot_name;
	        	$visitorlog_users_resident_parameters['botblock'] = $res->block;

	        	if ( '' != $res->short_name ) return $res->short_name;

	        	return $res->bot_name;
	        }	
	    }

        return '';
        
    } // END func


} // END class