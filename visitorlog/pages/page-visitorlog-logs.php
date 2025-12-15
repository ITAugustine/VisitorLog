<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Logs class
 * 
 * @method public static function  get_class_name()
 * @method public static function  init_menu()
 * @method public static function  init_left_menu()  
 * @method public static function  visitor_log()
 * @method public static function  render_account_activity_log()
 * @method public static function  render_action_log()
 * @method public static function  render_error_log()
 */
class VisitorLog_Logs
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
	 */
	public static function init_menu()
	{
		add_submenu_page(
			'visitorlog_tab',
			__( 'Logs', 'visitorlog' ),
			__( 'Logs', 'visitorlog' ),
			'read',
			'visitorlog_logs',
			array( self::get_class_name(), 'visitor_log' )
		);
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'visitorlog' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'VisitorLog', 'visitorlog' ),
				__( 'VisitorLog', 'visitorlog' ),
				'read',
				'visitorlog_visitorlog',
				array( self::get_class_name(), 'visitor_log' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'administrators' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Administrators', 'visitorlog' ),
				__( 'Administrators', 'visitorlog' ),
				'read',
				'visitorlog_administrators',
				array( self::get_class_name(), 'render_administrators' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'accountactivitylog' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Account activity log', 'visitorlog' ),
				__( 'Account activity log', 'visitorlog' ),
				'read',
				'visitorlog_accountactivity',
				array( self::get_class_name(), 'render_account_activity_log' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'actionlog' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Action Log', 'visitorlog' ),
				__( 'Action Log', 'visitorlog' ),
				'read',
				'visitorlog_actionlog',
				array( self::get_class_name(), 'render_action_log' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'errorlog' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Error Log', 'visitorlog' ),
				__( 'Error Log', 'visitorlog' ),
				'read',
				'visitorlog_errorlog',
				array( self::get_class_name(), 'render_error_log' )
			);
		}

		self::init_left_menu();

	} // END of init_menu()

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
		$menu_active_slugs['logs'] = 'visitorlog_logs';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Logs', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_logs',
				'href'       => 'admin.php?page=visitorlog_logs&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		$init_sub_subleftmenu = array(
			array(
				'title'      => __( 'Visitor Log', 'visitorlog' ),
				'parent_key' => 'visitorlog_logs',
				'href'       => 'admin.php?page=visitorlog_visitorlog&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_visitorlog',
				'right'      => '',
			),
			array(
				'title'      => __( 'Administrators', 'visitorlog' ),
				'parent_key' => 'visitorlog_logs',
				'href'       => 'admin.php?page=visitorlog_administrators&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_administrators',
				'right'      => '',
			),
			array(
				'title'      => __( 'Account activity log', 'visitorlog' ),
				'parent_key' => 'visitorlog_logs',
				'href'       => 'admin.php?page=visitorlog_accountactivity&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_accountactivity',
				'right'      => '',
			),
			array(
				'title'      => __( 'Action Log', 'visitorlog' ),
				'parent_key' => 'visitorlog_logs',
				'href'       => 'admin.php?page=visitorlog_actionlog&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_actionlog',
				'right'      => '',
			),
			array(
				'title'      => __( 'Error Log', 'visitorlog' ),
				'parent_key' => 'visitorlog_logs',
				'href'       => 'admin.php?page=visitorlog_errorlog&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_errorlog',
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
	 * Visitor Log.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Visitor_Log::Render_Visitor_Log()
	 */
	public static function visitor_log()
	{
		$params = array( 'title' => __('Visitor Log', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<?php VisitorLog_View::render_preloader();?>
						<section class="content home" >
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row">
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<p style="font-size:18px;">
							                	<em>
							                		<?php esc_html_e('Visitor Log', 'visitorlog');?>
							                	</em>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Visitor_Log::render_visitor_log();?>
							    </div>
							</div>    
						</section>
					</body>
				</div>	
		    </div>
		</div>
		<?php

	} // END func

	/**
	 * Render Administrators Log.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Administrators::render_administrators()
	 */
	public static function render_administrators()
	{
		$params = array( 'title' => __('Administrators', 'visitorlog') );
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
							            <div class="col-lg-7 col-md-6 col-sm-12">
							                <p style="font-size:18px;">
							                	<em>
							                		<?php esc_html_e('Administrators', 'visitorlog');?>
							                	</em>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid">
							    	<?php VisitorLog_Administrators::render_administrators();?>
							    </div>
							</div>    
						</section>
					</body>
				</div>	
		    </div>
		</div>
		<?php

	} // END func

	/**
	 * Render Account Activity Log.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Account_Activity_Log::Render_account_activity_log()
	 */
	public static function render_account_activity_log()
	{
		$params = array( 'title' => __('Account activity log', 'visitorlog') );
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
							                	<?php esc_html_e('Account activity log', 'visitorlog');?>
							                	</em>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							  <?php VisitorLog_Account_Activity_Log::render_account_activity_log();?>
							    </div>
							</div>    
						</section>
					</body>
				</div>	
		    </div>
		</div>
		<?php

	} // END func


	/**
	 * Renders action log page.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Action_Log::action_log_view()
	 */
	public static function render_action_log()
	{
		$params = array( 'title' => __('Action Log', 'visitorlog') );
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
								                	<?php esc_html_e('Action Log', 'visitorlog');?>
							                	</em>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Action_Log::action_log_view();?>
							    </div>
							</div>    
						</section>
					</body>
				</div>	
		    </div>
		</div>
		<?php	
		
	} // END func

	/**
	 * Renders error log page.
	 *
     * @uses VisitorLog_UI::render_top_header()
	 * @uses VisitorLog_Error_Log::error_log_view()
	 */
	public static function render_error_log()
	{
		$params = array( 'title' => __('Error Log', 'visitorlog') );
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
								                	<?php esc_html_e('Error Log', 'visitorlog');?>
							                	</em>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Error_Log::error_log_view();?>
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