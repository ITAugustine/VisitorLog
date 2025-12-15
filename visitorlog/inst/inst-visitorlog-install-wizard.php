<?php
/**
 *  Install Wizard
 *  Quick Setup Wizard enables you to quickly set basic plugin settings
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Install_Wizard class
 * 
 * @method public    static function class_name()
 * @method public    static function install_wizard()
 * @method public    static function admin_init()
 * @method protected static function get_next_step_link()
 * @method protected static function setup_wizard_steps()
 * @method public    static function view_system_check()        // Step 1
 * @method public    static function view_creating_tables()     // Step 2
 * @method public    static function view_media_library()       // Step 3
 * @method public    static function view_settings()            // Step 4
 * @method public    static function view_finish()              // Step 5
 * @method public    static function view_update()              // Step 1 update
 * @method public    static function view_finish_update()       // Step 2 update 
 */
class VisitorLog_Install_Wizard
{
	private static $step  = '';
	private static $steps = array();

	private static $count_tables_new = 16;
	private static $count_colomns_new = 139;

	public static function class_name()
	{
		return __CLASS__;
	} // END func

	public static function init_menu()
	{
		if ( ! VisitorLog_System_Utility::is_admin() ) return;	
		$step_setup = get_option('visitorlog_run_quick_setup', false);
		if ( false === $step_setup ) return;

		switch ($step_setup) {

			case 'system_check':
				$slug = 'visitorlog_setup_sysinfo';
				$parent_slug = 'visitorlog_setup';
				$menu = __( 'VisitorLog install', 'visitorlog' );
				break;
			case 'creating_tables':
				$slug = 'visitorlog_create_tables';
				$parent_slug = 'visitorlog_setup';
				$menu = __( 'Creating tables', 'visitorlog' );
				break;			
			case 'media_library':
				$slug = 'visitorlog_setup_medialib';
				$parent_slug = 'visitorlog_setup';
				$menu = __( 'Media library', 'visitorlog' );
				break;	
			case 'settings':
				$slug = 'visitorlog_setup_settings';
				$parent_slug = 'visitorlog_setup';
				$menu = __( 'Settings', 'visitorlog' );
				break;	
			case 'finish':
				$slug = 'visitorlog_setup_finish';
				$parent_slug = 'visitorlog_setup';
				$menu = __( 'Finish', 'visitorlog' );
				break;	
			case 'update':
				$slug = 'visitorlog_update';
				$parent_slug = 'visitorlog_update';
				$menu = __( 'VisitorLog Update', 'visitorlog' );
				break;	
			case 'update_finish':
				$slug = 'visitorlog_update_finish';
				$parent_slug = 'visitorlog_update';
				$menu = __( 'VisitorLog Update Finish', 'visitorlog' );
				break;	
			default:
				$slug = 'visitorlog_setup';
				$parent_slug = 'visitorlog_setup';
				$menu = __( 'VisitorLog Setup', 'visitorlog' );
				break;
		}
		add_menu_page(
			$menu,                                                        /* title       */
			$menu,                                                        /* menu        */
			'read',                                                       /* User rights */
			$slug,                                                        /* slug        */
			array( self::class_name(), 'admin_init' ),                    /* function    */
			VISITORLOG_PLUGIN_URL . 'assets/images/icons/sl-icon.png',    /* icon_url    */
			'2.00001'                                                     /* position    */
		);
		add_submenu_page(
			$parent_slug,                                    /* parent_slug */
			$menu,                                           /* title       */
			$menu,                                           /* menu        */ 
			'read',                                          /* User rights */
			$slug,                                           /* slug        */
			array( self::class_name(), 'admin_init' )        /* function    */
		);

	} // END func

