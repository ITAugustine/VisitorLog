<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Utility_Htaccess class
 * 
 * @method public    static function get_class_name()
 * @method public    static function write_rules_to_htaccess()
 * @method public    static function getrules()
 * @method protected static function getrules_blacklist() 
 * @method protected static function getrules_bots_table()
 * @method protected static function getrules_event404()
 * @method protected static function getrules_pingback_htaccess() 
 * @method protected static function getrules_failed_of_login()
 * @method protected static function getrules_block_wp_file_access() 
 * @method protected static function getrules_spam_comment()
 * @method protected static function getrules_block_spambots()
 * @method protected static function getrules_firewall_g7()
 * @method protected static function getrules_firewall_g6()
 * @method protected static function getrules_basic_firewall()
 * @method protected static function getrules_xss_firewall()
 * @method protected static function return_regularized_url()
 * @method protected static function create_apache2_access_denied_rule()
 * @method protected static function add_netmask()
 * @method protected static function handler_ip_address()
 * @method protected static function handler_user_agents()
 */
class VisitorLog_Utility_Htaccess
{
    public static $ip_blacklist_marker_start = '# VISITOR_LOG_IP_BLACKLIST_START';
    public static $ip_blacklist_marker_end   = '# VISITOR_LOG_IP_BLACKLIST_END';

    public static $ip_bots_table_marker_start = '# VISITOR_LOG_IP_BOTS_START';
    public static $ip_bots_table_marker_end   = '# VISITOR_LOG_IP_BOTS_END';

    public static $user_agent_bots_table_marker_start = '# VISITOR_LOG_USER_AGENT_BOTS_START';
    public static $user_agent_bots_table_marker_end   = '# VISITOR_LOG_USER_AGENT_BOTS_END';

    public static $ip_event404_marker_start = '# VISITOR_LOG_IP_EVENT404_START';
    public static $ip_event404_marker_end   = '# VISITOR_LOG_IP_EVENT404_END';

    public static $user_agent_event404_marker_start = '# VISITOR_LOG_USER_AGENT_EVENT404_START';
    public static $user_agent_event404_marker_end   = '# VISITOR_LOG_USER_AGENT_EVENT404_END';

    public static $pingback_htaccess_rules_marker_start = '# VISITOR_LOG_PINGBACK_HTACCESS_RULES_START';
    public static $pingback_htaccess_rules_marker_end   = '# VISITOR_LOG_PINGBACK_HTACCESS_RULES_END';

    public static $ip_login_marker_start = '# VISITOR_LOG_IP_LOGIN_START';
    public static $ip_login_marker_end   = '# VISITOR_LOG_IP_LOGIN_END';

    public static $prevent_wp_file_access_marker_start = '# VISITOR_LOG_BLOCK_WP_FILE_ACCESS_START';
    public static $prevent_wp_file_access_marker_end   = '# VISITOR_LOG_BLOCK_WP_FILE_ACCESS_END';

    public static $ip_spam_contactform_marker_start = '# VISITOR_LOG_IP_SPAM_CONTACTFORM_START';
    public static $ip_spam_contactform_marker_end   = '# VISITOR_LOG_IP_SPAM_CONTACTFORM_END';

    public static $useragent_spam_contactform_start = '# VISITOR_LOG_USERAGENT_SPAM_CONTACTFORM_START';
    public static $useragent_spam_contactform_end   = '# VISITOR_LOG_USERAGENT_SPAM_CONTACTFORM_END';

    public static $ip_spam_comment_marker_start = '# VISITOR_LOG_IP_SPAM_COMMENT_START';
    public static $ip_spam_comment_marker_end   = '# VISITOR_LOG_IP_SPAM_COMMENT_END';

    public static $useragent_spam_comment_start = '# VISITOR_LOG_USERAGENT_SPAM_COMMENT_START';
    public static $useragent_spam_comment_end   = '# VISITOR_LOG_USERAGENT_SPAM_COMMENT_END';

    public static $block_spambots_marker_start = '# VISITOR_LOG_BLOCK_SPAMBOTS_START';
    public static $block_spambots_marker_end   = '# VISITOR_LOG_BLOCK_SPAMBOTS_END';

    public static $forbid_proxy_comments_marker_start = '# VISITOR_LOG_FORBID_PROXY_COMMENTS_START';
    public static $forbid_proxy_comments_marker_end   = '# VISITOR_LOG_FORBID_PROXY_COMMENTS_END';

    public static $firewall_seven_g_marker_start = '# VISITOR_LOG_SEVEN_G_START';
    public static $firewall_seven_g_marker_end   = '# VISITOR_LOG_SEVEN_G_END';

    public static $firewall_six_g_marker_start = '# VISITOR_LOG_SIX_G_START';
    public static $firewall_six_g_marker_end   = '# VISITOR_LOG_SIX_G_END';

    public static $basic_firewall_rules_marker_start = '# VISITOR_LOG_BASIC_FIREWALL_RULES_START';
    public static $basic_firewall_rules_marker_end   = '# VISITOR_LOG_BASIC_FIREWALL_RULES_END';

    public static $getrules_xss_firewall_marker_start = '# VISITOR_LOG_XSS_FIREWALL_RULES_START';
    public static $getrules_xss_firewall_marker_end   = '# VISITOR_LOG_XSS_FIREWALL_RULES_END';

    public static function get_class_name()
    {
        return __CLASS__;
    }

    /**
     * Write all active rules to .htaccess file.
     *
     * @return boolean True/false when success/failure.
     *
     * @uses VisitorLog_Utility_File::backup_and_rename_file()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_System_View::show_message()
     */
    public static function write_rules_to_htaccess()
    {
        global $wp_rewrite;

        if ( is_multisite() ) return;

        $htaccess_file = VISITORLOG_HOME_DIR . '.htaccess';

        if ( false === VisitorLog_Utility_File::backup_and_rename_file($htaccess_file, 'htaccess-') ) {
            $err = __( 'Failed to make a backup copy htaccess file', 'visitorlog' );
            VisitorLog_System_View::show_message( 'yellow', $err );
            VisitorLog_System_Check::update_data_of_system_check( 19, '' );
            return false;           
        } 
        if ( ! function_exists('got_mod_rewrite') || ! function_exists('insert_with_markers') ) {
            require_once VISITORLOG_HOME_DIR . 'wp-' . 'admin/includes/misc.php';
        }  
        if ( ! got_mod_rewrite() ) {
            $err = __( 'Unable to write to .htaccess - server type not supported', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err );
            VisitorLog_System_Check::update_data_of_system_check( 21, '' );
            return false; 
        }

        $is_writable = VisitorLog_Utility_File::is_writable( $htaccess_file );
        if ( ! $is_writable ) {
            $err = __( 'Unable to write to .htaccess - server type not supported', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err );
            VisitorLog_System_Check::update_data_of_system_check( 21, '' );
            return false; 
        }
        $rules = self::getrules();
        $rulesarray = explode( PHP_EOL, $rules );

        if ( ! insert_with_markers( $htaccess_file, 'VISITOR_LOG', $rulesarray ) ) {
            $err = __( 'Unable to write to .htaccess file', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err );
            VisitorLog_System_Check::update_data_of_system_check( 21, '' );
            return false; 
        }
        
        VisitorLog_System_Check::update_data_of_system_check( 19, 1 );
        VisitorLog_System_Check::update_data_of_system_check( 21, 1 );
        return true; 

    } // END func

