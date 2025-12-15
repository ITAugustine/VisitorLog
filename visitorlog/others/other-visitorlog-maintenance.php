<?php
/**
 * Settings page
 * This class handles the maintenance function.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Maintenance class.
 *
 * @method public    static function get_class_name()
 * @method protected static function render_maintenance_content()
 * @method public    static function execute_maintenance()
 * @method protected static function stylesheet()
 */
class VisitorLog_Maintenance
{
	/**
	 * Get Class Name
	 * @return __CLASS__
	 */
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Render maintenance content.
	 *
	 * @uses VisitorLog_Logger::instance()->warning()
	 * @uses VisitorLog_System_View::show_message()
	 * @uses VisitorLog_Utility::update_option_sl()
	 * @uses VisitorLog_Utility::get_option_sl()
	 */
	public static function render_maintenance_content()
	{
		$msg = '';
		$color = '';
        $on_maintenance        = VisitorLog_Utility::get_option_sl( 'on_maintenance' );
		$maintenance_textarea1 = VisitorLog_Utility::get_option_sl( 'maintenance_textarea1' );
        $maintenance_textarea2 = VisitorLog_Utility::get_option_sl( 'maintenance_textarea2' );

        if ( isset( $_POST['visitorlog_submit_execute'] ) ) {

	        if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
	            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
	            wp_die();
	        }

            if ( isset($_POST['visitorlog_on_maintenance']) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                	$post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_maintenance']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
            } else {
                $post = ''; 
            }
            if ( $post != $on_maintenance ) {
                VisitorLog_Utility::update_option_sl( 'on_maintenance', $post );
                $on_maintenance = $post;
            }

            if ( isset($_POST['visitorlog_maintenance_textarea']) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                	$post = sanitize_text_field(wp_unslash($_POST['visitorlog_maintenance_textarea']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
            } else {
                $post = ''; 
            }
            if ( $post != $maintenance_textarea2 ) {
	            $post = trim( $post );
	           	$post = htmlentities( stripslashes($post), ENT_COMPAT, "UTF-8" );
	            VisitorLog_Utility::update_option_sl( 'maintenance_textarea2', $post );
                $maintenance_textarea2 = $post;

	            $msg = __( 'Site lockout feature settings saved', 'visitorlog' );
	            $color = 'green';
            }
	        if ( '' != $msg ) {
	            VisitorLog_System_View::show_message( $color, $msg );
	            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
	            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
	        }
            VisitorLog_Utility::vl_redirect( 'visitorlog_maintenance' );
            exit;
        }
        //------------------------- Display
        
        if ( '' != VisitorLog_Utility::get_option_sl( 'temp_message' ) ) {
         
            $msg   = VisitorLog_Utility::get_option_sl( 'temp_message' );
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }

        switch ( $on_maintenance ) {
            case 'on':
                $checked = 'checked';
                break;
            default:
                $checked = '';
                break; 
        }
        if ( '' == $maintenance_textarea1 ) {
        	$maintenance_textarea1 = __( 'Under maintenance', 'visitorlog' );
        	VisitorLog_Utility::update_option_sl( 'maintenance_textarea1', $maintenance_textarea1 );
        } 
        if ( '' == $maintenance_textarea2 ) {
        	$maintenance_textarea2 = __( 'The site is currently under maintenance and unavailable. Please try again later', 'visitorlog' );
        	VisitorLog_Utility::update_option_sl( 'maintenance_textarea2', $maintenance_textarea2 );
        }        
        ?>  
		<form method="post">
		<?php wp_nonce_field( 'visitorlog_nonce' ); ?>
		<div style="margin-top:30px;">
		    <em style="font-size:16px;">
<?php esc_html_e('This feature allows you to put your site into "maintenance mode" by locking down the front-end to all visitors except logged in users with super admin privileges', 'visitorlog');?>.
		    </em>
		</div>
        <div class="row clearfix" style="margin-top:10px;">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody style="background-color:#F2F2F2;">
                                    <tr>
										<td width="45">
										    <div class="switch" >
										    	<div class="switch__1">
<input id="visitorlog_on_maintenance" type="checkbox" name="visitorlog_on_maintenance" value="on" <?php echo esc_html($checked);?>>
										    		<label for="visitorlog_on_maintenance"></label>
										    	</div>
										    </div>
										</td>
										<td colspan="3">
										    <?php esc_html_e('Enable Front-end Lockout', 'visitorlog');?>
										</td>

                                    </tr>
                                    <tr>
										<td width="45"></td>
										<td width="160">
										    <?php esc_html_e('Enter a Message', 'visitorlog');?>:
										</td>
										<td width="222">
<textarea name="visitorlog_maintenance_textarea" style="width:380px;height:120px;margin-left:0px;" autofocus><?php echo esc_html( htmlspecialchars($maintenance_textarea2) );?></textarea>
										</td>                                        
										<td>
<?php esc_html_e('Enter a message you wish to display to visitors when your site is in maintenance mode', 'visitorlog');?>.
										</td>
                                    </tr>
                                </tbody>
                            </table>
							<div style="margin-top:30px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=otherfeatures' );
?>
							</div>
							<div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_submit_execute" class="btn btn__primary" value="<?php esc_html_e('Execute', 'visitorlog');?>"/>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		</form>
		<?php
		exit;

	} // END func

	/**
	 * Execute maintenance.
	 *
	 * @uses VisitorLog_Utility::get_option_sl()
	 */
	public static function execute_maintenance()
	{
		$msg1 = VisitorLog_Utility::get_option_sl( 'maintenance_textarea1' );
		$msg2 = VisitorLog_Utility::get_option_sl( 'maintenance_textarea2' );
		?>
		<head>
		    <meta charset="UTF-8">
		    <title>Maintenance</title>
		    <?php self::stylesheet();?>
		</head>
		<body>
		<div class="container">
		    <div class="box">
		        <div class="animation">
		        <div class="one spin-one"></div>
		        <div class="two spin-two"></div>
		        <div class="three spin-one"></div>
		        </div>
		        <h1><?php echo esc_html( $msg1 );?></h1>
		        <p><?php echo esc_html( $msg2 );?></p>
		        <p></p>
		    </div>
		</div>
		</body>
		<?php
        exit();

	} // END func

	/**
	 *  Note:
	 *  In this case, it is necessary to download the style file, since the database tables 
	 *  must be generated before loading the styles of the admin_enqueue_scripts event.
	 */
	protected static function stylesheet()
	{
		echo '<'.'link '.'rel="stylesheet" href="';
		echo esc_url(VISITORLOG_PLUGIN_URL);
		echo 'assets/css/maintenance.css">';

	} // END func


} // END class