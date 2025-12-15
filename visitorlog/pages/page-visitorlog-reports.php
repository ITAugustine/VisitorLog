<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Reports class
 * 
 * @method public static function  get_class_name()
 * @method public static function  init_menu()
 * @method public static function  init_left_menu()  
 * @method public static function  render_backup_report()
 * @method public static function  render_server_info()
 * @method public static function  render_file_permissions_report()
 * @method public static function  render_updates()
 */
class VisitorLog_Reports
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
			__( 'Reports', 'visitorlog' ),
			__( 'Reports', 'visitorlog' ),
			'read',
			'visitorlog_reports',
			array( self::get_class_name(), 'render_backup_report' )
		);
		// Reports
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'backupreport' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Backups Report', 'visitorlog' ),
				__( 'Backups Report', 'visitorlog' ),
				'read',
				'visitorlog_backupreport',
				array( self::get_class_name(), 'render_backup_report' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'serverinfo' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Server Info', 'visitorlog' ),
				__( 'Server Info', 'visitorlog' ),
				'read',
				'visitorlog_serverinfo',
				array( self::get_class_name(), 'render_server_info' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'visitorlog_filepermissions' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'File Permissions Report', 'visitorlog' ),
				__( 'File Permissions Report', 'visitorlog' ),
				'read',
				'visitorlog_filepermissions',
				array( self::get_class_name(), 'render_file_permissions_report' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'visitorlog_updates' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Updates', 'visitorlog' ),
				__( 'Updates', 'visitorlog' ),
				'read',
				'visitorlog_updates',
				array( self::get_class_name(), 'render_updates' )
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
		$menu_active_slugs['reports'] = 'visitorlog_reports';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Reports', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_reports',
				'href'       => 'admin.php?page=visitorlog_reports&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		$init_sub_subleftmenu = array(
			array(
				'title'      => __( 'Backups Report', 'visitorlog' ),
				'parent_key' => 'visitorlog_reports',
				'href'       => 'admin.php?page=visitorlog_backupreport&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_backupreport',
				'right'      => '',
			),
			array(
				'title'      => __( 'Server Info', 'visitorlog' ),
				'parent_key' => 'visitorlog_reports',
				'href'       => 'admin.php?page=visitorlog_serverinfo&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_serverinfo',
				'right'      => '',
			),
			array(
				'title'      => __( 'File Permissions Report', 'visitorlog' ),
				'parent_key' => 'visitorlog_reports',
				'href'       => 'admin.php?page=visitorlog_filepermissions&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_filepermissions',
				'right'      => '',
			),
			array(
				'title'      => __( 'Updates', 'visitorlog' ),
				'parent_key' => 'visitorlog_reports',
				'href'       => 'admin.php?page=visitorlog_updates&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_updates',
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
	 * Render Backup Report.
     *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_Backups_Report::render_backups_report()
	 */
	public static function render_backup_report()
	{
		$params = array( 'title' => __('Backups Report', 'visitorlog') );
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
								                	<?php esc_html_e('Backups Report', 'visitorlog');?>
							                	</em>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Backups_Report::render_backups_report();?>
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
	 * Render Files Permissions Report.
     *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_View::render_preloader()
     * @uses VisitorLog_File_Permissions::render_file_permissions_report()
	 */
	public static function render_file_permissions_report()
	{
		$params = array( 'title' => __('File Permissions Scan Report', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<?php VisitorLog_View::render_preloader();?>
						<section class="content home" >
						    <div class="block-header">
						        <div class="row" >
						            <div class="col-lg-7 col-md-6 col-sm-12" >
						                <p style="font-size:18px;">
						                	<em>
							                	<?php esc_html_e('File Permissions Scan Report', 'visitorlog');?>
						                	</em>
						            	</p>
						            </div>
						        </div>
						    </div>
						    <div class="container-fluid" >
						    	<?php VisitorLog_File_Permissions::render_file_permissions_report();?>
						    </div>
						</section>
					</body>
				</div>	
		    </div>
		</div>
		<?php	

	} // END func

	/**
	 * Render server info Report.
     *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_View::render_preloader()
     * @uses VisitorLog_Server_Info::render_server_info()
	 */
	public static function render_server_info()
	{
		$params = array( 'title' => __('Server Info', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<?php VisitorLog_View::render_preloader();?>
						<section class="content home" >
					    	<div class="vl-format">
							    <div class="block-header">
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Server_Info::render_server_info();?>
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
	 * Render updates Report.
     *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_View::render_preloader()
     * @uses VisitorLog_Updates::render_updates()
	 */
	public static function render_updates()
	{
		$params = array( 'title' => __('Updates', 'visitorlog') );
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
								                	<?php esc_html_e('Updates Report', 'visitorlog');?>
							                	</em>
							            	</p>
							            </div>
							        </div>
							    </div>
							    <div class="container-fluid" >
							    	<?php VisitorLog_Updates::render_updates();?>
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