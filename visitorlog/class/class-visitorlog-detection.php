<?php
/**
 * VisitorLog Detection
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of the VisitorLog_Detection class 
 * 
 * @method public function __construct()
 * @method public function detection()
 * @method public function detect_limits()
 * @method public function xhr_request()
 * @method public function get_user_ip()
 */
class VisitorLog_Detection
{
    private static  $instance = null;

    public static function instance()
    {
    	if ( null == self::$instance )  self::$instance = new VisitorLog_Detection();
        return self::$instance;
    }

	public function __construct()
	{
		$this->detection();

	} // END  __construct()

	/**
	 * Detection of visitor
	 */
	public function detection()
	{
		global $visitorlog_users_resident_parameters;

		$timestamp = current_time( 'timestamp' );
        $visitorlog_users_resident_parameters['timestamp'] = $timestamp;
        $visitorlog_users_resident_parameters['datetime']  = gmdate( 'Y-m-d H:i:s', $timestamp );
		$visitorlog_users_resident_parameters['event'] = '';
		$visitorlog_users_resident_parameters['block'] = '';
		$visitorlog_users_resident_parameters['bot'] = '';
        $visitorlog_users_resident_parameters['botblock'] = '';
		$visitorlog_users_resident_parameters['timerelease'] = '';

		$canal = $this->detect_limits();
		$visitorlog_users_resident_parameters['canal'] = $canal;

		$param_user = $this->get_user_ip();
		$visitorlog_users_resident_parameters['ip']       = $param_user['ip'];
		$visitorlog_users_resident_parameters['username'] = $param_user['useragent'];
		$visitorlog_users_resident_parameters['url']      = $param_user['uri'];
		$visitorlog_users_resident_parameters['refinfo']  = $param_user['httpreferer'];

	} // END func

	/**
	 * Detect request type
	 * 
	 */
	public function detect_limits()
	{
		if ( $this->xhr_request() ) {
			$canal = 'xhr';
		}
		elseif ( isset($_POST['pwd']) ) { //phpcs:ignore 
			$canal = 'auth';
		}
		elseif ( isset($_POST) ) { //phpcs:ignore
			$canal = 'post';	
		}
		else {
			$canal = 'get';
		}

		return $canal;

	} // END func

	/**
	 * Detect request XHR
	 * 
	 * @return true/false
	 */
	public function xhr_request()
	{
		if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
			if ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' )
				return true;
		} else {
			$headers = getallheaders();
			if ( isset($headers['X-Requested-With']) && $headers['X-Requested-With'] == 'XMLHttpRequest' )
				return true;
		}
		return false;

	} // END func

    /**
     * Get the user ip
     *
     * @param string $Flag - ''/'1' 
     * @return adresses $http_host, $http_referer, $user_agent, $uri 
     * @return $ip 
     */
    public function get_user_ip( $Flag='' )
    {
        if ( !empty( $_SERVER['HTTP_HOST'] ) ) {
            $http_host = trim(sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])));
            $http_host = htmlspecialchars( substr( $http_host, 0, 150 ) );
        } else {
            $http_host = '';
        }
        if ( !empty( $_SERVER['HTTP_REFERER'] ) ) {
            $http_referer = trim(sanitize_text_field(wp_unslash($_SERVER['HTTP_REFERER'])));
            $http_referer = htmlspecialchars( substr( $http_referer, 0, 150 ) );
        } else {
            $http_referer = '';
        }    
        if ( !empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
            $user_agent = trim(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])));
            $user_agent = htmlspecialchars( substr( $user_agent, 0, 150 ) );
        } else {
            $user_agent = '';
        }
        if ( !empty( $_SERVER['REQUEST_URI'] ) ) {
            $uri = trim(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])));
            $uri = htmlspecialchars( substr( $uri, 0, 150 ) );
        } else {
            $uri = '';
        }

        $ip = '';
        if ( '' == $Flag ) {

            if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
                $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
            } elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
                $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
            } elseif ( !empty( $_SERVER['REMOTE_ADDR'] ) ) {
                $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
            }
        } elseif ( !empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }
    
        $ip = trim( wp_strip_all_tags($ip) );
        $ip = htmlspecialchars( substr($ip, 0, 50) );

        $param['ip'] = $ip;
        $param['httphost'] = $http_host;
        $param['httpreferer'] = $http_referer;
        $param['useragent'] = $user_agent;
        $param['uri'] = $uri;

        return $param;

    } // END of func


} // END class