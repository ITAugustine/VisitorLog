<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Overview class.
 *
 * @method  public static function get_class_name()
 * @method	public static function init_menu() 
 * @method	public static function init_left_menu() 
 * @method	public static function add_meta_boxes() 
 * @method	public static function on_show_page() 
 */
class VisitorLog_Overview
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Add Overview top level menu.
	 *
	 * @uses VisitorLog_System_Utility::is_admin()
	 */
	public static function init_menu()
	{
		if ( ! VisitorLog_System_Utility::is_admin() ) return;	

		add_menu_page(
			'VisitorLog',                                              /* title       */
			'VisitorLog',                                              /* menu        */   
			'read',                                                    /* User rights */
			'visitorlog_tab',                                          /* slug        */
			array( self::get_class_name(), 'on_show_page' ),           /* function    */
			VISITORLOG_PLUGIN_URL . 'assets/images/icons/sl-icon.png', /* icon_url    */
			'2.00001'                                                  /* position    */
		);

		add_submenu_page(
			'visitorlog_tab',                                /* parent_slug */
			__( 'Overview', 'visitorlog' ),                  /* title       */
			__( 'Overview', 'visitorlog' ),                  /* menu        */ 
			'read',                                          /* User rights */
			'visitorlog_tab',                                /* slug        */
			array( self::get_class_name(), 'on_show_page' )  /* function    */
		);

		self::init_left_menu();

	} // END func

	/**
	 * Instantiate the VisitorLog Overview Menu item.
	 *
	 * @uses VisitorLog_Menu::add_left_menu()
	 */
	public static function init_left_menu()
	{
		global $visitorlog_global_parameters;		

		if ( isset($visitorlog_global_parameters['menu_active_slugs']) ) {
			$menu_active_slugs = $visitorlog_global_parameters['menu_active_slugs'];
		} 
		$menu_active_slugs['security'] = 'visitorlog_tab';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Overview', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_tab',
				'href'       => 'admin.php?page=visitorlog_tab&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);

	} // END func

	public static function on_show_page()
	{
		$params = array( 'title' => __( 'Overview', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
		?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<?php VisitorLog_View::render_preloader();?>
						<section class="content home">
					    	<div class="vl-format">
							    <div class="container-fluid" >
							        <?php VisitorLog_Overview_View::render_server_info();?>
							    </div>
							</div>
						</section>
		            </body>
				</div>
			</div>
		</div>
        <?php

	} // END func

} // END class