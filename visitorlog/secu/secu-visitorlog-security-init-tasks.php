<?php
/**
 * Initializing security tasks
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods VisitorLog_Security_Init_Tasks.
 *
 * @method public static function get_class_name()
 * @method public        function __construct()
 * @method public        function remove_standard_wp_meta_widget()
 * @method public        function decode_reset_pw_msg()
 * @method public        function check_rest_api_requests()
 * @method public        function disable_xmlrpc_pingback_methods()
 * @method public        function remove_x_pingback_header()
 */
class VisitorLog_Security_Init_Tasks
{
    public static function get_class_name()
    {
        return __CLASS__;
    }

    public function __construct()
    {
        new VisitorLog_Event_Login(); 
        new VisitorLog_Security_Spam_Protection();
        new VisitorLog_Event_404();

        add_action( 'widgets_init',              array( &$this, 'remove_standard_wp_meta_widget' ) );
        add_filter( 'retrieve_password_message', array( &$this, 'decode_reset_pw_msg' ), 10, 4 );

        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) ) {

            // REST API security
            if ( 'on' == VisitorLog_Utility::get_option_sl( 'disable_rest_requests' ) ) { 
                add_action( 'rest_api_init', array( &$this, 'check_rest_api_requests' ), 10 ,1 );
            } else {
                remove_action( 'rest_api_init', array( &$this, 'check_rest_api_requests' ), 10 ,1 );
            }  

            // Disable xmlrpc methods    
            if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_block_xmlrpc' ) ) {

                add_filter( 'xmlrpc_methods', array( &$this, 'disable_xmlrpc_pingback_methods' ) );
            } else {
                remove_filter( 'xmlrpc_methods', array( &$this, 'disable_xmlrpc_pingback_methods' ) );
            }

            if ( '' == VisitorLog_Utility::get_option_sl( 'disable_xmlrpc_methods' ) ) {
                add_filter( 'wp_headers', array( &$this, 'remove_x_pingback_header' ) ); 
            } else {
                remove_filter( 'wp_headers', array( &$this, 'remove_x_pingback_header' ) );
            }
        } else {
            remove_action( 'rest_api_init',  array( &$this, 'check_rest_api_requests' ), 10 ,1 );
            remove_filter( 'xmlrpc_methods', array( &$this, 'disable_xmlrpc_pingback_methods' ) );
            remove_filter( 'wp_headers',     array( &$this, 'remove_x_pingback_header' ) );
        }

    } // end _construct()

    public function remove_standard_wp_meta_widget()
    {
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) )  return; 
        
        unregister_widget('WP_Widget_Meta');
      
    } // END func

    //This is a fix for cases when the password reset URL in the email was not decoding all html entities properly
    public function decode_reset_pw_msg( $message, $key, $user_login, $user_data )
    {
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) )  return; 

            $message = html_entity_decode($message);
            return $message;

    } // END func
   
    /**
     * Below uses the "rest_api_init" action hook to check for REST requests.
     * The code will block "unauthorized" requests whilst allowing genuine requests. 
     */
    public function check_rest_api_requests( $rest_server_object )
    {
        // REST API security
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) || 
             '' == VisitorLog_Utility::get_option_sl( 'disable_rest_requests' ) )         return;

        $rest_user = wp_get_current_user();

        if ( empty($rest_user->ID) ) {
            $error_message = __( 'You are not authorized to perform this actio', 'visitorlog' );
            wp_die( esc_html($error_message) ); 
        }
        
    } // END func

    public function disable_xmlrpc_pingback_methods( $methods )
    {
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) || 
             '' == VisitorLog_Utility::get_option_sl( 'on_block_xmlrpc' ) )       return;

        unset( $methods['pingback.ping'] );
        unset( $methods['pingback.extensions.getPingbacks'] );
        return $methods;

    } // END func
    
    public function remove_x_pingback_header( $headers )
    {
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) || 
             '' == VisitorLog_Utility::get_option_sl( 'disable_xmlrpc_methods' ) )       return;

        unset( $headers['X-Pingback'] );
        return $headers;

    } // END func


} // END class