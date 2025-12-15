<?php
/**
 * VisitorLog System.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_System class.
 *
 * @method	public static function class_name()
 * @method	public static function instance()
 * @method	public        function __construct() 
 * @method	public static function run()
 * @method	public static function menu_notitle()
 * @method	public static function execute_maintenance()
 * @method	public static function allow_specific_html_tags()
 * @method	public static function admin_body_class()
 * @method	public static function admin_print_styles()
 * @method	public static function loggedin_time()
 * @method	public static function plugin_description()
 * @method	public static function is_current_pages()
 * @method	public static function main()
 */
class VisitorLog_System
{
	private static  $instance = null;

	public static function class_name()
	{
		return __CLASS__;

	} // END func

	public static function instance()
	{
		if ( null == self::$instance ) 	self::$instance = new self();
		return self::$instance;
		
	} // END func

	public function __construct()
	{
		/* null */
	}

	public static function run()
	{
		$enqueue = VisitorLog_Enqueue_Script::get_class_name();
		add_action( 'admin_enqueue_scripts', array($enqueue, 'base_enqueue_styles') );

		new VisitorLog_DB_Base();

		add_action( 'admin_body_class',   array(self::class_name(), 'admin_body_class'), 10, 1 );
		add_action( 'admin_print_styles', array(self::class_name(), 'admin_print_styles') );

		if ( false !== get_option('visitorlog_run_quick_setup', false) ) {

			add_action( 'admin_enqueue_scripts', array($enqueue, 'setup_enqueue_styles') );
			add_action( 'admin_enqueue_scripts', array($enqueue, 'setup_enqueue_scripts') );
			new VisitorLog_Menu();
			return;
		}
		if ( false === VisitorLog_DB_Base::checking_table() ) {
			update_option('visitorlog_run_quick_setup', 'system_check');
			add_action( 'admin_enqueue_scripts', array($enqueue, 'setup_enqueue_styles') );
			add_action( 'admin_enqueue_scripts', array($enqueue, 'setup_enqueue_scripts') );
			new VisitorLog_Menu();
			return;			
		}
		if ( true === VisitorLog_Utility::check_update_db_version() ) {
			update_option('visitorlog_run_quick_setup', 'update');
			add_action( 'admin_enqueue_scripts', array($enqueue, 'setup_enqueue_styles') );
			add_action( 'admin_enqueue_scripts', array($enqueue, 'setup_enqueue_scripts') );
			new VisitorLog_Menu();
			return;
		}

		$upload_dir = wp_upload_dir();
		if ( ! defined( 'VISITORLOG_UPLOADS_DIR' ) ) {
			define( 'VISITORLOG_UPLOADS_DIR', $upload_dir['basedir'] . '/' . VISITORLOG_DIR );
		}
		if ( ! defined( 'VISITORLOG_BACKUPS_DIR' ) ) {
			define( 'VISITORLOG_BACKUPS_DIR', VISITORLOG_UPLOADS_DIR . '/' . VisitorLog_Utility::get_option_sl('backups') );
		}	
		if ( ! defined( 'VISITORLOG_BACKUPS_URL' ) ) {
			$backup_url = $upload_dir['baseurl'] . '/' . VISITORLOG_DIR . '/' . VisitorLog_Utility::get_option_sl('backups');
			define( 'VISITORLOG_BACKUPS_URL', $backup_url );
		}	
		if ( ! defined( 'VISITORLOG_HOME_DIR' ) ) {
			$home_dir = mb_substr($upload_dir['basedir'], 0, -18);
			define( 'VISITORLOG_HOME_DIR', $home_dir );
		}
		if ( ! defined( 'VISITORLOG_CONTENT_DIR' ) ) {
			$content_dir = mb_substr($upload_dir['basedir'], 0, -8);
			define( 'VISITORLOG_CONTENT_DIR', $content_dir );
		}	
		add_filter( 'wp_nav_menu',        array(self::class_name(), 'menu_notitle') );
		add_filter( 'wp_page_menu',       array(self::class_name(), 'menu_notitle') );
		add_filter( 'wp_list_categories', array(self::class_name(), 'menu_notitle') );
		add_filter( 'esc_html',           array(self::class_name(), 'allow_specific_html_tags'), 10, 2 );
		add_filter( 'all_plugins',        array(self::class_name(), 'plugin_description') );

		add_action( 'init', array(self::class_name(), 'execute_maintenance') );

		new VisitorLog_Menu();

		add_action( 'admin_enqueue_scripts', array($enqueue, 'admin_enqueue_styles') );
		add_action( 'admin_enqueue_scripts', array($enqueue, 'setup_enqueue_styles') );
		add_action( 'admin_enqueue_scripts', array($enqueue, 'admin_enqueue_scripts') );
        add_action( 'login_enqueue_scripts', array($enqueue, 'login_enqueue_scripts') );

		add_action( 'init', array(self::class_name(), 'main') );

	} // END func

