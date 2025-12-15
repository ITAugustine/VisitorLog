<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Tables class
 * 
 * @method public static function  get_class_name()
 * @method public static function  init_menu()
 * @method public static function  init_left_menu()  
 * @method public static function  public static function locked_ips()
 * @method public static function  public static function render_spam()
 * @method public static function  public static function render_bots_table()
 */
class VisitorLog_Tables
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
			__( 'Tables', 'visitorlog' ),
			__( 'Tables', 'visitorlog' ),
			'read',
			'visitorlog_tables',
			array( self::get_class_name(), 'locked_ips' )
		);
		// Tables 
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'lockedaddr' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Blacklist Locked IPs', 'visitorlog' ),
				__( 'Blacklist Locked IPs', 'visitorlog' ),
				'read',
				'visitorlog_lockedaddr',
				array( self::get_class_name(), 'locked_ips' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'failedlogins' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Failed Logins', 'visitorlog' ),
				__( 'Failed Logins', 'visitorlog' ),
				'read',
				'visitorlog_failedlogins',
				array( self::get_class_name(), 'Failed_Logins' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'spam' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'spam', 'visitorlog' ),
				__( 'spam', 'visitorlog' ),
				'read',
				'visitorlog_spam',
				array( self::get_class_name(), 'render_spam' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'event404' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Event 404', 'visitorlog' ),
				__( 'Event 404', 'visitorlog' ),
				'read',
				'visitorlog_event404',
				array( self::get_class_name(), 'render_event404' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'botstable' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Table of Bots', 'visitorlog' ),
				__( 'Table of Bots', 'visitorlog' ),
				'read',
				'visitorlog_botstable',
				array( self::get_class_name(), 'render_bots_table' )
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
		$menu_active_slugs['tables'] = 'visitorlog_tables';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Tables', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_tables',
				'href'       => 'admin.php?page=visitorlog_tables&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		$init_sub_subleftmenu = array(
			array(
				'title'      => __( 'Blacklist Locked IPs', 'visitorlog' ),
				'parent_key' => 'visitorlog_tables',
				'href'       => 'admin.php?page=visitorlog_lockedaddr&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_lockedaddr',
				'right'      => '',
			),
			array(
				'title'      => __( 'Failed Logins', 'visitorlog' ),
				'parent_key' => 'visitorlog_tables',
				'href'       => 'admin.php?page=visitorlog_failedlogins&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_failedlogins',
				'right'      => '',
			),
			array(
				'title'      => __( 'Spam', 'visitorlog' ),
				'parent_key' => 'visitorlog_tables',
				'href'       => 'admin.php?page=visitorlog_spam&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_spam',
				'right'      => '',
			),
			array(
				'title'      => __( 'Event 404', 'visitorlog' ),
				'parent_key' => 'visitorlog_tables',
				'href'       => 'admin.php?page=visitorlog_event404&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_event404',
				'right'      => '',
			),
			array(
				'title'      => __( 'Table of Bots', 'visitorlog' ),
				'parent_key' => 'visitorlog_tables',
				'href'       => 'admin.php?page=visitorlog_botstable&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_botstable',
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
	 * Renders Locked IP addresses. Blacklist
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Locked_IP::render_locked_ip_addresses()
	 */
	public static function locked_ips()
	{
		$params = array( 'title' => __( 'Blacklist', 'visitorlog' ) );
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
						                	<div style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	esc_html_e('Registration and blocking of IP addresses', 'visitorlog');
} 
?>
							            	</div>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Locked_IP::render_locked_ip_addresses();?>
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
	 * Renders Failed Logins & 404.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Failed_Logins::render_failed_logins()
	 */
	public static function failed_logins()
	{
		$params = array( 'title' => __( 'Failed Logins', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home">
					    	<div class="vl-format">
							    <div class="block-header">
							        <div class="row">
							            <div class="col-lg-7 col-md-6 col-sm-12" >
						                	<div style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	VisitorLog_View::render_preloader();
	esc_html_e('Failed Logins', 'visitorlog');
} 
?>
							            	</div>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Failed_Logins::render_failed_logins();?>
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
	 * Renders Spam.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Spam::render_spam()
	 */
	public static function render_spam()
	{
		$params = array( 'title' => __( 'Spam', 'visitorlog' ) );
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
							                <div style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	VisitorLog_View::render_preloader();
	esc_html_e('A table with data on spammers on the site', 'visitorlog');
} 
?>
							            	</div>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Spam::render_spam();?>
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
	 * Renders event 404.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_404::render_404()
	 */
	public static function render_event404()
	{
		$params = array( 'title' => __( 'Event 404', 'visitorlog' ) );
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
							                <div style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	VisitorLog_View::render_preloader();
	esc_html_e('Event 404', 'visitorlog');
} 
?>
							            	</div>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_404::render_404();?>
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
	 * Renders table of bots.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Bots_Table::render_bots_table()
	 */
	public static function render_bots_table()
	{
		$params = array( 'title' => __('Table of Bots', 'visitorlog') );
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
							                <div style="font-size:18px;">
<?php
if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
    if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
        VisitorLog_System_View::show_message( 'orange', $err, $err );
        wp_die();
    }
	esc_html_e('The system file .htaccess', 'visitorlog');
} else {
	esc_html_e('Table of Bots', 'visitorlog');
} 
?>
							            	</div>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Bots_Table::render_bots_table();?>
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