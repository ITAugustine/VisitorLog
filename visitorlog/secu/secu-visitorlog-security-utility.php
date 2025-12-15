<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of the VisitorLog_Security_Utility class
 *
 * @method public    static function get_class_name()
 * @method public    static function is_multisite_install()
 * @method public    static function get_blog_ids()
 * @method public    static function explode_trim_filter_empty()
 * @method protected static function multiexplode()
 * @method public    static function get_current_page_url()
 * @method public    static function redirect_to_url()
 * @method public    static function generate_alpha_numeric_random_string()
 * @method public    static function set_cookie_value()
 * @method public    static function get_cookie_value()
 * @method public    static function write_file_config()
 * @method public    static function write_file_config_debug()
 * @method public    static function check_file_config_debug()
 * @method public    static function update_file_config_debug()
 * @method public    static function disable_enable_file_edit()
 * @method public    static function disable_file_edits()
 * @method public    static function enable_file_edits()
 * @method public    static function delete_expired_captcha_transients()
 * @method protected static function block_fake_googlebots()
 * @method public    static function checking_data_entry()
 * @method public    static function register_secret_field()
 */
class VisitorLog_Security_Utility
{ 
    public static function get_class_name()
    { 
        return __CLASS__;
    }

    /**
     * Checks if installation is multisite
     * @return type
     */
    public static function is_multisite_install()
    {
        return function_exists( 'is_multisite' ) && is_multisite();

    } // END func

    /**
     * Returns an array of blog_ids for a multisite install
     * 
     * @return array or empty array if not multisite
     */
    public static function get_blog_ids()
    {
        if ( self::is_multisite_install() ) {
            $blog_ids = VisitorLog_DB_Base::$wpdb_vl->get_col( "SELECT blog_id FROM " . VisitorLog_DB_Base::$wpdb_vl->prefix . "blogs" );
        } else {
            $blog_ids = array();
        }

        return $blog_ids;

    } // END func

    /**
     * Explode $string with $delimiter, trim all lines and filter out empty ones.
     *
     * @param string $string
     * @param string $delimiter
     * @return array
     */
    public static function explode_trim_filter_empty( $string, $delimiter = PHP_EOL )
    {
        $explode = self::multiexplode( array(",", " ", ":", PHP_EOL), $string );
        $result  = array_filter( array_map( 'trim', $explode ), 'strlen' );
        return $result;

    } // END func

    /**
     * Work function.
     */
    protected static function multiexplode( $delimiters, $string )
    {
        $ready  = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
        
    } // END func

    /**
     * Returns the current URL
     * 
     * @return string
     */
    public static function get_current_page_url()
    {
        if ( !isset($_SERVER["SERVER_NAME"]) ) return '';

        $server_name = sanitize_text_field(wp_unslash($_SERVER["SERVER_NAME"]));

        if ( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" ) {
            $http = "https://";
        } else {
            $http = "http://";
        }
        if ( isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] != "80" ) {
            $port = ':' . sanitize_text_field(wp_unslash($_SERVER["SERVER_PORT"]));
        } else {
            $port = '';
        }    
        if ( isset($_SERVER["REQUEST_URI"]) ) {
            $uri = sanitize_text_field(wp_unslash($_SERVER["REQUEST_URI"]));
        } else {
            $uri = '';
        } 

