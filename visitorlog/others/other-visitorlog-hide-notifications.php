<?php
/**
 * Hide notifications from WordPress
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit('Silens is gold'); 

/**
 * Methods of VisitorLog_Hide_Notifications class.
 *
 * @method public    static function get_class_name()
 * @method public    static function init_menu()
 * @method public    static function init_left_menu()
 * @method public    static function render_settings()
 * @method protected static function settings_options_page()
 */
class VisitorLog_Hide_Notifications
{
	/**
	 * Render hide notifications from WordPress.
	 *
	 * @uses VisitorLog_UI::render_top_header()
	 * @uses VisitorLog_View::render_preloader()
	 */
	public static function render_hide_notifications()
	{
		$msg = $err = '';
		$update_hide_notifications = false;

        $total_update_admin_bar  = VisitorLog_Utility::get_option_sl( 'on_total_update_admin_bar' );
        $wp_available_console    = VisitorLog_Utility::get_option_sl( 'on_wp_available_console' );
        $download_version_footer = VisitorLog_Utility::get_option_sl( 'on_download_version_footer' );
        $total_update_admin_menu = VisitorLog_Utility::get_option_sl( 'on_total_update_admin_menu' );
        $plugin_update           = VisitorLog_Utility::get_option_sl( 'on_plugin_update' );

        if ( isset( $_POST['visitorlog_submit_execute'] ) ) {

	        if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
	            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
	            wp_die();
	        }

        	if ( isset($_POST['visitorlog_total_update_admin_bar']) ) {
		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                	$post = sanitize_text_field(wp_unslash($_POST['visitorlog_total_update_admin_bar']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
        	} else {
        		$post = '';
        	}
            if ( $post != $total_update_admin_bar ) {
                VisitorLog_Utility::update_option_sl( 'on_total_update_admin_bar', $post );
                $total_update_admin_bar = $post;
                if ( '' == $post ) {
	                $update_hide_notifications = true;
                }
            }

        	if ( isset($_POST['visitorlog_wp_available_console']) ) {
		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                	$post = sanitize_text_field(wp_unslash($_POST['visitorlog_wp_available_console']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
        	} else {
        		$post = '';
        	}
            if ( $post != $wp_available_console ) {
                VisitorLog_Utility::update_option_sl( 'on_wp_available_console', $post );
                $wp_available_console = $post;
                if ( '' == $post ) {
	                $update_hide_notifications = true;
                }
            }

        	if ( isset($_POST['visitorlog_download_version_footer']) ) {
		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                	$post = sanitize_text_field(wp_unslash($_POST['visitorlog_download_version_footer']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
        	} else {
        		$post = '';
        	}
            if ( $post != $download_version_footer ) {
                VisitorLog_Utility::update_option_sl( 'on_download_version_footer', $post );
                $download_version_footer = $post;
                if ( '' == $post ) {
	                $update_hide_notifications = true;
                }
            }

        	if ( isset($_POST['visitorlog_total_update_admin_menu']) ) {
		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                	$post = sanitize_text_field(wp_unslash($_POST['visitorlog_total_update_admin_menu']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
        	} else {
        		$post = '';
        	}
            if ( $post != $total_update_admin_menu ) {
                VisitorLog_Utility::update_option_sl( 'on_total_update_admin_menu', $post );
                $total_update_admin_menu = $post;
                if ( '' == $post ) {
	                $update_hide_notifications = true;
                }
            }

        	if ( isset($_POST['visitorlog_plugin_update']) ) {
		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                	$post = sanitize_text_field(wp_unslash($_POST['visitorlog_plugin_update']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
        	} else {
        		$post = '';
        	}
            if ( $post != $plugin_update ) {
                VisitorLog_Utility::update_option_sl( 'on_plugin_update', $post );
                $plugin_update = $post;
                if ( '' == $post ) {
	                $update_hide_notifications = true;
                }
            }

            if ( $update_hide_notifications ) {
            	self::hide_notifications();
                $msg = __( 'The selected notifications were successfully hidden', 'visitorlog' );
            }
            //--------------------------------------Message
	        if ( '' != $msg ) {
	        	$color = 'green';
	            VisitorLog_System_View::show_message( $color, $msg );
	            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
	            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
	            VisitorLog_Utility::vl_redirect( 'visitorlog_hidenotifications' );
	            exit;
	        }

        } // END if

        // Display--------

        $msg = VisitorLog_Utility::get_option_sl( 'temp_message' );
        if ( '' != $msg ) {
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }

		switch ($total_update_admin_bar) {
			case 'on':
				$total_uab = 'checked';
				break;
			default:
				$total_uab = '';
				break;
		}		
		switch ($wp_available_console) {
			case 'on':
				$wp_available = 'checked';
				break;
			default:
				$wp_available = '';
				break;
		}	
		switch ($download_version_footer) {
			case 'on':
				$download_version = 'checked';
				break;
			default:
				$download_version = '';
				break;
		}
		switch ($total_update_admin_menu) {
			case 'on':
				$total_uam = 'checked';
				break;
			default:
				$total_uam = '';
				break;
		}
		switch ($plugin_update) {
			case 'on':
				$plugin_u = 'checked';
				break;
			default:
				$plugin_u = '';
				break;
		}
        ?>  
		<form method="post">
		<?php wp_nonce_field( 'visitorlog_nonce' );?>
		<div style="margin-top:10px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead class="vl-color-thead">
                                    <tr>
                                    <tr>
                                        <td width="45"> <?php esc_html_e('Enable','visitorlog');?></td>
                                        <td width="220"><?php esc_html_e('Functions','visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description','visitorlog');?></td> 
                                    </tr> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
									<tr>
										<td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_total_update_admin_bar" name="visitorlog_total_update_admin_bar" type="checkbox" value="on" <?php echo esc_html($total_uab);?>>
                                                    <label for="visitorlog_total_update_admin_bar"></label>
                                                </div>
                                            </div>
										</td>
				                        <td>
<?php esc_html_e('Total update counter in the admin bar (engine + themes + plugins + translations)', 'visitorlog');?>
				                        </td>  
				                        <td>
<?php
VisitorLog_Utility::render_picture('total_counter.png');
?>
				                        </td>
									</tr>
									<tr>
										<td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_wp_available_console" name="visitorlog_wp_available_console" type="checkbox" value="on" <?php echo esc_html($wp_available);?>>
                                                    <label for="visitorlog_wp_available_console"></label>
                                                </div>
                                            </div>
										</td>
				                        <td>
<?php esc_html_e('"WordPress X.X Available" in the Console', 'visitorlog');?>
				                        </td>  
				                        <td>
<?php
VisitorLog_Utility::render_picture('wp_available_console.png');
?>
				                        </td>
									</tr>
									<tr>
										<td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_download_version_footer" name="visitorlog_download_version_footer" type="checkbox" value="on" <?php echo esc_html($download_version);?>>
                                                    <label for="visitorlog_download_version_footer"></label>
                                                </div>
                                            </div>
										</td>
				                        <td>
<?php esc_html_e('"Download Version X.X" in the footer', 'visitorlog');?>
				                        </td>  
				                        <td>
<?php
VisitorLog_Utility::render_picture('version_footer.png');
?>
				                        </td>
									</tr>
									<tr>
										<td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_plugin_update" name="visitorlog_plugin_update" type="checkbox" value="on" <?php echo esc_html($plugin_u);?>>
                                                    <label for="visitorlog_plugin_update"></label>
                                                </div>
                                            </div>
										</td>
				                        <td>
<?php esc_html_e('Plugin update counter in the admin menu', 'visitorlog');?>
				                        </td>  
				                        <td>
<?php
VisitorLog_Utility::render_picture('plugin_update.png');
?>
				                        </td>
									</tr>
									<tr>
										<td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_total_update_admin_menu" name="visitorlog_total_update_admin_menu" type="checkbox" value="on" <?php echo esc_html($total_uam);?>>
                                                    <label for="visitorlog_total_update_admin_menu"></label>
                                                </div>
                                            </div>
										</td>
				                        <td>
<?php esc_html_e('Total update counter in the admin menu (engine + themes + plugins + translations)', 'visitorlog');?>
				                        </td>  
				                        <td>
<?php
VisitorLog_Utility::render_picture('update_menu.png');
?>
				                        </td>
									</tr>
                                </tbody>
                            </table>
							<div style="margin-top:10px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=otherfeatures' );
?>
							</div>
							<div style="display:flex;flex-direction:row;justify-content:flex-end;margin-top:-20px;">
<input type="submit" name="visitorlog_submit_execute" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
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
	 * Hiding notifications of new WordPress version in admin panel
	 *
	 * @uses VisitorLog_Utility::get_option_sl()
	 */
	public static function hide_notifications()
	{
        $total_update_admin_bar  = VisitorLog_Utility::get_option_sl( 'on_total_update_admin_bar' );
        $wp_available_console    = VisitorLog_Utility::get_option_sl( 'on_wp_available_console' );
        $download_version_footer = VisitorLog_Utility::get_option_sl( 'on_download_version_footer' );
        $total_update_admin_menu = VisitorLog_Utility::get_option_sl( 'on_total_update_admin_menu' );
        $plugin_update           = VisitorLog_Utility::get_option_sl( 'on_plugin_update' );

        if ( '' == $total_update_admin_bar ) {

			// Total update counter in the admin bar
			add_action( 'admin_bar_menu', function ( $wp_adminbar ) {
				$wp_adminbar->remove_node( 'updates' );
			}, 999 );
        }

        if ( '' == $wp_available_console ) {

			add_action( 'admin_menu', function () {

				if ( is_multisite() ) {
					// "WordPress X.X Available" in the Console - For Multisite installation
					remove_action( 'network_admin_notices', 'update_nag', 3 );
				} else {
					// "WordPress X.X Available" in the Console - For Single installation
					remove_action( 'admin_notices', 'update_nag', 3 );
				}
			}, 999 );
        }

        if ( '' == $download_version_footer ) {

			add_action( 'admin_menu', function () {

				// "Download Version X.X" in the footer
				remove_action( 'update_footer', 'core_update_footer' );
			}, 999 );
        }

        if ( '' == $total_update_admin_menu ) {

			add_action( 'admin_menu', function () {

				// Total update counter in the admin menu
				remove_submenu_page( 'index.php', 'update-core.php' );
			}, 999 );
        }

        if ( '' == $plugin_update ) {

			add_action( 'admin_menu', function () {

				// Plugin update counter in the admin menu
				$GLOBALS['menu'][65][0] = __( 'Plugins', 'visitorlog' );
			}, 999 );
        }

    } // END func


} // END class