	public static function menu_notitle( $menu )
	{
		return $menu = preg_replace('/ title=\"(.*?)\"/', '', $menu );

	} // END func

	public static function execute_maintenance()
	{
        if ( '' == VisitorLog_Utility::get_option_sl('on_maintenance') ) return;
		if ( is_admin() ) return;
		if ( VisitorLog_Utility::is_admin_ip() ) return; 

		VisitorLog_Maintenance::execute_maintenance();

	} // END func

	/**
	 * Function for `esc_html` filter-hook
	 */
	public static function allow_specific_html_tags( $safe_text, $text )
	{
		if ( '' == $text ) return $safe_text;

	    $allowed_tags = array('<b>', '</b>', '<i>', '</i>');
	    $safe_text = strip_tags($text, implode('', $allowed_tags));

	    return $safe_text;

	} // END func

	public static function admin_body_class( $class_string )
	{
		return VisitorLog_System_View::admin_body_class( $class_string );

	} // END func

	public static function admin_print_styles()
	{
		if ( ! self::is_current_pages() ) return;

		$URL  = VISITORLOG_PLUGIN_URL . 'assets/';
		wp_enqueue_style( 'visitorlog-1', $URL.'css/visitorlog/styles1.css', array(), 1.0 );

	} // END func

	/**
	 * Translation of the plugin description.
	 */
	public static function plugin_description( $all_plugins )
	{
		if ( isset( $all_plugins['visitorlog/visitorlog.php'] ) ) {

			$msg  = '<strong style="font-size:14px;">' . __( 'Security management', 'visitorlog' ) . '.</strong>&nbsp;';
			$msg .= __( 'Anti DDoS, admin panel protection, database protection, file system protection, anti-spam', 'visitorlog' );
			$msg .= '.&nbsp;<strong style="font-size:14px;">' . __( 'Statistics', 'visitorlog' ) . '.</strong>&nbsp;';
			$msg .= __( 'Reports, tables, graphs, logs of your site', 'visitorlog' ) . '.&nbsp;';
			$msg .= '<strong style="font-size:14px;">' . __( 'A lot of what you need', 'visitorlog' ) . '.</strong>';

			$all_plugins['visitorlog/visitorlog.php']['Description'] = $msg;
		}
		return $all_plugins;

	} // END func

	/**
	 * Get the current page and check it for "visitorlog_".
	 * @return boolean ture|false.
	 */
	public static function is_current_pages()
	{
		$screen = get_current_screen();

		if ( ( $screen && strpos( $screen->base, 'visitorlog' )     !== false ) ||
			 ( $screen && strpos( $screen->base, 'visitorlog_tab' ) !== false ) ) {
			
			return true;
		}
		return false;

	} // END func

	/**
	 * Function for `init` filter-hook
	 * Main
	 */
	public static function main()
	{
		new VisitorLog_Logger();
		new VisitorLog_Init();
        new VisitorLog_Detection();
        new VisitorLog_Bot_Detection();
        new VisitorLog_Anti_Ddos();
		new VisitorLog_Blocking();
		new VisitorLog_Security_Init_Tasks();
		new VisitorLog_Registration();
		new VisitorLog_Statistics_Utility();
		new VisitorLog_System_Cron_Jobs();
		VisitorLog_Hide_Notifications::hide_notifications();

	} // END func


} // END class