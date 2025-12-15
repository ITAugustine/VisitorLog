<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Security_Controlpanel_View class
 * 
 * @method public static function control_panel_security()
 */
class VisitorLog_Security_Controlpanel
{
    /**
     * Render Control panel security.
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     */
    public static function control_panel_security()
    {
        $write_htaccess = false;

        $on_log_blocking_visitor   = VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' );
        $on_admin_panel_protection = VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' );
        $on_file_system_protection = VisitorLog_Utility::get_option_sl( 'on_file_system_protection' );
        $on_spam_protection        = VisitorLog_Utility::get_option_sl( 'on_spam_protection' );
        $on_firewall               = VisitorLog_Utility::get_option_sl( 'on_firewall' );
        $on_db_protection          = VisitorLog_Utility::get_option_sl( 'on_db_protection' );

        if ( isset ($_POST['visitorlog_control_panel_save']) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            if ( isset($_POST['visitorlog_on_blocking_visitor']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_blocking_visitor']));
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_log_blocking_visitor ) {
                VisitorLog_Utility::update_option_sl( 'on_log_blocking_visitor', $post );
                $on_log_blocking_visitor = $post;
                $write_htaccess = true;
            }

            if ( isset($_POST['visitorlog_on_admin_panel_protection']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_admin_panel_protection']));
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_admin_panel_protection ) {
                VisitorLog_Utility::update_option_sl( 'on_admin_panel_protection', $post );
                $on_admin_panel_protection = $post;
                $write_htaccess = true;
            }

            if ( isset($_POST['visitorlog_on_file_system_protection']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_file_system_protection']));
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_file_system_protection ) {
                VisitorLog_Utility::update_option_sl( 'on_file_system_protection', $post );
                $on_file_system_protection = $post;
                $write_htaccess = true;
            }

            if ( isset($_POST['visitorlog_on_spam_protection']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_spam_protection']));
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_spam_protection ) {
                VisitorLog_Utility::update_option_sl( 'on_spam_protection', $post );
                $on_spam_protection = $post;
                $write_htaccess = true;
            }

            if ( isset($_POST['visitorlog_on_firewall']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_firewall']));   
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_firewall ) {
                VisitorLog_Utility::update_option_sl( 'on_firewall', $post );
                $on_firewall = $post;
                $write_htaccess = true;
            }

            if ( isset($_POST['visitorlog_on_db_protection']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_db_protection'])); 
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_db_protection ) {
                VisitorLog_Utility::update_option_sl( 'on_db_protection', $post );
                $on_db_protection = $post;
            }

        } // END if

        if ( $write_htaccess ) {
            $htaccess_res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess();
            if ( !$htaccess_res ) {
                $err = __( 'Could not delete the directives from the .htaccess file. Please check the file permissions', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
            } else {
                $msg = __( 'Successful entry of the directives to the file .htaccess', 'visitorlog' );
                VisitorLog_System_View::show_message( 'green', $msg );
            }
        }

        switch ( $on_log_blocking_visitor ) {
            case 'on':
                $elbv = 'checked';
                break;
            default:
                $elbv = '';
                break; 
        }
        switch ( $on_admin_panel_protection ) {
            case 'on':
                $eapp = 'checked';
                break;
            default:
                $eapp = '';
                break; 
        }
        switch ( $on_db_protection ) {
            case 'on':
                $edp = 'checked';
                break;
            default:
                $edp = '';
                break; 
        }
        switch ( $on_file_system_protection ) {
            case 'on':
                $efsp = 'checked';
                break;
            default:
                $efsp = '';
                break; 
        }
        switch ( $on_spam_protection ) {
            case 'on':
                $esp = 'checked';
                break;
            default:
                $esp = '';
                break; 
        }
        switch ( $on_firewall ) {
            case 'on':
                $ef = 'checked';
                break;
            default:
                $ef = '';
                break; 
        }
        ?>  
        <form method="post">
        <?php wp_nonce_field('visitorlog_nonce');?>
        <div class="row clearfix" style="margin-top:-10px;">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="visitorlog_checkbox_select" value="on">
                                            &ensp;<?php esc_html_e('Enable/Disable all','visitorlog');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-14px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead class="vl-color-thead">
                                    <tr>
                                        <td width="45"> <?php esc_html_e('Enable', 'visitorlog');?></td>
                                        <td width="160"><?php esc_html_e('Functions', 'visitorlog');?></td>
                                        <td width="222"><?php esc_html_e('Parameters', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td colspan="4">
                                            <em>
                                                <span style="font-size:16px;">
                <?php esc_html_e('Modules and functions for protecting your site', 'visitorlog');?>
                                                </span>
                                            </em>
                                        </td>
                                    </tr>        
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_blocking_visitor" name="visitorlog_on_blocking_visitor" type="checkbox" value="on" <?php echo esc_html($elbv);?>>
                                                    <label for="visitorlog_on_blocking_visitor"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
<?php esc_html_e('Registration and blocking of unwanted visitor','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Logging & blocking functions', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_blockvisitor' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('Enable this if you want to register and block unwanted site visitors and bots', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_admin_panel_protection" name="visitorlog_on_admin_panel_protection" type="checkbox" value="on" <?php echo  esc_html($eapp);?>>
                                                    <label for="visitorlog_on_admin_panel_protection"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Admin panel protection','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Admin panel security features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_adminpanel' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('Security management functions of the site Control Panel', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_db_protection" name="visitorlog_on_db_protection" type="checkbox" value="on" <?php echo esc_html($edp);?>>
                                                    <label for="visitorlog_on_db_protection"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Enable Database protection','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Database protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_dbprotection' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('When attacking databases, hackers use SQL injections and malicious and automated code. For protection, we use changing the prefix of WordPress tables and backing up the database', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_file_system_protection" name="visitorlog_on_file_system_protection" type="checkbox" value="on" <?php echo  esc_html($efsp);?>>
                                                    <label for="visitorlog_on_file_system_protection"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Enable file system protection','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('File system protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_fileprotection' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('Ban on editing PHP files. Forbidding access to WP files','visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_spam_protection" name="visitorlog_on_spam_protection" type="checkbox" value="on" <?php echo esc_html($esp);?>>
                                                    <label for="visitorlog_on_spam_protection"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Enable spam protection','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Spam protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_spamprotection' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('Blocking access to a file wp-comments-post.php using the htaccess file instructions, adding the captcha field to the comments form', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_firewall" name="visitorlog_on_firewall" type="checkbox" value="on" <?php echo esc_html($ef);?>>
                                                    <label for="visitorlog_on_firewall"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Enable Firewall','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Firewall protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_firewall' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('This feature allows you to activate more advanced firewall settings to your site. The advanced firewall rules are applied via the insertion of special code to your currently active .htaccess file', 'visitorlog');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
<div style="margin-top:20px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=control_panel' );
?>
</div>
<div style="display:flex;flex-direction:row;justify-content:flex-end;margin-top:-15px;">
    <input type="submit" name="visitorlog_control_panel_save" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
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

} // END Class