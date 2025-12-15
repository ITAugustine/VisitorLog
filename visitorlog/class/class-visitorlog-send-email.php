<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Send_Email class.
 *
 * @method public static function smtp_phpmailer_init()
 * @method public static function send_email()
 * @method public static function log_mailer_errors()
 */
class VisitorLog_Send_Email
{
	protected static $error = '';

	public static function class_name() {
		return __CLASS__;

	} // END func

    /**
     * Smtp phpmailer init
	 * Function for `phpmailer_init` filter-hook.
	 *
     * @param  object $phpmailer
     * @return object $phpmailer
     *
     * @uses VisitorLog_Settings::get_option_pass()
     * @uses VisitorLog_Utility::get_option_sl()
     */  
	public static function smtp_phpmailer_init( $phpmailer )
	{
        $host     = VisitorLog_Utility::get_option_sl( 'smtphost' );
        $port     = VisitorLog_Utility::get_option_sl( 'smtpport' );
        $username = VisitorLog_Utility::get_option_sl( 'smtpusername' );
        $password = VisitorLog_Settings::get_option_pass( 'smtppass' );

        $toaddress1 = VisitorLog_Utility::get_option_sl( 'send_stat_email1' );
        $toaddress2 = VisitorLog_Utility::get_option_sl( 'send_stat_email2' );
        $toaddress3 = VisitorLog_Utility::get_option_sl( 'send_stat_email3' );

        $fromaddress = $username;

        if ( '' == $username || '' == $password || '' == $toaddress1 ) {
			return $phpmailer;
        }

		$phpmailer->SMTPDebug = false;
		$phpmailer->IsSMTP();
		$phpmailer->CharSet  = 'UTF-8';
		$phpmailer->Username = $username;
		$phpmailer->Password = $password;
		$phpmailer->SMTPAuth = true;

		$phpmailer->Host = $host;
		$phpmailer->Port = $port;
		$phpmailer->From = $fromaddress;

		$phpmailer->SMTPSecure = 'ssl';
		$phpmailer->isHTML( true );

	    //Recipients
	    $phpmailer->setFrom( $fromaddress, 'Mailer' );
	    $phpmailer->addAddress( $toaddress1 );                             // Add a recipient
	    if ( '' != $toaddress2 ) $phpmailer->addAddress( $toaddress2 );    // Name is optional
	    if ( '' != $toaddress3 ) $phpmailer->addReplyTo( $toaddress3 );

		return $phpmailer;

	} // END func

    /**
     * Send email
     *
     * @param string $headers    - title
     *               $subject    - statistics/backup
     *               $attachment - link file
     * @return array $err        - error
     *         bool  true/false  - result
     * @uses VisitorLog_Settings::get_option_pass()
     * @uses VisitorLog_Utility::get_option_sl()
     */    
    public static function send_email( $message, $subject, $attachment, &$err )
    { 
        $toaddress1 = VisitorLog_Utility::get_option_sl( 'send_stat_email1' );
        $toaddress2 = VisitorLog_Utility::get_option_sl( 'send_stat_email2' );
        $toaddress3 = VisitorLog_Utility::get_option_sl( 'send_stat_email3' );
        $err = '';

		add_action( 'wp_mail_failed', array(self::class_name(), 'log_mailer_errors'), 10, 1 );
		add_filter( 'phpmailer_init', array(self::class_name(), 'smtp_phpmailer_init'), 10, 1 );

		$multiple_to_recipients = array(
			$toaddress1,
		);
		if ( '' != $toaddress2 && '' != $toaddress3 ) {
			$multiple_to_recipients = array(
				$toaddress1,
				$toaddress2,
				$toaddress3,
			);
		} elseif ( '' != $toaddress2 ) {
			$multiple_to_recipients = array(
				$toaddress1,
				$toaddress2,
			);
		} elseif ( '' != $toaddress3 ) {
			$multiple_to_recipients = array(
				$toaddress1,
				$toaddress3,
			);
		}
		$headers = array(
			'content-type: text/html',
		);
		$err = self::$error;

		return wp_mail( $multiple_to_recipients, $subject, $message, $headers, $attachment );

	} // END func

	public static function log_mailer_errors( $wp_error )
	{
		self::$error = $wp_error->get_error_message();

	} // END func


} // END class