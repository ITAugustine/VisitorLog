<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_System_Check class
 *
 * @method public    static function get_data_of_system_check()
 * @method public    static function update_data_of_system_check()
 * @method public    static function handler_system_check_common()
 * @method protected static function kmg()
 * @method public    static function show_error_system_message()
 * @method public    static function get_filesystem_method()
 */
class VisitorLog_System_Check
{
    /**
     * update data of system check.
     *
     * @uses VisitorLog_Utility::get_option_sl()
     */
    public static function get_data_of_system_check()
    {
        $string = VisitorLog_Utility::get_option_sl( 'check_sys_msg' );
        $array = explode( ';', $string );

        return $array;

    } // END func

    /**
     * update data of system check.
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::update_option_sl()
     */
    public static function update_data_of_system_check( $key, $value )
    {
        $string = VisitorLog_Utility::get_option_sl( 'check_sys_msg' );

        $array = explode( ';', $string );
        $array[$key] = $value;
        $string = implode( ';', $array );

        VisitorLog_Utility::update_option_sl( 'check_sys_msg', $string );

    } // END func

	/**
	 * handler system check common.
	 *
	 * @uses VisitorLog_Server_Information_Handler::get_php_safe_mode()
	 * @uses VisitorLog_DB_Base::instance()->get_my_sql_version()
	 * @uses VisitorLog_Utility::update_option_sl()
	 *
	 * @return array $result
	 */
	public static function handler_system_check_common()
    {
        $string = VisitorLog_Utility::get_option_sl( 'check_sys_msg' );
        $result = explode( ';', $string );

    //-----------------WordPress---------
        
/* 00 WordPress Version */  
        global $wp_version;
		if ( version_compare( $wp_version, '3.6', '>=' ) ) {
            $result[0] = 1;
        } else {
            $result[0] = '';
        }
/* 01 WordPress Memory Limit */
        if ( self::kmg( WP_MEMORY_LIMIT ) >= 64000 ) {
            $result[1] = 1;
        } else {
            $result[1] = '';
        }
/* 02 MultiSite Disabled */
        if ( !is_multisite() ) {
            $result[2] = 1;          
        } else {
            $result[2] = '';
        }
/* 03 FileSystem Method */
        if ( 'direct' == self::get_filesystem_method() ) {
            $result[3] = 1;
        } else {
            $result[3] = ''; 
        }

    //-----------------PHP----------

/* 04 PHP Version */ 
        if ( version_compare( phpversion(), '7.4', '>=' ) ) {
            $result[4] = 1;
        } else {
            $result[4] = '';
        }
/* 05 PHP Safe Mode Disabled */ 
        if ( true === VisitorLog_Server_Information_Handler::get_php_safe_mode() ) {
            $result[5] = 1;
        } else {
            $result[5] = '';
        }
/* 06 PHP Max Execution Time */ 
        if ( ini_get( 'max_execution_time' ) >= 30 ) {
            $result[6] = 1;
        } else {
            $result[6] = '';
        }
/* 07 PHP Max Input Time */ 
        $col07 = ini_get( 'max_input_time' ); 
        if ( -1 == $col07 ) $col07 = 300; 
        if ( 0  == $col07 ) $col07 = 999999;      
        if ( $col07 >= 30 ) {
            $result[7] = 1;
        } else {
            $result[7] = '';
        }
/* 08 PHP Memory Limit */ 
        $col08_1 = ini_get( 'memory_limit' ); 
        if ( -1 == $col08_1 || 0  == $col08_1 ) {
            $col08 = 999999999;
        } else {
            $col08 = self::kmg($col08_1);            
        }  
        if ( $col08 >= 128000 ) {
            $result[8] = 1;
        } else {
            $result[8] = '';
        }
/* 09 PCRE Backtracking Limit */ 
        if ( ini_get( 'pcre.backtrack_limit' ) >= 10000 ) {
            $result[9] = 1;
        } else {
            $result[9] = '';
        }
/* 10 PHP Upload Max Filesize */
        if ( self::kmg( ini_get( 'upload_max_filesize' ) ) >= 2000 ) {
            $result[10] = 1;
        } else {
            $result[10] = '';
        }
/* 11 PHP Post Max Size */ 
        if ( self::kmg( ini_get( 'post_max_size' ) ) >= 2000 ) {
            $result[11] = 1;
        } else {
            $result[11] = '';
        }
/* 12 SSL Extension Enabled */ 
        global $visitorlog_users_resident_parameters;
        $ip = $visitorlog_users_resident_parameters['ip'];
        if ( is_ssl() ) {
			$result[12] = 1;
        } else {
            if (substr($ip, 0, 4) == '127.' || $ip == '::1') {
				$result[12] = 1;
            } else {
				$result[12] = '';
            }
        }
/* 13 cURL Extension Enabled */ 
        if ( function_exists('curl_version') ) {
            $result[13] = 1;
        } else {
            $result[13] = '';
        }
/* 14 cURL Timeout */ 
        if ( ini_get( 'default_socket_timeout' ) >= 60 ) {
            $result[14] = 1;
        } else {
            $result[14] = '';
        }
/* 15 cURL Version */ 
        $curlversion = curl_version();
        if ( version_compare( $curlversion['version'], '7.18.1', '>=' ) ) {
            $result[15] = 1;
        } else {
            $result[15] = '';
        }
/* 16 cURL SSL Version */ 
        $curlversion = curl_version();
        if ( version_compare( $curlversion['ssl_version'], 'OpenSSL/0.9.8l', '>=' ) ) {
            $result[16] = 1;
        } else {
            $result[16] = '';
        }

    //----------------MySQL-------------------

/* 17 MySQL Version */ 
        $sql_version = VisitorLog_DB_Base::instance()->get_my_sql_version();
        if ( false === strpos($sql_version, 'MariaDB') ) {
            if ( version_compare( $sql_version, 5.7, '>=' ) ) {
                $result[17] = 1;
            } else {
                $result[17] = '';
            }
        } else {
            if ( version_compare( $sql_version, 10.4, '>=' ) ) {
                $result[17] = 1;
            } else {
                $result[17] = '';
            }
        }

		$string = implode( ';', $result );
		VisitorLog_Utility::update_option_sl( 'check_sys_msg', $string );

		return $result;

	} // END func

