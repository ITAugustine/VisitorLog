<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Init class.
 *
 * @method public    function __construct()
 * @method protected function init()
 * @method public    function loggedin_time()
 */
class VisitorLog_Init
{
	public function __construct()
	{
		$this->init();
		add_filter( 'auth_cookie_expiration', array( &$this, 'loggedin_time' ), 10, 3 );
	}	

	/**
	 * Instantiate Plugin.
	 */
	protected function init()
	{
		/* disabled menu items array */
		global $visitorlog_global_parameters;

		$visitorlog_global_parameters['disable_menus_items'] = 
			apply_filters( 
				'visitorlog_disablemenuitems', 
				$visitorlog_global_parameters['disable_menus_items'] 
			);

		/**
		 * Filter: visitorlog_main_menu_disable_menu_items
		 * Filters disabled navigation items.
		 */
		$visitorlog_global_parameters['disable_menus_items'] = 
			apply_filters( 
				'visitorlog_main_menu_disable_menu_items', 
				$visitorlog_global_parameters['disable_menus_items'] 
			);

	} // END func

	/**
	 * Function for `auth_cookie_expiration` filter-hook.
	 * Logged in time.
	 * 
	 * @param int  $expirein  Duration of the expiration period in seconds.
	 * @param int  $user_id   User ID.
	 * @param bool $remember  Whether to remember the user login.
	 *
	 * @uses VisitorLog_Utility::get_option_sl()
	 *
	 * @return int
	 */
	public function loggedin_time( $expirein, $user_id, $remember )
	{
		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_forced_logout' ) &&
			 'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' )
		   )
		{
			$expirein = (int)VisitorLog_Utility::get_option_sl( 'logout_time_period' );
		} else {
			$expirein = HOUR_IN_SECONDS;
		}
	    return $expirein; 

	} // END func


} // END class