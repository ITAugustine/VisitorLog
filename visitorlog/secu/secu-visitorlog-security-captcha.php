<?php
/**
 * Security Captcha.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security_Captcha class
 *
 * @method public    static get_class_name()
 * @method public    static function captcha_form()
 * @method protected static function generate_maths_question()
 * @method protected static function number_word_mapping() 
 * @method public    static function verify_captcha_submit() 
 * @method public    static function verify_math_captcha_answer()
 * @method public    static function recaptcha_form()
 * @method protected static function verify_google_recaptcha()
 */
class VisitorLog_Security_Captcha
{
	public static function get_class_name()
    {
		return __CLASS__;
	}

    /**
     * Displays simple maths captcha form
     * 
     */
    public static function captcha_form()
    {
        echo '<p><label for="visitorlog-captcha-answer">';
        echo esc_html__('Please enter an answer in digits:', 'visitorlog');
        echo '</label><div><strong>';
        self::generate_maths_question();
        echo '</strong></div></p>';

    } // END func

    /**
     * generate maths question
     * 
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Security_Utility::generate_alpha_numeric_random_string()
     * @uses VisitorLog_Security_Utility::is_multisite_install()
     */   
    protected static function generate_maths_question( $mode='' )
    {
        //For now we will only do plus, minus, multiplication
        $equation_string = '';
        $operator_type   = array('&#43;', '&#8722;', '&#215;');
        $operand_display = array('word', 'number');
        
        //let's now generate an equation
        $operator = $operator_type[wp_rand(0,2)];

        if($operator === '&#215;'){
            //Don't make the question too hard if multiplication
            $first_digit  = wp_rand(1,5);    
            $second_digit = wp_rand(1,5); 
        }else{
            $first_digit  = wp_rand(1,20);
            $second_digit = wp_rand(1,20); 
        }
        
        if ($operand_display[wp_rand(0,1)] == 'word'){
            $first_operand = self::number_word_mapping( $first_digit );
        } else {
            $first_operand = $first_digit;
        }
        
        if ($operand_display[wp_rand(0,1)] == 'word'){
            $second_operand = self::number_word_mapping( $second_digit );
        } else {
            $second_operand = $second_digit;
        }

        //Let's caluclate the result and construct the equation string
        if ($operator === '&#43;') {
            //Addition
            $result = $first_digit+$second_digit;
            $equation_string .= $first_operand . ' ' . $operator . ' ' . $second_operand . ' = ';
        }
        elseif ($operator === '&#8722;') {
            //Subtraction
            //If we are going to be negative let's swap operands around
            if ($first_digit < $second_digit){
                $equation_string .= $second_operand . ' ' . $operator . ' ' . $first_operand . ' = ';
                $result = $second_digit-$first_digit;
            } else {
                $equation_string .= $first_operand . ' ' . $operator . ' ' . $second_operand . ' = ';
                $result = $first_digit-$second_digit;
            }
        }
        elseif ($operator === '&#215;') {
            //Multiplication
            $equation_string .= $first_operand . ' ' . $operator . ' ' . $second_operand . ' = ';
            $result = $first_digit * $second_digit;
        }
        
        //Let's encode correct answer
        $captcha_secret_string = VisitorLog_Utility::get_option_sl( 'captcha_secret_key' );
        $current_time = time();
        $enc_result = base64_encode( $current_time . $captcha_secret_string . $result );
        $random_str = VisitorLog_Security_Utility::generate_alpha_numeric_random_string(10);

        VisitorLog_Security_Utility::is_multisite_install() ? 
        	set_site_transient( 'visitorlog_captcha_string_info_' . $random_str, $enc_result, 30 * 60 ) : 
                 set_transient( 'visitorlog_captcha_string_info_' . $random_str, $enc_result, 30 * 60 );

        if ( '' == $mode ) {
            echo '<input type="hidden" name="visitorlog-captcha-string-info" id="visitorlog-captcha-string-info" value="'.esc_html($random_str).'" />';
            echo '<input type="hidden" name="visitorlog-captcha-temp-string" id="visitorlog-captcha-temp-string" value="'.esc_html($current_time).'" />';
            echo '<input type="text" size="2" id="visitorlog-captcha-answer" name="visitorlog-captcha-answer" value="" autocomplete="off" />';
        } else {
            $equation_string .= '<input type="hidden" name="visitorlog-captcha-string-info" id="visitorlog-captcha-string-info" value="'.$random_str.'" />';
            $equation_string .= '<input type="hidden" name="visitorlog-captcha-temp-string" id="visitorlog-captcha-temp-string" value="'.$current_time.'" />';
            $equation_string .= '<input type="text" size="2" id="visitorlog-captcha-answer" name="visitorlog-captcha-answer" value="" autocomplete="off" />';

            return $equation_string;
        }        

    } // END func

    
    protected static function number_word_mapping( $num )
    {
        $number_map = array(
            1 => __('one', 'visitorlog'),
            2 => __('two', 'visitorlog'),
            3 => __('three', 'visitorlog'),
            4 => __('four', 'visitorlog'),
            5 => __('five', 'visitorlog'),
            6 => __('six', 'visitorlog'),
            7 => __('seven', 'visitorlog'),
            8 => __('eight', 'visitorlog'),
            9 => __('nine', 'visitorlog'),
            10 => __('ten', 'visitorlog'),
            11 => __('eleven', 'visitorlog'),
            12 => __('twelve', 'visitorlog'),
            13 => __('thirteen', 'visitorlog'),
            14 => __('fourteen', 'visitorlog'),
            15 => __('fifteen', 'visitorlog'),
            16 => __('sixteen', 'visitorlog'),
            17 => __('seventeen', 'visitorlog'),
            18 => __('eighteen', 'visitorlog'),
            19 => __('nineteen', 'visitorlog'),
            20 => __('twenty', 'visitorlog'),
        ); 
        return $number_map[$num];

    } // END func
    
     
    /**
     * Verifies the math or Google recaptcha v2 forms
     * Returns TRUE if correct answer.
     * Returns FALSE on wrong captcha result or missing data.
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @return boolean
     */
    public static function verify_captcha_submit()
    {
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_login_recaptcha' ) ) {

            //Google reCaptcha enabled
            if ( array_key_exists('g-recaptcha-response', $_POST) ) { //phpcs:ignore

                $g_recaptcha_response = isset($_POST['g-recaptcha-response']) ? sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) : ''; //phpcs:ignore
                $verify_captcha = self::verify_google_recaptcha( $g_recaptcha_response );
                return $verify_captcha;
            } else {
                // Expected captcha field in $_POST but got none!
                return false;
            }            
        } elseif ( 'on' == VisitorLog_Utility::get_option_sl( 'on_login_captcha' ) ) {

	        // math captcha is enabled
	        if ( array_key_exists('visitorlog-captcha-answer', $_POST) ) { //phpcs:ignore

 	            $captcha_answer = isset($_POST['visitorlog-captcha-answer']) ? sanitize_text_field(wp_unslash($_POST['visitorlog-captcha-answer'])) : ''; //phpcs:ignore
	            $verify_captcha = self::verify_math_captcha_answer( $captcha_answer );
	            return $verify_captcha;
	        } else {
	            // Expected captcha field in $_POST but got none!
	            return false;
	        } 
	    }    

    } // END func

    /**
     * Verifies the math captcha comment submit
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @return boolean
     */
    public static function verify_captcha_comment_submit()
    {
        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_comment_recaptcha' ) ) {

            //Google reCaptcha enabled
            if ( array_key_exists('g-recaptcha-response', $_POST) ) { //phpcs:ignore

                $g_recaptcha_response = isset($_POST['g-recaptcha-response']) ? sanitize_text_field(wp_unslash($_POST['g-recaptcha-response'])) : ''; //phpcs:ignore
                $verify_captcha = self::verify_google_recaptcha( $g_recaptcha_response );
                return $verify_captcha;
            } else {
                // Expected captcha field in $_POST but got none!
                return false;
            }            
        } elseif ( 'on' == VisitorLog_Utility::get_option_sl( 'on_comment_captcha' ) ) {

            // math captcha is enabled
            if ( array_key_exists('visitorlog-captcha-answer', $_POST) ) { //phpcs:ignore

                $captcha_answer = isset($_POST['visitorlog-captcha-answer']) ? sanitize_text_field(wp_unslash($_POST['visitorlog-captcha-answer'])) : ''; //phpcs:ignore
                $verify_captcha = self::verify_math_captcha_answer( $captcha_answer );
                return $verify_captcha;
            } else {
                // Expected captcha field in $_POST but got none!
                return false;
            } 
        }    

    } // END func
   
    /**
     * Verifies the math captcha answer entered by the user
     *
     * @param type $captcha_answer
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Security_Utility::is_multisite_install()
     * @return boolean
     */
    public static function verify_math_captcha_answer( $captcha_answer='' )
    {
        $captcha_secret_string = VisitorLog_Utility::get_option_sl( 'captcha_secret_key' );

        if ( isset($_POST['visitorlog-captcha-temp-string']) ) { //phpcs:ignore

            $captcha_temp_string = sanitize_text_field(wp_unslash($_POST['visitorlog-captcha-temp-string'])); //phpcs:ignore
        } else {
            $captcha_temp_string = '';
        }
 
        $submitted_encoded_string = base64_encode( $captcha_temp_string . $captcha_secret_string . $captcha_answer );

        if ( isset($_POST['visitorlog-captcha-string-info']) ) { //phpcs:ignore

            $trans_handle = sanitize_text_field(wp_unslash($_POST['visitorlog-captcha-string-info'])); //phpcs:ignore
        } else {
            $trans_handle = '';
        }

        $captcha_string_info_trans = ( VisitorLog_Security_Utility::is_multisite_install() ? 
        				    		get_site_transient( 'visitorlog_captcha_string_info_' . $trans_handle ) : 
        					   		get_transient( 'visitorlog_captcha_string_info_' . $trans_handle ) );

        if ( $submitted_encoded_string === $captcha_string_info_trans ) {
            return true;
        } else {
            return false; // wrong answer was entered
        }

    } // END func

    /**
     * Displays Google reCaptcha form v2
     * 
     * @uses VisitorLog_Utility::get_option_sl()
     */
    public static function recaptcha_form()
    {
        $site_key = VisitorLog_Utility::get_option_sl( 'recaptcha_site_key' );

        echo '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div class="g-recaptcha" data-sitekey="' . esc_html($site_key) . '"></div></div>';

    } // END func

    /**
     * Send a query to Google api to verify reCaptcha submission
     *
     * @param  type $resp_token
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Security_Utility_IP::get_user_ip_address()
     * @return boolean
     */
    protected static function verify_google_recaptcha( $resp_token='' )
    {
        $is_humanoid = false;

        if ( empty( $resp_token ) ) return false;

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        
        $sitekey = VisitorLog_Utility::get_option_sl( 'recaptcha_site_key' );
        $secret  = VisitorLog_Utility::get_option_sl( 'recaptcha_secret_key' );

        $ip_address = VisitorLog_Security_Utility_IP::get_user_ip_address();
        $response   = wp_safe_remote_post( $url, array( 'body' => array( 
											                'secret'   => $secret,
											                'response' => $resp_token,
											                'remoteip' => $ip_address,
    														 ),
        											  ) );

        if ( wp_remote_retrieve_response_code( $response ) != 200 ) return $is_humanoid;

        $response = wp_remote_retrieve_body( $response );
        $response = json_decode( $response, true );

        if ( isset( $response['success'] ) && $response['success'] == true ) return true;
        
        return $is_humanoid; 

    } // END func


} // END class