        $page_url = $http . $server_name . $port . $uri;
        return $page_url;

    } // END func

    /**
     * Redirects to specified URL
     * 
     * @param type $url
     * @param type $delay
     * @param type $exit
     */
    public static function redirect_to_url( $url, $delay = '0', $exit = '1' )
    {
        if ( empty($url) ) {
            echo "<br /><strong>Error! The URL value is empty. Please specify a correct URL value to redirect to!</strong>";
            exit;
        }
        if ( !headers_sent() ) {
            header('Location: ' . $url);
        } else {
            echo '<meta http-equiv="refresh" content="' . esc_html($delay) . ';url=' . esc_url($url) . '" />';
        }
        if ( $exit == '1' ) {
            exit;
        }

    } // END func

    /**
     * Generates a random alpha-numeric number
     *
     * @param  type    $string_length
     * @return string
     */
    public static function generate_alpha_numeric_random_string( $string_length )
    {
        //Charecters present in table prefix
        $allowed_chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = '';
        //Generate random string
        for ( $i = 0; $i < $string_length; $i++ ) {
            $string .= $allowed_chars[ wp_rand( 0, strlen( $allowed_chars ) - 1 ) ];
        }
        return $string;

    } // END func

    /**
     * Sets cookie
     * @param type $cookie_name
     * @param type $cookie_value
     * @param type $expiry_seconds
     * @param type $path
     * @param string $cookie_domain
     */
    public static function set_cookie_value( $cookie_name, $cookie_value, $expiry_seconds = 86400, $path = '/', $cookie_domain = '' )
    {
        $expiry_time = time() + intval( $expiry_seconds );
        if ( empty( $cookie_domain ) ) {
            $cookie_domain = COOKIE_DOMAIN;
        }
        setcookie( $cookie_name, $cookie_value, $expiry_time, $path, $cookie_domain );

    } // END func
    
    /**
     * Gets cookie
     * @param type $cookie_name
     * @return string
     */
    public static function get_cookie_value( $cookie_name )
    {
        if ( isset($_COOKIE[$cookie_name]) ) {
            return sanitize_text_field(wp_unslash($_COOKIE[$cookie_name]));
        }
        return "";

    } // END func


    public static function write_file_config( $snippet )
    {
        $config_file = VISITORLOG_HOME_DIR . 'wp-config.php';
        $config_contents = file( $config_file );
        if ( false === $config_contents ) {
            VisitorLog_System_Check::update_data_of_system_check( 18, '' );
            $err = 'write_file_config(): Can\'t read the file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            return -1;
        }  

        $i = 0;
        $n = -99;
        foreach ( $config_contents as $line_num => $line ) {

            $new_config_contents[$i] = $line;
            if ( strpos($line, "define( 'ABSPATH'") ) $n = $i+1;
            if ( $i == $n ) {
                $i++;
                $new_config_contents[$i] = $snippet;
            }
            $i++;
        }
        $put_content = VisitorLog_Utility_File::wp_put_contents( $config_file, $new_config_contents );
        if ( ! $put_content ) {
            VisitorLog_System_Check::update_data_of_system_check( 20, '' );
            $err = 'write_file_config(): Can\'t write to a file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            return -2;
        }

        VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
        VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
        return true;

    } // END func

    public static function write_file_config_debug( $param )
    {
        $config_file = VISITORLOG_HOME_DIR . 'wp-config.php';
        $config_contents = file( $config_file );
        if ( false === $config_contents ) {
            VisitorLog_System_Check::update_data_of_system_check( 18, '' );
            $err = 'write_file_config_debug(): Can\'t read the file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        }  

        $param['debug']         = '';
        $param['debug_log']     = '';
        $param['debug_display'] = '';

        $i = 0;
        $n = -99;
        foreach ( $config_contents as $line_num => $line ) {

            $new_config_contents[$i] = $line;
            if ( strpos($line, "define( 'ABSPATH'") ) $n = $i+1;
            if ( $i == $n ) {
                $i++;
                $new_config_contents[$i] = $snippet;
            }
            $i++;
        }

        $put_content = VisitorLog_Utility_File::wp_put_contents( $config_file, $new_config_contents );
        if ( ! $put_content ) {
            VisitorLog_System_Check::update_data_of_system_check( 20, '' );
            $err = 'write_file_config_debug(): Can\'t write to a file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        }

        VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
        VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
        return true;

    } // END func

    public static function check_file_config_debug()
    {
        $config_file = VISITORLOG_HOME_DIR . 'wp-config.php';

        $config_contents = file( $config_file );
        if ( false === $config_contents ) {
            VisitorLog_System_Check::update_data_of_system_check( 18, '' );
            $err = 'check_file_config_debug(): Can\'t read the file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        }    

        $result_check['debug']         = 'false';
        $result_check['debug_log']     = 'false';
        $result_check['debug_display'] = 'false';

        $result_check['debug_num']         = '';
        $result_check['debug_log_num']     = '';
        $result_check['debug_display_num'] = '';

        foreach ( $config_contents as $num => $line ) {

            if ( strpos ($line, 'WP_DEBUG_LOG') ) {
                if ( strpos ($line, 'true') ) $result_check['debug_log'] = 'true';
                else                          $result_check['debug_log'] = 'false';
                $result_check['debug_log_num'] = $num;
                continue;
            }
            if ( strpos ($line, 'WP_DEBUG_DISPLAY') ) {
                if ( strpos ($line, 'true') ) $result_check['debug_display'] = 'true';
                else                          $result_check['debug_display'] = 'false';
                $result_check['debug_display_num'] = $num;
                continue;
            }
            if ( strpos ($line, 'WP_DEBUG') ) {
                if ( strpos ($line, 'true') ) $result_check['debug'] = 'true';
                else                          $result_check['debug'] = 'false';
                $result_check['debug_num'] = $num;
            }

        } /* end foreach */

        VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
        return $result_check;

    } // END func   

    public static function update_file_config_debug( $param=array() )
    {
        $config_file = VISITORLOG_HOME_DIR . 'wp-config.php';

        $lines = file( $config_file );
        if ( false === $lines ) {
            VisitorLog_System_Check::update_data_of_system_check( 18, '' );
            $err = 'update_file_config_debug(): Can\'t read the file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        }   

        //Make a backup of the config file
        if ( ! VisitorLog_Utility_File::backup_and_rename_file( $config_file, 'wp-config' ) ) {
            $err = __( 'Failed to make a backup of the wp-config.php file. This operation will not go ahead', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err ); 
            return false;
        }

        $result_check = self::check_file_config_debug();

        $i     = $result_check['debug_num'];
        $i_dis = $result_check['debug_display_num'];
        $i_log = $result_check['debug_log_num'];

        if ( $result_check['debug'] != $param['debug'] ) {

            $str1 = "define( 'WP_DEBUG', " . $result_check['debug'] . " );";
            $str2 = "define( 'WP_DEBUG', " . $param['debug'] . " );";
            $lines[$i] = str_replace( $str1, $str2, $lines[$i] );
        } //----------

        if ( '' == $i_dis ) {
            if ( 'true' == $param['debug_display'] ) {

                $str = $lines[$i] . "define( 'WP_DEBUG_DISPLAY', true );" . PHP_EOL;
                $lines[$i] = str_replace( $lines[$i], $str, $lines[$i] );
            }
        } else {
            if ( $result_check['debug_display'] != $param['debug_display'] ) {

                $str1 = "define( 'WP_DEBUG_DISPLAY', " . $result_check['debug_display'] . " );";
                $str2 = "define( 'WP_DEBUG_DISPLAY', " . $param['debug_display'] . " );";
                $lines[$i_dis] = str_replace( $str1, $str2, $lines[$i_dis] );
            }
        } //----------

        if ( '' == $i_log ) {
            if ( 'true' == $param['debug_log'] ) {

                $str = $lines[$i] . "define( 'WP_DEBUG_LOG', true );" . PHP_EOL;
                $lines[$i] = str_replace( $lines[$i], $str, $lines[$i] );
            }
        } else {
            if ( $result_check['debug_log'] != $param['debug_log'] ) {

                $str1 = "define( 'WP_DEBUG_LOG', " . $result_check['debug_log'] . " );";
                $str2 = "define( 'WP_DEBUG_LOG', " . $param['debug_log'] . " );";
                $lines[$i_log] = str_replace( $str1, $str2, $lines[$i_log] );
            }
        } //----------

        $new_contents = '';
        foreach ( $lines as $line_num => $line ) {
            $new_contents .= $line;
        }

        $result = VisitorLog_Utility_File::wp_put_contents( $config_file, $new_contents );
        if ( false === $result ) {
            $err = 'update_file_config_debug(): Can\'t write to a file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            VisitorLog_System_Check::update_data_of_system_check( 20, '' );
            return false;
        }

        VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
        VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
        return true;

    } // END func 


    public static function disable_enable_file_edit( $mode )
    {
        $config_file = VISITORLOG_HOME_DIR . 'wp-config.php';
        $snippet_action = 'create';

        $config_contents = file( $config_file );
        if ( false === $config_contents ) {
            $err = 'disable_enable_file_edit(): Can\'t read the file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            VisitorLog_System_Check::update_data_of_system_check( 18, '' );
            return -10;
        }    

        if ( 'on' == $mode ) {
            $i = 0;
            foreach ( $config_contents as $line_num => $line ) {

                if ( strpos ($line, 'DISALLOW_FILE_EDIT' ) ) {
                    if ( strpos ($line, 'false' ) ) {
                        $line = str_replace( 'false', 'true', $line );
                        $snippet_action = 'edit';                        
                    } else {
                        $snippet_action = '';
                    }
                }    
                $new_config_contents[$i] = $line;
                $i++;
            }
            if ( 'edit' == $snippet_action ) {

                $result = VisitorLog_Utility_File::wp_put_contents( $config_file, $new_config_contents );
                if ( ! $result ) {
                    $err = 'disable_enable_file_edit(): Can\'t write to a file config.php';
                    VisitorLog_Logger::instance()->warning( $err );
                    VisitorLog_System_Check::update_data_of_system_check( 20, '' );
                    return -10;
                }
                return 10;
            }
            if ( 'create' == $snippet_action ) {
                $snippet  = ' ' . PHP_EOL;
                $snippet .= '//Disable File Edits' . PHP_EOL;
                $snippet .= 'define(\'DISALLOW_FILE_EDIT\', true);' . PHP_EOL;
                $snippet .= ' ' . PHP_EOL;

                $result = self::write_file_config( $snippet );
                if ( $result < 0 ) return $result;

                VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
                VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
                return 11;
            }

            VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
            VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
            return 12;
        } //-------------------------

        if ( '' == $mode ) {
            $i = 0;
            foreach ( $config_contents as $line_num => $line ) {

                if ( strpos($line, "DISALLOW_FILE_EDIT") ) {
                    if ( strpos($line, "true") ) {
                        $line = str_replace( 'true', 'false', $line );
                        $snippet_action = 'edit'; 
                    }
                }    
                $new_config_contents[$i] = $line;
                $i++; 
            }
            if ( 'edit' == $snippet_action ) {
                $result = VisitorLog_Utility_File::wp_put_contents( $config_file, $new_config_contents );
                if ( ! $result ) {
                    $err = 'disable_enable_file_edit(): Can\'t write to a file config.php';
                    VisitorLog_Logger::instance()->warning( $err );
                    VisitorLog_System_Check::update_data_of_system_check( 20, '' );
                    return -20;
                }
                VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
                VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
                return 20;
            }
            VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
            VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
            return 21;  
        }

    } // END func

    /**
     * Modifies the wp-config.php file to disable PHP file editing from the admin panel
     * This function will add the following code:
     * define('DISALLOW_FILE_EDIT', false);
     * 
     * NOTE: This function will firstly check if the above code already exists 
     * and it will modify the bool value, otherwise it will insert the code mentioned above
     * 
     * @return boolean
     */
    public static function disable_file_edits()
    {
        $edit_file_config_entry_exists = false;
        $config_file = VisitorLog_Utility_File::get_wp_config_file_path();

        //Get wp-config.php file contents so we can check if the "DISALLOW_FILE_EDIT" variable already exists
        $config_contents = file( $config_file );

        if ( false === $config_contents ) {
            $err = 'disable_file_edits(): Can\'t read the file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            VisitorLog_System_Check::update_data_of_system_check( 18, '' );
            return false;
        } 

        foreach ( $config_contents as $line_num => $line ) {

            if ( strpos($line, "'DISALLOW_FILE_EDIT', false") ) {
                $config_contents[$line_num] = str_replace('false', 'true', $line);
                $edit_file_config_entry_exists = true;
            } elseif ( strpos($line, "'DISALLOW_FILE_EDIT', true") ) {
                $edit_file_config_entry_exists = true;
                return true;
            }

            //For wp-config.php files originating from early WP versions we will remove the closing php tag
            if ( strpos($line, "?>") !== false ) {
                $config_contents[$line_num] = str_replace("?>", "", $line);
            }
        }

        if ( ! $edit_file_config_entry_exists ) {

            //Construct the config code which we will insert into wp-config.php
            $new_snippet  = '//Disable File Edits' . PHP_EOL;
            $new_snippet .= 'define(\'DISALLOW_FILE_EDIT\', true);';
            $config_contents[] = $new_snippet;   //Append the new snippet to the end of the array
        }

        //Make a backup of the config file
        if ( ! VisitorLog_Utility_File::backup_and_rename_file( $config_file, 'wp-config' ) ) {
            $err = __( 'Failed to make a backup of the wp-config.php file. This operation will not go ahead', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err ); 
            return false;
        }

        //Now let's modify the wp-config.php file
        if ( VisitorLog_Utility_File::wp_put_contents($config_file, $config_contents) ) {

            $msg = __( 'Settings Saved - Your system is now configured to not allow PHP file editing', 'visitorlog' );
            VisitorLog_System_View::show_message( 'green', $msg, $msg );
            VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
            VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
            return true;
        } else {
            $err = __( 'Disable PHP File Edit - Unable to modify wp-config.php', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err );
            VisitorLog_System_Check::update_data_of_system_check( 20, '' );
            return false;
        }

    } // END func

    /**
     * Modifies the wp-config.php file to allow PHP file editing from the admin panel
     * This func will modify the following code by replacing "true" with "false":
     * define('DISALLOW_FILE_EDIT', true);
     * 
     * @return boolean
     */
    public static function enable_file_edits()
    {
        $edit_file_config_entry_exists = false;

        //Config file path
        $config_file = VisitorLog_Utility_File::get_wp_config_file_path();

        //Get wp-config.php file contents
        $config_contents = file($config_file);
        if ( false === $config_contents ) {
            $err = 'enable_file_edits(): Can\'t read the file config.php';
            VisitorLog_Logger::instance()->warning( $err );
            VisitorLog_System_Check::update_data_of_system_check( 18, '' );
            return false;
        } 

        foreach ( $config_contents as $line_num => $line ) {

            if ( strpos($line, "'DISALLOW_FILE_EDIT', true") ) {

                $config_contents[$line_num] = str_replace('true', 'false', $line);
                $edit_file_config_entry_exists = true;
            } elseif ( strpos($line, "'DISALLOW_FILE_EDIT', false") ) {
                $edit_file_config_entry_exists = true;
                $msg = __( 'Your system config file is already configured to allow PHP file editing', 'visitorlog' );
                VisitorLog_System_View::show_message( 'blue', $msg, $msg );
                return true;
            }
        }
        if ( ! $edit_file_config_entry_exists ) {

            $msg = __( 'Your system config file is already configured to allow PHP file editing', 'visitorlog' );
            VisitorLog_System_View::show_message( 'blue', $msg, $msg );
            return true;
        } else {
            //Now let's modify the wp-config.php file
            if( VisitorLog_Utility_File::wp_put_contents( $config_file, $config_contents ) ) {

                $msg = __( 'Settings Saved - Your system is now configured to allow PHP file editing', 'visitorlog' );
                VisitorLog_System_View::show_message( 'green', $msg, $msg );                
                VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
                VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
                return true;
            } else {
                $err = __( 'Disable PHP File Edit - Unable to modify wp-config.php', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                VisitorLog_System_Check::update_data_of_system_check( 20, '' );
                return false;
            }
        }

    } // ENF func
    
    /**
     * Delete expired captcha info transients
     * 
     * Note: A unique instance these transients is created everytime the login page is loaded with captcha enabled
     * This function will help prune the options table of old expired entries.
     */
    public static function delete_expired_captcha_transients()
    {
        $current_unix_time = current_time( 'timestamp' );
        $tbl = VisitorLog_DB_Base::$wpdb_vl->prefix . 'options';
        $res = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $tbl WHERE `option_name` LIKE '%_transient_timeout_visitorlog_captcha_string_info%' AND `option_value` < %d", $current_unix_time), ARRAY_A );

        if( !empty( $res ) ) {
            foreach( $res as $item ) {
                $transient_name = str_replace( '_transient_timeout_', '', $item['option_name'] ); //extract transient name
                self::is_multisite_install() ? 
                    delete_site_transient( $transient_name ) : 
                    delete_transient( $transient_name );
            }
        }

    } // END func

    /**
     * Block fake googlebots
     *
     * @uses VisitorLog_Utility::get_option_sl()
     */
    protected static function block_fake_googlebots( $user_agent, $ip )
    {
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) ||
             '' == VisitorLog_Utility::get_option_sl( 'block_fake_googlebots' ) 
           ) return '';

        if ( ($user_agent == '' ) && ($ip == '' ) ) return ''; 

        $pattern = '/Googlebot/i';
        if ( preg_match($pattern, $user_agent, $matches) ) {
            $name    = gethostbyaddr($ip);
            $host_ip = gethostbyname($name);                //Reverse lookup - let's get the IP using the name

            if ( preg_match($pattern, $name, $matches) ) {

                if ( $host_ip == $ip ) {
                    //Genuine googlebot allow it through....
                    return true;
                } else {
                    //fake googlebot - block it!
                    return false;
                }
            } else {
                //fake googlebot - block it!
                return false;
            }
        }
        return '';
        
    } // END func

    /**
     * Checking data entry
     * 
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Security_Handler::instance()->validate_ip_list()
     */      
    public static function checking_data_entry( $textarea_addr, $textarea_name, $short_name, $comment )
    {
        if ( '' == $textarea_addr && '' ==  $textarea_name ) {
            $msg_err = __( 'One of the UserName or IP ADDRESS fields must be entered', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $msg_err );
            return 1;
        }
        if ( '' != $textarea_name ) {
            if ( strlen($textarea_name) > 150 ) {
                $msg_err = __( 'The limit on the number of characters has been exceeded', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $msg_err );
                return 2;
            } else {
                $textarea_name = sanitize_text_field( $textarea_name );
            }
        }
        if ( '' != $textarea_addr ) {
            if ( strlen($textarea_addr) > 50 ) {

                $msg_err = __( 'The limit on the number of characters has been exceeded', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $msg_err );
                return 4;
            } else {
                $ip_list_array = preg_split("/\R/", $textarea_addr);
                if ( !empty( $ip_list_array ) ) {
                    $payload = VisitorLog_Security_Handler::instance()->validate_ip_list( $ip_list_array, 'ban_your_ip' );
                    if( $payload[0] != 1 ) {
                        $msg_err = $payload[1][0];
                        VisitorLog_System_View::show_message( 'orange', $msg_err, $msg_err );
                        return 5;
                    }
                } else {
                    $msg_err = __( 'Check that the IP address is entered correctly', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $msg_err, $msg_err );
                    return 6;
                }
            }
        }
        if ( '' != $short_name ) {
            if ( strlen($short_name) > 10 ) {

                $msg_err = __( 'The limit on the number of characters has been exceeded', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $msg_err );
                return 7;
            } else {
                $short_name = sanitize_text_field( $short_name );
            }
        }
        if ( '' != $comment ) {
            if ( strlen($comment) > 150 ) {

                $msg_err = __( 'The limit on the number of characters has been exceeded', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $msg_err );
                return 8;
            } else {
                $comment = sanitize_text_field( $comment );
            }
        }
        return '';

    } // END func  

    /**
     * A shortcode for registering a secret field
     *
     * @param string $name   
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::generate_alpha_numeric_random_string()
     */
    public static function register_secret_field( $name )
    {
        $code = current_time( 'timestamp' );
        $s1 = VisitorLog_Utility::generate_alpha_numeric_random_string(1);
        $s2 = VisitorLog_Utility::generate_alpha_numeric_random_string(3);
        $code1 = mb_substr( $code, 0, 6);
        $code2 = mb_substr( $code, 6, 4);
        $code3 = $s1 . $code2 . $code1 . $s2;

        echo '<p style="display: none;"><label>';
        echo esc_html(__('Enter something special:','visitorlog'));
        echo '</label><input name="' . esc_html($name) . '" type="text" id="';
        echo esc_html($name) . '" value="' . esc_html($code3) . '" /></p>';

    } // END func 


} // END class
