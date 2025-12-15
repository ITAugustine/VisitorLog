<?php
/**
 * Security Utility IP.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security_Utility_IP class
 *
 * @method public static function get_user_ip_address()
 */
class VisitorLog_Security_Utility_IP
{ 
    /**
     * get user ip address
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @return visitor_ip
     */    
    public static function get_user_ip_address()
    {
        //check if user configured custom IP retrieval method
        $ip_method  = VisitorLog_Utility::get_option_sl( 'ip_retrieve_method' );
        $visitor_ip = ''; //set default

        if ( !empty($ip_method) ) {

            //means user has configured non default IP retrieval
            if ( $ip_method == 1 && !empty($_SERVER['HTTP_CF_CONNECTING_IP']) ) {
                $visitor_ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CF_CONNECTING_IP']));
            } else if ( $ip_method == 2 && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
                $visitor_ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
            } else if ( $ip_method == 3 && !empty($_SERVER['HTTP_X_FORWARDED']) ) {
                $visitor_ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED']));
            } else if ( $ip_method == 4 && !empty($_SERVER['HTTP_CLIENT_IP']) ) {
                $visitor_ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
            }
        }
        
        //Check if multiple IPs were given - these will be present as comma-separated list
        if ( stristr($visitor_ip, ',') ) {
            $visitor_ip = trim( reset((explode(',', $visitor_ip))) ); //get first address because this will likely be the original connecting IP
        }
        
        //Now remove port portion if applicable
        if ( strpos($visitor_ip, '.') !== FALSE && strpos($visitor_ip, ':') !== FALSE ) {
            //likely ipv4 address with port
            $visitor_ip = preg_replace('/:\d+$/', '', $visitor_ip); //Strip off port
        }

        if ( filter_var($visitor_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {    
            return $visitor_ip;
        } else if ( filter_var($visitor_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ) {
            return $visitor_ip;
        } else {
            $visitor_ip = !empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
            return $visitor_ip;
        }

    } // END func
    

} // END class