	public static function admin_init()
	{
		$step_setup = get_option('visitorlog_run_quick_setup', false);

		if ( 'update'        != $step_setup &&
			 'update_finish' != $step_setup ) {

			self::$steps = array(
				'system_check' => array(
					'name'    => __( 'System information', 'visitorlog' ),
					'view'    => array( self::class_name(), 'view_system_check' ),
				),
				'creating_tables' => array(
					'name'    => __( 'Creating tables', 'visitorlog' ),
					'view'    => array( self::class_name(), 'view_creating_tables' ),
					'handler' => array( self::class_name(), 'handler_creating_tables' ),
				),
				'media_library' => array(
					'name'    => __( 'Creating a media library', 'visitorlog' ),
					'view'    => array( self::class_name(), 'view_media_library' ),
				),
				'settings' => array(
					'name'    => __( 'Settings', 'visitorlog' ),
					'view'    => array( self::class_name(), 'view_settings' ),
				),
				'finish' => array(
					'name'    => __( 'Finish', 'visitorlog' ),
					'view'    => array( self::class_name(), 'view_finish' ),
				),
			);
		} else {
			self::$steps = array(
				'update' => array(
					'name' => __( 'Update', 'visitorlog' ),
					'view' => array( self::class_name(), 'view_update' ),
				),
				'update_finish' => array(
					'name' => __( 'Update Finish', 'visitorlog' ),
					'view' => array( self::class_name(), 'view_finish_update' ),
				),
			);
		}	

       	self::$step = $step_setup;

		if ( ! empty( $_POST['visitorlog_handler_step'] ) && false !== $step_setup ) {

	        if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
	            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
	            wp_die();
	        }

			switch ($step_setup) {

				case 'system_check':
					VisitorLog_Install::instance()->install();
					update_option('visitorlog_run_quick_setup', 'creating_tables');
					VisitorLog_Utility::vl_redirect('visitorlog_create_tables');
					exit;
				case 'creating_tables':
					$err_tables  = get_option( 'visitorlog_err_tables' );
					$err_options = get_option( 'visitorlog_error_options' );
					$copy_images = 1;
					$copy_lang   = 1;
					if ( '' == $err_tables && '' == $err_options ) {

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

						$res_copy_images = VisitorLog_Utility_File::copy_images();
						if ( true !== $res_copy_images ) $copy_images = 0;

						update_option( 'visitorlog_copy_images', $copy_images );
						update_option( 'visitorlog_copy_lang', $copy_lang );
						update_option( 'visitorlog_run_quick_setup', 'media_library');

						VisitorLog_Utility::vl_redirect('visitorlog_setup_medialib');
						exit;
					}
					break;
				case 'media_library':
					update_option('visitorlog_run_quick_setup', 'settings');
					VisitorLog_Utility::vl_redirect('visitorlog_setup_settings');
					exit;
				case 'settings':
					update_option('visitorlog_run_quick_setup', 'finish');
					VisitorLog_Utility::vl_redirect('visitorlog_setup_finish');
					exit;
				case 'finish':
					VisitorLog_Event_Login::insert_data_into_table_admin_ip();
					VisitorLog_Event_Login::insert_data_into_table_login_activity();
					VisitorLog_Logger::instance()->log_update( 'activated' );

					delete_option('visitorlog_run_quick_setup');
					delete_option('visitorlog_err_tables');
					delete_option('visitorlog_error_options');
					delete_option('visitorlog_count_options');
					delete_option('visitorlog_count_tables');
					delete_option('visitorlog_copy_lang');
					delete_option('visitorlog_copy_images');

					VisitorLog_Utility::vl_redirect('visitorlog_tab');
					exit;
				case 'update':
					VisitorLog_Install::instance()->install('update');
					$err_tables  = get_option( 'visitorlog_err_tables' );
					$err_options = get_option( 'visitorlog_error_options' );
					$copy_images = 1;
					$copy_lang   = 1;

					$res_copy_images = VisitorLog_Utility_File::copy_images();
					if ( true !== $res_copy_images ) $copy_images = 0;

					update_option( 'visitorlog_copy_images', $copy_images );
					update_option( 'visitorlog_copy_lang', $copy_lang );
					update_option( 'visitorlog_run_quick_setup', 'update_finish');
					VisitorLog_Utility::vl_redirect('visitorlog_update_finish');
					exit;
				case 'update_finish':
					VisitorLog_Event_Login::insert_data_into_table_admin_ip();
					VisitorLog_Event_Login::insert_data_into_table_login_activity();
					VisitorLog_Logger::instance()->log_update( 'update' );

					delete_option('visitorlog_run_quick_setup');
					delete_option('visitorlog_err_tables');
					delete_option('visitorlog_error_options');
					delete_option('visitorlog_count_options');
					delete_option('visitorlog_count_tables');
					delete_option('visitorlog_copy_lang');
					delete_option('visitorlog_copy_images');
					VisitorLog_Utility::vl_redirect('visitorlog_tab');
					exit;
				default:
					break;
			}
		}
		ob_start();
		?>
		<!DOCTYPE html>
			<html <?php language_attributes();?> class="visitorlog-quick-setup">
			<head>
				<meta name="viewport" content="width=device-width" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title><?php esc_html_e( 'Install Wizard', 'visitorlog' );?></title>
			</head>
			<body>
				<div class="ui hidden divider"></div>
				<div class="ui hidden divider"></div>
				<div class="ui padded segment" style="margin-left:40px;margin-right:40px;">
					<?php self::setup_wizard_steps();?>
					<div style="float:right; width:72%;">
					<?php call_user_func( self::$steps[self::$step]['view'] );?>
					</div>
					<div class="ui clearing hidden divider"></div>
				</div>
				<div class="ui grid">
					<div class="row">
						<div class="left aligned column">
							<a style="font-size:small; font-weight:bolder; padding-left:40px;" href="<?php echo esc_url(VisitorLog_Utility::$home_site);?>" target="_blank">
								<?php echo esc_html(VisitorLog_Utility::$name_firm);?>
							</a>
						</div>
					</div>
				</div>
			</body>
		</html>
		<?php
		exit;

	} // END func

	public static function get_next_step_link( $step = '' )
	{
		if ( ! empty( $step ) && isset( $step, self::$steps ) ) {
			return esc_url_raw( remove_query_arg( 'noregister', add_query_arg( 'step', $step ) ) );
		}
		$keys = array_keys( self::$steps );
		return esc_url_raw( remove_query_arg( 'noregister', add_query_arg( 'step', $keys[ array_search( self::$step, array_keys( self::$steps ) ) + 1 ] ) ) );

	} // END func

	public static function setup_wizard_steps()
	{
		$ouput_steps = self::$steps;
		?>
		<div class="ui tablet stackable ordered vertical steps" style="float:left;margin-right:3em;">
			<?php foreach ( $ouput_steps as $step_key => $step ) :
				$param = 'step';
				if ( isset( $step['hidden'] ) && $step['hidden'] ) continue;
				if ( $step_key == self::$step ) $param = 'step active';
				elseif ( array_search( self::$step, array_keys( self::$steps ) ) > array_search( $step_key, array_keys( self::$steps ) ) ) $param = 'step completed';
				?>
				<div class="<?php echo esc_html( $param );?>"> 
					<div class="content">
						<div class="title"><?php echo esc_html( $step['name'] );?></div>
					</div>
				</div>
			<?php endforeach;?>
		</div>
		<?php

	} // END func

	/**
	 *  Render System Requirements Step 1 install.
	 *
	 *  @param  not
	 *  @uses   VisitorLog_Server_Information_Handler::get_class_name()
	 *  @uses   VisitorLog_Utility::get_server()
	 *  @uses   VisitorLog_Server_Information_Handler::get_server_ip()
	 *  @uses   VisitorLog_System_View::get_badge()
	 *  @uses   VisitorLog_Server_Information_Handler::get_os()
	 *  @uses   VisitorLog_Server_Information_Handler::get_server_name()
	 *  @uses   VisitorLog_Server_Information_Handler::get_server_protocol()
	 *  @uses   VisitorLog_Server_Information_Handler::get_architecture()
	 *  @return void
	 */
	public static function view_system_check()
	{
		global $wp_version;

		$class_name = VisitorLog_Server_Information_Handler::get_class_name();
		$currentPHPversion = call_user_func( array($class_name, 'get_php_version') );
		$SSLextension      = call_user_func( array($class_name, 'get_ssl_support') );
		$URLextension      = call_user_func( array($class_name, 'get_curl_support') );
		$currentSQLversion = call_user_func( array($class_name, 'get_mysql_version') );
		$newPlugversion    = call_user_func( array($class_name, 'get_new_version') );
		$newDBversion      = call_user_func( array($class_name, 'get_new_db_version') );		

		$compare_php_version  = 'Pass';
		$compare_SSLextension = 'Pass';
		$compare_URLextension = 'Pass';
		$compare_sql_version  = 'Pass';
		$compare_plug_version = 'Pass';
		$compare_db_version   = 'Pass';

		if ( $SSLextension ) $view_SSLextension = 'true';
		else            	 $view_SSLextension = 'false';

		if ( $URLextension ) $view_URLextension = 'true';
		else 				 $view_URLextension = 'false';

		if ( 'true' != $view_SSLextension ) $compare_SSLextension = 'Warning';	
		if ( 'true' != $view_URLextension ) $compare_URLextension = 'Warning';

		if ( version_compare( $currentPHPversion, '5.6' ) < 0 )  $compare_php_version  = 'Warning';
		if ( version_compare( $currentSQLversion, '5.0' ) < 0 )  $compare_sql_version  = 'Warning';
		if ( version_compare( $newPlugversion, '1.0.0' ) < 0 )   $compare_plug_version = 'Warning';
		if ( version_compare( $newDBversion, '1.0.1' ) < 0 )     $compare_db_version   = 'Warning';

        //------- Server info
        $note = '';
        VisitorLog_Utility::get_server( $server_software );
        $server_ip = VisitorLog_Server_Information_Handler::get_server_ip( true );
        if ( '127.' == substr($server_ip, 0, 4) ) $note = 'localhost';
		?>
		<h1 class="ui dividing header" style="color: #0073AA;">
			<?php esc_html_e( 'System Requirements Check', 'visitorlog' );?>
		</h1>
		<div style="margin-top:0px"></div>
		<form method="post" class="ui form">
		<?php wp_nonce_field( 'visitorlog_nonce' );?>
		<div class="ui message warning">
			<?php esc_html_e( 'If there was a warning, it is recommended to contact the support service of your host and update the server configuration to ensure optimal performance', 'visitorlog' );?>
		</div>
		<table class="ui tablet stackable single line table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Check', 'visitorlog' );?></th>
					<th><?php esc_html_e( 'Required Value', 'visitorlog' );?></th>
					<th><?php esc_html_e( 'Detected Value', 'visitorlog' );?></th>
					<th class="collapsing center aligned"><?php esc_html_e( 'Status', 'visitorlog' );?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'PHP Version', 'visitorlog' );?></td>
					<td><?php echo '>=5.6';?></td>
					<td><?php echo esc_html($currentPHPversion);?></td>
					<td><?php VisitorLog_System_View::get_badge($compare_php_version, 1);?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'SSL Extension Enabled', 'visitorlog' );?></td>
					<td><?php echo '=true';?></td>
					<td><?php echo esc_html( $view_SSLextension );?></td>
					<td><?php VisitorLog_System_View::get_badge($compare_SSLextension, 1);?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'cURL Extension Enabled', 'visitorlog' );?></td>
					<td><?php echo '=true';?></td>
					<td><?php echo esc_url( $view_URLextension );?></td>
					<td><?php VisitorLog_System_View::get_badge($compare_URLextension, 1);?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'MySQL Version', 'visitorlog' );?></td>
					<td><?php echo '>=5.0';?></td>
					<td><?php echo esc_html( $currentSQLversion );?></td>
					<td><?php VisitorLog_System_View::get_badge($compare_sql_version, 1);?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Plugin Version', 'visitorlog' );?></td>
					<td><?php echo '>=1.0.0';?></td>
					<td><?php echo esc_html( $newPlugversion ); ?></td>
					<td><?php VisitorLog_System_View::get_badge($compare_plug_version, 1);?></td> 
				</tr>
				<tr>
					<td><?php esc_html_e( 'DB Version', 'visitorlog' );?></td>
					<td><?php echo '>=1.0.1';?></td>
					<td><?php echo esc_html( $newDBversion );?></td>
					<td><?php VisitorLog_System_View::get_badge($compare_db_version, 1);?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'WordPress Version', 'visitorlog' );?></td>
					<td></td>
					<td><?php echo esc_html( $wp_version );?></td>
					<td></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Server Software', 'visitorlog' );?></td>
					<td></td>
					<td><?php echo esc_html( $server_software );?></td>
					<td></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Operating System', 'visitorlog' );?></td>
					<td></td>
					<td><?php VisitorLog_Server_Information_Handler::get_os();?></td>
					<td></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Server Name', 'visitorlog' );?></td>
					<td></td>
					<td><?php VisitorLog_Server_Information_Handler::get_server_name();?></td>
					<td></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Server IP', 'visitorlog' );?></td>
					<td></td>
					<td><?php VisitorLog_Server_Information_Handler::get_server_ip();?></td>
					<td></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Server Protocol', 'visitorlog' );?></td>
					<td></td>
					<td><?php VisitorLog_Server_Information_Handler::get_server_protocol();?></td>
					<td></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Architecture', 'visitorlog' );?></td>
					<td></td>
					<td><?php VisitorLog_Server_Information_Handler::get_architecture();?></td>
					<td></td>
				</tr>

			</tbody>
		</table>
		<div class="ui hidden divider"></div>
		<input type="submit" class="ui big grey right floated button" value="<?php esc_html_e('Continue', 'visitorlog');?>" name="visitorlog_handler_step" />
		</form>
		<?php

	} // END func

	/**
	 *  Method view creating tables in the wp database Step 2
	 *
	 *  @uses VisitorLog_System_View::get_badge() 
	 */
	public static function view_creating_tables()
	{
		$err_tables  = get_option( 'visitorlog_err_tables' );
		$err_options = get_option( 'visitorlog_error_options' );
		$old_version = VisitorLog_Utility::get_option_sl( 'db_version' );
		$count_tables  = 0;
		$count_colomns = 0;
		$errorType1 = 'Pass';
		$errorType2 = 'Pass';

		if ( '' == $err_options && '' == $err_tables ) {
			$table_prefix = VisitorLog_DB_Base::$table_prefix_sl;
			$tables = VisitorLog_DB_Base::$wpdb_vl->get_results("SHOW TABLES");

			if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
				$count_tables = '---';
				$count_colomns = '---';
				$errorType1 = 'Warning';
				$errorType2 = 'Warning';
			} else {
				foreach ($tables as $table) {
				    foreach ($table as $t) {
			    		if ( strpos( $t, $table_prefix ) !== false ) {
			    			$count_tables++;
			    			$columns = VisitorLog_DB_Base::$wpdb_vl->get_results("SHOW COLUMNS FROM $t");
			    			if ( ! empty($columns) ) {
			    				$count_colomns = $count_colomns + count($columns);
			    			}
			    		}       
				    }
				}			
				if ( self::$count_tables_new != $count_tables ) {
					$errorType1 = 'Warning';
				}
				if ( self::$count_colomns_new != $count_colomns ) {
					$errorType2 = 'Warning';
				}
			}
		} elseif ( '' != $err_tables ) {
			$errorType1 = 'Warning';
			$errorType2 = 'Warning';
			$count_tables = '---';
			$count_colomns = '---';
		} elseif ( '' != $err_options ) {
			$errorType2 = 'Warning';
			$count_colomns = '---';
		}
		?>
		<h1 class="ui dividing header" style="color: #0073AA;">
			<?php esc_html_e( 'Creating tables in the database', 'visitorlog' );?>
		</h1>
		<form method="post" class="ui form">
		<?php wp_nonce_field( 'visitorlog_nonce' );?>
		<div class="ui info message">
			<?php esc_html_e('During activation, the plugin checks for the presence in the database of tables necessary for all the functions of the plugin to work, and starts the procedure for creating them', 'visitorlog');?>
		</div>
		<?php if ( '' != $err_tables ) {
			?><div class="ui orange message"><?php echo esc_html($err_tables);?></div><?php
		} elseif ( '' != $err_options ) { 
			?><div class="ui orange message"><?php echo esc_html($err_options);?></div><?php
		}
		?>
		<table class="ui tablet stackable single line table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Actions', 'visitorlog' );?></th>
					<th><?php esc_html_e( 'Required', 'visitorlog' );?></th>
					<th><?php esc_html_e( 'Quantity', 'visitorlog' );?></th>
					<th class="collapsing center aligned"><?php esc_html_e( 'Status', 'visitorlog' );?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'DB version', 'visitorlog' );?>&nbsp;-&nbsp;
						<?php echo esc_html($old_version);?>
					</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Tables created', 'visitorlog' );?></td>
					<td><?php echo esc_html(self::$count_tables_new);?></td>
					<td><?php echo esc_html($count_tables);?></td>
					<td><?php VisitorLog_System_View::get_badge($errorType1, 1);?></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Columns created', 'visitorlog' );?></td>
					<td><?php echo esc_html(self::$count_colomns_new);?></td>
					<td><?php echo esc_html($count_colomns);?></td>
					<td><?php VisitorLog_System_View::get_badge($errorType2, 1);?></td>
				</tr>
			</tbody>
		</table>
		<div style="margin-top:40px"></div>
		<div class="ui hidden divider"></div>
		<input type="submit" class="ui big grey right floated button" value="<?php esc_html_e('Continue', 'visitorlog');?>" name="visitorlog_handler_step"/>
		</form>
		<?php

	} // END func

	/**
	 *  Method view creating media library - Step 3
	 *
	 *  @uses VisitorLog_System_View::get_badge() 
	 */
	public static function view_media_library()
	{
		$err_copy_images = get_option( 'visitorlog_copy_images' );
		$errorType1 = 'Pass';

		if ( 1 != $err_copy_images ) {
			$errorType1 = 'Warning';
		}
		?>
		<h1 class="ui dividing header" style="color: #0073AA;">
			<?php esc_html_e( 'Creating a media library', 'visitorlog' );?>
		</h1>
		<div style="margin-top:20px"></div>
		<form method="post" class="ui form">
		<?php wp_nonce_field( 'visitorlog_nonce' );?>
		<div class="ui info message">
			<?php esc_html_e('At this stage, a library of files with images, icons, and logos for the plugin is being formed.', 'visitorlog');?>
		</div>
		<table class="ui tablet stackable single line table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Actions', 'visitorlog' );?></th>
					<th class="collapsing center aligned"><?php esc_html_e( 'Status', 'visitorlog' );?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Created files with icons in the wp-content/uploads directory', 'visitorlog' );?></td>
					<td><?php VisitorLog_System_View::get_badge($errorType1, 1);?></td>
				</tr>
			</tbody>
		</table>
		<div style="margin-top:60px"></div>
		<div class="ui hidden divider"></div>
		<input type="submit" class="ui big grey right floated button" value="<?php esc_html_e('Continue', 'visitorlog');?>" name="visitorlog_handler_step"/>
		</form>
		<?php

	} // END func

	/**
	 *  Render settings Step 4.
	 *
	 *  @uses VisitorLog_Utility::get_option_sl()
	 *  @uses VisitorLog_Settings::get_option_pass()
	 *  @uses VisitorLog_System_View::show_message()
	 *  @uses VisitorLog_Utility::update_option_sl()
	 */
	public static function view_settings()
	{
		$record_limit      = VisitorLog_Utility::get_option_sl( '#_record_limit' );
		$display_limit     = VisitorLog_Utility::get_option_sl( '#_display_limit' );
		$log_record_limit  = VisitorLog_Utility::get_option_sl( 'log_record_limit' );
		$log_display_limit = VisitorLog_Utility::get_option_sl( 'log_display_limit' );

		$smtphost     = VisitorLog_Utility::get_option_sl( 'smtphost' );
		$smtpport     = VisitorLog_Utility::get_option_sl( 'smtpport' );
		$smtpusername = VisitorLog_Utility::get_option_sl( 'smtpusername' );
		$smtppass     = VisitorLog_Settings::get_option_pass( 'smtppass' );

		$send_email1 = VisitorLog_Utility::get_option_sl( 'send_stat_email1' );
		$send_email2 = VisitorLog_Utility::get_option_sl( 'send_stat_email2' );
		$send_email3 = VisitorLog_Utility::get_option_sl( 'send_stat_email3' );

		$msg = $err = '';

        if ( isset( $_POST['visitorlog_save_inst'] ) ) {

	        if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
	            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
	            wp_die();
	        }

			if ( isset($_POST['visitorlog_record_limit']) && $record_limit != sanitize_text_field(wp_unslash($_POST['visitorlog_record_limit'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$record_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_record_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( '#_record_limit', $record_limit );
				$msg .= __('The parameter for limiting the size of database tables has been successfully entered', 'visitorlog');
				$msg .= '.&nbsp;';
			}
			if ( isset($_POST['visitorlog_display_limit']) && $display_limit != sanitize_text_field(wp_unslash($_POST['visitorlog_display_limit'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$display_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_display_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( '#_display_limit', $display_limit );
				if ( '' != $msg ) $msg .= '<br>';
				$msg .= __('The parameter for limiting table rows to display on the screen has been successfully entered', 'visitorlog');
				$msg .= '.&nbsp;';
			}
			if ( isset($_POST['visitorlog_log_record_limit']) && $log_record_limit != sanitize_text_field(wp_unslash($_POST['visitorlog_log_record_limit'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$log_record_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_log_record_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'log_record_limit', $log_record_limit );
				if ( '' != $msg ) $msg .= '<br>';
				$msg .= __('A parameter has been successfully entered to limit the size of the error log table', 'visitorlog');
				$msg .= '.&nbsp;';
			}
			if ( isset($_POST['visitorlog_log_display_limit']) && $log_display_limit != sanitize_text_field(wp_unslash($_POST['visitorlog_log_display_limit'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$log_display_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_log_display_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'log_display_limit', $log_display_limit );
				if ( '' != $msg ) $msg .= '<br>';
				$msg .= __('A parameter has been successfully entered to limit the display of error logging table rows on the screen', 'visitorlog');
				$msg .= '.&nbsp;';
			}
			if ( isset($_POST['visitorlog_smtphost']) && $smtphost != sanitize_text_field(wp_unslash($_POST['visitorlog_smtphost'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$smtphost = sanitize_text_field(wp_unslash($_POST['visitorlog_smtphost']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'smtphost', $smtphost );
				if ( '' != $msg ) $msg .= '<br>';
				$msg .= __('Smtp host parameter has been successfully entered', 'visitorlog');
				$msg .= '.&nbsp;';
			}
			if ( isset($_POST['visitorlog_smtpport']) && $smtpport != sanitize_text_field(wp_unslash($_POST['visitorlog_smtpport'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$smtp = sanitize_text_field(wp_unslash($_POST['visitorlog_smtpport']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				if ( is_numeric($smtp) && strlen($smtp) < 9 ) {
					$smtpport = $smtp;
					VisitorLog_Utility::update_option_sl( 'smtpport', $smtpport );
					if ( '' != $msg ) $msg .= '<br>';
					$msg .= __('Smtp port parameter has been successfully entered', 'visitorlog');
					$msg .= '.&nbsp;';
				} else {
					if ( '' != $err ) $err .= '<br>';
                    $err .= __( 'Error entering the Smtp port parameter', 'visitorlog' );
                    $msg .= '.&nbsp;';
				}
			}
			if ( isset($_POST['visitorlog_smtpusername']) && $smtpusername != sanitize_text_field(wp_unslash($_POST['visitorlog_smtpusername'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$smtpusername = sanitize_text_field(wp_unslash($_POST['visitorlog_smtpusername']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'smtpusername', $smtpusername );
				if ( '' != $msg ) $msg .= '<br>';
				$msg .= __('The Email parameter has been successfully entered', 'visitorlog');
				$msg .= '.&nbsp;';
			}
			if ( isset($_POST['visitorlog_smtppass']) && $smtppass != sanitize_text_field(wp_unslash($_POST['visitorlog_smtppass'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$pass = sanitize_text_field(wp_unslash( $_POST['visitorlog_smtppass'] ));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				$res = VisitorLog_Settings::update_option_pass( 'smtppass', $pass );
				if ( $res ) {
					if ( '' != $msg ) $msg .= '<br>';
	                $msg .= __( 'The password was entered successfully', 'visitorlog' );
	                $msg .= '.&nbsp;';
				} else {
					if ( '' != $err ) $err .= '<br>';
                    $err .= __( 'Error writing the password to the db', 'visitorlog' );
                    $msg .= '.&nbsp;';
				}
			}
			if ( isset($_POST['visitorlog_input_email_1']) && $send_email1 != sanitize_text_field(wp_unslash($_POST['visitorlog_input_email_1'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$send_email1_inp = sanitize_text_field(wp_unslash( $_POST['visitorlog_input_email_1'] ));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }

                if( !filter_var($send_email1_inp, FILTER_VALIDATE_EMAIL) ) {
                	if ( '' != $err ) $err .= '<br>';
                    $err .= __( 'Error entering the address for sending an email message', 'visitorlog' );
                    $msg .= '.&nbsp;';
                } else {
                	$send_email1 = $send_email1_inp;
					VisitorLog_Utility::update_option_sl( 'send_stat_email1', $send_email1 );
					if ( '' != $msg ) $msg .= '<br>';
	                $msg .= __( 'The email address has been successfully entered', 'visitorlog' );
	                $msg .= '.&nbsp;';
                }
			}
			if ( isset($_POST['visitorlog_input_email_2']) && $send_email2 != sanitize_text_field(wp_unslash($_POST['visitorlog_input_email_2'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$send_email2_inp = sanitize_text_field(wp_unslash( $_POST['visitorlog_input_email_2'] ));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }

                if( !filter_var($send_email2_inp, FILTER_VALIDATE_EMAIL ) ) {
                	if ( '' != $err ) $err .= '<br>';
                    $err .= __( 'Error entering the address for sending an email message', 'visitorlog' );
                    $msg .= '.&nbsp;';
                } else {
                	$send_email2 = $send_email2_inp;
					VisitorLog_Utility::update_option_sl( 'send_stat_email2', $send_email2 );
					if ( '' != $msg ) $msg .= '<br>';
	                $msg .= __( 'The email address has been successfully entered', 'visitorlog' );
	                $msg .= '.&nbsp;';
                }
			}
			if ( isset($_POST['visitorlog_input_email_3']) && $send_email3 != sanitize_text_field(wp_unslash($_POST['visitorlog_input_email_3'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$send_email3_inp = sanitize_text_field(wp_unslash( $_POST['visitorlog_input_email_3'] ));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }

                if( !filter_var($send_email3_inp, FILTER_VALIDATE_EMAIL) ) {
                	if ( '' != $err ) $err .= '<br>';
                    $err .= __( 'Error entering the address for sending an email message', 'visitorlog' );
                    $msg .= '.&nbsp;';
                } else {
                	$send_email3 = $send_email3_inp;
					VisitorLog_Utility::update_option_sl( 'send_stat_email3', $send_email3 );
					if ( '' != $msg ) $msg .= '<br>';
	                $msg .= __( 'The email address has been successfully entered', 'visitorlog' );
	                $msg .= '.&nbsp;';
                }
			}
	        if ( '' != $err ) {
	        	$color = 'yellow';
	        	$msg = $err;
	        } else {
	        	$color = 'green';
	        }
	        if ( '' != $msg ) {
	            VisitorLog_System_View::show_message( $color, $msg );
	            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
	            VisitorLog_Utility::update_option_sl( 'temp_value', $color );

				VisitorLog_Utility::vl_redirect('visitorlog_setup_settings');
				exit;
	        }
        }
        // Display--------
        $msg = VisitorLog_Utility::get_option_sl( 'temp_message' );
        if ( '' != $msg ) {
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }
		$a100=$a500=$a1000=$a2000=$a5000=$a10_000=$a100_000=$a200_000 = '';
		$b20=$b40=$b60=$b80=$b100=$b200=$b300 = '';
		$r100=$r500=$r1000=$r2000=$r5000=$r10_000=$r100_000=$r200_000 = '';
		$d20=$d40=$d60=$d80=$d100=$d200=$d300 = '';

		switch ($record_limit) {
			case 100:
				$a100 = 'selected';
				break;
			case 500:
				$a500 = 'selected';
				break;
			case 1000:
				$a1000 = 'selected';
				break;
			case 2000:
				$a2000 = 'selected';
				break;			
			case 5000:
				$a5000 = 'selected';
				break;
			case 10000:
				$a10_000 = 'selected';
				break;
			case 100000:
				$a100_000 = 'selected';
				break;
			case 200000:
				$a200_000 = 'selected';
				break;
			default:
				$a10_000 = 'selected';
				break;
		}
		switch ($display_limit) {
			case 20:
				$b20 = 'selected';
				break;
			case 40:
				$b40 = 'selected';
				break;
			case 60:
				$b60 = 'selected';
				break;
			case 80:
				$b80 = 'selected';
				break;			
			case 100:
				$b100 = 'selected';
				break;
			case 200:
				$b200 = 'selected';
				break;
			case 300:
				$b300 = 'selected';
				break;
			default:
				$b100 = 'selected';
				break;
		}
		switch ($log_record_limit) {
			case 100:
				$r100 = 'selected';
				break;
			case 500:
				$r500 = 'selected';
				break;
			case 1000:
				$r1000 = 'selected';
				break;
			case 2000:
				$r2000 = 'selected';
				break;			
			case 5000:
				$r5000 = 'selected';
				break;
			case 10000:
				$r10_000 = 'selected';
				break;
			case 100000:
				$r100_000 = 'selected';
				break;
			case 200000:
				$r200_000 = 'selected';
				break;
			default:
				$r10_000 = 'selected';
				break;
		}
		switch ($log_display_limit) {
			case 20:
				$d20 = 'selected';
				break;
			case 40:
				$d40 = 'selected';
				break;
			case 60:
				$d60 = 'selected';
				break;
			case 80:
				$d80 = 'selected';
				break;			
			case 100:
				$d100 = 'selected';
				break;
			case 200:
				$d200 = 'selected';
				break;
			case 300:
				$d300 = 'selected';
				break;
			default:
				$d100 = 'selected';
				break;
		}
		?>
		<h1 class="ui dividing header" style="color: #0073AA;">
			<?php esc_html_e( 'Parameters settings', 'visitorlog' );?>
		</h1>
		<div style="margin-top:20px"></div>
		<form method="post" class="ui form">
			<?php wp_nonce_field( 'visitorlog_nonce' );?>
			<table class="ui tablet stackable single line table">
				<thead>
					<tr>
	                    <th><?php esc_html_e('Options', 'visitorlog');?></th>
	                    <th><?php esc_html_e('Parameters', 'visitorlog');?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e( 'DB limit of entries', 'visitorlog' );?>:</td>
						<td>
							<select name="visitorlog_record_limit" style="width:10rem;margin-top:-5px;">
							    <option value="100"    <?php echo esc_html($a100)?>>        100 </option>
							    <option value="500"    <?php echo esc_html($a500)?>>        500 </option>
							    <option value="1000"   <?php echo esc_html($a1000)?>>     1 000 </option>
							    <option value="2000"   <?php echo esc_html($a2000)?>>     2 000 </option>
							    <option value="5000"   <?php echo esc_html($a5000)?>>     5 000 </option>
							    <option value="10000"  <?php echo esc_html($a10_000)?>>  10 000 </option>
							    <option value="100000" <?php echo esc_html($a100_000)?>>100 000 </option>
							    <option value="200000" <?php echo esc_html($a200_000)?>>200 000 </option>
							</select>
						</td> 
					</tr>
					<tr>
						<td><?php esc_html_e( 'The limit of displaying lines on the screen', 'visitorlog' );?>:</td>
						<td>
							<select name="visitorlog_display_limit" style="width:10rem;margin-top:-5px;">
							    <option value="20"  <?php echo esc_html($b20) ?>> 20 </option>
							    <option value="40"  <?php echo esc_html($b40) ?>> 40 </option>
							    <option value="60"  <?php echo esc_html($b60) ?>> 60 </option>
							    <option value="80"  <?php echo esc_html($b80) ?>> 80 </option>
							    <option value="100" <?php echo esc_html($b100)?>>100 </option>
							    <option value="200" <?php echo esc_html($b200)?>>200 </option>
							    <option value="300" <?php echo esc_html($b300)?>>300 </option>
							</select>
						</td> 
					</tr>
					<tr>
						<td><?php esc_html_e( 'Limit of entries in the log table', 'visitorlog' );?>:</td>
						<td>
							<select name="visitorlog_log_record_limit" style="width:10rem;margin-top:-5px;">
							    <option value="100"    <?php echo esc_html($r100)?>>        100 </option>
							    <option value="500"    <?php echo esc_html($r500)?>>        500 </option>
							    <option value="1000"   <?php echo esc_html($r1000)?>>     1 000 </option>
							    <option value="2000"   <?php echo esc_html($r2000)?>>     2 000 </option>
							    <option value="5000"   <?php echo esc_html($r5000)?>>     5 000 </option>
							    <option value="10000"  <?php echo esc_html($r10_000)?>>  10 000 </option>
							    <option value="100000" <?php echo esc_html($r100_000)?>>100 000 </option>
							    <option value="200000" <?php echo esc_html($r200_000)?>>200 000 </option>
							</select>
						</td> 
					</tr>
					<tr>
						<td><?php esc_html_e( 'The limit for displaying lines on the screen from the log', 'visitorlog' );?>:</td>
						<td>
							<select name="visitorlog_log_display_limit" style="width:10rem;margin-top:-5px;">
							    <option value="20"  <?php echo esc_html($d20) ?>> 20 </option>
							    <option value="40"  <?php echo esc_html($d40) ?>> 40 </option>
							    <option value="60"  <?php echo esc_html($d60) ?>> 60 </option>
							    <option value="80"  <?php echo esc_html($d80) ?>> 80 </option>
							    <option value="100" <?php echo esc_html($d100)?>>100 </option>
							    <option value="200" <?php echo esc_html($d200)?>>200 </option>
							    <option value="300" <?php echo esc_html($d300)?>>300 </option>
							</select>
						</td> 
					</tr>
                    <tr>
                        <td colspan="2">
                            <div>
                                <em style="font-size:14px;">
<?php esc_html_e('Configuring PHPMailer settings for sending emails via SMTP from a real mailbox', 'visitorlog');?>
                                </em>
                            </div>
                        </td>
                    </tr> 
					<tr>
						<td>SMTP host</td>
                        <td>
                            <p>
<input type="text" size="20" name="visitorlog_smtphost" value="<?php echo esc_html($smtphost);?>"/>
                            </p>    
                        </td>  
					</tr>
					<tr>
						<td>SMTP port</td>
                        <td>
                            <p>
<input type="text" size="20" name="visitorlog_smtpport" value="<?php echo esc_html($smtpport);?>"/>
                            </p>    
                        </td>  
					</tr>
					<tr>
						<td>Email</td>
                        <td>
                            <p>
<input type="email" size="20" name="visitorlog_smtpusername" value="<?php echo esc_html($smtpusername);?>"/>
                            </p>    
                        </td>  
					</tr>
					<tr>
						<td>password</td>
                        <td>
                            <p>
<input type="password" autocomplete="off" size="20" name="visitorlog_smtppass" value="<?php echo esc_html($smtppass);?>"/>
                            </p>    
                        </td>  
					</tr>
                    <tr>
                        <td>
							<?php esc_html_e('Email address','visitorlog');?>:
                        </td>
                        <td>
<input type="email" size="25" name="visitorlog_input_email_1" value="<?php echo esc_html($send_email1);?>"/>
                        </td>    
                    </tr> 
                    <tr>
                        <td>
							<?php esc_html_e('Email address','visitorlog');?>:
                        </td>
                        <td>
<input type="email" size="25" name="visitorlog_input_email_2" value="<?php echo esc_html($send_email2);?>"/>
                        </td>    
                    </tr>
                    <tr>
                        <td>
							<?php esc_html_e('Email address','visitorlog');?>:
                        </td>
                        <td>
<input type="email" size="25" name="visitorlog_input_email_3" value="<?php echo esc_html($send_email3);?>"/>
                        </td>    
                    </tr>
				</tbody>
			</table>
			<div style="margin-top:20px">
<input type="submit" class="ui big blue left floated button" value="<?php esc_html_e( 'Save', 'visitorlog' );?>" name="visitorlog_save_inst"/>
<input type="submit" class="ui big grey right floated button" value="<?php esc_html_e( 'Continue', 'visitorlog' );?>" name="visitorlog_handler_step"/>
			</div>
		</form>
		<?php

	} // END func

	/**
	 *   Render ready message Step 5.
	 */
	public static function view_finish()
	{
		$err_tables  = get_option( 'visitorlog_err_tables' );
		$err_options = get_option( 'visitorlog_error_options' );

		if ( '' == $err_options && '' == $err_tables ) { 
			?>
			<div style="margin-top:60px"></div>
			<form method="post" class="ui form">
				<?php wp_nonce_field( 'visitorlog_nonce' );?>
				<h1 class="ui icon header" style="display:block">
					<i>
						<?php
						VisitorLog_Utility::render_picture('install.png');
						?>  
					</i>
					<div style="margin-top:30px"></div>
					<div class="content">
						<?php esc_html_e( 'Your Plugin Is Ready To Work', 'visitorlog' ); ?>
						<div class="sub header" style="margin-top:60px"></div>
						<input type="submit" name="visitorlog_handler_step" class="ui massive grey button" value="<?php esc_html_e( 'Let\'s Go!', 'visitorlog' ); ?>"/>
					</div>
				</h1>
			</form>
			<div style="margin-top:60px"></div>
			<?php
		} else {
			if ( '' != $err_tables ) {
				?>
				<div style="margin-top:20px"></div>
				<div class="ui orange message"><?php echo esc_html($err_tables);?></div>
				<?php
			} elseif ( '' != $err_options ) { 
				?>
				<div style="margin-top:20px"></div>
				<div class="ui orange message"><?php echo esc_html($err_options);?></div>
				<?php
			}
		}

	} // END func

	/**
	 *  Render Step 1 update.
	 *
	 *  @uses VisitorLog_Utility::get_option_sl()
	 *  @uses VisitorLog_System_View::get_badge() 
	 */
	public static function view_update()
	{
		$old_version  = VisitorLog_Utility::get_option_sl('db_version');
		$count_tables = 0;
		$count_colomns = 0;

		$table_prefix = VisitorLog_DB_Base::$table_prefix_sl;
		$tables = VisitorLog_DB_Base::$wpdb_vl->get_results("SHOW TABLES");
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$count_tables = '---';
		} else {
			foreach ($tables as $table) {
			    foreach ($table as $t) {
		    		if ( strpos( $t, $table_prefix ) !== false ) {
		    			$count_tables++;
		    			$columns = VisitorLog_DB_Base::$wpdb_vl->get_results("SHOW COLUMNS FROM $t");
		    			if ( ! empty($columns) ) {
		    				$count_colomns = $count_colomns + count($columns);
		    			}
		    		}       
			    }
			}			
		}
		?>
		<h1 class="ui dividing header" style="color: #0073AA;">
			<?php esc_html_e( 'Updating the plugin\'s database tables', 'visitorlog' );?>
		</h1>
		<div style="margin-top:20px"></div>
		<form method="post" class="ui form">
			<?php wp_nonce_field( 'visitorlog_nonce' );?>
			<div class="ui info message">
				<?php esc_html_e('The new version of the plugin requires updating the tables in the database', 'visitorlog');?>.&nbsp;
				<?php esc_html_e('It\'s a good idea to make a backup copy of the database beforehand', 'visitorlog');?>.
			</div>
			<table class="ui tablet stackable single line table">
				<thead>
					<tr>
						<th></th>
						<th><?php esc_html_e( 'DB version', 'visitorlog' );?></th>
						<th><?php esc_html_e( 'Number of tables', 'visitorlog' );?></th>
						<th><?php esc_html_e( 'Number of columns', 'visitorlog' );?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Old version', 'visitorlog' );?></td>
						<td><?php echo esc_html($old_version);?></td> 
						<td><?php echo esc_html($count_tables);?></td>
						<td><?php echo esc_html($count_colomns);?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'New version', 'visitorlog' );?></td>
						<td><?php echo esc_html(VISITORLOG_DB_VERSION);?></td> 
						<td><?php echo esc_html(self::$count_tables_new);?></td> 
						<td><?php echo esc_html(self::$count_colomns_new);?></td>
					</tr>
				</tbody>
			</table>
			<div class="ui hidden divider"></div>
			<input type="submit" class="ui big grey right floated button" value="<?php esc_html_e('Continue', 'visitorlog');?>" name="visitorlog_handler_step"/>
		</form>
		<?php

	} // END func

	/**
	 *  Method Step 2 update
	 *
	 *  @uses VisitorLog_System_View::get_badge() 
	 */
	public static function view_finish_update()
	{
		$err_tables  = get_option( 'visitorlog_err_tables' );
		$err_options = get_option( 'visitorlog_error_options' );
		$old_version = VisitorLog_Utility::get_option_sl( 'db_version' );
		$count_tables  = 0;
		$count_colomns = 0;
		$errorType1 = 'Pass';

		if ( '' != $err_options || '' != $err_tables ) {
			$errorType1 = 'Warning';
			$count_tables = '---';
			$count_colomns = '---';
		} else {
			$table_prefix = VisitorLog_DB_Base::$table_prefix_sl;
			$tables = VisitorLog_DB_Base::$wpdb_vl->get_results("SHOW TABLES");
			if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
				$count_tables = '---';
				$count_colomns = '---';
				$errorType1 = 'Warning';
			} else {
				foreach ($tables as $table) {
				    foreach ($table as $t) {
			    		if ( strpos( $t, $table_prefix ) !== false ) {
			    			$count_tables++;
			    			$columns = VisitorLog_DB_Base::$wpdb_vl->get_results("SHOW COLUMNS FROM $t");
			    			if ( ! empty($columns) ) {
			    				$count_colomns = $count_colomns + count($columns);
			    			}
			    		}       
				    }
				}			
				if ( self::$count_tables_new  != $count_tables ||
				     self::$count_colomns_new != $count_colomns ) {

					$errorType1 = 'Warning';
					$count_tables = '---';
					$count_colomns = '---';
				}
			}
		}

		$err_copy_images = get_option( 'visitorlog_copy_images' );
		$errorType11 = 'Pass';

		if ( 0 == $err_copy_images ) {
			$errorType11 = 'Warning';
		}
		?>
		<h1 class="ui dividing header" style="color: #0073AA;">
			<?php esc_html_e( 'Updating of database tables and media files is completed', 'visitorlog' );?>
		</h1>
		<div style="margin-top:20px"></div>
		<?php if ( '' != $err_tables ) {
			?>
			<div class="ui orange message"><?php echo esc_html($err_tables);?></div>
			<?php
		} elseif ( '' != $err_options ) { 
			?>
			<div class="ui orange message"><?php echo esc_html($err_options);?></div>
			<?php
		}
		?>
		<table class="ui tablet stackable single line table">
			<thead>
				<tr>
					<th></th>
					<th><?php esc_html_e( 'DB version', 'visitorlog' );?></th>
					<th><?php esc_html_e( 'Number of tables', 'visitorlog' );?></th>
					<th><?php esc_html_e( 'Number of columns', 'visitorlog' );?></th>
					<th class="collapsing center aligned"><?php esc_html_e( 'Status', 'visitorlog' );?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'New version', 'visitorlog' );?></td>
					<td><?php echo esc_html($old_version);?></td> 
					<td><?php echo esc_html($count_tables);?></td>
					<td><?php echo esc_html($count_colomns);?></td>
					<td><?php VisitorLog_System_View::get_badge($errorType1);?></td>
				</tr>
			</tbody>
		</table>
		<table class="ui tablet stackable single line table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Actions', 'visitorlog' );?></th>
					<th class="collapsing center aligned"><?php esc_html_e( 'Status', 'visitorlog' );?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Created files with icons in the wp-content/uploads directory', 'visitorlog' );?></td>
					<td><?php VisitorLog_System_View::get_badge($errorType11);?></td>
				</tr>
			</tbody>
		</table>
		<div style="margin-top:40px"></div>
		<?php
		if ( 'Pass' == $errorType1 ) {
			?>	
			<form method="post" class="ui form">
				<?php wp_nonce_field( 'visitorlog_nonce' );?>
				<h1 class="ui icon header" style="display:block">
					<div class="content">
						<i>
							<?php VisitorLog_Utility::render_picture('install.png');?>
							&emsp;
							<?php esc_html_e( 'Your Plugin Is Ready To Work', 'visitorlog' );?>
						</i>
						<div class="sub header" style="margin-top:40px"></div>
						<input type="submit" name="visitorlog_handler_step" class="ui massive grey button" value="<?php esc_html_e( 'Let\'s Go!', 'visitorlog' ); ?>"/>
					</div>
				</h1>
			</form>
			<?php
		}	

	} // END func


} // END class