    protected static function kmg( $value )
    {
        $cut_value = substr($value, 0, -1);
        $last      = substr($value, -1);  

        switch ( $last ) {
            case 'K':
                $cut_value_out = $cut_value;
                break;
            case 'M':
                $cut_value_out = $cut_value * 1000;
                break;
            case 'G':
                $cut_value_out = $cut_value * 1000000;
                break;
            default:
                $cut_value_out = 0;
        }
        return $cut_value_out;

    } // END func

	/**
	 * Method show error system message
	 *
	 * @return ''/$j and print screen of message 
	 */
	public static function show_error_system_message()
    {
		$string = VisitorLog_Utility::get_option_sl( 'check_sys_msg' );
		$array = explode( ';', $string );
        $check_count = count($array);

        $array_sum = 0;
        foreach ($array as $arr) {
           if ( 1 == $arr ) $array_sum++;
        }

        if ( 0 == $check_count ) return '';
        if ( $check_count == $array_sum ) return '';

		$count = $check_count - $array_sum;
		if ( '' == VisitorLog_Utility::get_option_sl( 'admin_notices' ) ) return $count;

		$error_system_message = array(
			// WordPress	
/*00*/		__( 'The WordPress version is less than the required one', 'visitorlog' ),
/*01*/		__( 'The memory limit for WordPress is less than required', 'visitorlog' ),
/*02*/      __( 'Plugin is not designed nor fully tested on WordPress Multisite installations. Various features may not work properly. We highly recommend installing it on a single site installation', 'visitorlog' ),
/*03*/		__( 'The file system method is not equal to direct', 'visitorlog' ),
			// PHP
/*04*/		sprintf(	
            /* translators: 1: PHP version, 2: your host, 3: link. */
			__( 'Your server is currently running PHP version %1$s. Please upgrade your server to at least 7.4, requirements WordPress. You can find a template email to send your host %2$s here %3$s', 'visitorlog' ), phpversion(), '<a href="https://wordpress.org/about/requirements/" target="_blank">', '</a>'), 
/*05*/		__( 'PHP Safe mode is enabled', 'visitorlog' ),
/*06*/		__( 'The maximum PHP execution time is less than required', 'visitorlog' ),
/*07*/		__( 'The maximum input time in PHP is less than required', 'visitorlog' ),
/*08*/		__( 'PHP memory limit is less than required', 'visitorlog' ),
/*09*/		__( 'The PCRE backtracking limit is less than required', 'visitorlog' ),
/*10*/		__( 'The maximum size of the uploaded file in PHP is less than the required one', 'visitorlog' ),
/*11*/		__( 'The maximum PHP record size is less than required', 'visitorlog' ),
/*12*/		__( 'SSL extension is disabled', 'visitorlog' ),
/*13*/		__( 'cURL is off', 'visitorlog' ),
/*14*/		__( 'cURL Timeout is less than required', 'visitorlog' ),
/*15*/		__( 'cURL Version is less than required', 'visitorlog' ),
/*16*/		__( 'cURL SSL Version is less than required', 'visitorlog' ),
			// MySQL
/*17*/		__( 'MySQL version is less than required', 'visitorlog' ),
            // htaccess
/*18*/      __( 'File reading error', 'visitorlog' ) . ' config.php',
            // config.php
/*19*/      __( 'File reading error', 'visitorlog' ) . ' .htaccess',
            // htaccess
/*20*/      __( 'Error writing to the file', 'visitorlog' ) . ' config.php',
            // config.php
/*21*/      __( 'Error writing to the file', 'visitorlog' ) . ' .htaccess',
            // Maintenance
/*22*/      __( 'Maintenance - on', 'visitorlog' ),
		);

		$i = 0;
		foreach ( $array as $index ) {

			if ( '' == $index ) {
				$msg_err = $error_system_message[$i];
                VisitorLog_System_View::show_message( 'yellow', $msg_err );
			}
            $i++;
		}
		return $count;

	} // END func


