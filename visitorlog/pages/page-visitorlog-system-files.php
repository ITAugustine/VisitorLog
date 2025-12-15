<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_System_Files class.
 *
 * @method  public static function get_class_name() 
 * @method  public static function init_menu() 
 * @method  public static function init_left_menu() 
 * @method 	public static function render_wp_config() 
 * @method 	public static function render_htaccess() 
 */
class VisitorLog_System_Files
{
	public static $subPages;

	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Initiate Information subPage menu.
	 *
	 * @uses VisitorLog_Menu::is_disable_menu_item()
	 * @uses VisitorLog_Server_Information_Handler::is_apache_server_software()
	 */
	public static function init_menu()
	{
		add_submenu_page(
			'visitorlog_tab',							               // parent_slug 
			__( 'System Files', 'visitorlog' ),					       // <title>     
			__( 'System Files', 'visitorlog' ),                        // menu        
			'read',												       // User rights 
			'visitorlog_systemfiles',							       // slug        
			array( self::get_class_name(), 'render_wp_config' )	       // function    
		);

		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'wpconfig' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'WP-Config File', 'visitorlog' ),
				__( 'WP-Config File', 'visitorlog' ),
				'read',
				'visitorlog_wpconfig',
				array( self::get_class_name(), 'render_wp_config' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'htaccess' ) ) {
			if ( VisitorLog_Server_Information_Handler::is_apache_server_software() ) {
				add_submenu_page(
					'visitorlog_tab',
					__( 'htaccess File', 'visitorlog' ),
					__( 'htaccess File', 'visitorlog' ),
					'read',
					'visitorlog_htaccess',
					array( self::get_class_name(), 'render_htaccess' )
				);
			}
		}
		self::init_left_menu( self::$subPages );

	} // END func

	/**
	 * Initiates left menu.
	 *
	 * @uses VisitorLog_Menu::add_left_menu()
	 * @uses VisitorLog_Server_Information_Handler::is_apache_server_software()
	 * @uses VisitorLog_Menu::is_disable_menu_item()
	 */
	public static function init_left_menu( $subPages = array() )
	{
		global $visitorlog_global_parameters;

		if ( isset($visitorlog_global_parameters['menu_active_slugs']) ) {
			$menu_active_slugs = $visitorlog_global_parameters['menu_active_slugs'];
		} 
		$menu_active_slugs['systemfiles'] = 'visitorlog_systemfiles';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'System Files', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_systemfiles',
				'href'       => 'admin.php?page=visitorlog_systemfiles&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		$init_sub_subleftmenu = array(
			array(
				'title'      => __( 'WP-Config File', 'visitorlog' ),
				'parent_key' => 'visitorlog_systemfiles',
				'href'       => 'admin.php?page=visitorlog_wpconfig&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_wpconfig',
				'right'      => '',
			),
			array(
				'title'      => __( 'htaccess File', 'visitorlog' ),
				'parent_key' => 'visitorlog_systemfiles',
				'href'       => 'admin.php?page=visitorlog_htaccess&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_htaccess',
				'right'      => '',
			),
		);

		if ( ! VisitorLog_Server_Information_Handler::is_apache_server_software() ) {
			if ( 'htaccess' == $init_sub_subleftmenu[3]['slug'] ) {
				unset( $init_sub_subleftmenu[3] );
			}
		}

		foreach ( $init_sub_subleftmenu as $item ) {
			if ( VisitorLog_Menu::is_disable_menu_item( 3, $item['slug'] ) ) {
				continue;
			}
			VisitorLog_Menu::add_left_menu( $item, 2 );
		}

	} // END func

	/**
	 * Renders the wp comfig page.
	 *
	 * @uses VisitorLog_UI::render_top_header()
	 * @uses VisitorLog_View::render_preloader()
	 * @uses VisitorLog_System_Files_View::wp_config_view()
	 * @return void
	 */
	public static function render_wp_config()
	{
		$params = array( 'title' => __( 'WPConfig', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<?php VisitorLog_View::render_preloader();?>
						<section class="content home" >
						    <div class="block-header" style="margin-top:-15px;">
						        <div class="row" >
						            <div class="col-lg-7 col-md-6 col-sm-12" >
						                <h2><em>
						                		<?php esc_html_e('WP Config file', 'visitorlog');?>
						                	</em>
						            	</h2>
						            </div>
						        </div>
						    </div>
						    <div class="ui segment">
					    		<?php VisitorLog_System_Files_View::wp_config_view();?>
						    </div>
						</section>
					</body>
				</div>	
		    </div>
		</div>
		<?php

	} // END func

	/**
	 * Renders .htaccess File page.
	 *
	 * @uses VisitorLog_UI::render_top_header()
	 * @uses VisitorLog_View::render_preloader()
	 * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
	 * @return void
	 */
	public static function render_htaccess()
	{
		$params = array( 'title' => __( 'htaccess File', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
		?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<?php VisitorLog_View::render_preloader();?>
						<section class="content home" >
						    <div class="block-header" style="margin-top:-15px;">
						        <div class="row" >
						            <div class="col-lg-7 col-md-6 col-sm-12" >
						                <h2><em><?php esc_html_e('htaccess File', 'visitorlog');?></em></h2>
						            </div>
						        </div>
						    </div>
						    <div class="container-fluid" >
						        <?php VisitorLog_Security_Handler::instance()->handler_htaccess_view();?>
						    </div>
						</section>
		            </body>
				</div>
			</div>
		</div>
        <?php

	} // END func

} // END class