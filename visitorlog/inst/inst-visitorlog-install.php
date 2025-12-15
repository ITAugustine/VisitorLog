<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Install class
 *
 * @method public  static function instance()
 * @method public         function install()
 * @method private        function fill_data()
 * @method private        function handler_update_data()
 * @method private static function dbDelta()
 */
class VisitorLog_Install extends VisitorLog_DB_Base
{
	private static $instance = null;

	public static function instance()
	{
		if ( null == self::$instance ) self::$instance = new self;
		return self::$instance;

	} // END func

	public function __construct()
	{
		// null
	}

	/**
	 * install.
	 *
	 * @uses VisitorLog_DB_Base::checking_table()
	 * @uses VisitorLog_Logger::instance()->log_update()
	 */
	public function install( $param='' )
	{ 
		$charset_collate = VisitorLog_DB_Base::$wpdb_vl->get_charset_collate();
		$sql = array();

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'action_log' ) . '` (
		  `id`            int(11) NOT NULL AUTO_INCREMENT,
		  `log_content`   mediumtext   COLLATE utf8mb4_unicode_520_ci DEFAULT "",
		  `log_type`      tinyint(5)   DEFAULT 0,
		  `log_user`      varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT "",
		  `log_timestamp` int(11) DEFAULT 0';
		if ( false === VisitorLog_DB_Base::checking_table('action_log') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'admin_ip' ) . '` (
		  `id`         bigint(20)   NOT NULL AUTO_INCREMENT,
		  `date_time`  varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `user_ip`    varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('admin_ip') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'backups' ) . '` (
		  `id`           int(11) NOT NULL AUTO_INCREMENT,
		  `initiator`    varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `mode`         varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `success`      varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `text`         mediumtext   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `mail`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `success_mail` varchar(50)  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `date_time`    int(11) DEFAULT 0';
		if ( false === VisitorLog_DB_Base::checking_table('backups') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'bots_table' ) . '` (
		  `id`           bigint(20)   NOT NULL AUTO_INCREMENT,
		  `type`         varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT "",
		  `bot_name`     varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT "",
		  `bot_ip`       varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT "",
		  `short_name`   varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT "",
		  `block`        varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT "",
		  `comment`      varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT ""';
		if ( false === VisitorLog_DB_Base::checking_table('bots_table') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'hour' ) . '` (
		  `id`         bigint(20)  NOT NULL AUTO_INCREMENT,
		  `hour`       varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `user`       int(10)     DEFAULT 0,
		  `admin`      int(10)     DEFAULT 0,
		  `adminlogin` int(10)     DEFAULT 0,
		  `admin404`   int(10)     DEFAULT 0,
		  `bot`        int(10)     DEFAULT 0,
		  `block`      int(10)     DEFAULT 0,
		  `botddos`    int(10)     DEFAULT 0,
		  `botlogin`   int(10)     DEFAULT 0,
		  `botspamcf`  int(10)     DEFAULT 0,
		  `botspamco`  int(10)     DEFAULT 0,
		  `bot404`     int(10)     DEFAULT 0,
		  `botother`   int(10)     DEFAULT 0,
		  `blockddos`  int(10)     DEFAULT 0,
		  `blocklogin` int(10)     DEFAULT 0,
		  `blockcf`    int(10)     DEFAULT 0,
		  `blockco`    int(10)     DEFAULT 0,
		  `block404`   int(10)     DEFAULT 0,
		  `blockother` int(10)     DEFAULT 0,
		  `buy`        int(10)     DEFAULT 0,
		  `pay`        int(10)     DEFAULT 0,
		  `download`   int(10)     DEFAULT 0';
		if ( false === VisitorLog_DB_Base::checking_table('hour') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'events_failed' ) . '` (
		  `id`           bigint(20)   NOT NULL AUTO_INCREMENT,
		  `timestamp`    int(20)      DEFAULT 0,
		  `timerelease`  int(20)      DEFAULT 0,
		  `date_time`    varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `user_name`    varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `user_ip`      varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `category`     varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `canal`        varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `count`        int(12)      DEFAULT 0,
		  `block`        varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `login`        varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `pswd`         varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `url`          varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `referer_info` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('events_failed') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'event_404' ) . '` (
		  `id`             bigint(20)   NOT NULL AUTO_INCREMENT,
		  `date_time`      varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `timestamp`      int(11)      DEFAULT 0,
		  `release_date`   varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `release_tstmp`  int(11)      DEFAULT 0,
		  `user_ip`        varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `count`          int(11)      DEFAULT 0,
		  `block`          varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `canal`          varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `category`       varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `user_agent`     varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `url`            varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `refinfo`        varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('event_404') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'fcd_scan' ) . '` (
		  `id`           int(11)     NOT NULL AUTO_INCREMENT,
		  `initiator`    varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `mode`         varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `success`      varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `text`         mediumtext  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `mail`         varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `success_mail` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `date_time`    int(11)     DEFAULT 0';
		if ( false === VisitorLog_DB_Base::checking_table('fcd_scan') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'images' ) . '` (
		  `id`      bigint(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
		  `name`    varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `id_post` int(11)      DEFAULT 0,
		  `class`   varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('images') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'lockdown_ip' ) . '` (
		  `id`          bigint(20)   NOT NULL AUTO_INCREMENT,
		  `timestamp`   int(11)      DEFAULT 0,
		  `timerelease` int(11)      DEFAULT 0,
		  `user_ip`     varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `count`       int(11)      DEFAULT 0,
		  `user_name`   varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `block`       varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `canal`       varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `category`    varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `url`         varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `comment`     varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('lockdown_ip') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'login_activity' ) . '` (
		  `id`          int(10)      NOT NULL AUTO_INCREMENT,
		  `login_date`  int(11)      DEFAULT 0,
		  `logout_date` int(11)      DEFAULT 0,
		  `user_login`  varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `user_id`     int(10)      DEFAULT 0,
		  `user_ip`     varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('login_activity') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'options' ) . '` (
		  `option_id`    bigint(20)   UNSIGNED NOT NULL AUTO_INCREMENT,
		  `option_name`  varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `option_value` longtext     COLLATE utf8mb4_unicode_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('options') ) {
			$tbl .= ',
  			PRIMARY KEY (`option_id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'spam' ) . '` (
		  `id`           bigint(20)   NOT NULL AUTO_INCREMENT,
		  `timestamp`    int(20)      DEFAULT 0,
		  `date_time`    varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `author_ip`    varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `block`        varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `reason`       varchar(50)  COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `author_url`   varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `author`       varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `content`      text         COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `refinfo`      varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `author_email` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		  `agent`        varchar(150) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('spam') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'update' ) . '` (
		  `id`             int(11)     NOT NULL AUTO_INCREMENT,
		  `category`       varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `plugin_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `db_version`     varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `date_time`      int(11)     DEFAULT 0';
		if ( false === VisitorLog_DB_Base::checking_table('update') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'users' ) . '` (
		  `id`           bigint(20)   NOT NULL AUTO_INCREMENT,
		  `timestamp`    int(20)      DEFAULT 0,
		  `timerelease`  int(20)      DEFAULT 0,
		  `user_ip`      varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `pages`        int(10)      DEFAULT 0,
		  `allpages`     int(10)      DEFAULT 0,
		  `session`      int(10)      DEFAULT 0,
		  `category`     varchar(50)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `canal`        varchar(50)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `user_agent`   varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `url`          varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `referer_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('users') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$tbl = 'CREATE TABLE `' . $this->table_name_sl( 'pass' ) . '` (
		  `id`             int(11)      NOT NULL AUTO_INCREMENT,
		  `pass_key`       varchar(50)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `pass_value`     varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `encryption_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
		  `encryption_iv`  varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL';
		if ( false === VisitorLog_DB_Base::checking_table('pass') ) {
			$tbl .= ',
  			PRIMARY KEY (`id`) ';
		}
		$tbl  .= ') ' . $charset_collate . ';';
		$sql[] = $tbl;

		$err_action = '';
		$i = 0;
		foreach ( $sql as $query ) {

			$result[$i] = self::dbDelta( $query );
			$err[$i] = VisitorLog_DB_Base::$wpdb_vl->last_error;
			if ( null != $err[$i] ) {
				$pos = strpos( $query, '(' );
				$name_table = substr( $query, 14, $pos-14 );
				$err[$i] .= ', Table: ' . $name_table;
				$err_action = $i;
			}	
			$i++;
		}

		update_option( 'visitorlog_count_tables', $i );
		if ( '' != $err_action ) {
			update_option( 'visitorlog_err_tables', $err[$err_action] );
			return false;
		} else {
			update_option( 'visitorlog_err_tables', '' );
		}
			
		$count = $this->fill_data( $error );

		if ( $count < 0 ) {
			if ( '' == $error ) {
				$error = __( 'An error occurred while filling in the tables of this plugin with data. Contact the administrator', 'visitorlog' );
				update_option( 'visitorlog_error_options', $error );
				return false;
			}
		}
		update_option( 'visitorlog_count_options', $count );
		update_option( 'visitorlog_error_options', $error );
		VisitorLog_Utility::update_option_sl( 'db_version', VISITORLOG_DB_VERSION );

		if ( '' == $param ) {
			VisitorLog_Logger::instance()->log_update( 'install' );
		} else {
			VisitorLog_Logger::instance()->log_update( 'update' );
		}
		return true;

	} // END func


	/**
	 * Fill in the table with data
	 *
	 * @uses VisitorLog_Utility::generate_alpha_numeric_random_string()
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 */
	private function fill_data( &$error )
	{
		$admin_email = get_option( 'admin_email' );
		$redirect_url = 'http://127.0.0.1';
		$smtphost = 'smtp.google.com';
		$fcd_exclude_files = 'visitorlog debug.log';
		$msg_maintenance1 = __( 'Under maintenance', 'visitorlog' );
		$msg_maintenance2 = __( 'The site is currently under maintenance and unavailable. Please try again later', 'visitorlog' );
		$msg_stopsite = __( 'Due to the protection of the site from DDoS attacks from several IP addresses, the site was stopped. Check back in a minute.', 'visitorlog' );
		$error = '';

		$options_table = array(
			'#_display_limit', '100',
			'#_record_limit', '10000',
			'actionlogs', '2',
			'active_session_limit', '1800',
			'admin_notices', '',
			'advanced_char_string_filter', '',
			'antispam-cf-shortcode1-', VisitorLog_Utility::generate_alpha_numeric_random_string(6),
			'backups', 'backups-' . VisitorLog_Utility::generate_alpha_numeric_random_string(8),
			'backups_period', '1',
			'block_fake_googlebots', 'on',
			'captcha_secret_key', '',
			'check_sys_msg', '1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1;1',
			'check_update_msg', '',
			'cron_test', '',
			'cron_test_time', '',
			'db_version', VISITORLOG_DB_VERSION,
			'ddos_delay_time', '1800',
			'ddos_enable', 'on',
			'ddos_permanent_block_ip', '5',
			'ddos_redirect_url', $redirect_url,
			'ddos_temporary_block_site', '30',
			'deny_bad_query_strings', '',
			'disable_file_edit', 'on',
			'disable_index_views', '',
			'disable_rest_requests', 'on',
			'disable_server_signature', '',
			'disable_trace_and_track', '',
			'disable_xmlrpc_methods', '',
			'fcd_exclude_files', $fcd_exclude_files,
			'fcd_exclude_filetypes', 'bmp jpg jpeg png cache',
			'fcd_filename', 'visitorlog_scan_' . VisitorLog_Utility::generate_alpha_numeric_random_string(8),
			'feature_db_backup', '',
			'file_change_detection_scan', '1',
			'forbid_proxy_comments', '',
			'ip_retrieve_method', '',
			'last_backup_time', '',
			'last_fcd_scan_time', '',
			'last_send_sales_time', '',
			'last_send_stat_time', '',
			'last_update_check', '0',
			'log_display_limit', '100',
			'log_record_limit', '10000',
			'login_page_slug', '',
			'login_slow_time_period', '1800',
			'logout_time_period', '3600',
			'maintenance_ddos', '',
			'maintenance_textarea1', $msg_maintenance1,
			'maintenance_textarea2', $msg_maintenance2,
			'maintenance_time', '',
			'max_file_upload_size', '10',
			'max_slow_attempts', '10',
			'msgstopsite', $msg_stopsite,
			'number_backup_instances', '1',
			'on_404_logging', 'on',
			'on_admin_panel_protection', 'on',
			'on_admin_panel_speed_up', '',
			'on_auto_backups', '',
			'on_auto_send_sales', '',
			'on_auto_send_stat', '',
			'on_autoblock_spam_ip', '',
			'on_automated_fcd_scan', '',
			'on_block_ip_perm', 'on',
			'on_block_ip_temp', 'on',
			'on_block_spam_perm', '',
			'on_block_xmlrpc', 'on',
			'on_comment_captcha', '',
			'on_comment_recaptcha', '',
			'on_db_protection', 'on',
	        'on_download_version_footer', 'on',
			'on_file_system_protection', 'on',
			'on_firewall', 'on',
			'on_firewall_g6', '',
			'on_firewall_g7', '',
			'on_forbid_commentspost', '',
			'on_forced_logout', '',
			'on_input_email_manual', 'on',
			'on_log_blocking_visitor', 'on',
			'on_login_captcha', '',
			'on_login_lockdown', 'on',
			'on_login_recaptcha', '',
			'on_maintenance', '',
			'on_max_file_upload_size', '',
	        'on_plugin_update', 'on',
			'on_prevent_hotlinking', '',
			'on_prevent_wp_file_access', 'on',
			'on_protect_wp_config', '',
			'on_rename_login_page', '',
			'on_send_stat_email', '',
			'on_sending_scan_email', 'on',
			'on_spam_protection', 'on',
			'on_spambot_blocking', '',
	        'on_total_update_admin_bar', 'on',
	        'on_total_update_admin_menu', 'on',
			'on_update', '',
	        'on_update_widget_console', 'on',
			'on_whitelisting', '',
	        'on_wp_available_console', 'on',
			'options1', '',
			'options2', '',
			'period_statistics', '1',
			'previous_readings_day', '',
			'recaptcha_secret_key', '',
			'recaptcha_site_key', '',
			'redirect_url_login', $redirect_url,
			'row_report_file_limit', '300',
			'scan_period', '2',
			'send_stat_email1', $admin_email,
			'send_stat_email2', '',
			'send_stat_email3', '',
			'send_stat_period', '1',
			'smtphost', $smtphost,
			'smtpport', '465',
			'smtpusername', $admin_email,
			'temp_message', '',
			'temp_value', '',
			'update_notices', '',
			'use_htaccess_file_login', 'on',
		);

		$bots_table = array(
			"","Accoon","","AccoonBot","",
			"","Applebot","","Applebot","",
			"","CCBot","","CCBot","",
			"","Duck","","DuckBot","",
			"","Sogou","","SogouBot","",
			"","AhrefsBot","","AhrefsBot","",
			"","Aport","","AportBot","",
			"","archive_org","","ArchiveBot","",
			"","Ask Jeeves","","AskJeeves","",
			"","Baidu","","BaiduBot","",
			"","bingbot","","Bingbot","",
			"","booch","","BoochBot","",
			"","C-T bot","","C-Tbot","",
			"","DCPbot","","DCPbot","",
			"","DomainVader","","DomainVad","",
			"","discobot","","Discobot","",
			"","Exabot","","Exabot","",
			"","Ezooms","","Ezooms","",
			"","fast","","FastSearch","",
			"","findlinks","","findlinks","",
			"","GetSmart","","GetSmart","",
			"","Giga","","Giga","",
			"useful","Google","notblock","Googlebot","",
			"","GrepNetstat","","GrepNet","",
			"","grub-client","","GrubClient","",
			"","heritrix","","heritrix","",
			"","ia_archiver","","Archiver","",
			"","Jetbot","","Jetbot","",
			"","libwww","","libwww","PuntoBot",
			"","lycos","","Lycos","",
			"","Mail","","Mail","",
			"","MJ12bot","","MJ12bot","Majestic-12",
			"","MnoGoSearch","","MnoGoS","Mno Go Search Bot",
			"useful","Yahoo","notblock","Yahoo","Yahoo!",
			"","msnbot","","msnbot","MSN",
			"","NaverBot","","NaverBot","Naver Bot",
			"","Nigma","","Nigma","",
			"","OmniExplorer_Bot","","OmniExp","",
			"","oBot","","oBot","",
			"","Openbot","","Openfind","Openfind Bot",
			"","OpenindexSpider","","Openind","",
			"","PaperLiBot","","PaperBot","",
			"","proximic","","proximic","",
			"useful","Rambler","notblock","Rambler","Rambler Bot",
			"","Scooter","","Scooter","AltaVista",
			"","SearchBot","","SearchBot","",
			"","SeznamBot","","SeznamBot","",
			"","SISTRIX","","SISTRIX","",
			"","SiteStatus","","SiteStatus","",
			"","slurp@inktomi","","slurpin","Hot Bot",
			"","Snoopy","","Snoopy","",
			"","Statsbot","","Statsbot","",
			"","Subscribe","","Subscribe","",
			"","SurveyBot","","SurveyBot","Whois Source",
			"","TurnitinBot","","Turnitin","",
			"","Tourlentabot","","Tourlenta","",
			"","TurtleScanner","","TurtlScan","Turtle Scanner Bot",
			"","Updownerbot","","Updowner","",
			"","Vampire","","Vampire","Net Vampire Bot",
			"","W3C_Validator","","W3CVal","",
			"","Webalta","","WebAlta","",
			"","WebCrawler","","WebCraw","",
			"","WebZIP","","WebZIP","",
			"","WhatUSeek","","WhatSeek","What You Seek Bot",
			"","Wotbox","","Wotbox","",
			"useful","Yandex","notblock","Yandex","",
			"","Yeti","","Yeti","",
			"","ZipppBot","","ZipBot","",
			"","ZyBorg","","ZyBorg",""
		);

		$pass_table = array(
			"smtppass","","","",
			"license","","",""
		);

		$hour_table = array(
			'00',
			'01',
			'02',
			'03',
			'04',
			'05',
			'06',
			'07',
			'08',
			'09',
			'10',
			'11',
			'12',
			'13',
			'14',
			'15',
			'16',
			'17',
			'18',
			'19',
			'20',
			'21',
			'22',
			'23',
		);
		$count = 0;

		$bots_table_key = array( 'type', 'bot_name', 'block', 'short_name', 'comment' );
		$result = $this->handler_update_data( 'bots_table', $bots_table_key, $bots_table, $error );
		$count++;
		if ( !$result ) return -$count;

		$options_table_key = array( 'option_name', 'option_value' );
		$result = $this->handler_update_data( 'options', $options_table_key, $options_table, $error );
		$count++;
		if ( !$result ) return -$count;

		$options_table_key = array( 'pass_key', 'pass_value', 'encryption_key', 'encryption_iv' );
		$result = $this->handler_update_data( 'pass', $options_table_key, $pass_table, $error );
		$count++;
		if ( !$result ) return -$count;

		$hour_table_key = array( 'hour' );
		$result = $this->handler_update_data( 'hour', $hour_table_key, $hour_table, $error );
		$count++;
		if ( !$result ) return -$count;

		return $count;

	} // END func	

	private function handler_update_data( $table, $table_key, $table_value, &$error )
	{
		$arr  = array();
		$arr2 = array();
		$error = '';

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;
		switch ($table) {
			case 'options':
				$id_table = 'option_id';
				break;
			default:
				$id_table = 'id';
				break;
		}

        $select = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE $id_table != %d", 0));

		$num_arr     = count( $select );
		$num_columns = count( $table_key );
		$num_rows    = count( $table_value );
		$num_rows    = $num_rows / $num_columns;

		for ($i=0; $i < $num_rows; $i++) {

			reset($arr);  // table
			reset($arr2); // db

			for ($j=0; $j < $num_columns; $j++) {
				$arr[ $table_key[$j] ] = $table_value[$i * $num_columns + $j];
			}

			if ( ! isset($select[$i]) ) {
				$result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $arr );
				if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
					$error = VisitorLog_DB_Base::$wpdb_vl->last_error;
					return false;
				}

			} else {

				$update = '';
				$id = $i + 1;
				for ($k=0; $k < $num_columns; $k++) {

					$key = $table_key[$k];
					$arr2[$key] = $select[$i]->{$key};

					if ( $arr2[$key] != $arr[$key] ) {
						$arr2[$key] = $arr[$key];
						$id = $select[$i]->$id_table;
						$update = 1;
					}
				}

				if ( $update ) {

					$result = VisitorLog_DB_Base::$wpdb_vl->update( $table_name, $arr2, [$id_table => $id]);
					if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
						$error = VisitorLog_DB_Base::$wpdb_vl->last_error;
						return false;
					}
				}
			}
		}
		return true;

	} // END func

	/**
	 * Modifies the database based on specified SQL statements.
	 *
	 * Useful for creating new tables and updating existing tables to a new structure.
	 *
	 * @param string[]|string $queries Optional. The query to run. Can be multiple queries
	 *                                 in an array, or a string of queries separated by
	 *                                 semicolons. Default empty string.
	 * @param bool            $execute Optional. Whether or not to execute the query right away.
	 *                                 Default true.
	 * @return string[] Strings containing the results of the various update queries.
	 */
	private static function dbDelta( $queries = '', $execute = true )
	{
		if ( in_array( $queries, array( '', 'all', 'blog', 'global', 'ms_global' ), true ) ) {
			$queries = \wp_get_db_schema( $queries );
		}

		// Separate individual queries into an array.
		if ( ! is_array( $queries ) ) {
			$queries = explode( ';', $queries );
			$queries = array_filter( $queries );
		}

		/**
		 * Filters the dbDelta SQL queries.
		 *
		 * @param string[] $queries An array of dbDelta SQL queries.
		 */
		$queries = apply_filters( 'dbdelta_queries', $queries );

		$cqueries   = array(); // Creation queries.
		$iqueries   = array(); // Insertion queries.
		$for_update = array();

		// Create a tablename index for an array ($cqueries) of recognized query types.
		foreach ( $queries as $qry ) {
			if ( preg_match( '|CREATE TABLE ([^ ]*)|', $qry, $matches ) ) {
				$cqueries[ trim( $matches[1], '`' ) ] = $qry;
				$for_update[ $matches[1] ]            = 'Created table ' . $matches[1];
				continue;
			}

			if ( preg_match( '|CREATE DATABASE ([^ ]*)|', $qry, $matches ) ) {
				array_unshift( $cqueries, $qry );
				continue;
			}

			if ( preg_match( '|INSERT INTO ([^ ]*)|', $qry, $matches ) ) {
				$iqueries[] = $qry;
				continue;
			}

			if ( preg_match( '|UPDATE ([^ ]*)|', $qry, $matches ) ) {
				$iqueries[] = $qry;
				continue;
			}
		}

		/**
		 * Filters the dbDelta SQL queries for creating tables and/or databases.
		 * Queries filterable via this hook contain "CREATE TABLE" or "CREATE DATABASE".
		 *
		 * @param string[] $cqueries An array of dbDelta create SQL queries.
		 */
		$cqueries = apply_filters( 'dbdelta_create_queries', $cqueries );

		/**
		 * Filters the dbDelta SQL queries for inserting or updating.
		 *
		 * Queries filterable via this hook contain "INSERT INTO" or "UPDATE".
		 *
		 * @param string[] $iqueries An array of dbDelta insert or update SQL queries.
		 */
		$iqueries = apply_filters( 'dbdelta_insert_queries', $iqueries );

		$text_fields = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
		$blob_fields = array( 'tinyblob', 'blob', 'mediumblob', 'longblob' );
		$int_fields  = array( 'tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint' );

		$global_tables  = VisitorLog_DB_Base::$wpdb_vl->tables( 'global' );
		$db_version     = VisitorLog_DB_Base::$wpdb_vl->db_version();
		$db_server_info = VisitorLog_DB_Base::$wpdb_vl->db_server_info();

		foreach ( $cqueries as $table => $qry ) {
			// Upgrade global tables only for the main site. Don't upgrade at all if conditions are not optimal.
			if ( in_array( $table, $global_tables, true ) && ! \wp_should_upgrade_global_tables() ) {
				unset( $cqueries[ $table ], $for_update[ $table ] );
				continue;
			}

			// Fetch the table column structure from the database.
			$suppress    = VisitorLog_DB_Base::$wpdb_vl->suppress_errors();
			$tablefields = VisitorLog_DB_Base::$wpdb_vl->get_results( "DESCRIBE {$table};" );
			VisitorLog_DB_Base::$wpdb_vl->suppress_errors( $suppress );

			if ( ! $tablefields ) {
				continue;
			}

			// Clear the field and index arrays.
			$cfields                  = array();
			$indices                  = array();
			$indices_without_subparts = array();

			// Get all of the field names in the query from between the parentheses.
			preg_match( '|\((.*)\)|ms', $qry, $match2 );
			$qryline = trim( $match2[1] );

			// Separate field lines into an array.
			$flds = explode( "\n", $qryline );

			// For every field line specified in the query.
			foreach ( $flds as $fld ) {
				$fld = trim( $fld, " \t\n\r\0\x0B," ); // Default trim characters, plus ','.

				// Extract the field name.
				preg_match( '|^([^ ]*)|', $fld, $fvals );
				$fieldname            = trim( $fvals[1], '`' );
				$fieldname_lowercased = strtolower( $fieldname );

				// Verify the found field name.
				$validfield = true;
				switch ( $fieldname_lowercased ) {
					case '':
					case 'primary':
					case 'index':
					case 'fulltext':
					case 'unique':
					case 'key':
					case 'spatial':
						$validfield = false;

						// Extract type, name and columns from the definition.
						preg_match(
							'/^
								(?P<index_type>             # 1) Type of the index.
									PRIMARY\s+KEY|(?:UNIQUE|FULLTEXT|SPATIAL)\s+(?:KEY|INDEX)|KEY|INDEX
								)
								\s+                         # Followed by at least one white space character.
								(?:                         # Name of the index. Optional if type is PRIMARY KEY.
									`?                      # Name can be escaped with a backtick.
										(?P<index_name>     # 2) Name of the index.
											(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+
										)
									`?                      # Name can be escaped with a backtick.
									\s+                     # Followed by at least one white space character.
								)*
								\(                          # Opening bracket for the columns.
									(?P<index_columns>
										.+?                 # 3) Column names, index prefixes, and orders.
									)
								\)                          # Closing bracket for the columns.
							$/imx',
							$fld,
							$index_matches
						);

						// Uppercase the index type and normalize space characters.
						$index_type = strtoupper( preg_replace( '/\s+/', ' ', trim( $index_matches['index_type'] ) ) );

						// 'INDEX' is a synonym for 'KEY', standardize on 'KEY'.
						$index_type = str_replace( 'INDEX', 'KEY', $index_type );

						// Escape the index name with backticks. An index for a primary key has no name.
						$index_name = ( 'PRIMARY KEY' === $index_type ) ? '' : '`' . strtolower( $index_matches['index_name'] ) . '`';

						// Parse the columns. Multiple columns are separated by a comma.
						$index_columns                  = array_map( 'trim', explode( ',', $index_matches['index_columns'] ) );
						$index_columns_without_subparts = $index_columns;

						// Normalize columns.
						foreach ( $index_columns as $id => &$index_column ) {
							// Extract column name and number of indexed characters (sub_part).
							preg_match(
								'/
									`?                      # Name can be escaped with a backtick.
										(?P<column_name>    # 1) Name of the column.
											(?:[0-9a-zA-Z$_-]|[\xC2-\xDF][\x80-\xBF])+
										)
									`?                      # Name can be escaped with a backtick.
									(?:                     # Optional sub part.
										\s*                 # Optional white space character between name and opening bracket.
										\(                  # Opening bracket for the sub part.
											\s*             # Optional white space character after opening bracket.
											(?P<sub_part>
												\d+         # 2) Number of indexed characters.
											)
											\s*             # Optional white space character before closing bracket.
										\)                  # Closing bracket for the sub part.
									)?
								/x',
								$index_column,
								$index_column_matches
							);

							// Escape the column name with backticks.
							$index_column = '`' . $index_column_matches['column_name'] . '`';

							// We don't need to add the subpart to $index_columns_without_subparts
							$index_columns_without_subparts[ $id ] = $index_column;

							// Append the optional sup part with the number of indexed characters.
							if ( isset( $index_column_matches['sub_part'] ) ) {
								$index_column .= '(' . $index_column_matches['sub_part'] . ')';
							}
						}

						// Build the normalized index definition and add it to the list of indices.
						$indices[]                  = "{$index_type} {$index_name} (" . implode( ',', $index_columns ) . ')';
						$indices_without_subparts[] = "{$index_type} {$index_name} (" . implode( ',', $index_columns_without_subparts ) . ')';

						// Destroy no longer needed variables.
						unset( $index_column, $index_column_matches, $index_matches, $index_type, $index_name, $index_columns, $index_columns_without_subparts );

						break;
				}

				// If it's a valid field, add it to the field array.
				if ( $validfield ) {
					$cfields[ $fieldname_lowercased ] = $fld;
				}
			}

			// For every field in the table.
			foreach ( $tablefields as $tablefield ) {
				$tablefield_field_lowercased = strtolower( $tablefield->Field );
				$tablefield_type_lowercased  = strtolower( $tablefield->Type );

				$tablefield_type_without_parentheses = preg_replace(
					'/'
					. '(.+)'       // Field type, e.g. `int`.
					. '\(\d*\)'    // Display width.
					. '(.*)'       // Optional attributes, e.g. `unsigned`.
					. '/',
					'$1$2',
					$tablefield_type_lowercased
				);

				// Get the type without attributes, e.g. `int`.
				$tablefield_type_base = strtok( $tablefield_type_without_parentheses, ' ' );

				// If the table field exists in the field array...
				if ( array_key_exists( $tablefield_field_lowercased, $cfields ) ) {

					// Get the field type from the query.
					preg_match( '|`?' . $tablefield->Field . '`? ([^ ]*( unsigned)?)|i', $cfields[ $tablefield_field_lowercased ], $matches );
					$fieldtype            = $matches[1];
					$fieldtype_lowercased = strtolower( $fieldtype );

					$fieldtype_without_parentheses = preg_replace(
						'/'
						. '(.+)'       // Field type, e.g. `int`.
						. '\(\d*\)'    // Display width.
						. '(.*)'       // Optional attributes, e.g. `unsigned`.
						. '/',
						'$1$2',
						$fieldtype_lowercased
					);

					// Get the type without attributes, e.g. `int`.
					$fieldtype_base = strtok( $fieldtype_without_parentheses, ' ' );

					// Is actual field type different from the field type in query?
					if ( $tablefield->Type !== $fieldtype ) {
						$do_change = true;
						if ( in_array( $fieldtype_lowercased, $text_fields, true ) && in_array( $tablefield_type_lowercased, $text_fields, true ) ) {
							if ( array_search( $fieldtype_lowercased, $text_fields, true ) < array_search( $tablefield_type_lowercased, $text_fields, true ) ) {
								$do_change = false;
							}
						}

						if ( in_array( $fieldtype_lowercased, $blob_fields, true ) && in_array( $tablefield_type_lowercased, $blob_fields, true ) ) {
							if ( array_search( $fieldtype_lowercased, $blob_fields, true ) < array_search( $tablefield_type_lowercased, $blob_fields, true ) ) {
								$do_change = false;
							}
						}

						if ( in_array( $fieldtype_base, $int_fields, true ) && in_array( $tablefield_type_base, $int_fields, true )
							&& $fieldtype_without_parentheses === $tablefield_type_without_parentheses
						) {
							if ( version_compare( $db_version, '8.0.17', '>=' )
								&& ! str_contains( $db_server_info, 'MariaDB' )
							) {
								$do_change = false;
							}
						}

						if ( $do_change ) {
							// Add a query to change the column type.
							$cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN `{$tablefield->Field}` " . $cfields[ $tablefield_field_lowercased ];

							$for_update[ $table . '.' . $tablefield->Field ] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
						}
					}

					// Get the default value from the array.
					if ( preg_match( "| DEFAULT '(.*?)'|i", $cfields[ $tablefield_field_lowercased ], $matches ) ) {
						$default_value = $matches[1];
						if ( $tablefield->Default !== $default_value ) {
							// Add a query to change the column's default value
							$cqueries[] = "ALTER TABLE {$table} ALTER COLUMN `{$tablefield->Field}` SET DEFAULT '{$default_value}'";

							$for_update[ $table . '.' . $tablefield->Field ] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
						}
					}

					// Remove the field from the array (so it's not added).
					unset( $cfields[ $tablefield_field_lowercased ] );
				} else {
					// This field exists in the table, but not in the creation queries?
				}
			}

			// For every remaining field specified for the table.
			foreach ( $cfields as $fieldname => $fielddef ) {
				// Push a query line into $cqueries that adds the field to that table.
				$cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";

				$for_update[ $table . '.' . $fieldname ] = 'Added column ' . $table . '.' . $fieldname;
			}

			// Index stuff goes here. Fetch the table index structure from the database.
			$tableindices = VisitorLog_DB_Base::$wpdb_vl->get_results( "SHOW INDEX FROM {$table};" );

			if ( $tableindices ) {
				// Clear the index array.
				$index_ary = array();

				// For every index in the table.
				foreach ( $tableindices as $tableindex ) {
					$keyname = strtolower( $tableindex->Key_name );

					// Add the index to the index data array.
					$index_ary[ $keyname ]['columns'][]  = array(
						'fieldname' => $tableindex->Column_name,
						'subpart'   => $tableindex->Sub_part,
					);
					$index_ary[ $keyname ]['unique']     = ( '0' === $tableindex->Non_unique ) ? true : false;
					$index_ary[ $keyname ]['index_type'] = $tableindex->Index_type;
				}

				// For each actual index in the index array.
				foreach ( $index_ary as $index_name => $index_data ) {

					// Build a create string to compare to the query.
					$index_string = '';
					if ( 'primary' === $index_name ) {
						$index_string .= 'PRIMARY ';
					} elseif ( $index_data['unique'] ) {
						$index_string .= 'UNIQUE ';
					}

					if ( 'FULLTEXT' === strtoupper( $index_data['index_type'] ) ) {
						$index_string .= 'FULLTEXT ';
					}

					if ( 'SPATIAL' === strtoupper( $index_data['index_type'] ) ) {
						$index_string .= 'SPATIAL ';
					}

					$index_string .= 'KEY ';
					if ( 'primary' !== $index_name ) {
						$index_string .= '`' . $index_name . '`';
					}

					$index_columns = '';

					// For each column in the index.
					foreach ( $index_data['columns'] as $column_data ) {
						if ( '' !== $index_columns ) {
							$index_columns .= ',';
						}

						// Add the field to the column list string.
						$index_columns .= '`' . $column_data['fieldname'] . '`';
					}

					// Add the column list to the index create string.
					$index_string .= " ($index_columns)";

					// Check if the index definition exists, ignoring subparts.
					$aindex = array_search( $index_string, $indices_without_subparts, true );
					if ( false !== $aindex ) {
						// If the index already exists (even with different subparts), we don't need to create it.
						unset( $indices_without_subparts[ $aindex ] );
						unset( $indices[ $aindex ] );
					}
				}
			}

			// For every remaining index specified for the table.
			foreach ( (array) $indices as $index ) {
				// Push a query line into $cqueries that adds the index to that table.
				$cqueries[] = "ALTER TABLE {$table} ADD $index";

				$for_update[] = 'Added index ' . $table . ' ' . $index;
			}

			// Remove the original table creation query from processing.
			unset( $cqueries[ $table ], $for_update[ $table ] );
		}

		$allqueries = array_merge( $cqueries, $iqueries );
		if ( $execute ) {
			foreach ( $allqueries as $query ) {
				VisitorLog_DB_Base::$wpdb_vl->query( $query );
			}
		}
		return $for_update;

	} // END func


} // END class