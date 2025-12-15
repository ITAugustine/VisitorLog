<?php
/**
 * Spam Protection
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security_Spam_Protection class.
 *
 * @method public static    function get_class_name()
 * @method public           function __construct()
 * @method public           function spam_comment_post()
 * @method public           function transition_comment_status()
 * @method public           function insert_captcha_form()
 * @method public           function insert_recaptcha_form()
 * @method public           function add_recaptcha_script()
 * @method public           function process_comment_post()
 * @method protected        function block_comment_ip()
 */
class VisitorLog_Security_Spam_Protection
{ 
	public static function get_class_name()
	{
			return __CLASS__;
	}

	public function __construct()
	{
		if ( '' == VisitorLog_Utility::get_option_sl( 'on_spam_protection' ) ) return;

		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_forbid_commentspost' ) ) {
			add_action( 'comment_post',              array( &$this, 'spam_comment_post' ), 10, 2 );
			add_action( 'transition_comment_status', array( &$this, 'transition_comment_status' ), 10, 3 );
		}

		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_comment_recaptcha' ) ) {
			add_action( 'wp_head',                      array( &$this, 'add_recaptcha_script' ) );
			add_action( 'comment_form_after_fields',    array( &$this, 'insert_recaptcha_form'), 1 );
			add_action( 'comment_form_logged_in_after', array( &$this, 'insert_recaptcha_form'), 1 );
			add_filter( 'preprocess_comment',           array( &$this, 'process_comment_post' ) );
			$recaptcha = 'on';
		} else {
			$recaptcha = 'off';
		}  

		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_comment_captcha' ) ) {
			if ( 'off' == $recaptcha ) {
				add_action( 'comment_form_after_fields',    array( &$this, 'insert_captcha_form'), 1);
				add_action( 'comment_form_logged_in_after', array( &$this, 'insert_captcha_form'), 1);
				add_filter( 'preprocess_comment',           array( &$this, 'process_comment_post' ) );
			}
		} 

	} // end __construct()

	/**
	 * spam comment post.
	 */
	public function spam_comment_post( $comment_id, $comment_approved )
	{
		if ( $comment_approved === "spam" ) {
			$block = $this->block_comment_ip( $comment_id );
		}

	} // END func

	/**
	 * transition comment status.
	 */
	public function transition_comment_status( $new_status, $old_status, $comment )
	{
		if ( $new_status == 'spam' ) {
			$block = $this->block_comment_ip( $comment->comment_ID );
		}

	} // END func  

	/**
	 * insert captcha form.
	 *
	 * @uses VisitorLog_Utility::get_option_sl() 
	 * @uses VisitorLog_Security_Captcha::captcha_form() 
	 */
    public function insert_captcha_form()
    {
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_spam_protection' ) &&
             'on' == VisitorLog_Utility::get_option_sl( 'on_comment_captcha' ) ) {

            VisitorLog_Security_Captcha::captcha_form();  
            return;      
        }
         
    } // END func

	/**
	 * insert recaptcha form.
	 *
	 * @uses VisitorLog_Utility::get_option_sl() 
	 * @uses VisitorLog_Security_Captcha::recaptcha_form() 
	 */
	public function insert_recaptcha_form()
	{
		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_spam_protection' ) &&
			 'on' == VisitorLog_Utility::get_option_sl( 'on_comment_recaptcha' ) ) {

			$calling_hook = current_filter();
			$site_key = VisitorLog_Utility::get_option_sl( 'recaptcha_site_key' );

			if ( $calling_hook == 'woocommerce_login_form' || $calling_hook == 'woocommerce_lostpassword_form') {
				echo '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div id="woo_recaptcha_1" class="g-recaptcha" data-sitekey="' . esc_html($site_key) . '"></div></div>';
				return;
			}
			if ( $calling_hook == 'woocommerce_register_form' ) {
				echo '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div id="woo_recaptcha_2" class="g-recaptcha" data-sitekey="' . esc_html($site_key) . '"></div></div>';
				return;
			}

			VisitorLog_Security_Captcha::recaptcha_form();
			return;
		}
		 
	} // END func
	
	/**
	 * Enqueues the Google recaptcha api URL in the wp_head for general pages
	 * Caters for scenarios when recaptcha used on wp comments or custom wp login form
	 * 
	 * @uses VisitorLog_Utility::get_option_sl() 
	 */
	public function add_recaptcha_script()
	{
		if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_spam_protection' ) &&
			 'on' == VisitorLog_Utility::get_option_sl( 'on_comment_recaptcha' ) ) {

			// Enqueue the recaptcha api url 
			// Do NOT enqueue if this is the main woocommerce account login page because for woocommerce page we "explicitly" render the recaptcha widget
			$is_woo = false;
			
			// We don't want to load for woo account page because we have a special function for this
			if ( function_exists('is_account_page') ) {
				// Check if this a woocommerce account page
				$is_woo = is_account_page(); 
			}
											 
			if ( empty($is_woo) ) {
				//only enqueue when not a woocommerce page
				wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), 1.0, false );
			} 
		}   

	} // END func

	/**
	 * process comment post
	 * 
	 * @uses VisitorLog_Security_Captcha::verify_captcha_comment_submit() 
	 */
	public function process_comment_post( $comment )
	{
		//Don't process captcha for comment replies inside admin menu
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'replyto-comment' &&
			 ( check_ajax_referer('replyto-comment', '_ajax_nonce', false) || 
			   check_ajax_referer('replyto-comment', '_ajax_nonce-replyto-comment', false) ) ) {

			return $comment;
		}

		//Don't do captcha for pingback/trackback
		if ( $comment['comment_type'] != '' && 
			 $comment['comment_type'] != 'comment' && 
			 $comment['comment_type'] != 'review') {

			return $comment;
		}
		
		$verify_captcha = VisitorLog_Security_Captcha::verify_captcha_comment_submit(); 

		if ( $verify_captcha === false ) {
			//Wrong answer
			wp_die( esc_html__( 'Error: You entered an incorrect CAPTCHA answer. Please go back and try again', 'visitorlog') );
		} else {
			return $comment;
		}

	} // END func

	/**
	 * block comment ip
	 * 
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 * @uses VisitorLog_Logger::instance()->warning()
	 */
	protected function block_comment_ip( $comment_id )
	{
		global $visitorlog_users_resident_parameters;

        $timestamp = $visitorlog_users_resident_parameters['timestamp'];		
		$comment_obj = get_comment( $comment_id );

		$comment_date         = $comment_obj->comment_date;
		$comment_author       = $comment_obj->comment_author;
		$comment_author_ip    = $comment_obj->comment_author_IP;
		$comment_author_url   = $comment_obj->comment_author_url;
		$comment_author_email = $comment_obj->comment_author_email;
		$comment_agent        = $comment_obj->comment_agent;
		$comment_content      = $comment_obj->comment_content;

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'spam';

		$array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `author_ip` != %s", $comment_author_ip), ARRAY_A);
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$err = 'block_comment_ip()-Error SELECT into table-spam: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
			VisitorLog_Logger::instance()->warning( $err );
			return false;
		}

		if ( !empty($array) ) {

			$block = $arr[0]['block'];
			if ( '' == $block ) $block = 0;
			$block++;
			$data = array(
				'timestamp'    => $timestamp,
				'date_time'    => $comment_date,
				'author_ip'    => $comment_author_ip,
				'block'        => $block,
				'reason'       => 'Comment',
				'author_url'   => $comment_author_url,
				'author'       => $comment_author,
				'content'      => $comment_content,
				'refinfo'      => $visitorlog_users_resident_parameters['refinfo'],
				'author_email' => $comment_author_email,
				'agent'        => $comment_agent
			);
			$result = VisitorLog_DB_Base::$wpdb_vl->update( $table_name, $data, ['author_ip'=>$comment_author_ip]);
	        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
	            $err = 'block_comment_ip()-Error update table spam: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
	            VisitorLog_Logger::instance()->warning($err);
	            return false;
	        }

		} else {

			$block = 1;
			$data = array(
				'timestamp'    => $timestamp,
				'date_time'    => $comment_date,
				'author_ip'    => $comment_author_ip,
				'block'        => $block,
				'reason'       => 'Comment',
				'author_url'   => $comment_author_url,
				'author'       => $comment_author,
				'content'      => $comment_content,
				'refinfo'      => $visitorlog_users_resident_parameters['refinfo'],
				'author_email' => $comment_author_email,
				'agent'        => $comment_agent
			);
			$result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
	        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
	            $err = 'block_comment_ip()-Error insert table spam: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
	            VisitorLog_Logger::instance()->warning($err);
	            return false;
	        }  

	        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT COUNT(id) FROM $table_name WHERE `id` != %d", 0), ARRAY_A);
	        $c_rows = $array[0]['COUNT(id)'];
	        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
	            $err = 'block_comment_ip()-Error select table spam: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
	            VisitorLog_Logger::instance()->warning($err);
	            return false;
	        }  

	        $rows_record_limit = (int)VisitorLog_Utility::get_option_sl( '#_record_limit' );
	        if ( $c_rows > $rows_record_limit ) {
	            $delete = $c_rows - $rows_record_limit;
	            $result2 = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name  ORDER BY `id` ASC LIMIT %d", $delete));
	        }
	        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
	            $err = 'block_comment_ip()-Error delete rows into spam: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
	            VisitorLog_Logger::instance()->warning($err);
	            return false;
	        } 
		}
		return $block;

	} // END func


} // END class 