<?php
/**
 * Server Information Page Handler
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

class VisitorLog_Server_Information_Handler
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Gets current Plugin version.
	 *
	 * @return mixed $currentVersion version number/
	 */
	public static function get_current_version()
	{
		$currentVersion = VISITORLOG_PLUGIN_VERSION;
		return $currentVersion;

	} // END func

	/**
	 * Gets new Plugin version.
	 *
	 * @return mixed $currentVersion version number/
	 */
	public static function get_new_version()
	{
		$newVersion = get_plugin_data( VISITORLOG_PLUGIN_FILE );
		return $newVersion['Version'];

	} // END func

	/**
	 * Gets current DB version.
	 *
	 * @return mixed $currentDBversion version number/
	 */
	public static function get_current_db_version()
	{
		$currentDBversion = VISITORLOG_DB_VERSION;
		return $currentDBversion;

	} // END func

	/**
	 * Gets new DB version.
	 *
	 * @return mixed $newDBversion version number/
	 */
	public static function get_new_db_version()
	{
		$newDBversion = VISITORLOG_DB_VERSION;
		return $newDBversion;

	} // END func

	/**
	 * Compares time.
	 *
	 * @param mixed $a first time to compare.
	 * @param mixed $b second time to compare.
	 */
	public static function time_compare( $a, $b )
	{
		if ( $a == $b ) return 0;
		return ( strtotime( $a['time'] ) > strtotime( $b['time'] ) ) ? - 1 : 1;

	} // END func

	/**
	 * Compares filesize.
	 *
	 * @param mixed $value1   First file size.
	 * @param mixed $value2   Second file size.
	 * @param null  $operator Comparison operator.
	 *
	 * @return mixed version_compare() value.
	 */
	public static function filesize_compare( $value1, $value2, $operator = null )
	{
		if ( false !== strpos( $value1, 'G' ) ) {
			$value1 = preg_replace( '/[A-Za-z]/', '', $value1 );
			$value1 = intval( $value1 ) * 1024;
		} else {
			$value1 = preg_replace( '/[A-Za-z]/', '', $value1 );
		}

		if ( false !== strpos( $value2, 'G' ) ) {
			$value2 = preg_replace( '/[A-Za-z]/', '', $value2 );
			$value2 = intval( $value2 ) * 1024;
		} else {
			$value2 = preg_replace( '/[A-Za-z]/', '', $value2 );
		}
		return version_compare( $value1, $value2, $operator );

	} // END func

	/**
	 * Compares cURL SSL Version.
	 *
	 * @param mixed $value    CURL SSL version number.
	 * @param null  $operator Comparison operator.
	 *
	 * @return mixed false|version
	 */
	public static function curlssl_compare( $value, $operator = null )
	{
		if ( isset( $value['version_number'] ) && defined( 'OPENSSL_VERSION_NUMBER' ) ) {
			return version_compare( OPENSSL_VERSION_NUMBER, $value['version_number'], $operator );
		}
		return false;

	} // END func

	/**
	 * Gets loaded PHP Extensions.
	 */
	public static function get_loaded_php_extensions()
	{
		$extensions = get_loaded_extensions();
		sort( $extensions );
		echo esc_html(implode( ', ', $extensions ));

	} // END func

	public static function get_loaded_php_extensions2()
	{
		$extensions = get_loaded_extensions();
		sort( $extensions );
		return implode( ', ', $extensions );

	} // END func

	/**
	 * Get SSL Support.
	 *
	 * @return mixed Open SSL Extentions loaded.
	 */
	public static function get_ssl_support()
	{
		return extension_loaded( 'openssl' );

	} // END func

	/**
	 * Get cURL Version.
	 *
	 * @return string cURL Version.
	 */
	public static function get_curl_support()
	{
		return function_exists( 'curl_version' );

	} // END func

	/**
	 * Get PHP Version.
	 *
	 * @return string phpversion().
	 */
	public static function get_php_version()
	{
		return phpversion();

	} // END func

	/**
	 * Get MAX_EXECUTION_TIME.
	 *
	 * @return string MAX_EXECUTION_TIME.
	 */
	public static function get_max_execution_time()
	{
		return ini_get( 'max_execution_time' );

	} // END func

	/**
	 * Get MAX_INPUT_TIME.
	 *
	 * @return string MAX_EXECUTION_TIME.
	 */
	public static function get_max_input_time()
	{
		return ini_get( 'max_input_time' );

	} // END func

	/**
	 * Get MySql Version.
	 *
	 * @uses VisitorLog_DB_Base::instance()->get_my_sql_version()
	 * @return string MySQL Version.
	 */
	public static function get_mysql_version()
	{
		return VisitorLog_DB_Base::instance()->get_my_sql_version();

	} // END func

	/**
	 * Get PHP_MEMORY_LIMIT.
	 *
	 * @return string PHP_MEMORY_LIMIT.
	 */
	public static function get_php_memory_limit()
	{
		return ini_get( 'memory_limit' );

	} // END func

	/**
	 * Get Host OS.
	 *
	 * @param  bool $return = false.
	 * @return mixed PHP_OS.
	 */
	public static function get_os( $return = false )
	{
		if ( $return ) {
			return PHP_OS;
		} else {
			echo esc_html(PHP_OS);
		}

	} // END func

	/**
	 * Get PHP_INT_SIZE * 8bit.
	 */
	public static function get_architecture()
	{
		echo esc_html(PHP_INT_SIZE) * 8
		?>
		&nbsp;bit
		<?php

	} // END func

	/**
	 * Get currently used memory.
	 */
	public static function memory_usage()
	{
		if ( function_exists( 'memory_get_usage' ) ) {
			$memory_usage = round( memory_get_usage() / 1024 / 1024, 2 ) . ' MB';
		} else {
			$memory_usage = 'N/A';
		}
		echo esc_html($memory_usage);

	} // END func

	public static function memory_usage2()
	{
		if ( function_exists( 'memory_get_usage' ) ) {
			$memory_usage = round( memory_get_usage() / 1024 / 1024, 2 ) . ' MB';
		} else {
			$memory_usage = 'N/A';
		}
		return $memory_usage;

	} // END func

	/**
	 * Get PHP Safe Mode.
	 *
	 * @return bool true|false.
	 */
	public static function get_php_safe_mode()
	{
		if ( version_compare( self::get_php_version(), '5.3.0' ) >= 0 ) return true;
		if ( ini_get( 'safe_mode' ) ) return false;

		return true;

	} // END func

	/**
	 * Get SQL Mode.
	 */
	public static function get_sql_mode()
	{
		$mysqlinfo = VisitorLog_DB_Base::$wpdb_vl->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
		if ( is_array( $mysqlinfo ) ) {
			$sql_mode = $mysqlinfo[0]->Value;
		}
		if ( empty( $sql_mode ) ) {
			$sql_mode = __( 'NOT SET', 'visitorlog' );
		}
		echo esc_html($sql_mode);

	} // END func

	public static function get_sql_mode2()
	{
		$mysqlinfo = VisitorLog_DB_Base::$wpdb_vl->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
		if ( is_array( $mysqlinfo ) ) {
			$sql_mode = $mysqlinfo[0]->Value;
		}
		if ( empty( $sql_mode ) ) {
			$sql_mode = esc_html__( 'NOT SET', 'visitorlog' );
		}
		return $sql_mode;

	} // END func

	/**
	 * Checks if PHP fopen is allowed.
	 */
	public static function get_php_allow_url_fopen()
	{
		if ( ini_get( 'allow_url_fopen' ) ) {
			$allow_url_fopen = __( 'YES', 'visitorlog' );
		} else {
			$allow_url_fopen = __( 'NO', 'visitorlog' );
		}
		echo esc_html($allow_url_fopen);

	} // END func

	public static function get_php_allow_url_fopen2()
	{
		if ( ini_get( 'allow_url_fopen' ) ) {
			$allow_url_fopen = esc_html__( 'YES', 'visitorlog' );
		} else {
			$allow_url_fopen = esc_html__( 'NO', 'visitorlog' );
		}
		return $allow_url_fopen;

	} // END func

	/**
	 * Check if PHP Exif is enabled.
	 */
	public static function get_php_exif()
	{
		if ( is_callable( 'exif_read_data' ) ) {
			$exif = __( 'YES', 'visitorlog' ) . ' ( V' . substr( phpversion( 'exif' ), 0, 4 ) . ')';
		} else {
			$exif = __( 'NO', 'visitorlog' );
		}
		echo esc_html($exif);

	} // END func

	public static function get_php_exif2()
	{
		if ( is_callable( 'exif_read_data' ) ) {
			$exif = __( 'YES', 'visitorlog' ) . ' ( V' . substr( phpversion( 'exif' ), 0, 4 ) . ')';
		} else {
			$exif = __( 'NO', 'visitorlog' );
		}
		return $exif;

	} // END func

	/**
	 * Check if iptcparse is enabled.
	 */
	public static function get_php_iptc()
	{
		if ( is_callable( 'iptcparse' ) ) {
			$iptc = __( 'YES', 'visitorlog' );
		} else {
			$iptc = __( 'NO', 'visitorlog' );
		}
		echo esc_html($iptc);

	} // END func

	public static function get_php_iptc2()
	{
		if ( is_callable( 'iptcparse' ) ) {
			$iptc = __( 'YES', 'visitorlog' );
		} else {
			$iptc = __( 'NO', 'visitorlog' );
		}
		return $iptc;

	} // END func

	/**
	 * Check if PHP XML Parser is enabled.
	 */
	public static function get_php_xml()
	{
		if ( is_callable( 'xml_parser_create' ) ) {
			$xml = __( 'YES', 'visitorlog' );
		} else {
			$xml = __( 'NO', 'visitorlog' );
		}
		echo esc_html($xml);

	} // END func

	public static function get_php_xml2()
	{
		if ( is_callable( 'xml_parser_create' ) ) {
			$xml = __( 'YES', 'visitorlog' );
		} else {
			$xml = __( 'NO', 'visitorlog' );
		}
		return $xml;

	} // END func

	/**
	 * Get server gateway interface.
	 */
	public static function get_server_gateway_interface()
	{
		echo isset( $_SERVER['GATEWAY_INTERFACE'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['GATEWAY_INTERFACE'] ) ) ) : '';

	} // END func

	/**
	 * Get server IP address.
	 */
	public static function get_server_ip( $return = false )
	{
		if ( $return ) {
			return isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';
		} else {	
			echo isset( $_SERVER['SERVER_ADDR'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) ) : '';
		}

	} // END func

	/**
	 * Get server name.
	 *
	 * @param  bool   $return = false.
	 * @return string $_SERVER['SERVER_NAME'].
	 */
	public static function get_server_name( $return = false )
	{
		if ( $return ) {
			return isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
		} else {
			echo isset( $_SERVER['SERVER_NAME'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) ) : '';
		}

	} // END func

	/**
	 * Get server software.
	 *
	 * @param  bool   $return = false.
	 * @return string $_SERVER['SERVER_SOFTWARE'].
	 */
	public static function get_server_software( $return = false )
	{
		if ( $return ) {
			return isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
		} else {
			echo isset( $_SERVER['SERVER_SOFTWARE'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) ) : '';
		}

	} // END func

	/**
	 * Check if server software is apache.
	 *
	 * @param bool $return = false.
	 * @return bool True|false.
	 */
	public static function is_apache_server_software( $return = false )
	{
		$server = self::get_server_software( true );
		return ( false !== stripos( $server, 'apache' ) ) ? true : false;

	} // END func

	/**
	 * Gets server protocol.
	 */
	public static function get_server_protocol()
	{
		echo isset( $_SERVER['SERVER_PROTOCOL'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['SERVER_PROTOCOL'] ) ) ) : '';

	} // END func

	/**
	 * Gets server request time.
	 */
	public static function get_server_request_time()
	{
		echo isset( $_SERVER['REQUEST_TIME'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_TIME'] ) ) ) : '';

	} // END func


	/**
	 * Gets server HTTP accept.
	 */
	public static function get_server_http_accept()
	{
		echo isset( $_SERVER['HTTP_ACCEPT'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ) ) : '';

	} // END func

	/**
	 * Gets server accepted character set.
	 */
	public static function get_server_accept_charset()
	{
		echo ( ! isset( $_SERVER['HTTP_ACCEPT_CHARSET'] ) || ( '' == $_SERVER['HTTP_ACCEPT_CHARSET'] ) ) ? 'N/A' : esc_html( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_CHARSET'] ) ) );

	} // END func

	/**
	 * Gets HTTP host.
	 */
	public static function get_http_host()
	{
		echo isset( $_SERVER['HTTP_HOST'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : '';

	} // END func

	/**
	 * Gets complete URL.
	 */
	public static function get_complete_url()
	{
		echo isset( $_SERVER['HTTP_REFERER'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) ) : '';

	} // END func

	/**
	 * Gets user agent.
	 */
	public static function get_user_agent()
	{
		echo isset( $_SERVER['HTTP_USER_AGENT'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) : '';

	} // END func

	/**
	 * Checks if HTTPS is enabled.
	 */
	public static function get_https()
	{
		if ( isset( $_SERVER['HTTPS'] ) && '' !== $_SERVER['HTTPS'] ) {
			esc_html_e( 'ON', 'visitorlog' );
			echo ' - ';
			echo esc_html( sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) );
		} else {
			esc_html_e( 'OFF', 'visitorlog' );
		}

	} // END func

	public static function get_https2()
	{
		if ( isset( $_SERVER['HTTPS'] ) && '' !== $_SERVER['HTTPS'] ) {
			return __( 'ON', 'visitorlog' ) . ' - ' . sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) );
		} else {
			return __( 'OFF', 'visitorlog' );
		}

	} // END func

	/**
	 * Check if server self-connect is possible.
	 */
	public static function server_self_connect()
	{
		$url        = site_url( 'wp-cron.php' );
		$query_args = array( 'visitorlog_run' => 'test' );
		$url        = esc_url_raw( add_query_arg( $query_args, $url ) );

		/**
		 * Filter: https_local_ssl_verify
		 * Filters whether the server-self check shoul verify SSL Cert.
		 * @since Unknown
		 */
		$args        = array(
			'blocking'  => true,
			'sslverify' => apply_filters( 'https_local_ssl_verify', true ),
			'timeout'   => 15,
		);
		$response    = wp_remote_post( $url, $args );
		$test_result = '';
		if ( is_wp_error( $response ) ) {
			/* translators: 1: error. */
			$test_result .= sprintf( __( 'The HTTP response test get an error "%s"', 'visitorlog' ), $response->get_error_message() );
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 > $response_code && 204 < $response_code ) {
			/* translators: 1: http status. */
			$test_result .= sprintf( __( 'The HTTP response test get a false http status (%s)', 'visitorlog' ), wp_remote_retrieve_response_code( $response ) );
		} else {
			$response_body = wp_remote_retrieve_body( $response );
			if ( false === strstr( $response_body, 'VisitorLog Test' ) ) {
				/* translators: 1: response. */
				$test_result .= sprintf( __( 'Not expected HTTP response body: %s', 'visitorlog' ), esc_attr( wp_strip_all_tags( $response_body ) ) );
			}
		}
		if ( empty( $test_result ) ) {
			esc_html_e( 'Response Test O.K.', 'visitorlog' );
		} else {
			echo esc_html($test_result);
		}

	} // END func

	/**
	 * Gets server remote address.
	 */
	public static function get_remote_address()
	{
		echo isset( $_SERVER['REMOTE_ADDR'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) ) : '';

	} // END func

	/**
	 * Gets server remote host.
	 */
	public static function get_remote_host()
	{
		if ( ! isset( $_SERVER['REMOTE_HOST'] ) || ( '' === $_SERVER['REMOTE_HOST'] ) ) {
			'N/A';
		} else {
			echo isset( $_SERVER['REMOTE_HOST'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_HOST'] ) ) ) : '';
		}

	} // END func

	public static function get_remote_host2()
	{
		if ( ! isset( $_SERVER['REMOTE_HOST'] ) || ( '' === $_SERVER['REMOTE_HOST'] ) ) {
			return 'N/A';
		} else {
			return isset( $_SERVER['REMOTE_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_HOST'] ) ) : '';
		}

	} // END func

	/**
	 * Gets server remote port.
	 */
	public static function get_remote_port()
	{
		echo isset( $_SERVER['REMOTE_PORT'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_PORT'] ) ) ) : '';

	} // END func

	/**
	 * Gets server script filename.
	 */
	public static function get_script_file_name()
	{
		echo isset( $_SERVER['SCRIPT_FILENAME'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_FILENAME'] ) ) ) : '';

	} // END func

	/**
	 * Get server port.
	 */
	public static function get_server_port()
	{
		echo isset( $_SERVER['SERVER_PORT'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) ) : '';

	} // END func

	/**
	 * Get current page URI.
	 */
	public static function get_current_page_uri()
	{
		echo isset( $_SERVER['REQUEST_URI'] ) ? esc_html( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '';

	} // END func

	/**
	 * Get WP Root Path.
	 */
	public static function get_wp_root()
	{
		echo esc_html(get_home_path());

	} // END func


} // END func