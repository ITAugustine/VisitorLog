<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Other_Features class
 * 
 * @method public static function  get_class_name()
 * @method public static function  init_menu()
 * @method public static function  init_left_menu()  
 * @method public static function  render_maintenance()
 * @method public static function  render_hide_notifications()
 */
class VisitorLog_Other_Features
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Initiate Information subPage menu.
	 *
	 * @uses VisitorLog_Menu::is_disable_menu_item()
	 */
	public static function init_menu()
	{
		add_submenu_page(
			'visitorlog_tab',
			__( 'Other Features', 'visitorlog' ),
			__( 'Other Features', 'visitorlog' ),
			'read',
			'visitorlog_otherfeatures',
			array( self::get_class_name(), 'render_maintenance' ) 
		);
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'maintenance' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Maintenance', 'visitorlog' ),
				__( 'Maintenance', 'visitorlog' ),
				'read',
				'visitorlog_maintenance',
				array( self::get_class_name(), 'render_maintenance' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'hidenotifications' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Hide notifications', 'visitorlog' ),
				__( 'Hide notifications', 'visitorlog' ),
				'read',
				'visitorlog_hidenotifications',
				array( self::get_class_name(), 'render_hide_notifications' )
			);
		}
		self::init_left_menu();

	} // END func

	/**
	 * Initiates left menu.
	 *
	 * @uses VisitorLog_Menu::add_left_menu()
	 * @uses VisitorLog_Menu::is_disable_menu_item()
	 */
	public static function init_left_menu()
	{
		global $visitorlog_global_parameters;		

		$menu_active_slugs = $visitorlog_global_parameters['menu_active_slugs'];
		$menu_active_slugs['otherfeatures'] = 'visitorlog_otherfeatures';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Other Features', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_otherfeatures',
				'href'       => 'admin.php?page=visitorlog_otherfeatures&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		$init_sub_subleftmenu = array(
			array(
				'title'      => __( 'Maintenance', 'visitorlog' ),
				'parent_key' => 'visitorlog_otherfeatures',
				'href'       => 'admin.php?page=visitorlog_maintenance&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_maintenance',
				'right'      => '',
			),
			array(
				'title'      => __( 'Hide notifications', 'visitorlog' ),
				'parent_key' => 'visitorlog_otherfeatures',
				'href'       => 'admin.php?page=visitorlog_hidenotifications&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_hidenotifications',
				'right'      => '',
			),
		);
		foreach ( $init_sub_subleftmenu as $item ) {
			if ( VisitorLog_Menu::is_disable_menu_item( 3, $item['slug'] ) ) {
				continue;
			}
			VisitorLog_Menu::add_left_menu( $item, 2 );
		}

	} // END func

	/**
	 * Render Maintenance Page.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Maintenance::render_maintenance_content()
	 */
	public static function render_maintenance()
	{
		$params = array( 'title' => __( 'Maintenance', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
		?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
						    <div class="block-header" style="margin-top:-15px;">
						        <div class="row" >
						            <div class="col-lg-7 col-md-6 col-sm-12" >
						                <h2><em><?php esc_html_e('Maintenance', 'visitorlog');?></em></h2>
						            </div>
						        </div>
						    </div>
						    <div class="container-fluid" >
						        <?php VisitorLog_Maintenance::render_maintenance_content();?>
						    </div>
						</section>
		            </body>
				</div>
			</div>
		</div>
        <?php

	} // END func

	/**
	 * Render Hide notifications from WordPress.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Hide_Notifications::render_hide_notifications()
	 */
	public static function render_hide_notifications()
	{
		$params = array( 'title' => __( 'Hide notifications', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
		?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
						    <div class="block-header" style="margin-top:-15px;">
						        <div class="row" >
						            <div class="col-lg-7 col-md-6 col-sm-12" >
						                <h2><em><?php esc_html_e('Hiding notifications of new WordPress version in admin panel', 'visitorlog');?></em></h2>
						            </div>
						        </div>
						    </div>
						    <div class="container-fluid" >
						        <?php VisitorLog_Hide_Notifications::render_hide_notifications();?>
						    </div>
						</section>
		            </body>
				</div>
			</div>
		</div>
        <?php

	} // END func


} // END class