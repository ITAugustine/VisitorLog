<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Security_Adminpanel class
 * 
 * @method public static function admin_panel_protection() 
 */
class VisitorLog_Security_Adminpanel
{
    /**
     * Render Admin panel protection.
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Utility::redirect_to_url()
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     */
    public static function admin_panel_protection()
    {
        $att2=$att3=$att4=$att5=$att6=$att7=$att8=$att9='';
        $ats1=$ats2=$ats3=$ats4=$ats5=$ats6='';
        $rtp1=$rtp2=$rtp3=$rtp4=$rtp5=$rtp6='';
        $rts1=$rts2=$rts3=$rts4=$rts5=$rts6=$rts7=$rts8='';
        $m1=$m2=$m5=$m10=$m30=$h1=$h2=$h3=$h6=$h12=$d1=$d2=$d3=$d7='';
        $uhfl0=$uhfl1='';

        $write_htaccess = false;

        $on_admin_panel_protection = VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' );

        $on_login_lockdown       = VisitorLog_Utility::get_option_sl( 'on_login_lockdown' );
        $max_slow_attempts       = VisitorLog_Utility::get_option_sl( 'max_slow_attempts' );
        $slow_time_period        = VisitorLog_Utility::get_option_sl( 'login_slow_time_period' );
        $use_htaccess_file_login = VisitorLog_Utility::get_option_sl( 'use_htaccess_file_login' );
        $redirect_url_login      = VisitorLog_Utility::get_option_sl( 'redirect_url_login' );                

        $on_login_captcha   = VisitorLog_Utility::get_option_sl( 'on_login_captcha' );
        $on_login_recaptcha = VisitorLog_Utility::get_option_sl( 'on_login_recaptcha' );
        $on_forced_logout   = VisitorLog_Utility::get_option_sl( 'on_forced_logout' );

        $sitekey            = VisitorLog_Utility::get_option_sl( 'recaptcha_site_key' );
        $secretkey          = VisitorLog_Utility::get_option_sl( 'recaptcha_secret_key' );
        $logout_time_period = VisitorLog_Utility::get_option_sl( 'logout_time_period' );

        if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {
            
            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Security_Handler::instance()->handler_htaccess_view();
            exit;
        } 

        if ( isset ($_POST['visitorlog_admin_panel_save']) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            if ( '' == $on_admin_panel_protection ) {
               $err = __( 'Turn on the security management module of the administrative panel', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err );
            }

            if ( isset($_POST['visitorlog_on_login_lockdown']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_login_lockdown']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_login_lockdown ) {
                VisitorLog_Utility::update_option_sl( 'on_login_lockdown', $post );
                $on_login_lockdown = $post;
            }

            if ( isset( $_POST['visitorlog_max_slow_attempts'] ) && $max_slow_attempts != $_POST['visitorlog_max_slow_attempts'] ) { 

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $max_slow_attempts = sanitize_text_field(wp_unslash($_POST['visitorlog_max_slow_attempts']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                VisitorLog_Utility::update_option_sl( 'max_slow_attempts', $max_slow_attempts );
            } 

            if ( isset( $_POST['visitorlog_slow_time_period'] ) && 
                $slow_time_period != $_POST['visitorlog_slow_time_period'] ) { 

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $slow_time_period = sanitize_text_field(wp_unslash($_POST['visitorlog_slow_time_period']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                VisitorLog_Utility::update_option_sl( 'login_slow_time_period', $slow_time_period );
            } 

            if ( isset($_POST['visitorlog_on_forced_logout']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_forced_logout']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_forced_logout ) {
                VisitorLog_Utility::update_option_sl( 'on_forced_logout', $post );
                $on_forced_logout = $post;
            }

            $captcha = $recaptcha = '';
            if ( isset($_POST['visitorlog_on_login_captcha'] ) ) {

                if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $captcha = 'on';
            }    
            if ( isset($_POST['visitorlog_on_login_recaptcha'] ) ) {

                if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $recaptcha = 'on';
            }
            if ( 'on' == $captcha && 'on' == $recaptcha ) {
                $msg_err = __( 'Choose one type of captcha', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $msg_err );
            } else {
                if ( $on_login_captcha != $captcha ) {
                    VisitorLog_Utility::update_option_sl( 'on_login_captcha', $captcha );
                    $on_login_captcha = $captcha; 
                }
                if ( $on_login_recaptcha != $recaptcha ) {
                    VisitorLog_Utility::update_option_sl( 'on_login_recaptcha', $recaptcha );
                    $on_login_recaptcha = $recaptcha;
                }
            }

            if ( isset($_POST['visitorlog_recaptcha_site_key']) && $sitekey != $_POST['visitorlog_recaptcha_site_key'] ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $sitekey = sanitize_text_field(wp_unslash($_POST['visitorlog_recaptcha_site_key']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                VisitorLog_Utility::update_option_sl( 'recaptcha_site_key', $sitekey );
            }

            if ( isset($_POST['visitorlog_recaptcha_secret_key']) && $secretkey != $_POST['visitorlog_recaptcha_secret_key'] ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $begin = mb_substr(sanitize_text_field(wp_unslash($_POST['visitorlog_recaptcha_secret_key'])), 0, 6 );
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }

                if ( '******' != $begin ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $secretkey = sanitize_text_field(wp_unslash($_POST['visitorlog_recaptcha_secret_key']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                    VisitorLog_Utility::update_option_sl( 'recaptcha_secret_key', $secretkey );
                }
            }

            if ( isset( $_POST['visitorlog_select_time_period'] ) && $logout_time_period != $_POST['visitorlog_select_time_period'] ) { 

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $logout_time_period = sanitize_text_field(wp_unslash($_POST['visitorlog_select_time_period']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                VisitorLog_Utility::update_option_sl( 'logout_time_period', $logout_time_period );
            } 

            if ( isset( $_POST['visitorlog_htaccess_file_login'] ) && $use_htaccess_file_login != $_POST['visitorlog_htaccess_file_login'] ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $use_htaccess_file_login = sanitize_text_field(wp_unslash($_POST['visitorlog_htaccess_file_login']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                VisitorLog_Utility::update_option_sl( 'use_htaccess_file_login', $use_htaccess_file_login );
            }

            if ( isset( $_POST['visitorlog_redirect_url_login'] ) && $redirect_url_login != $_POST['visitorlog_redirect_url_login'] ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $redirect_url = sanitize_url(wp_unslash($_POST['visitorlog_redirect_url_login']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }

                if ( $redirect_url == '' || (false === str_contains( $redirect_url, 'https://') && false === str_contains( $redirect_url, 'http://')) ) {

                    $err = __( 'You entered an incorrect format for the "Redirect URL" field. It has been set to the default value', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err );
                } else {
                    VisitorLog_Utility::update_option_sl( 'redirect_url_login', $redirect_url );
                    $redirect_url_login = $redirect_url;
                }
            }
            //-----------------------------------------------------------
            if ( $write_htaccess ) {

                $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess();
                if ( $res > 0 ) {
                    $msg = __( 'Successful entry of the directives to the file .htaccess', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'green', $msg );
                } else {
                    $err = __( 'Could not write to the <.htaccess> file. Please check the file permissions', 'visitorlog' );
                    VisitorLog_Logger::instance()->warning($err);
                }    
            }

        } // END if
       
        //---------------------DISPLAY------

        if ( '' != $secretkey ) {
            $end = mb_substr( $secretkey, -4 );
            $secretkeyview = '**************************' . $end;
        } else {
            $secretkeyview = '';
        }
        switch ( $logout_time_period ) {
            case '60':
                $m1 = 'selected';
                break;
            case '120':
                $m2 = 'selected';
                break;
            case '300':
                $m5 = 'selected';
                break;
            case '600':
                $m10 = 'selected';
                break;
            case '1800':
                $m30 = 'selected';
                break;
            case '3600':
                $h1 = 'selected';
                break;
            case '7200':
                $h2 = 'selected';
                break;
            case '10800':
                $h3 = 'selected';
                break;
            case '21600':
                $h6 = 'selected';
                break;
            case '43200':
                $h12 = 'selected';
                break;
            case '86400':
                $d1 = 'selected';
                break;
            case '172800':
                $d2 = 'selected';
                break;
            case '259200':
                $d3 = 'selected';
                break;
            case '604800':
                $d7 = 'selected';
                break;
            default:
                $h1 = 'selected';
                break; 
        }
        switch ( $max_slow_attempts ) {
            case '3':
                $ats1 = 'selected';
                break;
            case '5':
                $ats2 = 'selected';
                break;
            case '10':
                $ats3 = 'selected';
                break;
            case '15':
                $ats4 = 'selected';
                break;
            case '20':
                $ats5 = 'selected';
                break;
            case '25':
                $ats6 = 'selected';
                break;
            default:
                $ats3 = 'selected';
                break; 
        }
        switch ( $slow_time_period ) {
            case '60':
                $rts1 = 'selected';
                break;
            case '120':
                $rts2 = 'selected';
                break;
            case '180':
                $rts3 = 'selected';
                break;
            case '300':
                $rts4 = 'selected';
                break;
            case '600':
                $rts5 = 'selected';
                break;
            case '1800':
                $rts6 = 'selected';
                break;
            case '3600':
                $rts7 = 'selected';
                break;
            case '7200':
                $rts8 = 'selected';
                break;
            default:
                $rts7 = 'selected';
                break;  
        }
        switch ( $use_htaccess_file_login ) {
            case 'off':
                $uhfl0 = 'selected';
                break;
            case 'on':
                $uhfl1 = 'selected';
                break;
            default:
                $uhfl1 = 'selected';
                break; 
        }
        switch ( $on_login_captcha ) {
            case 'on':
                $elc = 'checked';
                break;
            default:
                $elc = '';
                break; 
        }
        switch ( $on_login_recaptcha ) {
            case 'on':
                $elrc = 'checked';
                break;
            default:
                $elrc = '';
                break; 
        }
        switch ( $on_forced_logout ) {
            case 'on':
                $efl = 'checked';
                break;
            default:
                $efl = '';
                break; 
        }
        switch ( $on_login_lockdown ) {
            case 'on':
                $ell = 'checked';
                break;
            default:
                $ell = '';
                break; 
        }
        if ( 'on' == $on_admin_panel_protection ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg  = __( 'The protection of the administrative panel is disabled!', 'visitorlog' );
            $msg .= '<br>';
            $msg .= __( 'Activate the administrative panel protection functions on the control panel.', 'visitorlog' );
            VisitorLog_System_View::show_message( 'yellow', $msg );
        }
        ?>  
        <form method="post" id="security_control_panel">
        <?php wp_nonce_field('visitorlog_nonce');?>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td> 
                                            <input type="checkbox" id="visitorlog_checkbox_select" value="on">
                                            &ensp;
                                            <?php esc_html_e('Enable/Disable all','visitorlog');?>
                                            &emsp;&vellip;&emsp;
<?php  
if ('' != $icon) {
?>
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_controlpanel')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('This panel with functions is disabled', 'visitorlog');?>!">
    <?php VisitorLog_Utility::render_picture('remove.png');?>&nbsp;
    <?php esc_html_e('This panel with functions is disabled','visitorlog');?>!
</a>
                                            &emsp;&vellip;&emsp;
<?php
}
$title = __('View file .htaccess', 'visitorlog');
VisitorLog_Utility::redirect_html( 'file-code.png', $title, 'visitorlog_adminpanel', '&reg=htaccess' );
?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-13px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead class="vl-color-thead">
                                    <tr>
                                        <td width="45"> <?php esc_html_e('Enable','visitorlog');?></td>
                                        <td width="160"><?php esc_html_e('Functions', 'visitorlog');?></td>
                                        <td width="222"><?php esc_html_e('Parameters', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td colspan="4">
                                            <div>
                                                <em style="font-size:16px;">
<?php esc_html_e('Permanent or Registration and blocking of bots attacking the entrance to the admin panel', 'visitorlog');?>
                                                </em>
                                            </div>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_login_lockdown" name="visitorlog_on_login_lockdown" type="checkbox" value="on" <?php echo esc_html($ell);?>>
                                                    <label for="visitorlog_on_login_lockdown"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                            <?php esc_html_e('Blocking bots when logging in','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Failed Login Attempt table', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_failedlogins' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('Enable it if you want erroneous login attempts using usernames or their IP addresses to be blocked','visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                            <?php esc_html_e('IP blocking options','visitorlog');?>
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                                <select name="visitorlog_max_slow_attempts">
                                <option value="3"  <?php echo esc_html($ats1);?> > 3  </option>
                                <option value="5"  <?php echo esc_html($ats2);?> > 5  </option>
                                <option value="10" <?php echo esc_html($ats3);?> > 10 </option>
                                <option value="15" <?php echo esc_html($ats4);?> > 15 </option>
                                <option value="20" <?php echo esc_html($ats5);?> > 20 </option>
                                <option value="25" <?php echo esc_html($ats6);?> > 25 </option>
                                                </select>
                                                <select name="visitorlog_slow_time_period">
<option value="60"   <?php echo esc_html($rts1);?>><?php esc_html_e('1 minute','visitorlog');?></option>
<option value="120"  <?php echo esc_html($rts2);?>><?php esc_html_e('2 minutes','visitorlog');?></option>
<option value="180"  <?php echo esc_html($rts3);?>><?php esc_html_e('3 minutes','visitorlog');?></option>
<option value="300"  <?php echo esc_html($rts4);?>><?php esc_html_e('5 minutes','visitorlog');?></option>
<option value="600"  <?php echo esc_html($rts5);?>><?php esc_html_e('10 minutes','visitorlog');?></option>
<option value="1800" <?php echo esc_html($rts6);?>><?php esc_html_e('30 minutes','visitorlog');?></option>
<option value="3600" <?php echo esc_html($rts7);?>><?php esc_html_e('1 hour','visitorlog');?></option>
<option value="7200" <?php echo esc_html($rts8);?>><?php esc_html_e('2 hours','visitorlog');?></option>
                                                </select>
                                            </label>  
                                        </td>                                        
                                        <td>
<?php esc_html_e('Blocking the bot with the specified parameters. For example, 10 attempts in 30 minutes', 'visitorlog')?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Using a file .htaccess', 'visitorlog');?>
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                                <select name="visitorlog_htaccess_file_login">
                                                    <option value="off" <?php echo esc_html($uhfl0);?> >
                                                        <?php esc_html_e('off', 'visitorlog');?>
                                                    </option>
                                                    <option value="on"  <?php echo esc_html($uhfl1);?> >
                                                        <?php esc_html_e('on', 'visitorlog');?>
                                                    </option>
                                                </select>
                                            </label>
                                        </td>                                        
                                        <td>
<?php esc_html_e('Permanent blocking of IP address using the instructions in the file .htaccess for Apache server', 'visitorlog');?>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Redirect to this URL', 'visitorlog');?>
                                        </td>
                                        <td>
                                            <p style="margin-top:-5px;">
<input type="text" size="25" name="visitorlog_redirect_url_login" placeholder="<?php esc_html_e('Lock redirect url', 'visitorlog');?>" value="<?php echo esc_url($redirect_url_login);?>"/>
                                            </p>    
                                        </td>                                        
                                        <td>
<?php esc_html_e('A blocked visitor will be automatically redirected to this URL', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                                <?php esc_html_e('Effective ways to protect the admin panel', 'visitorlog')?>
                                            </em>
                                        </td>    
                                    </tr>    
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_login_captcha" name="visitorlog_on_login_captcha" type="checkbox" value="on" <?php echo esc_html($elc);?>>
                                                    <label for="visitorlog_on_login_captcha"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Enable Captcha On Login Page','visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
<?php esc_html_e('Enable this feature if you want to insert a captcha form on the login page', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_login_recaptcha" name="visitorlog_on_login_recaptcha" type="checkbox" value="on" <?php echo esc_html($elrc);?>>
                                                    <label for="visitorlog_on_login_recaptcha"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                    <?php esc_html_e('Enable Google reCAPTCHA On Login Page','visitorlog');?>
                                        </td>
                                        <td>
<input type="text" size="25" name="visitorlog_recaptcha_site_key" placeholder="<?php esc_html_e('Site key', 'visitorlog');?>" value="<?php echo esc_html($sitekey);?>" style="margin-bottom:2px;"/>
<input type="text" size="25" name="visitorlog_recaptcha_secret_key" placeholder="<?php esc_html_e('Secret key', 'visitorlog');?>" value="<?php echo esc_html($secretkeyview);?>"/>
                                        </td>                                        
                                        <td>
                            <?php esc_html_e('Enable it if you want to use Google reCAPTCHA v2','visitorlog');?>.
                                            <br>-<?php esc_html_e('Site key', 'visitorlog')?>
                                            <br>-<?php esc_html_e('Secret key', 'visitorlog')?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_forced_logout" name="visitorlog_on_forced_logout" type="checkbox" value="on" <?php echo esc_html($efl);?>>
                                                    <label for="visitorlog_on_forced_logout"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Enable Force User Logout','visitorlog');?>
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                                <select name="visitorlog_select_time_period">
                <option value="60"     <?php echo esc_html($m1);?> ><?php esc_html_e('1 minute','visitorlog');?></option>
                <option value="120"    <?php echo esc_html($m2);?> ><?php esc_html_e('2 minutes','visitorlog');?></option>
                <option value="300"    <?php echo esc_html($m5);?> ><?php esc_html_e('5 minutes','visitorlog');?></option>
                <option value="600"    <?php echo esc_html($m10);?>><?php esc_html_e('10 minutes','visitorlog');?></option>
                <option value="1800"   <?php echo esc_html($m30);?>><?php esc_html_e('30 minutes','visitorlog');?></option>
                <option value="3600"   <?php echo esc_html($h1);?> ><?php esc_html_e('1 hour','visitorlog');?></option>
                <option value="7200"   <?php echo esc_html($h2);?> ><?php esc_html_e('2 hours','visitorlog');?></option>
                <option value="10800"  <?php echo esc_html($h3);?> ><?php esc_html_e('3 hours','visitorlog');?></option>
                <option value="21600"  <?php echo esc_html($h6);?> ><?php esc_html_e('6 hours','visitorlog');?></option>
                <option value="43200"  <?php echo esc_html($h12);?>><?php esc_html_e('12 hours','visitorlog');?></option>
                <option value="86400"  <?php echo esc_html($d1);?> ><?php esc_html_e('1 day','visitorlog');?></option>
                <option value="172800" <?php echo esc_html($d2);?> ><?php esc_html_e('2 days','visitorlog');?></option>
                <option value="259200" <?php echo esc_html($d3);?> ><?php esc_html_e('3 days','visitorlog');?></option>
                <option value="604800" <?php echo esc_html($d7);?> ><?php esc_html_e('7 days','visitorlog');?></option>
                                                </select>
                                            </label>
                                        </td>                                        
                                        <td>
<?php esc_html_e('The user will be forced to log back in after this time period has elapased', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Authorization via XMLRPC','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Logging & blocking functions', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_blockvisitor' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('If you are not using the XMLRPC functionality, block it. Attackers use this channel to log in to the admin panel. The blocking is carried out in the section Registration and blocking of visitors', 'visitorlog');?>.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="margin-top:10px;"></div>
                    <div class="body">
                        <div class="table-responsive">
                            <div>
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=admin_panel' );
?>
                            </div>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end; margin-top:-15px;">
<input type="submit" name="visitorlog_admin_panel_save" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
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