    /**
     * Get rules
     *
     * @return array $rules 
     */
    public static function getrules()
    {
        $rules = "";

        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) ) {
            $rules .= self::getrules_blacklist();
            $rules .= self::getrules_bots_table();
            $rules .= self::getrules_event404();
            $rules .= self::getrules_pingback_htaccess();
        }
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) ) {
            $rules .= self::getrules_failed_of_login();
        }
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_file_system_protection' ) ) {
            $rules .= self::getrules_block_wp_file_access();
        }  
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_spam_protection' ) ) {
            $rules .= self::getrules_spam_comment();
            $rules .= self::getrules_block_spambots();
        }    
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_firewall' ) ) {
            $rules .= self::getrules_firewall_g7(); 
            $rules .= self::getrules_firewall_g6();
            $rules .= self::getrules_basic_firewall();
            $rules .= self::getrules_xss_firewall();
        }    
        return $rules;

    } // END func

    /**
     * Write rules in the file htaccess
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::get_data_for_htaccess()
     * @uses VisitorLog_Logger::instance()->debug()
     * @uses VisitorLog_Utility::get_server()
     *
     * @return write rules in the file htaccess 
     */
    protected static function getrules_blacklist()
    { 
        $rules = '';

        if ( '' == VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' ) ) return $rules;

        $result = VisitorLog_Utility::get_data_for_htaccess( 'lockdown_ip', $n_rows, $err_ );
        if ( $err_ ) {
            VisitorLog_Logger::instance()->debug( $err_ );
            return $rules;
        }

        $ip_list = $user_agents = array();
        $ip_last = $agent_last = '';
        $i = $j = 0;

        foreach ($result as $res) {

            if ( $res->user_ip != null && $res->user_ip != $ip_last ) {
                $ip_list[$i] = $res->user_ip;
                $ip_last = $res->user_ip;
                $i++;
            }
            if ( $res->user_name != null && $res->user_name != $agent_last ) {
                $user_agents[$j] = $res->user_name;
                $agent_last = $res->user_name;
                $j++;
            }
        }

        if ( 0 == $i && 0 == $j  ) return $rules;

        $server = VisitorLog_Utility::get_server( $full_server );
        $apache_or_litespeed = $server == 'apache' || $server == 'litespeed';
        $ips_with_netmask = self::add_netmask( array_unique($ip_list) ); 

        if ( !empty($ips_with_netmask) ) {

            $rules .= PHP_EOL . self::$ip_blacklist_marker_start . PHP_EOL; 

            if ( $apache_or_litespeed ) {
                // Apache or LiteSpeed webserver
                $server = substr($server, 7);
                if ( version_compare($server, '2.3') >= 0 ) {
                    // Apache 2.3 and newer
                    $rules .= "<IfModule mod_authz_core.c>" . PHP_EOL;
                    $rules .= "<RequireAll>" . PHP_EOL;
                    $rules .= "   Require all granted" . PHP_EOL;

                    foreach ($ips_with_netmask as $ip_with_netmask) {
                        $rules .= "   Require not ip " . $ip_with_netmask . PHP_EOL;
                    }
                    $rules .= "</RequireAll>" . PHP_EOL;
                    $rules .= "</IfModule>" . PHP_EOL;
                } else {
                    // Apache 2.2 and older
                    $rules .= "<IfModule !mod_authz_core.c>" . PHP_EOL;
                    $rules .= "   Order allow,deny" . PHP_EOL;
                    $rules .= "   Allow from all" . PHP_EOL;

                    foreach ($ips_with_netmask as $ip_with_netmask) {
                        $rules .= "   Deny from " . $ip_with_netmask . PHP_EOL;
                    }
                    $rules .= "</IfModule>" . PHP_EOL;
                }
            }
            else {
                // Nginx webserver
                foreach ($ips_with_netmask as $ip_with_netmask) {
                    $rules .= "\tdeny " . $ip_with_netmask . ";" . PHP_EOL;
                }
            }

            $rules .= self::$ip_blacklist_marker_end . PHP_EOL; 
        }

        return implode( PHP_EOL, array_diff(explode(PHP_EOL, $rules), array('Deny from ', 'Deny from')) );

    } // END func

    /**
     * Write rules in the file htaccess
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_Logger::instance()->debug()
     *
     * @return write rules in the file htaccess 
     */
    protected static function getrules_bots_table()
    { 
        $rules = '';

        if ( '' == VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' ) ) return $rules;

        $result = VisitorLog_Utility::get_data_for_htaccess( 'bots_table', $n_rows, $err_ );
        if ( $err_ ) {
            VisitorLog_Logger::instance()->debug( $err_ );
            return $rules;
        } 

        $ip_list = $user_agents = array();
        $ip_last = $agent_last = '';
        $i = $j = 0;

        foreach ($result as $res) {

            if ( $res->bot_ip != null && $res->bot_ip != $ip_last ) {
                $ip_list[$i] = $res->bot_ip;
                $ip_last = $res->bot_ip;
                $i++;
            }
            if ( $res->bot_name != null && $res->bot_name != $agent_last ) {
                $user_agents[$j] = $res->bot_name;
                $agent_last = $res->bot_name;
                $j++;
            }
        }

        if ( 0 == $i && 0 == $j  ) return $rules;

        $rules_ip = $rules_user_agents = '';

        $ips_with_netmask = self::add_netmask( array_unique($ip_list) ); 
 
        if ( !empty($ips_with_netmask) ) {
 
            $rules = self::handler_ip_address( $ips_with_netmask );

            if ( '' != $rules ) {
                $rules1 = PHP_EOL . self::$ip_bots_table_marker_start . PHP_EOL;
                $rules2 = self::$ip_bots_table_marker_end . PHP_EOL; 
                $rules_ip = $rules1 . $rules . $rules2;
            }            
        }

        if ( !empty($user_agents) ) {

            $rules = self::handler_user_agents( $user_agents );

            if ( '' != $rules ) {
                $rules1 = PHP_EOL . self::$user_agent_bots_table_marker_start . PHP_EOL;
                $rules2 = self::$user_agent_bots_table_marker_end . PHP_EOL;
                $rules_user_agents = $rules1 . $rules . $rules2;
            }
        }

        $rules = $rules_ip . $rules_user_agents;

        return implode( PHP_EOL, array_diff(explode(PHP_EOL, $rules), array('Deny from ', 'Deny from')) );

    } // END func


    /**
     * Write rules in the file htaccess
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_Logger::instance()->debug()
     *
     * @return write rules in the file htaccess 
     */
    protected static function getrules_event404()
    { 
        $rules = '';

        if ( '' == VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' ) )  return $rules;
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_404_logging' ) )    return $rules;

        $result = VisitorLog_Utility::get_data_for_htaccess( 'event_404', $n_rows, $err_ );
        if ( $err_ ) {
            VisitorLog_Logger::instance()->debug( $err_ );
            return $rules;
        }

        $ip_list = $user_agents = array();
        $ip_last = $agent_last = '';
        $i = $j = 0;

        foreach ($result as $res) {

            if ( $res->user_ip != null && $res->user_ip != $ip_last ) {
                $ip_list[$i] = $res->user_ip;
                $ip_last = $res->user_ip;
                $i++;
            }
            if ( $res->user_agent != null && $res->user_agent != $agent_last ) {
                $user_agents[$j] = $res->user_agent;
                $agent_last = $res->user_agent;
                $j++;
            }
        }

        if ( 0 == $i && 0 == $j  ) return $rules;

        $rules_ip = $rules_user_agents = '';

        $ips_with_netmask = self::add_netmask( array_unique($ip_list) ); 

        if ( !empty($ips_with_netmask) ) {
 
            $rules = self::handler_ip_address( $ips_with_netmask );

            if ( '' != $rules ) {
                $rules1 = PHP_EOL . self::$ip_event404_marker_start . PHP_EOL;
                $rules2 = self::$ip_event404_marker_end . PHP_EOL; 
                $rules_ip = $rules1 . $rules . $rules2;
            }            
        }

        if ( !empty($user_agents) ) {

            $rules = self::handler_user_agents( $user_agents );

            if ( '' != $rules ) {
                $rules1 = PHP_EOL . self::$user_agent_event404_marker_start . PHP_EOL;
                $rules2 = self::$user_agent_event404_marker_end . PHP_EOL;
                $rules_user_agents = $rules1 . $rules . $rules2;
            }
        }

        $rules = $rules_ip . $rules_user_agents;

        return implode( PHP_EOL, array_diff(explode(PHP_EOL, $rules), array('Deny from ', 'Deny from')) );

    } // END func


    public static function getrules_pingback_htaccess()
    {
        $rules = '';
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_block_xmlrpc' ) ) {

            $rules .= PHP_EOL . self::$pingback_htaccess_rules_marker_start . PHP_EOL;
            $rules .= self::create_apache2_access_denied_rule( 'xmlrpc.php' );
            $rules .= self::$pingback_htaccess_rules_marker_end . PHP_EOL;
        }
        return $rules;

    } // END of func

    /**
     * Write rules in the file htaccess
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::get_data_for_htaccess()
     * @uses VisitorLog_Logger::instance()->debug()
     * @uses VisitorLog_Utility::get_server()
     *
     * @return write rules in the file htaccess  
     */
    protected static function getrules_failed_of_login()
    { 
        $rules = '';

        if ( ''   == VisitorLog_Utility::get_option_sl( 'on_login_lockdown' )   ||
             'on' != VisitorLog_Utility::get_option_sl( 'use_htaccess_file_login' ) ) return $rules;

        $result = VisitorLog_Utility::get_data_for_htaccess( 'events_failed', $n_rows, $err_ ); 
        if ( $err_ ) {
            VisitorLog_Logger::instance()->debug( $err_ );
            return $rules;
        }

        if (empty($result)) return $rules;

        $ip_list[0] = 0;
        $ip_last = '';
        $i = 0;

        foreach ($result as $res) {
            if ( $res->user_ip != null && $res->user_ip != $ip_last ) {
                $ip_list[$i] = $res->user_ip;
                $ip_last = $res->user_ip;
                $i++;
            }
        }
        $ips_with_netmask = self::add_netmask( array_unique($ip_list) );

        if ( !empty($ips_with_netmask) ) {

            $rules .= PHP_EOL . self::$ip_login_marker_start . PHP_EOL; 

            $server = VisitorLog_Utility::get_server( $full_server );
            $apache_or_litespeed = $server == 'apache' || $server == 'litespeed';

            if ( $apache_or_litespeed ) {

                // Apache or LiteSpeed webserver
                // Apache 2.2 and older
                $rules .= "<IfModule !mod_authz_core.c>" . PHP_EOL;
                $rules .= "Order allow,deny" . PHP_EOL;
                $rules .= "Allow from all" . PHP_EOL;

                foreach ($ips_with_netmask as $ip_with_netmask) {
                    $rules .= "Deny from " . $ip_with_netmask . PHP_EOL;
                }
                $rules .= "</IfModule>" . PHP_EOL;

                // Apache 2.3 and newer
                $rules .= "<IfModule mod_authz_core.c>" . PHP_EOL;
                $rules .= "<RequireAll>" . PHP_EOL;
                $rules .= "Require all granted" . PHP_EOL;

                foreach ($ips_with_netmask as $ip_with_netmask) {
                    $rules .= "Require not ip " . $ip_with_netmask . PHP_EOL;
                }
                $rules .= "</RequireAll>" . PHP_EOL;
                $rules .= "</IfModule>" . PHP_EOL;
            }
            else {
                // Nginx webserver
                foreach ($ips_with_netmask as $ip_with_netmask) {
                    $rules .= "\tdeny " . $ip_with_netmask . ";" . PHP_EOL;
                }
            }

            $rules .= self::$ip_login_marker_end . PHP_EOL; 
        }

        return implode( PHP_EOL, array_diff(explode(PHP_EOL, $rules), array('Deny from ', 'Deny from')) );

    } // END func

    /**
     * This function will write rules to prevent people from accessing the following files:
     * readme.html, license.txt and wp-config-sample.php.
     *
     * @uses VisitorLog_Utility::get_option_sl()
     */
    protected static function getrules_block_wp_file_access()
    {
        $rules = '';
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_prevent_wp_file_access' ) ) {

            $rules .= PHP_EOL . self::$prevent_wp_file_access_marker_start . PHP_EOL; 
            $rules .= self::create_apache2_access_denied_rule('license.txt');
            $rules .= self::create_apache2_access_denied_rule('wp-config-sample.php');
            $rules .= self::create_apache2_access_denied_rule('readme.html');
            $rules .= self::$prevent_wp_file_access_marker_end . PHP_EOL; 
        }

        return $rules;

    } // END func

    /**
     * Write rules in the file htaccess
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::get_data_select() 
     * @uses VisitorLog_Logger::instance()->debug()
     *
     * @return write rules in the file htaccess
     */
    protected static function getrules_spam_comment()
    { 
        $rules = '';

        if ( ''   == VisitorLog_Utility::get_option_sl( 'on_block_spam_perm' ) )      return $rules;
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_autoblock_spam_ip' )  ||
             'on' == VisitorLog_Utility::get_option_sl( 'on_forbid_commentspost' ) ) {

            $result = VisitorLog_Utility::get_data_for_htaccess( 'spam', $n_rows, $err_ );
            if ( $err_ ) {
                VisitorLog_Logger::instance()->debug( $err_ );
                return $rules;
            }

            $rules_ip = '';
            $ip_last = '';
            $ip_list = array();
            $i = 0;

            foreach ($result as $res) {

                if ( $res->block == 'perm' ) {

                    if ( $res->author_ip != null && $res->author_ip != $ip_last ) {
                        $ip_list[$i] = $res->author_ip;
                        $ip_last = $res->author_ip;
                        $i++;
                    }
                }    
            }

            $ips_with_netmask = self::add_netmask( array_unique($ip_list) );

            if ( !empty($ips_with_netmask) ) {
     
                $rules = self::handler_ip_address( $ips_with_netmask );

                if ( '' != $rules ) {
                    $rules1 = PHP_EOL . self::$ip_spam_comment_marker_start . PHP_EOL;
                    $rules2 = self::$ip_spam_comment_marker_end . PHP_EOL; 
                    $rules_ip = $rules1 . $rules . $rules2;
                }            
            }

            $rules = $rules_ip;
            return implode( PHP_EOL, array_diff(explode(PHP_EOL, $rules), array('Deny from ', 'Deny from')) );
        }

        return $rules;           

    } // END of func

    /**
     * This function will write some directives to block all comments which do not originate from the blog's domain
     * OR if the user agent is empty. All blocked requests will be redirected to 127.0.0.1
     */
    protected static function getrules_block_spambots()
    {
        $rules = '';

        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_spambot_blocking' ) ) {

            $home_url = home_url();
            $url_string = self::return_regularized_url( $home_url );
            if ( $url_string == FALSE ) {
                $url_string = $home_url;
            }
            $rules .= PHP_EOL . self::$block_spambots_marker_start . PHP_EOL;
            $rules .= 
'<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} POST
    RewriteCond %{REQUEST_URI} ^(.*)?wp-comments-post\.php(.*)$
    RewriteCond %{HTTP_REFERER} !^' . $url_string . ' [NC,OR]
    RewriteCond %{HTTP_USER_AGENT} ^$
    RewriteRule .* http://127.0.0.1 [L]
</IfModule>' . PHP_EOL;

            $rules .= self::$block_spambots_marker_end . PHP_EOL;
        }

        if ( 'on' == VisitorLog_Utility::get_option_sl( 'forbid_proxy_comments' ) ) {

            $rules .= PHP_EOL . self::$forbid_proxy_comments_marker_start . PHP_EOL;
            $rules .= 
'<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} ^POST
    RewriteCond %{HTTP:VIA} !^$ [OR]
    RewriteCond %{HTTP:FORWARDED} !^$ [OR]
    RewriteCond %{HTTP:USERAGENT_VIA} !^$ [OR]
    RewriteCond %{HTTP:X_FORWARDED_FOR} !^$ [OR]
    RewriteCond %{HTTP:X_FORWARDED_HOST} !^$ [OR]
    RewriteCond %{HTTP:PROXY_CONNECTION} !^$ [OR]
    RewriteCond %{HTTP:XPROXY_CONNECTION} !^$ [OR]
    RewriteCond %{HTTP:HTTP_PC_REMOTE_ADDR} !^$ [OR]
    RewriteCond %{HTTP:HTTP_CLIENT_IP} !^$
    RewriteRule wp-comments-post\.php - [F]
</IfModule>' . PHP_EOL;

            $rules .= self::$forbid_proxy_comments_marker_end . PHP_EOL;
        }
        return $rules;

    } // END func

    /*
     * This function contains the rules for the 7G produced by Jeff Starr:
     * https://perishablepress.com/7g/
     */
    protected static function getrules_firewall_g7()
    {
        $rules = '';

        if ( '' == VisitorLog_Utility::get_option_sl( 'on_firewall_g7' ) ) return $rules;
           
        $rules .= PHP_EOL . self::$firewall_seven_g_marker_start . PHP_EOL;
        $rules .= 
'# 7G-SetEnvIf FIREWALL v1.6 20230411
# @ https://perishablepress.com/7g-firewall/
# SetEnvIf version by Tonkünstler-on-the-Bund
# 7G:[QUERY STRING]' . PHP_EOL;
        $rules .=
    'SetEnvIfExpr "%{QUERY_STRING} =~ m#([a-z0-9]{2000,})#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(/|%2f)(:|%3a)(/|%2f)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(order(\s|%20)by(\s|%20)1--)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(/|%2f)(\*|%2a)(\*|%2a)(/|%2f)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(ckfinder|fckeditor|fullclick)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(`|<|>|\^|\|\\|0x00|%00|%0d%0a)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#((.*)header:|(.*)set-cookie:(.*)=)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(localhost|127(\.|%2e)0(\.|%2e)0(\.|%2e)1)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(cmd|command)(=|%3d)(chdir|mkdir)(.*)(x20)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(globals|mosconfig([a-z_]{1,22})|request)(=|\[)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(/|%2f)((wp-)?config)((\.|%2e)inc)?((\.|%2e)php)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(thumbs?(_editor|open)?|tim(thumbs?)?)((\.|%2e)php)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(absolute_|base|root_)(dir|path)(=|%3d)(ftp|https?)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(s)?(ftp|inurl|php)(s)?(:(/|%2f|%u2215)(/|%2f|%u2215))#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(\.|20)(get|the)(_|%5f)(permalink|posts_page_url)(\(|%28)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#((boot|win)((\.|%2e)ini)|etc(/|%2f)passwd|self(/|%2f)environ)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(((/|%2f){3,3})|((\.|%2e){3,3})|((\.|%2e){2,2})(/|%2f|%u2215))#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(benchmark|char|exec|fopen|function|html)(.*)(\(|%28)(.*)(\)|%29)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(php)([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(e|%65|%45)(v|%76|%56)(a|%61|%31)(l|%6c|%4c)(.*)(\(|%28)(.*)(\)|%29)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(/|%2f)(=|%3d|$&|_mm|cgi(\.|-)|inurl(:|%3a)(/|%2f)|(mod|path)(=|%3d)(\.|%2e))#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(<|%3c)(.*)(e|%65|%45)(m|%6d|%4d)(b|%62|%42)(e|%65|%45)(d|%64|%44)(.*)(>|%3e)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(<|%3c)(.*)(i|%69|%49)(f|%66|%46)(r|%72|%52)(a|%61|%41)(m|%6d|%4d)(e|%65|%45)(.*)(>|%3e)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(<|%3c)(.*)(o|%4f|%6f)(b|%62|%42)(j|%4a|%6a)(e|%65|%45)(c|%63|%43)(t|%74|%54)(.*)(>|%3e)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(<|%3c)(.*)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(.*)(>|%3e)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(\+|%2b|%20)(d|%64|%44)(e|%65|%45)(l|%6c|%4c)(e|%65|%45)(t|%74|%54)(e|%65|%45)(\+|%2b|%20)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(\+|%2b|%20)(i|%69|%49)(n|%6e|%4e)(s|%73|%53)(e|%65|%45)(r|%72|%52)(t|%74|%54)(\+|%2b|%20)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(\+|%2b|%20)(s|%73|%53)(e|%65|%45)(l|%6c|%4c)(e|%65|%45)(c|%63|%43)(t|%74|%54)(\+|%2b|%20)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(\+|%2b|%20)(u|%75|%55)(p|%70|%50)(d|%64|%44)(a|%61|%41)(t|%74|%54)(e|%65|%45)(\+|%2b|%20)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(\\x00|(\"|%22|\'|%27)?0(\"|%22|\'|%27)?(=|%3d)(\"|%22|\'|%27)?0|cast(\(|%28)0x|or%201(=|%3d)1)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(g|%67|%47)(l|%6c|%4c)(o|%6f|%4f)(b|%62|%42)(a|%61|%41)(l|%6c|%4c)(s|%73|%53)(=|\[|%[0-9A-Z]{0,2})#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(_|%5f)(r|%72|%52)(e|%65|%45)(q|%71|%51)(u|%75|%55)(e|%65|%45)(s|%73|%53)(t|%74|%54)(=|\[|%[0-9A-Z]{2,})#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(j|%6a|%4a)(a|%61|%41)(v|%76|%56)(a|%61|%31)(s|%73|%53)(c|%63|%43)(r|%72|%52)(i|%69|%49)(p|%70|%50)(t|%74|%54)(:|%3a)(.*)(;|%3b|\)|%29)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(b|%62|%42)(a|%61|%41)(s|%73|%53)(e|%65|%45)(6|%36)(4|%34)(_|%5f)(e|%65|%45|d|%64|%44)(e|%65|%45|n|%6e|%4e)(c|%63|%43)(o|%6f|%4f)(d|%64|%44)(e|%65|%45)(.*)(\()(.*)(\))#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(@copy|\$_(files|get|post)|allow_url_(fopen|include)|auto_prepend_file|blexbot|browsersploit|(c99|php)shell|curl(_exec|test)|disable_functions?|document_root|elastix|encodeuricom|exploit|fclose|fgets|file_put_contents|fputs|fsbuff|fsockopen|gethostbyname|grablogin|hmei7|input_file|open_basedir|outfile|passthru|phpinfo|popen|proc_open|quickbrute|remoteview|root_path|safe_mode|shell_exec|site((.){0,2})copier|sux0r|trojan|user_func_array|wget|xertive)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(;|<|>|\'|\"|\)|%0a|%0d|%22|%27|%3c|%3e|%00)(.*)(/\*|alter|base64|benchmark|cast|concat|convert|create|encode|declare|delete|drop|insert|md5|request|script|select|set|union|update)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#((\+|%2b)(concat|delete|get|select|union)(\+|%2b))#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(union)(.*)(select)(.*)(\(|%28)#i" bot
    SetEnvIfExpr "%{QUERY_STRING} =~ m#(concat|eval)(.*)(\(|%28)#i" bot
# 7G:[REQUEST URI]
    SetEnvIfNoCase Request_URI (\^|`|<|>|\\|\|) bot
    SetEnvIfNoCase Request_URI ([a-z0-9]{2000,}) bot
    #SetEnvIfNoCase Request_URI (=?\\\(\'|%27)/?)(\.) bot
    RedirectMatch 403 (?i)(=\\\'|=\\%27|/\\\'/?)\.
    SetEnvIfNoCase Request_URI (/)(\*|\"|\'|\.|,|&|&amp;?)/?$ bot
    SetEnvIfNoCase Request_URI (\.)(php)(\()?([0-9]+)(\))?(/)?$ bot
    SetEnvIfNoCase Request_URI (/)(vbulletin|boards|vbforum)(/)? bot
    SetEnvIfNoCase Request_URI /((.*)header:|(.*)set-cookie:(.*)=) bot
    SetEnvIfNoCase Request_URI (/)(ckfinder|fck|fckeditor|fullclick) bot
    SetEnvIfNoCase Request_URI (\.(s?ftp-?)config|(s?ftp-?)config\.) bot
    SetEnvIfNoCase Request_URI (\{0\}|\"?0\"?=\"?0|\(/\(|\.\.\.|\+\+\+|\\\") bot
    SetEnvIfNoCase Request_URI (thumbs?(_editor|open)?|tim(thumbs?)?)(\.php) bot
    SetEnvIfNoCase Request_URI (\.|20)(get|the)(_)(permalink|posts_page_url)(\() bot
    SetEnvIfNoCase Request_URI (///|\?\?|/&&|/\*(.*)\*/|/:/|\\\\|0x00|%00|%0d%0a) bot
    SetEnvIfNoCase Request_URI (/%7e)(root|ftp|bin|nobody|named|guest|logs|sshd)(/) bot
    SetEnvIfNoCase Request_URI (/)(etc|var)(/)(hidden|secret|shadow|ninja|passwd|tmp)(/)?$ bot
    SetEnvIfNoCase Request_URI (s)?(ftp|http|inurl|php)(s)?(:(/|%2f|%u2215)(/|%2f|%u2215)) bot
    SetEnvIfNoCase Request_URI (/)(=|\$&?|&?(pws|rk)=0|_mm|_vti_|cgi(\.|-)?|(=|/|;|,)nt\.) bot
    SetEnvIfNoCase Request_URI (\.)(ds_store|htaccess|htpasswd|init?|mysql-select-db)(/)?$ bot
    SetEnvIfNoCase Request_URI (/)(bin)(/)(cc|chmod|chsh|cpp|echo|id|kill|mail|nasm|perl|ping|ps|python|tclsh)(/)?$ bot
    SetEnvIfNoCase Request_URI (/)(::[0-9999]|%3a%3a[0-9999]|127\.0\.0\.1|localhost|makefile|pingserver|wwwroot)(/)? bot
    SetEnvIfNoCase Request_URI (\(null\)|\{\$itemURL\}|cAsT\(0x|echo(.*)kae|etc/passwd|eval\(|self/environ|\+union\+all\+select) bot
    SetEnvIfNoCase Request_URI (/)?j((\s)+)?a((\s)+)?v((\s)+)?a((\s)+)?s((\s)+)?c((\s)+)?r((\s)+)?i((\s)+)?p((\s)+)?t((\s)+)?(%3a|:) bot
    SetEnvIfNoCase Request_URI (/)(awstats|(c99|php|web)shell|document_root|error_log|listinfo|muieblack|remoteview|site((.){0,2})copier|sqlpatch|sux0r) bot
    SetEnvIfNoCase Request_URI (/)((php|web)?shell|crossdomain|fileditor|locus7|nstview|php(get|remoteview|writer)|r57|remview|sshphp|storm7|webadmin)(.*)(\.|\() bot
    SetEnvIfNoCase Request_URI (/)(author-panel|class|database|(db|mysql)-?admin|filemanager|htdocs|httpdocs|https?|mailman|mailto|msoffice|_?php-my-admin(.*)|tmp|undefined|usage|var|vhosts|webmaster|www)(/) bot
    SetEnvIfNoCase Request_URI (base64_(en|de)code|benchmark|child_terminate|curl_exec|e?chr|eval|function|fwrite|(f|p)open|html|leak|passthru|p?fsockopen|phpinfo|posix_(kill|mkfifo|setpgid|setsid|setuid)|proc_(close|get_status|nice|open|terminate)|(shell_)?exec|system)(.*)(\()(.*)(\)) bot
    SetEnvIfNoCase Request_URI (/)(^$|00.temp00|0day|3index|3xp|70bex?|admin_events|bkht|(php|web)?shell|c99|config(\.)?bak|curltest|db|dompdf|filenetworks|hmei7|index\.php/index\.php/index|jahat|kcrew|keywordspy|libsoft|marg|mobiquo|mysql|nessus|php-?info|racrew|sql|vuln|(web-?|wp-)?(conf\b|config(uration)?)|xertive)(\.php) bot
    SetEnvIfNoCase Request_URI (\.)(7z|ab4|ace|afm|ashx|aspx?|bash|ba?k?|bin|bz2|cfg|cfml?|cgi|conf\b|config|ctl|dat|db|dist|dll|eml|engine|env|et2|exe|fec|fla|git|hg|inc|ini|inv|jsp|log|lqd|make|mbf|mdb|mmw|mny|module|old|one|orig|out|passwd|pdb|phtml|pl|profile|psd|pst|ptdb|pwd|py|qbb|qdf|rar|rdf|save|sdb|sql|sh|soa|svn|swf|swl|swo|swp|stx|tar|tax|tgz|theme|tls|tmd|wow|xtmpl|ya?ml|zlib)$ bot
# 7G:[USER AGENT]
    SetEnvIfNoCase User-Agent ([a-z0-9]{2000,}) bot
    SetEnvIfNoCase User-Agent (&lt;|%0a|%0d|%27|%3c|%3e|%00|0x00) bot
    SetEnvIfNoCase User-Agent (ahrefs|alexibot|majestic|mj12bot|rogerbot) bot
    SetEnvIfNoCase User-Agent ((c99|php|web)shell|remoteview|site((.){0,2})copier) bot
    SetEnvIfNoCase User-Agent (econtext|eolasbot|eventures|liebaofast|nominet|oppo\sa33) bot
    SetEnvIfNoCase User-Agent (base64_decode|bin/bash|disconnect|eval|lwp-download|unserialize|\\\x22) bot
    SetEnvIfNoCase User-Agent (acapbot|acoonbot|asterias|attackbot|backdorbot|becomebot|binlar|blackwidow|blekkobot|blexbot|blowfish|bullseye|bunnys|butterfly|careerbot|casper|checkpriv|cheesebot|cherrypick|chinaclaw|choppy|clshttp|cmsworld|copernic|copyrightcheck|cosmos|crescent|cy_cho|datacha|demon|diavol|discobot|dittospyder|dotbot|dotnetdotcom|dumbot|emailcollector|emailsiphon|emailwolf|extract|eyenetie|feedfinder|flaming|flashget|flicky|foobot|g00g1e|getright|gigabot|go-ahead-got|gozilla|grabnet|grafula|harvest|heritrix|httrack|icarus6j|jetbot|jetcar|jikespider|kmccrew|leechftp|libweb|linkextractor|linkscan|linkwalker|loader|masscan|miner|mechanize|morfeus|moveoverbot|netmechanic|netspider|nicerspro|nikto|ninja|nutch|octopus|pagegrabber|planetwork|postrank|proximic|purebot|pycurl|python|queryn|queryseeker|radian6|radiation|realdownload|scooter|seekerspider|semalt|siclab|sindice|sistrix|sitebot|siteexplorer|sitesnagger|skygrid|smartdownload|snoopy|sosospider|spankbot|spbot|sqlmap|stackrambler|stripper|sucker|surftbot|sux0r|suzukacz|suzuran|takeout|teleport|telesoft|true_robots|turingos|turnit|vampire|vikspider|voideye|webleacher|webreaper|webstripper|webvac|webviewer|webwhacker|winhttp|wwwoffle|woxbot|xaldon|xxxyy|yamanalab|yioopbot|youda|zeus|zmeu|zune|zyborg) bot
# 7G:[REMOTE HOST]
    SetEnvIfNoCase Remote_Host (163data|amazonaws|colocrossing|crimea|g00g1e|justhost|kanagawa|loopia|masterhost|onlinehome|poneytel|sprintdatacenter|reverse.softlayer|safenet|ttnet|woodpecker|wowrack) bot
# 7G:[HTTP REFERRER]
    SetEnvIfNoCase Referer (semalt\.com|todaperfeita) bot
    SetEnvIfNoCase Referer (order(\s|%20)by(\s|%20)1--) bot
    SetEnvIfNoCase Referer (blue\spill|cocaine|ejaculat|erectile|erections|hoodia|huronriveracres|impotence|levitra|libido|lipitor|phentermin|pro[sz]ac|sandyauer|tramadol|troyhamby|ultram|unicauca|valium|viagra|vicodin|xanax|ypxaieo) bot
# 7G:[REQUEST METHOD]
    SetEnvIfNoCase Request_Method ^(connect|debug|move|trace|track) bot
# Extra:[HTTP HOST]
    SetEnvIfNoCase Host "^\d|^$" bot
# Controls who can get stuff from this server.
<RequireAll>
    Require all granted
    Require not env bot
</RequireAll>' . PHP_EOL;

        $rules .= self::$firewall_seven_g_marker_end . PHP_EOL; 

        return $rules;

    } // END func

    /*
     * This function contains the rules for the 6G blacklist produced by Jeff Starr:
     * https://perishablepress.com/6g/
     */
    protected static function getrules_firewall_g6()
    {
        $rules = '';

        if ( '' == VisitorLog_Utility::get_option_sl( 'on_firewall_g6' ) ) return $rules;
            
        $rules .= PHP_EOL . self::$firewall_six_g_marker_start;
        $rules .= '
# 6G FIREWALL/BLACKLIST
# @ https://perishablepress.com/6g/
# 6G:[QUERY STRINGS]
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{QUERY_STRING} (eval\() [NC,OR]
    RewriteCond %{QUERY_STRING} (127\.0\.0\.1) [NC,OR]
    RewriteCond %{QUERY_STRING} ([a-z0-9]{2000,}) [NC,OR]
    RewriteCond %{QUERY_STRING} (javascript:)(.*)(;) [NC,OR]
    RewriteCond %{QUERY_STRING} (base64_encode)(.*)(\() [NC,OR]
    RewriteCond %{QUERY_STRING} (GLOBALS|REQUEST)(=|\[|%) [NC,OR]
    RewriteCond %{QUERY_STRING} (<|%3C)(.*)script(.*)(>|%3) [NC,OR]
    RewriteCond %{QUERY_STRING} (\\|\.\.\.|\.\./|~|`|<|>|\|) [NC,OR]
    RewriteCond %{QUERY_STRING} (boot\.ini|etc/passwd|self/environ) [NC,OR]
    RewriteCond %{QUERY_STRING} (thumbs?(_editor|open)?|tim(thumb)?)\.php [NC,OR]
    RewriteCond %{QUERY_STRING} (\'|\")(.*)(drop|insert|md5|select|union) [NC]
    RewriteRule .* - [F]
</IfModule>
# 6G:[REQUEST METHOD]
<IfModule mod_rewrite.c>
    RewriteCond %{REQUEST_METHOD} ^(connect|debug|move|put|trace|track) [NC]
    RewriteRule .* - [F]
</IfModule>
# 6G:[REFERRERS]
<IfModule mod_rewrite.c>
    RewriteCond %{HTTP_REFERER} ([a-z0-9]{2000,}) [NC,OR]
    RewriteCond %{HTTP_REFERER} (semalt.com|todaperfeita) [NC]
    RewriteRule .* - [F]
</IfModule>
# 6G:[REQUEST STRINGS]
<IfModule mod_alias.c>
    RedirectMatch 403 (?i)([a-z0-9]{2000,})
    RedirectMatch 403 (?i)(https?|ftp|php):/
    RedirectMatch 403 (?i)(base64_encode)(.*)(\()
    RedirectMatch 403 (?i)(=\\\'|=\\%27|/\\\'/?)\.
    RedirectMatch 403 (?i)/(\$(\&)?|\*|\"|\.|,|&|&amp;?)/?$
    RedirectMatch 403 (?i)(\{0\}|\(/\(|\.\.\.|\+\+\+|\\\"\\\")
    RedirectMatch 403 (?i)(~|`|<|>|:|;|,|%|\\|\s|\{|\}|\[|\]|\|)
    RedirectMatch 403 (?i)/(=|\$&|_mm|cgi-|etc/passwd|muieblack)
    RedirectMatch 403 (?i)(&pws=0|_vti_|\(null\)|\{\$itemURL\}|echo(.*)kae|etc/passwd|eval\(|self/environ)
    RedirectMatch 403 (?i)\.(aspx?|bash|bak?|cfg|cgi|dll|exe|git|hg|ini|jsp|log|mdb|out|sql|svn|swp|tar|rar|rdf)$
    RedirectMatch 403 (?i)/(^$|(wp-)?config|mobiquo|phpinfo|shell|sqlpatch|thumb|thumb_editor|thumbopen|timthumb|webshell)\.php
</IfModule>
# 6G:[USER AGENTS]
<IfModule mod_setenvif.c>
    SetEnvIfNoCase User-Agent ([a-z0-9]{2000,}) bad_bot
    SetEnvIfNoCase User-Agent (archive.org|binlar|casper|checkpriv|choppy|clshttp|cmsworld|diavol|dotbot|extract|feedfinder|flicky|g00g1e|harvest|heritrix|httrack|kmccrew|loader|miner|nikto|nutch|planetwork|postrank|purebot|pycurl|python|seekerspider|siclab|skygrid|sqlmap|sucker|turnit|vikspider|winhttp|xxxyy|youda|zmeu|zune) bad_bot
    # Apache < 2.3
    <IfModule !mod_authz_core.c>
        Order Allow,Deny
        Allow from all
        Deny from env=bad_bot
    </IfModule>
    # Apache >= 2.3
    <IfModule mod_authz_core.c>
        <RequireAll>
            Require all Granted
            Require not env bad_bot
        </RequireAll>
    </IfModule>
</IfModule>' . PHP_EOL;

        $rules .= self::$firewall_six_g_marker_end . PHP_EOL; 

        return $rules;

    } // END func

    /*
     * Basic firewall
     */
    protected static function getrules_basic_firewall()
    {
        $rules = '';

        $on_protect_wp_config     = VisitorLog_Utility::get_option_sl( 'on_protect_wp_config' );
        $disable_server_signature = VisitorLog_Utility::get_option_sl( 'disable_server_signature' );
        $on_max_file_upload_size  = VisitorLog_Utility::get_option_sl( 'on_max_file_upload_size' );
        $disable_index_views      = VisitorLog_Utility::get_option_sl( 'disable_index_views' );
        $on_prevent_hotlinking    = VisitorLog_Utility::get_option_sl( 'on_prevent_hotlinking' );

        if ( 'on' == $on_protect_wp_config ) {
            $rules .= self::create_apache2_access_denied_rule('wp-config.php');
        }
        if ( 'on' == $disable_server_signature ) {
            $rules .= 'ServerSignature Off' . PHP_EOL;
        }
        if ( 'on' == $on_max_file_upload_size ) {
            $max_file_upload_size = (int)VisitorLog_Utility::get_option_sl( 'max_file_upload_size' );
            $upload_limit = $max_file_upload_size * 1024 * 1024; // Convert from MB to Bytes - approx but close enough
            $rules .= 'LimitRequestBody '.$upload_limit . PHP_EOL;
        }
        if ( 'on' == $disable_index_views ) {
            $rules .= 'Options -Indexes' . PHP_EOL;
        }
        if ( 'on' == $on_prevent_hotlinking ) {
            $rules .= 
'<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteCond %{REQUEST_FILENAME} \.(gif|jpe?g?|png)$ [NC]
    RewriteCond %{HTTP_REFERER} !^' . $url_string . ' [NC]
    RewriteRule \.(gif|jpe?g?|png)$ - [F,NC,L]
</IfModule>' . PHP_EOL;            
        }

        if ( '' == $rules ) return $rules; 

        $rules_start = PHP_EOL . self::$basic_firewall_rules_marker_start . PHP_EOL;
        $rules_out  = $rules_start . $rules;
        $rules_out .= self::$basic_firewall_rules_marker_end . PHP_EOL;

        return $rules_out;

    } // END func

    /*
     * This function will write rules to disable trace and track.
     * HTTP Trace attack (XST) can be used to return header requests 
     * and grab cookies and other information and is used along with 
     * a cross site scripting attacks (XSS)
     */
    protected static function getrules_xss_firewall()
    {
        $rules = '';

        $disable_trace_and_track     = VisitorLog_Utility::get_option_sl( 'disable_trace_and_track' );
        $deny_bad_query_strings      = VisitorLog_Utility::get_option_sl( 'deny_bad_query_strings' );
        $advanced_char_string_filter = VisitorLog_Utility::get_option_sl( 'advanced_char_string_filter' );

        if ( 'on' == $disable_trace_and_track ) {
            $rules .= 
'<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} ^(TRACE|TRACK)
    RewriteRule .* - [F]
</IfModule>' . PHP_EOL;
        }
        if ( 'on' == $deny_bad_query_strings ) {
            $rules .= 
'<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{QUERY_STRING} ftp:     [NC,OR]
    RewriteCond %{QUERY_STRING} http:    [NC,OR]
    RewriteCond %{QUERY_STRING} https:   [NC,OR]
    RewriteCond %{QUERY_STRING} mosConfig [NC,OR]
    RewriteCond %{QUERY_STRING} ^.*(globals|encode|localhost|loopback).* [NC,OR]
    RewriteCond %{QUERY_STRING} (\;|\'|\"|%22).*(request|insert|union|declare|drop) [NC]
    RewriteRule ^(.*)$ - [F,L]
</IfModule>' . PHP_EOL;
        }
        if ( 'on' == $advanced_char_string_filter ) {
            $rules .= 
'<IfModule mod_alias.c>
    RedirectMatch 403 \,
    RedirectMatch 403 \:
    RedirectMatch 403 \;
    RedirectMatch 403 \=
    RedirectMatch 403 \[
    RedirectMatch 403 \]
    RedirectMatch 403 \^
    RedirectMatch 403 \`
    RedirectMatch 403 \{
    RedirectMatch 403 \}
    RedirectMatch 403 \~
    RedirectMatch 403 \"
    RedirectMatch 403 \$
    RedirectMatch 403 \<
    RedirectMatch 403 \>
    RedirectMatch 403 \|
    RedirectMatch 403 \.\.
    RedirectMatch 403 \%0
    RedirectMatch 403 \%A
    RedirectMatch 403 \%B
    RedirectMatch 403 \%C
    RedirectMatch 403 \%D
    RedirectMatch 403 \%E
    RedirectMatch 403 \%F
    RedirectMatch 403 \%22
    RedirectMatch 403 \%27
    RedirectMatch 403 \%28
    RedirectMatch 403 \%29
    RedirectMatch 403 \%3C
    RedirectMatch 403 \%3E
    RedirectMatch 403 \%3F
    RedirectMatch 403 \%5B
    RedirectMatch 403 \%5C
    RedirectMatch 403 \%5D
    RedirectMatch 403 \%7B
    RedirectMatch 403 \%7C
    RedirectMatch 403 \%7D
    # COMMON PATTERNS
    Redirectmatch 403 \_vpi
    RedirectMatch 403 \.inc
    Redirectmatch 403 xAou6
    Redirectmatch 403 db\_name
    Redirectmatch 403 select\(
    Redirectmatch 403 convert\(
    Redirectmatch 403 \/query\/
    RedirectMatch 403 ImpEvData
    Redirectmatch 403 \.XMLHTTP
    Redirectmatch 403 proxydeny
    RedirectMatch 403 function\.
    Redirectmatch 403 remoteFile
    Redirectmatch 403 servername
    Redirectmatch 403 \&rptmode\=
    Redirectmatch 403 sys\_cpanel
    RedirectMatch 403 db\_connect
    RedirectMatch 403 doeditconfig
    RedirectMatch 403 check\_proxy
    Redirectmatch 403 system\_user
    Redirectmatch 403 \/\(null\)\/
    Redirectmatch 403 clientrequest
    Redirectmatch 403 option\_value
    RedirectMatch 403 ref\.outcontrol
    # SPECIFIC EXPLOITS
    RedirectMatch 403 errors\.
    RedirectMatch 403 config\.
    RedirectMatch 403 include\.
    RedirectMatch 403 display\.
    RedirectMatch 403 register\.
    Redirectmatch 403 password\.
    RedirectMatch 403 maincore\.
    RedirectMatch 403 authorize\.
    Redirectmatch 403 macromates\.
    RedirectMatch 403 head\_auth\.
    RedirectMatch 403 submit\_links\.
    RedirectMatch 403 change\_action\.
    Redirectmatch 403 com\_facileforms\/
    RedirectMatch 403 admin\_db\_utilities\.
    RedirectMatch 403 admin\.webring\.docs\.
    Redirectmatch 403 Table\/Latest\/index\.
</IfModule>' . PHP_EOL;
        }

        if ( '' == $rules ) return $rules; 

        $rules_start = PHP_EOL . self::$getrules_xss_firewall_marker_start . PHP_EOL;
        $rules_out  = $rules_start . $rules;
        $rules_out .= self::$getrules_xss_firewall_marker_end . PHP_EOL;

        return $rules_out;

    } // END func

    /*
     * This function will take a URL string and convert it to a form useful for using in htaccess rules.
     * Example: If URL passed to function = "http://www.mysite.com"
     * Result = "http(s)?://(.*)?mysite\.com"
     */
    protected static function return_regularized_url( $url )
    {
        if ( filter_var($url, FILTER_VALIDATE_URL) ) {

            $xyz = explode('.', $url);
            $y = '';
            if (count($xyz) > 1) {
                $j = 1;
                foreach ($xyz as $x) {
                    if (strpos($x, 'www') !== false) {
                        $y .= str_replace('www', '(.*)?', $x);
                    } else if ($j == 1) {
                        $y .= $x;
                    } else if ($j > 1) {
                        $y .= '\.' . $x;
                    }
                    $j++;
                }
                //Now replace the "http" with "http(s)?" to cover both secure and non-secure
                if (strpos($y, 'https') !== false) {
                    $y = str_replace('https', 'http(s)?', $y);
                }else if (strpos($y, 'http') !== false) {
                    $y = str_replace('http', 'http(s)?', $y);
                }
                return $y;
            } else {
                return $url;
            }
        } else {
            return FALSE;
        }

    } // END func

    /**
     * Returns a string with <Files $filename> directive that contains rules
     * to effectively block access to any file that has basename matching
     * $filename under Apache webserver.
     *
     * @link http://httpd.apache.org/docs/current/mod/core.html#files
     *
     * @param string $filename
     * @return string
     */
    protected static function create_apache2_access_denied_rule( $filename )
    {
        $html  = "<Files $filename>" . PHP_EOL;
        $html .= "<IfModule mod_authz_core.c>" . PHP_EOL;
        $html .= "   Require all denied" . PHP_EOL;
        $html .= "</IfModule>" . PHP_EOL;
        $html .= "<IfModule !mod_authz_core.c>" . PHP_EOL;
        $html .= "   Order deny,allow" . PHP_EOL;
        $html .= "   Deny from all" . PHP_EOL;
        $html .= "</IfModule>" . PHP_EOL;
        $html .= "</Files>" . PHP_EOL;

        return $html;

    } // END func


    /**
     * Convert an array of optionally asterisk-masked or partial IPv4 addresses
     * into network/netmask notation. Netmask value for a "full" IP is not
     * added (see example below)
     *
     * Example:
     * In: array('1.2.3.4', '5.6', '7.8.9.*')
     * Out: array('1.2.3.4', '5.6.0.0/16', '7.8.9.0/24')
     *
     * Simple validation is performed:
     * In: array('1.2.3.4.5', 'abc', '1.2.xyz.4')
     * Out: array()
     *
     * Simple sanitization is performed:
     * In: array('6.7.*.9')
     * Out: array('6.7.0.0/16')
     *
     * @param array $ips
     * @return array
     */
    protected static function add_netmask( $ips = array() )
    {
        $output = array();
        if(empty($ips)) return array();
        foreach ( $ips as $ip ) {
            //Check if ipv6
            if (strpos($ip, ':') !== false) {
                //for now we'll only support whole ipv6 (not address ranges)
                $ipv6 = \WP_Http::is_ip_address($ip);
                if (FALSE === $ipv6) {
                    continue;
                }
                $output[] = $ip;
            }
            $parts = explode('.', $ip);

            // Skip any IP that is empty, has more parts than expected or has
            // a non-numeric first part.
            if ( empty($parts) || (count($parts) > 4) || !is_numeric($parts[0]) ) {
                continue;
            }
            $ip_out = array( $parts[0] );
            $netmask = 8;

            for ( $i = 1, $force_zero = false; ($i < 4) && $ip_out; $i++ ) {
                if ( $force_zero || !isset($parts[$i]) || ($parts[$i] === '') || ($parts[$i] === '*') ) {
                    $ip_out[$i] = '0';
                    $force_zero = true; // Forces all subsequent parts to be a zero
                }
                else if ( is_numeric($parts[$i]) ) {
                    $ip_out[$i] = $parts[$i];
                    $netmask += 8;
                }
                else {
                    // Invalid IP part detected, invalidate entire IP
                    $ip_out = false;
                }
            }
            if ( $ip_out ) {
                // Glue IP back together, add netmask if IP denotes a subnet, store for output.
                $output[] = implode('.', $ip_out) . (($netmask < 32) ? ('/' . $netmask) : '');
            }
        }

        return $output;

    } // END func

    protected static function handler_ip_address( $ips_with_netmask )
    {
        $rules = '';

        $server = VisitorLog_Utility::get_server( $full_server );
        $apache_or_litespeed = $server == 'apache' || $server == 'litespeed';

        if ( $apache_or_litespeed ) {
            // Apache or LiteSpeed webserver
            $server = substr($server, 7);
            if ( '' == $server ) $server = 999; 
            if ( version_compare($server, '2.3') >= 0 ) {
                // Apache 2.3 and newer
                $rules .= "<IfModule mod_authz_core.c>" . PHP_EOL;
                $rules .= "<RequireAll>" . PHP_EOL;
                $rules .= "Require all granted" . PHP_EOL;

                foreach ($ips_with_netmask as $ip_with_netmask) {
                    $rules .= "Require not ip " . $ip_with_netmask . PHP_EOL;
                }
                $rules .= "</RequireAll>" . PHP_EOL;
                $rules .= "</IfModule>" . PHP_EOL;
            } else {
                // Apache 2.2 and older
                $rules .= "<IfModule !mod_authz_core.c>" . PHP_EOL;
                $rules .= "Order allow,deny" . PHP_EOL;
                $rules .= "Allow from all" . PHP_EOL;

                foreach ($ips_with_netmask as $ip_with_netmask) {
                    $rules .= "Deny from " . $ip_with_netmask . PHP_EOL;
                }
                $rules .= "</IfModule>" . PHP_EOL;
            }
        }
        else {
            // Nginx webserver
            foreach ($ips_with_netmask as $ip_with_netmask) {
                $rules .= "\tdeny " . $ip_with_netmask . ";" . PHP_EOL;
            }
        }
        return $rules;

    } // END func

    protected static function handler_user_agents( $user_agents )
    {
        $rules = '';

        $server = VisitorLog_Utility::get_server( $full_server );
        $apache_or_litespeed = $server == 'apache' || $server == 'litespeed';

        if ( $apache_or_litespeed ) {
            $count = 1;
            foreach ( $user_agents as $agent ) {

                if ( strlen($agent) > 20 ) continue;

                $agent_escaped = quotemeta($agent);
                $pattern = '/\s/';            //Find spaces in the string
                $replacement = '\s';          //Replace spaces with \s so apache can understand
                $agent_sanitized = preg_replace( $pattern, $replacement, $agent_escaped );

                $rules .= "RewriteCond %{HTTP_USER_AGENT} ^" . trim($agent_sanitized);

                if ( $count < sizeof( $user_agents ) ) {

                    $rules .= " [NC,OR]" . PHP_EOL;
                    $count++;
                } else {
                    $rules .= " [NC]" . PHP_EOL;
                }
            }

            if ( '' == $rules ) return $rules;

            $rules1 = "<IfModule mod_rewrite.c>" . PHP_EOL . "RewriteEngine On" . PHP_EOL;
            //...
            $rules2 = "RewriteRule ^(.*)$ - [F,L]" . PHP_EOL;
            $rules3 = "</IfModule>" . PHP_EOL;
            $rules = $rules1 . $rules . $rules2 . $rules3;
        } else {
            $count = 1;
            $alist = '';
            foreach ( $user_agents as $agent ) {

                if ( strlen($agent) > 20 ) continue;

                $alist .= trim($agent);
                if ( $count < sizeof($user_agents) ) {
                    $alist .= '|';
                    $count++;
                }
            }
            $rules .= "\tif (\$http_user_agent ~* " . $alist . ") { return 403; }" . PHP_EOL; 
        }
        return $rules;

    } // END func


} // END class