    public static function get_filesystem_method( $args = array(), $context = '', $allow_relaxed_file_ownership = false )
    {
        // Please ensure that this is either 'direct', 'ssh2', 'ftpext', or 'ftpsockets'.
        $method = defined( 'FS_METHOD' ) ? FS_METHOD : false;

        if ( ! $context ) {
            $context = VISITORLOG_CONTENT_DIR;
        }

        // If the directory doesn't exist (wp-content/languages) then use the parent directory as we'll create it.
        if ( WP_LANG_DIR === $context && ! is_dir( $context ) ) {
            $context = dirname( $context );
        }
        $context = trailingslashit( $context );

        if ( ! $method ) {
            $temp_file_name = $context . 'temp-write-test-' . str_replace( '.', '-', uniqid( '', true ) );

            $put_contents = VisitorLog_Utility_File::wp_put_contents( $temp_file_name, ' ' );
            if ( $put_contents ) {
                // Attempt to determine the file owner of the WordPress files, and that of newly created files.
                $wp_file_owner   = false;
                $temp_file_owner = false;
                if ( function_exists( 'fileowner' ) ) {
                    $wp_file_owner   = @fileowner( __FILE__ );
                    $temp_file_owner = @fileowner( $temp_file_name );
                }

                if ( false !== $wp_file_owner && $wp_file_owner === $temp_file_owner ) {
                    $method = 'direct';
                } elseif ( $allow_relaxed_file_ownership ) {
                    $method = 'direct';
                }
                VisitorLog_Utility_File::wp_delete_file( $temp_file_name );
            }
        }

        if ( ! $method && isset( $args['connection_type'] ) && 'ssh' === $args['connection_type'] && extension_loaded( 'ssh2' ) ) {
            $method = 'ssh2';
        }
        if ( ! $method && extension_loaded( 'ftp' ) ) {
            $method = 'ftpext';
        }
        if ( ! $method && ( extension_loaded( 'sockets' ) || function_exists( 'fsockopen' ) ) ) {
            $method = 'ftpsockets'; // Sockets: Socket extension; PHP Mode: FSockopen / fwrite / fread.
        }
        return apply_filters( 'filesystem_method', $method, $args, $context, $allow_relaxed_file_ownership );

    } // END func


} // END class