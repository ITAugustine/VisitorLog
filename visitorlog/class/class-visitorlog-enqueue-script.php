<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Enqueue_Script class.
 *
 * @method public static function get_class_name()
 * @method public static function admin_enqueue_scripts()
 * @method public static function base_enqueue_styles()
 * @method public static function admin_enqueue_styles()
 * @method public static function wp_enqueue_script_system()
 * @method public static function wp_enqueue_script_custom()
 * @method public static function login_enqueue_scripts()
 */
class VisitorLog_Enqueue_Script
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Enqueue all  Admin Scripts.
	 *
	 * @param mixed $hook Enqueue hook.
     * @uses VisitorLog_System::is_current_pages()
	 */
	public static function admin_enqueue_scripts( $hook )
	{
		if ( !VisitorLog_System::is_current_pages() ) return;

		self::wp_enqueue_script_system();
		self::wp_enqueue_script_custom();

	} // END func

	/**
	 * Load base enqueue styles 
	 */
	public static function base_enqueue_styles()
	{
		if ( !VisitorLog_System::is_current_pages() ) return;

		$URL  = VISITORLOG_PLUGIN_URL . 'assets/';
		wp_enqueue_style( 'visitorlog-base', $URL.'css/visitor_log.css', array(), 1.0 );
        
	} // END func	

	/**
	 * Enqueue all Admin Styles.
	 *
	 * @param mixed $hook Enqueue hook.
	 * @uses VisitorLog_System::is_current_pages()
	 */
	public static function admin_enqueue_styles( $hook )
	{
		if ( !VisitorLog_System::is_current_pages() ) return;

		$URL  = VISITORLOG_PLUGIN_URL . 'assets/';

		wp_enqueue_style( 'visitorlog-css01', $URL.'css/main.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-css02', $URL.'css/colorskins.css', array(), 1.0 );

        // MAP
		wp_enqueue_style( 'visitorlog-map01', $URL.'css/map/jquery-jvectormap.min.css', array(), '2.0.5' );

        // Bootstrap-sheets Css
		wp_enqueue_style( 'visitorlog-Btstp-01', $URL.'css/ui/bootstrap.min.css', array(), '5.3.8' );
		wp_enqueue_style( 'visitorlog-Btstp-03', $URL.'css/ui/dataTables.bootstrap.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-smorris',  $URL.'css/ui/morris.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-sheet-1',  $URL.'css/ui/sheet.css', array(), 1.0, true );

        // Bootstrap Select Css
		wp_enqueue_style( 'visitorlog-select-1', $URL.'css/ui/bootstrap-select.min.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-select-2', $URL.'css/ui/multi-select.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-select-3', $URL.'css/ui/bootstrap-tagsinput.css', array(), 1.0 );

		// Awesome
		wp_enqueue_style( 'visitorlog-vl-semantic', $URL.'css/vl-semantic.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-jquery-ui',   $URL.'css/jquery-ui.min.css', array(), '1.14.1' );

		// Tables
		wp_enqueue_style( 'visitorlog-tables-1',   $URL.'css/tables/datatables.min.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-colreorder', $URL.'css/colreorder/colReorder.semanticui.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-scroller',   $URL.'css/scroller/scroller.dataTables.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-calendar',   $URL.'css/calendar/calendar.css', array(), 1.0 );

        // Neumorphic
		wp_enqueue_style( 'visitorlog-morphic1', $URL.'css/neumorphic/font.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-morphic2', $URL.'css/neumorphic/material-icons.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-morphic3', $URL.'css/neumorphic/normalize.css', array(), 1.0 );
		wp_enqueue_style( 'visitorlog-morphic4', $URL.'css/neumorphic/style-morphic.css', array(), 1.0 );

        // Sweet-alert
        wp_enqueue_style( 'visitorlog-alert1', $URL.'js/sweet-alert/sweet-alert.css', array(), 1.0 );

	} // END of func

	/**
	 * Load wp_enqueue_script_system
	 */
	public static function wp_enqueue_script_system()
	{
		$URL = VISITORLOG_PLUGIN_URL . 'assets/';

        // Bundles
		wp_enqueue_script( 'visitorlog-bundles1', $URL.'js/bundles/libscripts.bundle-1.js', array(), 1.0, true );
        // Bootstrap DataTable
		wp_enqueue_script( 'visitorlog-table1', $URL.'js/datatable/jquery.dataTables.min.js', array(), 1.0, true );
		wp_enqueue_script( 'visitorlog-table2', $URL.'js/datatable/dataTables.bootstrap.min.js', array(), 1.0, true );

	} // END of func

	/**
	 * Load wp_enqueue_script_custom
	 */
	public static function wp_enqueue_script_custom()
	{
		$URL = VISITORLOG_PLUGIN_URL . 'assets/';

        //  Neumorphic
		wp_enqueue_script( 'visitorlog-morphic12', $URL.'js/neumorphic/functions1.js', array(), 1.0, true );
        // Bundles
		wp_enqueue_script( 'visitorlog-bundles3', $URL.'js/bundles/vendorscripts.bundle.js', array(), 1.0, true ); 
		wp_enqueue_script( 'visitorlog-bundles4', $URL.'js/bundles/morrisscripts.bundle.js', array(), 1.0, true );
		wp_enqueue_script( 'visitorlog-bundles5', $URL.'js/bundles/mainscripts.bundle.js', array(), 1.0, true );

		//  Chart Js
		$screen = get_current_screen();
		if ( ( $screen && strpos( $screen->base, 'visitorlog_statistics' ) !== false ) ||
			 ( $screen && strpos( $screen->base, 'visitorlog_visitshour' ) !== false ) ) {

			wp_enqueue_script( 'visitorlog-chart1', $URL.'js/chart/chart-hour.js', array(), 1.0, true );
			wp_enqueue_script( 'visitorlog-chart2', $URL.'js/chart/chart-hour-bot.js', array(), 1.0, true );
		}

        //  Jquery DataTable
		wp_enqueue_script( 'visitorlog-table1', $URL.'js/table/jquery-datatable.js', array(), 1.0, true );
        //  Bootstrap-sheets Js
		wp_enqueue_script( 'visitorlog-sheet1',  $URL.'js/ui/bootstrap.bundle.min.js', array(), '5.3.8', true );
        //  Bootstrap-form-elements Js
		wp_enqueue_script( 'visitorlog-elnt-1',   $URL.'js/ui/jquery.multi-select.min.js', array(), 1.0, true );
		//  sweet-alert
		wp_enqueue_script( 'visitorlog-alert-1',  $URL.'js/sweet-alert/sweetalert.js', array(), 1.0, true );
        //  Visitorlog
		wp_enqueue_script( 'visitorlog-header', $URL.'js/visitorlog-header.js', array(), 1.0, false );
		wp_enqueue_script( 'visitorlog-action', $URL.'js/visitorlog-action.js', array(), 1.0, true );

	} // END of func

	/**
	 * Load login enqueue scripts 
	 *
	 * @uses VisitorLog_Utility::get_option_sl()
	 */
	public static function login_enqueue_scripts()
	{
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_login_recaptcha' ) &&
        	 'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' )
    	   ) {
    	   	// Google asks you not to copy or paste this code.
            wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), 1.0, false );
            // below is needed to provide some space for the google reCaptcha form (otherwise it appears partially hidden on RHS)
            wp_add_inline_script( 'google-recaptcha', 'document.addEventListener("DOMContentLoaded", ()=>{document.getElementById("login").style.width = "340px";});' );
        }
        
	} // END func	

	/**
	 * Load setup enqueue styles 
	 */
	public static function setup_enqueue_styles()
	{
		$URL  = VISITORLOG_PLUGIN_URL . 'assets/';
		// Semantic-UI
		wp_enqueue_style( 'visitorlog-semantic', $URL.'js/semantic/semantic.min.css', array(), 1.0 );
        
	} // END func	

	/**
	 * Load setup enqueue scripts 
	 */
	public static function setup_enqueue_scripts()
	{
		$URL = VISITORLOG_PLUGIN_URL . 'assets/';
		//  semantic wizard
		wp_enqueue_script( 'visitorlog-semantic', $URL.'js/semantic/semantic.js', array('jquery'), '2.5.0', true );
        
	} // END func


} // END of class