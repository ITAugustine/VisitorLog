<?php
/**
 * Event login.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Event_Login class
 *
 * @method public    static function get_class_name()
 * @method public           function __construct()
 * @method public    static function check_captcha()
 * @method public    static function authentication_handler()
 * @method public    static function insert_data_into_table_admin_ip()
 * @method public    static function authenticated_failed()
 * @method public    static function insert_data_into_table_login_activity()
 * @method public    static function insert_data_into_table_login_failed()
 * @method public    static function insert_captcha_question_form()
 * @method public    static function insert_recaptcha_question_form()
 * @method public    static function on_logout()
 */
class VisitorLog_Event_Login
{
    public static function get_class_name()
    {
        return __CLASS__;
    }

    public function __construct()
    {
        add_filter( 'authenticate', array( self::get_class_name(), 'check_captcha' ), 20, 1 );
        add_filter( 'authenticate', array( self::get_class_name(), 'authentication_handler'), 100, 3 );
        add_action( 'wp_logout',    array( self::get_class_name(), 'on_logout') );

        if ( '' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) ) return;
        if ( is_user_logged_in() ) return;

        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_login_recaptcha' ) ) {
            add_action( 'login_form', array( self::get_class_name(), 'insert_recaptcha_question_form' ) );
        } 
        elseif ( 'on' == VisitorLog_Utility::get_option_sl( 'on_login_captcha' ) ) { 
            add_action( 'login_form', array( self::get_class_name(), 'insert_captcha_question_form' ) );
        }

    } // END of __construct()

    /**
     * Check login captcha (if enabled).
     * 
     * @param  WP_Error|WP_User $user
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Security_Captcha::verify_captcha_submit()
     * @return WP_Error|WP_User
     */
    public static function check_captcha( $user )
    {
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) ||
             is_wp_error($user) 
           ) 
            return $user;

        // XML-RPC authentication (not via wp-login.php), nothing to do here.
        if ( ! (isset($_POST['log']) && isset($_POST['pwd'])) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            return $user;
        }

        $verify_captcha = VisitorLog_Security_Captcha::verify_captcha_submit();

        if ( $verify_captcha === false ) {

            $msg  = '<b>';
            $msg .= __('ERROR: Your answer was incorrect - please try again', 'visitorlog');
            $msg .= '</b>';
            $captcha_error = new \WP_Error( 'authentication_failed', $msg );
            return $captcha_error;
        }
        return $user;

    } // END func

    /**
     * Authentication handler
     *
     * @param WP_Error|WP_User $user
     * @param string $username
     * @param string $password
     * @return WP_Error|WP_User
     */
    public static function authentication_handler( $user, $username, $password )
    {
        if ( null == $user->errors ) {
            self::insert_data_into_table_admin_ip();
            self::insert_data_into_table_login_activity( $user );
        } else {
            self::authenticated_failed( $user, $username, $password );
        }
        return $user;

    } // END of func

    /**
     * Insert data into the table
     *
     * @param WP_Error|WP_User $user
     * @param string $username
     * @param string $password
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->warning()
     * @return WP_Error|WP_User
     */
    public static function insert_data_into_table_admin_ip()
    {
        global $visitorlog_users_resident_parameters;

        if ( isset($visitorlog_users_resident_parameters['datetime']) ) {
            $date_time = $visitorlog_users_resident_parameters['datetime'];
        } else {
            $date_time = gmdate( 'Y-m-d H:i:s', current_time('timestamp') );
        }
        if ( isset($visitorlog_users_resident_parameters['ip']) ) {
            $ip = $visitorlog_users_resident_parameters['ip'];
        } else {
            $param_user = VisitorLog_Detection::instance()->get_user_ip();
            $ip = $param_user['ip'];
        }

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'admin_ip';
        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_ip`=%s", $ip));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err_msg = "insert_data_into_table_admin_ip()-Error select data into table: " . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err_msg);
            return false;
        }
           
        if ( ! empty($array) ) return;

        $data = array( 'date_time' => $date_time, 
                       'user_ip'   => $ip 
                     );
        $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err_msg = "insert_data_into_table_admin_ip()-Error insert data into table: " . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err_msg); 
            return false;          
        }

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT COUNT(id) FROM $table_name WHERE `id`!=%d", 0), ARRAY_A);
        $c_rows = $array[0]['COUNT(id)'];
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_data_into_table_admin_ip()-Error select table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return;
        }  

        $rows_record_limit = (int)VisitorLog_Utility::get_option_sl( '#_record_limit' );
        if ( $c_rows > $rows_record_limit ) {
            $delete = $c_rows - $rows_record_limit;
            $res = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE `id`!=%d LIMIT %d", 0, $delete));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'insert_data_into_table_admin_ip()-Error delete rows: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return;
            } 
        }

        return;

    } // END of func  

    /**
     * Handle authentication steps (in case of failed login):
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Utility::redirect_to_url()
     *
     * @param WP_Error|WP_User $user
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public static function authenticated_failed( $user, $username, $password )
    {
        global $visitorlog_users_resident_parameters;

        // ----- Neither log nor block users attempting to log in before their registration is approved.
        if ( 'account_pending' == $user->get_error_code() ) return $user;
        if ( !is_wp_error( $user ) )                        return $user;

        $category = $visitorlog_users_resident_parameters['category'];

        if ( 'Admin' == $category ) {
            $visitorlog_users_resident_parameters['category'] = 'AdminLogin';         
            $visitorlog_users_resident_parameters['block'] = '';
            $visitorlog_users_resident_parameters['timerelease'] = 0;
            self::insert_data_into_table_login_failed( $username, $password );
            VisitorLog_Registration::instance()->registration();
            VisitorLog_Statistics_Utility::instance()->statistics_handler();
            $visitorlog_users_resident_parameters['event'] = 'eventlogin';
            return;
        }

        $block = self::insert_data_into_table_login_failed( $username, $password );
        $visitorlog_users_resident_parameters['timerelease'] = $block['timerelease'];

        if ( 'perm' == $block['block'] ) {   /* ----- need for permanent blocking */

            if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) &&
                 'on' == VisitorLog_Utility::get_option_sl( 'on_login_lockdown' )         &&
                 'on' == VisitorLog_Utility::get_option_sl( 'use_htaccess_file_login' ) ) {

                $visitorlog_users_resident_parameters['block'] = 'perm';
                $visitorlog_users_resident_parameters['category'] = 'blocklogin';
                VisitorLog_Registration::instance()->registration();
                VisitorLog_Statistics_Utility::instance()->statistics_handler();
                $visitorlog_users_resident_parameters['event'] = 'eventlogin';
                $ip = $visitorlog_users_resident_parameters['ip'];

                $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess(); 
                if ( $res > 0 ) {
                    $msg = 'authenticated_failed(): login-Successfully written to the htaccess file, IP-'.$ip;
                    VisitorLog_Logger::instance()->warning($msg);
                } else { 
                    $err = 'authenticated_failed(): login-Could not write to the htaccess file, IP-'.$ip;
                    VisitorLog_Logger::instance()->warning($err);
                }
                exit;
            }
            /* Temporary blocking */
            $visitorlog_users_resident_parameters['block'] = 'temp';
            $visitorlog_users_resident_parameters['category'] = 'blocklogin';
            VisitorLog_Registration::instance()->registration();
            VisitorLog_Statistics_Utility::instance()->statistics_handler();
            $visitorlog_users_resident_parameters['event'] = 'eventlogin';

            $redirect_url = VisitorLog_Utility::get_option_sl( 'redirect_url_login' );
            VisitorLog_Security_Utility::redirect_to_url( $redirect_url );
            exit;
        } //----------------------

        if ( 'temp' == $block['block'] ) {   /* ----- need for temporary blocking */

            if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) &&
                 'on' == VisitorLog_Utility::get_option_sl( 'on_login_lockdown' ) ) {

                /* Temporary blocking */
                $visitorlog_users_resident_parameters['block'] = 'temp';
                $visitorlog_users_resident_parameters['category'] = 'blocklogin';
                VisitorLog_Registration::instance()->registration();
                VisitorLog_Statistics_Utility::instance()->statistics_handler();
                $visitorlog_users_resident_parameters['event'] = 'eventlogin';

                $redirect_url = VisitorLog_Utility::get_option_sl( 'redirect_url_login' );
                VisitorLog_Security_Utility::redirect_to_url( $redirect_url );
                return; 
            }  
        } //----------------------

        /* No block */
        $visitorlog_users_resident_parameters['block'] = '';
        $visitorlog_users_resident_parameters['category'] = 'BotLogin';
        $visitorlog_users_resident_parameters['timerelease'] = 0;
        VisitorLog_Registration::instance()->registration();
        VisitorLog_Statistics_Utility::instance()->statistics_handler();
        $visitorlog_users_resident_parameters['event'] = 'eventlogin';
        return;

    } // END of func

    /**
     * Inserting data into table
     * 
     * @param WP_User $user WP_User object of the logged-in user.
     *
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Logger::instance()->warning()
     *
     * @return void
     */
    public static function insert_data_into_table_login_activity( $user=null )
    {
        global $visitorlog_users_resident_parameters;

        if ( null == $user ) $user = wp_get_current_user();

        if ( isset($visitorlog_users_resident_parameters['timestamp']) ) {
            $timestamp = $visitorlog_users_resident_parameters['timestamp'];
        } else {
            $timestamp = current_time('timestamp');
        }
        if ( isset($visitorlog_users_resident_parameters['datetime']) ) {
            $datetime = $visitorlog_users_resident_parameters['datetime'];
        } else {
            $datetime = gmdate( 'Y-m-d H:i:s', $timestamp );
        }
        if ( isset($visitorlog_users_resident_parameters['ip']) ) {
            $ip = $visitorlog_users_resident_parameters['ip'];
        } else {
            $param_user = VisitorLog_Detection::instance()->get_user_ip();
            $ip = $param_user['ip'];
        }

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'login_activity';
        update_user_meta( $user->ID, 'last_login_time', esc_html($datetime) ); //store last login time in meta table

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_id`=%d ORDER BY `id` DESC LIMIT 1", $user->ID));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_data_into_table_login_activity()-Error select into table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning( $err );
            return;
        }

        if ( !empty($array) ) {
            if ( 0 == $array[0]->logout_date ) {
                if ( 3600 > ($timestamp - $array[0]->login_date) ) {
                    return;
                } else {
                    $id_last = $array[0]->id;
                    $data  = array( 'logout_date' => $timestamp );
                    $where = array( 'id'          => $id_last );
                 
                    $result = VisitorLog_DB_Base::$wpdb_vl->update( $table_name, $data, $where );
                    if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                        $err = 'insert_data_into_table_login_activity()-Error update into table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                        VisitorLog_Logger::instance()->warning( $err );
                    }
                }    
            }   
        }
        $data = array( 'user_id'     => $user->ID, 
                       'user_login'  => $user->user_login, 
                       'login_date'  => $timestamp,
                       'logout_date' => 0,
                       'user_ip'     => $ip 
                    );
        $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_data_into_table_login_activity()-Error inserting into table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return;          
        }

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT COUNT(id) FROM $table_name WHERE `id`!=%d", 0), ARRAY_A);
        $c_rows = $array[0]['COUNT(id)'];
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_data_into_table_login_activity()-Error select table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return;
        }  

        $rows_record_limit = (int)VisitorLog_Utility::get_option_sl( '#_record_limit' );
        if ( $c_rows > $rows_record_limit ) {
            $delete = $c_rows - $rows_record_limit;
            $res = VisitorLog_DB_Base::$wpdb_vl->get_results( VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE `id` != 0 LIMIT %d", $delete));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'insert_data_into_table_login_activity()-Error delete rows: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return;
            } 
        }

    } // END func

    /**
     * Inserting the parameter value in the database table
     *
     * @param $login
     * @param $password
     *
     * @return false/array block
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function insert_data_into_table_login_failed( $login, $password )
    {
        global $visitorlog_users_resident_parameters;

        $ip          = $visitorlog_users_resident_parameters['ip'];
        $timestamp   = $visitorlog_users_resident_parameters['timestamp'];
        $timerelease = $visitorlog_users_resident_parameters['timerelease'];
        $category    = $visitorlog_users_resident_parameters['category'];
        $user_name   = $visitorlog_users_resident_parameters['username'];
        $canal       = $visitorlog_users_resident_parameters['canal'];
        $url         = $visitorlog_users_resident_parameters['url'];
        $ref_info    = $visitorlog_users_resident_parameters['refinfo'];

        $block['block'] = '';
        $block['timerelease'] = 0;
        $count = 0;

        $limit_block  = 3;
        $period_block = 1800; // 30 min

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'events_failed';

        if ( 'AdminLogin' != $category ) {

            $max_attempts = VisitorLog_Utility::get_option_sl( 'max_slow_attempts' );
            $per_period   = VisitorLog_Utility::get_option_sl( 'login_slow_time_period' );
            $analyze_period = $timestamp - $per_period; 

            $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_ip`=%s AND `timestamp`>%d ORDER BY `id` DESC", $ip, $analyze_period));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'insert_data_into_table_login_failed()-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error ;
                VisitorLog_Logger::instance()->warning( $err );
                return $block;
            }

            if ( !empty($array) ) {

                $timerelease = $array[0]->timerelease;
                $count = $array[0]->count;

                if ( $timestamp > $timerelease ) {

                    if ( count($array) > $max_attempts ) {
                        $count++;
                        if ( $count > $limit_block ) {
                            $block['block'] = 'perm';
                        } else {
                            $block['block'] = 'temp';
                            $block['timerelease'] = $timestamp + $period_block;
                        }
                    } else {
                        $block['timerelease'] = 0;
                        $block['block'] = '';                         
                    }
                } else {
                    $block['block'] = 'temp'; 
                    $block['timerelease'] = $timerelease;
                }
            }    

            $login_sanitize    = sanitize_text_field( $login );
            $password_sanitize = sanitize_text_field( $password );
            if ( strlen($login_sanitize)    > 255 ) $login_sanitize = '*>>';
            if ( strlen($password_sanitize) > 255 ) $password_sanitize = '*>>';

        } else {   

            $login_sanitize = $password_sanitize = '';
            if ( '' != $login )    $login_sanitize    = '*****';
            if ( '' != $password ) $password_sanitize = '*****';
        }    

        $data = array(
            'timestamp'    => $timestamp,
            'timerelease'  => $block['timerelease'],
            'user_name'    => $user_name,
            'user_ip'      => $ip,
            'category'     => $category,
            'canal'        => $canal,
            'count'        => $count,
            'block'        => $block['block'],
            'login'        => $login_sanitize,
            'pswd'         => $password_sanitize,
            'url'          => $url,
            'referer_info' => $ref_info
            ); 
        $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_data_into_table_login_failed()-Error insert to table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        }  

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `id` != %d", 0 ));
        $c_rows = count($array);
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_data_into_table_login_failed()-Error select table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        }  

        $rows_record_limit = (int)VisitorLog_Utility::get_option_sl('#_record_limit');
        if ( $c_rows > $rows_record_limit ) {
            $delete = $c_rows - $rows_record_limit;
            $res = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE id != 0 LIMIT %d", $delete ));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'insert_data_into_table_login_failed()-Error delete rows: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return $block;
            } 
        }

        return $block;

    } // END func

     /**
      * insert captcha question form
      *
      * @uses VisitorLog_Utility::get_option_sl()
      * @uses VisitorLog_Security_Captcha::captcha_form()
      */       
    public static function insert_captcha_question_form()
    {
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_login_captcha' ) &&
             'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) ) {

                VisitorLog_Security_Captcha::captcha_form();  
                return;      
        }
         
    } // END func

     /**
      * insert recaptcha question form
      *
      * @uses VisitorLog_Utility::get_option_sl()
      * @uses VisitorLog_Security_Captcha::recaptcha_form()
      */  
    public static function insert_recaptcha_question_form()
    {
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_login_recaptcha' ) &&
             'on' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) ) {

            // Woocommerce "my account" page needs special consideration, ie,
            // need to display two Google reCaptcha forms on same page (for login and register forms)
            // For this case we use the "explicit" recaptcha display
            $calling_hook = current_filter();
            $site_key = esc_html( VisitorLog_Utility::get_option_sl( 'recaptcha_site_key' ) );

            if ( $calling_hook == 'woocommerce_login_form' || $calling_hook == 'woocommerce_lostpassword_form') {

                    echo '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div id="woo_recaptcha_1" class="g-recaptcha" data-sitekey="' . esc_html($site_key) . '"></div></div>';
                    return;
            }

            if ( $calling_hook == 'woocommerce_register_form' ) {
                    echo '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div id="woo_recaptcha_2" class="g-recaptcha" data-sitekey="' . esc_html($site_key) . '"></div></div>';
                    return;
            }
            
            // For all other forms simply display google recaptcha as per normal
            VisitorLog_Security_Captcha::recaptcha_form();
            return;
        }
         
    } // END func

     /**
      * Function for `wp_logout` action-hook.
      * 
      * @param int $user_id ID of the user that was logged out
      *
      * @uses VisitorLog_DB_Base::$table_prefix_sl
      * @uses VisitorLog_Logger::instance()->warning()
      *
      * @return void
      */  
    public static function on_logout( $user_id )
    {
        global $visitorlog_users_resident_parameters;

        if ( null == $user_id ) return;

        $datetime  = $visitorlog_users_resident_parameters['datetime'];
        $timestamp = $visitorlog_users_resident_parameters['timestamp'];
        $ip        = $visitorlog_users_resident_parameters['ip'];

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'login_activity';
        update_user_meta( $user_id, 'last_login_time', esc_html($datetime) ); //store last login time in meta table

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_id`=%d ORDER BY `id` DESC LIMIT 1", $user_id));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'on_logout()-Error select into table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning( $err );
            return;
        }

        if ( empty($array) ) return;
        if ( 0 != $array[0]->logout_date ) return;   
  
        $id_last = $array[0]->id;
        $data  = array( 'logout_date' => $timestamp );
        $where = array( 'id'          => $id_last );
     
        $result = VisitorLog_DB_Base::$wpdb_vl->update( $table_name, $data, $where );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'on_logout()-Error update into table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning( $err );
        }

    } // END func


} // END class