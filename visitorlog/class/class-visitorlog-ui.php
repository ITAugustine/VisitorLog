<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_UI class
 *
 * @method public    static function get_class_name()
 * @method public    static function render_top_header()
 * @method public    static function render_header_actions()
 * @method protected static function link_label_guide()
 */
class VisitorLog_UI
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Render top header.
	 *
	 * @param array $params Page parameters.
	 *
	 * @uses VisitorLog_Menu::render_left_menu()
	 */
	public static function render_top_header( $params = array() )
	{
		global $wp_version;

		VisitorLog_Menu::render_left_menu();
		$fix_menu_overflow = '1';
		if ( version_compare( $wp_version, '5.5.3', '>' ) ) {
			$fix_menu_overflow = '2';
		}
		?>
		<div class="vl-content-wrap vl-sidebar-left" menu-overflow="<?php echo esc_url($fix_menu_overflow);?>">
			<div id="vl-top-header" class="ui sticky">
				<div class="ui stackable grid">
					<div class="column row">
						<div class="right floated column right aligned">
							<?php self::render_header_actions();?>
						</div>
					</div>
				</div>
			</div>

		<?php
		
	} // END func


	/**
	 * Render header action buttons,
	 * (Sync|Add|Options|Community|User|Updates).
	 *
	 * @uses VisitorLog_Utility::get_option_sl()
	 * @uses VisitorLog_Utility::update_option_sl()
	 * @uses VisitorLog_System_Check::show_error_system_message()
	 *
	 * @return mixed $output Render header action buttons html.
	 */
	public static function render_header_actions()
	{ 
        $msg_site  = __( 'Developer\'s website', 'visitorlog' ); 
        $msg_site .=  '&nbsp;&nbsp;' . VisitorLog_Utility::$name_firm;
        $link_site = VisitorLog_Utility::$home_site;

		$prev_url = isset($_SERVER['HTTP_REFERER']) ? wp_kses(wp_unslash($_SERVER['HTTP_REFERER']), 'post') : '';

		if ( false !== strpos( $prev_url, '/index.php') ) {
			VisitorLog_Utility::vl_redirect( 'visitorlog_tab' );
		}

		if ( isset($_GET['page']) ) {

	        if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
				$get_page = sanitize_text_field(wp_unslash($_GET['page']));
	        } else {
	            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
	            wp_die();
	        }
		} else { 
			$get_page = 'visitorlog_tab';
		}

        if ( isset($_GET ['reg']) && 'sysmsg' == $_GET ['reg'] ) {

	        if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
	            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
	            wp_die();
	        }

            if ( '' == VisitorLog_Utility::get_option_sl( 'admin_notices' ) ) {
            	VisitorLog_Utility::update_option_sl( 'admin_notices', 'on' );
            } else {
            	VisitorLog_Utility::update_option_sl( 'admin_notices', '' );
            }
            VisitorLog_Utility::vl_redirect( $get_page );
        }

	    $link_label = self::link_label_guide( $get_page );
		$link_system_msg = 'admin.php?page=' . $get_page . '&reg=sysmsg';
		$link_update_msg = 'admin.php?page=' . $get_page . '&reg=updatemsg';

		$color_basic  = 'ui button basic icon';
		$color_orange = 'ui button orange inverted icon';
		$color_red    = 'ui button red inverted icon';

		$color_config = $color_basic;
		$tooltip_con = __('Viewing the file', 'visitorlog') . ' config.php';
		$color_htaccess = $color_basic;
		$tooltip_ht = __('Viewing the file', 'visitorlog') . ' .htaccess';

        $config_file   = VISITORLOG_HOME_DIR . 'wp-config.php';
		$htaccess_file = VISITORLOG_HOME_DIR . '.htaccess';

		$array = VisitorLog_System_Check::get_data_of_system_check();

		if ( 1 != $array[18] ) {
			$color_config = $color_red;
			$tooltip_con = __('File reading error', 'visitorlog') . ' config.php';
		}
		if ( 1 != $array[19] ) {
			$color_htaccess = $color_red;
			$tooltip_ht = __('File reading error', 'visitorlog') . ' .htaccess';
		}
		if ( 1 != $array[20] ) {
			$color_config = $color_orange;
			$tooltip_con = __('Error writing to the file', 'visitorlog') . ' config.php';
		}
		if ( 1 != $array[21] ) {
			$color_htaccess = $color_orange;
			$tooltip_ht = __('Error writing to the file', 'visitorlog') . ' .htaccess';
		}
        if ( 'on' == VisitorLog_Utility::get_option_sl('on_maintenance') ||
        	 'on' == VisitorLog_Utility::get_option_sl('maintenance_ddos') ) {
			$color_maintenance = $color_orange;
			$tooltip_mtnc = __('Maintenance - on', 'visitorlog');
	        VisitorLog_System_Check::update_data_of_system_check( 22, '' );
		} else {
			$color_maintenance = $color_basic;
			$tooltip_mtnc = __('Maintenance', 'visitorlog');
			VisitorLog_System_Check::update_data_of_system_check( 22, 1 );
		}
		$count_sys_msg = VisitorLog_System_Check::show_error_system_message();
		?>
		<a class="<?php echo esc_html($color_htaccess);?>" id="vl-htaccess-sidebar" data-position="bottom left" href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_htaccess')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" data-tooltip="<?php echo esc_html($tooltip_ht);?>"><small style="font-size:8px;">Htaccess</small>
		</a>
		<a class="<?php echo esc_html($color_config);?>" id="vl-htaccess-sidebar" data-position="bottom left" href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_wpconfig')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" data-tooltip="<?php echo esc_html($tooltip_con);?>"><small style="font-size:9px;">config</small>
		</a>
		&#8942;
		<a class="ui button basic icon" id="vl-overview-sidebar" data-position="bottom right" href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_tab')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" data-tooltip="<?php esc_html_e('Overview','visitorlog');?>"><?php VisitorLog_Utility::render_picture('list-repo-com.png', array(16,16));?>
		</a>
		<a class="ui button basic icon" id="vl-security-sidebar" data-position="bottom right" href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_security')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" data-tooltip="<?php esc_html_e('Security','visitorlog');?>"><?php VisitorLog_Utility::render_picture('shield-halved.png', array(16,16));?>
		</a>
		<a class="ui button basic icon" id="vl-statistics-sidebar" data-position="bottom right" href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_statistics')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" data-tooltip="<?php esc_html_e('Statistics', 'visitorlog');?>"><?php VisitorLog_Utility::render_picture('chart-line.png', array(16,16));?>
		</a>
		<a class="ui button basic icon" id="vl-screenoptions-sidebar" data-position="bottom right" href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_settings')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" data-tooltip="<?php esc_html_e('Settings for displaying information on the screen and writing to the database', 'visitorlog');?>"><?php VisitorLog_Utility::render_picture('gear.png', array(16,16));?>
		</a>
		<a class="<?php echo esc_html($color_maintenance);?>" id="vl-maintenance-sidebar" data-position="bottom right" href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_maintenance')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" data-tooltip="<?php echo esc_html($tooltip_mtnc);?>"><?php VisitorLog_Utility::render_picture('wrench.png', array(16,16));?>
		</a>
		<a class="ui button basic icon" id="vl-help-sidebar" data-position="bottom right" href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_guide')).esc_html($link_label).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" data-tooltip="<?php esc_html_e('Guide','visitorlog');?>"><?php VisitorLog_Utility::render_picture('book.png', array(16,16));?>
		</a>
		<a class="ui button basic icon" data-position="bottom right" data-tooltip="<?php echo esc_html($msg_site);?>" href="<?php echo esc_url($link_site);?>"><?php VisitorLog_Utility::render_picture('globe.png', array(16,16));?>
		</a>
		<?php 
		if ( $count_sys_msg ) { //---------- Warning - system messages
			$tooltip2  = __('Read the system messages', 'visitorlog');
			$tooltip2 .= '(' . $count_sys_msg . ')' . '.&nbsp;';
			$tooltip2 .= __('Click on the icon to turn on/off the message', 'visitorlog');
			?>
			&#8942;
			<a href="<?php echo esc_url(admin_url($link_system_msg)).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" class="ui button orange inverted icon" data-position="bottom right" data-tooltip="<?php echo esc_html($tooltip2);?>"><?php VisitorLog_Utility::render_picture('warning.png', array(16,16));?>
			</a>
			<?php
		}
		?>
		&#8942;
		<a class="ui button blue inverted icon" data-position="bottom right" data-tooltip="<?php esc_html_e('Exit', 'visitorlog');?>" href="<?php echo esc_url(admin_url('index.php'));?>"><?php VisitorLog_Utility::render_picture('right-from-bracket.png', array(16,16));?>
		</a>
		<?php

	} // END func

	protected static function link_label_guide( $get_page )
	{
		$link_label = ''; 

		switch ( $get_page ) {
			case 'visitorlog_tab':
				$link_label = '&addr=overview';
				break;
			// ------------------------------- Security
			case 'visitorlog_security':
				$link_label = '&addr=security';
				break;
			case 'visitorlog_controlpanel':
				$link_label = '&addr=control_panel';
				break;
			case 'visitorlog_blockvisitor':
				$link_label = '&addr=block_ip';
				break;
			case 'visitorlog_adminpanel':
				$link_label = '&addr=admin_panel';
				break;
			case 'visitorlog_dbprotection':
				$link_label = '&addr=db_protect';
				break;
			case 'visitorlog_fileprotection':
				$link_label = '&addr=file_protect';
				break;
			case 'visitorlog_spamprotection':
				$link_label = '&addr=spam';
				break;
			case 'visitorlog_firewall':
				$link_label = '&addr=firewall';
				break;
			// ------------------------------- Report	
			case 'visitorlog_reports':
				$link_label = '&addr=backups_report';
				break;
			case 'visitorlog_backupreport':
				$link_label = '&addr=backups_report';
				break;
			case 'visitorlog_filesscan':
				$link_label = '&addr=file_scan_report';
				break;
			case 'visitorlog_serverinfo':
				$link_label = '&addr=server_info';
				break;
			case 'visitorlog_filepermissions':
				$link_label = '&addr=file_permissions';
				break;
			case 'visitorlog_updates':
				$link_label = '&addr=updates';
				break;
			// ------------------------------- Tables
			case 'visitorlog_tables':
				$link_label = '&addr=blacklist';
				break;
			case 'visitorlog_lockedaddr':
				$link_label = '&addr=blacklist';
				break;
			case 'visitorlog_failedlogins':
				$link_label = '&addr=failed_logins_table';
				break;
			case 'visitorlog_spam':
				$link_label = '&addr=spam_table';
				break;
			case 'visitorlog_event404':
				$link_label = '&addr=event404';
				break;
			case 'visitorlog_botstable':
				$link_label = '&addr=bots_table';
				break;
			// ------------------------------- Logs
			case 'visitorlog_logs':
				$link_label = '&addr=visitor_log';
				break;
			case 'visitorlog_visitorlog':
				$link_label = '&addr=visitor_log';
				break;
			case 'visitorlog_administrators':
				$link_label = '&addr=admins';
				break;
			case 'visitorlog_accountactivity':
				$link_label = '&addr=account_activity_log';
				break;
			case 'visitorlog_actionlog':
				$link_label = '&addr=action_log';
				break;
			case 'visitorlog_errorlog':
				$link_label = '&addr=error_log';
				break;
			// ------------------------------- Statistics
			case 'visitorlog_statistics':
				$link_label = '&addr=statistics';
				break;
			case 'visitorlog_visitshour':
				$link_label = '&addr=statistics';
				break;
			// ------------------------------- Settings
			case 'visitorlog_settings':
				$link_label = '&addr=settings';
				break;
			// ------------------------------- Cron
			case 'visitorlog_cron':
				$link_label = '&addr=cron';
				break;
			case 'visitorlog_schedultasks':
				$link_label = '&addr=cron';
				break;
			case 'visitorlog_crontest':
				$link_label = '&addr=cron';
				break;
			// ------------------------------- OtherFeatures
			case 'visitorlog_otherfeatures':
				$link_label = '&addr=otherfeatures';
				break;
			case 'visitorlog_maintenance':
				$link_label = '&addr=otherfeatures';
				break;
			case 'visitorlog_hidenotifications':
				$link_label = '&addr=otherfeatures';
				break;
		}
		return $link_label;

	} // END func


} // END class