<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Statistics class.
 *
 * @method	public static function get_class_name() 
 * @method	public static function init_menu() 
 * @method	public static function init_left_menu() 
 * @method	public static function render_hour() 
 */
class VisitorLog_Statistics
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Initiate Statistics subPage menu.
	 *
	 * @uses VisitorLog_Menu::is_disable_menu_item()
	 */
	public static function init_menu()
	{
		add_submenu_page(
			'visitorlog_tab',
			__( 'Statistics', 'visitorlog' ),
			__( 'Statistics', 'visitorlog' ),
			'read',
			'visitorlog_statistics',
			array( self::get_class_name(), 'render_for_current_day' )
		);
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'visitorlog_visitshour' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'For current day', 'visitorlog' ),
				__( 'For current day', 'visitorlog' ),
				'read',
				'visitorlog_visitshour',
				array( self::get_class_name(), 'render_for_current_day' )
			);
		}
		self::init_left_menu();
	}

	/**
	 * Initiates Server Information left menu.
	 *
	 * @uses  VisitorLog_Menu::add_left_menu()
	 * @uses  VisitorLog_Menu::is_disable_menu_item()
	 */
	public static function init_left_menu()
	{
		global $visitorlog_global_parameters;

		$menu_active_slugs = $visitorlog_global_parameters['menu_active_slugs'];
		$menu_active_slugs['statistics'] = 'visitorlog_statistics';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Statistics', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_statistics',
				'href'       => 'admin.php?page=visitorlog_statistics&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		$init_sub_subleftmenu = array(
			array(
				'title'      => __( 'For current day', 'visitorlog' ),
				'parent_key' => 'visitorlog_statistics',
				'href'       => 'admin.php?page=visitorlog_visitshour&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_visitshour',
				'right'      => '',
			)
		);

		foreach ( $init_sub_subleftmenu as $item ) {
			if ( VisitorLog_Menu::is_disable_menu_item( 3, $item['slug'] ) ) {
				continue;
			}
			VisitorLog_Menu::add_left_menu( $item, 2 );
		}

	} // END func

	/**
	 * Render for current day. 
	 *
	 * @uses VisitorLog_UI::render_top_header()
	 * @uses VisitorLog_View::render_preloader()
	 * @uses VisitorLog_Statistics_Hours::Render_visitors_report_hours()
	 */
	public static function render_for_current_day()
	{
		$params = array( 'title' => __( 'For current day', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
		?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<?php VisitorLog_View::render_preloader();?>
						<section class="content home" >
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row" >
							            <div class="col-lg-7 col-md-6 col-sm-12" >
							                <p style="font-size:18px;">
								                <em>
								<?php esc_html_e('Report on the current day\'s visits', 'visitorlog');?>
								                </em>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Statistics_Hours::render_visitors_report_hours();?>
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