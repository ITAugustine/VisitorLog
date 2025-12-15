<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security class
 * 
 * @method public    static function get_class_name()
 * @method public    static function init_menu()
 * @method protected static function init_left_menu()  
 * @method public    static function control_panel()
 * @method public    static function log_blocking_visitor()
 * @method public    static function admin_panel_protection()
 * @method public    static function render_db_protection()
 * @method public    static function render_file_system_protection()
 * @method public    static function render_anti_spam()
 * @method public    static function render_firewall()
 */
class VisitorLog_Security
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
			__( 'Security', 'visitorlog' ),
			__( 'Security', 'visitorlog' ),
			'read',
			'visitorlog_security',
			array( self::get_class_name(), 'control_panel' )
		);
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'controlpanel' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Control panel', 'visitorlog' ),
				__( 'Control panel', 'visitorlog' ),
				'read',
				'visitorlog_controlpanel',
				array( self::get_class_name(), 'control_panel' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'visitorlog_blockvisitor' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Log & Block Visitors', 'visitorlog' ),
				__( 'Log & Block Visitors', 'visitorlog' ),
				'read',
				'visitorlog_blockvisitor',
				array( self::get_class_name(), 'log_blocking_visitor' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'adminpanel' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Admin panel protection', 'visitorlog' ),
				__( 'Admin panel protection', 'visitorlog' ),
				'read',
				'visitorlog_adminpanel',
				array( self::get_class_name(), 'admin_panel_protection' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'dbprotection' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'DataBase protection', 'visitorlog' ),
				__( 'DataBase protection', 'visitorlog' ),
				'read',
				'visitorlog_dbprotection',
				array( self::get_class_name(), 'render_db_protection' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'fileprotection' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'File system protection', 'visitorlog' ),
				__( 'File system protection', 'visitorlog' ),
				'read',
				'visitorlog_fileprotection',
				array( self::get_class_name(), 'render_file_system_protection' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'spamprotection' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Spam Protection', 'visitorlog' ),
				__( 'Spam Protection', 'visitorlog' ),
				'read',
				'visitorlog_spamprotection',
				array( self::get_class_name(), 'render_anti_spam' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'firewall' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Firewall', 'visitorlog' ),
				__( 'Firewall', 'visitorlog' ),
				'read',
				'visitorlog_firewall',
				array( self::get_class_name(), 'render_firewall' )
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
	protected static function init_left_menu()
	{
		global $visitorlog_global_parameters;		

		if ( isset($visitorlog_global_parameters['menu_active_slugs']) ) {
			$menu_active_slugs = $visitorlog_global_parameters['menu_active_slugs'];
		} 
		$menu_active_slugs['security'] = 'visitorlog_security';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Security', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_security',
				'href'       => 'admin.php?page=visitorlog_security&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		$init_sub_subleftmenu = array(
			array(
				'title'      => __( 'Control panel', 'visitorlog' ),
				'parent_key' => 'visitorlog_security',
				'href'       => 'admin.php?page=visitorlog_controlpanel&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_controlpanel',
				'right'      => '',
			),
			array(
				'title'      => __( 'Log & Block Visitors', 'visitorlog' ),
				'parent_key' => 'visitorlog_security',
				'href'       => 'admin.php?page=visitorlog_blockvisitor&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_blockvisitor',
				'right'      => '',
			),
			array(
				'title'      => __( 'Admin panel protection', 'visitorlog' ),
				'parent_key' => 'visitorlog_security',
				'href'       => 'admin.php?page=visitorlog_adminpanel&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_adminpanel',
				'right'      => '',
			),
			array(
				'title'      => __( 'DataBase protection', 'visitorlog' ),
				'parent_key' => 'visitorlog_security',
				'href'       => 'admin.php?page=visitorlog_dbprotection&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_dbprotection',
				'right'      => '',
			),
			array(
				'title'      => __( 'File system protection', 'visitorlog' ),
				'parent_key' => 'visitorlog_security',
				'href'       => 'admin.php?page=visitorlog_fileprotection&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_fileprotection',
				'right'      => '',
			),
			array(
				'title'      => __( 'Spam Protection', 'visitorlog' ),
				'parent_key' => 'visitorlog_security',
				'href'       => 'admin.php?page=visitorlog_spamprotection&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_spamprotection',
				'right'      => '',
			),
			array(
				'title'      => __( 'Firewall', 'visitorlog' ),
				'parent_key' => 'visitorlog_security',
				'href'       => 'admin.php?page=visitorlog_firewall&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_firewall',
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
	 * Control panel.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Security_Controlpanel::control_panel_security()
	 */
	public static function control_panel()
	{
		$params = array( 'title' => __('Control panel', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home">
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row" >
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<p style="font-size:18px;">
												<?php esc_html_e('Control panel', 'visitorlog');?> 
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid">
						    		<?php VisitorLog_Security_Controlpanel::control_panel_security();?>
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
	 * Blacklist. 
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Security_Log_Blocking_Visitor_View::log_blocking_visitor()
	 */
	public static function log_blocking_visitor()
	{
		$params = array( 'title' => __('Log & Blocking of Visitors', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home">
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row" >
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<p style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	esc_html_e('Log & Blocking of Visitors', 'visitorlog');
} 
?>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid">
							    	<?php VisitorLog_Security_Log_Blocking_Visitor::log_blocking_visitor();?>
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
	 * Admin panel protection.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Security_Adminpanel_View::admin_panel_protection()
	 */
	public static function admin_panel_protection()
	{
		$params = array( 'title' => __('Admin panel protection', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row" >
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<p style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	esc_html_e('Admin panel protection', 'visitorlog');
} 
?>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
						    		<?php VisitorLog_Security_Adminpanel::admin_panel_protection();?>
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
	 * Render database protection.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Security_DB::database_protection()
	 */
	public static function render_db_protection()
	{
		$params = array( 'title' => __('DataBase protection', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row" >
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<p style="font-size:18px;">
												<?php esc_html_e('DataBase protection', 'visitorlog');?> 
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
						    		<?php VisitorLog_Security_DB::database_protection();?>
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
	 * Renders file system protection.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Security_FSP_View::file_system_protection()
	 */
	public static function render_file_system_protection()
	{
		$params = array( 'title' => __('File system protection', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row" >
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<p style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	esc_html_e('File system protection', 'visitorlog');
} 
?>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
						    		<?php VisitorLog_Security_FSP::file_system_protection();?>
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
	 * Renders SPAM Protection.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Security_Anti_Spam_View::anti_spam()
	 */
	public static function render_anti_spam()
	{
		$params = array( 'title' => __('SPAM Protection', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row" >
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<p style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	esc_html_e('SPAM Protection', 'visitorlog');
} 
?>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
						    		<?php VisitorLog_Security_Spam::anti_spam();?>
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
	 * Renders Firewall.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Security_Firewall::firewall()
	 */
	public static function render_firewall()
	{
		$params = array( 'title' => __('Firewall', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row" >
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<p style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	esc_html_e('Firewall Protection', 'visitorlog');
} 
?>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
						    		<?php VisitorLog_Security_Firewall::firewall();?>
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