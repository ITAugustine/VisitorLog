<?php
/**
 * Anti Spam
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security_Spam class
 * 
 * @method public static anti_spam()
 */
class VisitorLog_Security_Spam
{
    /**
     * Render Anti Spam.
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     */
    public static function anti_spam()
    {
        $err = '';
        $write_to_htaccess = false;

        $pbi0=$pbi1=$pbi2=$pbi3=$pbi4=$pbi5=$pbi6=$pbi7=$pbi8=$pbi9=$pbi10=''; 

        $on_spam_protection = VisitorLog_Utility::get_option_sl( 'on_spam_protection' );

        $on_forbid_commentspost   = VisitorLog_Utility::get_option_sl( 'on_forbid_commentspost' ); // 1
        $on_spambot_blocking      = VisitorLog_Utility::get_option_sl( 'on_spambot_blocking' );    // 2.1
        $on_forbid_proxy_comments = VisitorLog_Utility::get_option_sl( 'forbid_proxy_comments' );  // 2.2

        $on_comment_captcha   = VisitorLog_Utility::get_option_sl( 'on_comment_captcha' );
        $on_comment_recaptcha = VisitorLog_Utility::get_option_sl( 'on_comment_recaptcha' );

        $sitekey   = VisitorLog_Utility::get_option_sl( 'recaptcha_site_key' );
        $secretkey = VisitorLog_Utility::get_option_sl( 'recaptcha_secret_key' );

        $shortcode1         = VisitorLog_Utility::get_option_sl( 'antispam-cf-shortcode1-' );
        $on_block_spam_perm = VisitorLog_Utility::get_option_sl( 'on_block_spam_perm' );

        if ( isset($_POST['visitorlog_save_spam_block']) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( '' == $on_spam_protection ) {
               $err = __( 'Turn on the anti-spam feature on the control panel', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err );
            } else {
                //--------------------------------------1.1 Forbid_comments_post.php
                if ( isset($_POST['visitorlog_forbid_commentspost']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $post = sanitize_text_field(wp_unslash($_POST['visitorlog_forbid_commentspost']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                } else {
                    $post = ''; 
                }
                if ( $post != $on_forbid_commentspost ) {
                    VisitorLog_Utility::update_option_sl( 'on_forbid_commentspost', $post );
                    $on_forbid_commentspost = $post;
                }
                //---------------------------------------------1.2.1 Enable spambot blocking
                if ( isset($_POST['visitorlog_on_spambot_blocking']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_spambot_blocking']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                } else {
                    $post = ''; 
                }
                if ( $post != $on_spambot_blocking ) {
                    VisitorLog_Utility::update_option_sl( 'on_spambot_blocking', $post );
                    $on_spambot_blocking = $post;
                    $write_to_htaccess = true;
                }
                //---------------------------------------------1.2.2 Forbid Proxy Comment Posting
                if ( isset($_POST['visitorlog_forbid_proxy_comments']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $post = sanitize_text_field(wp_unslash($_POST['visitorlog_forbid_proxy_comments']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                } else {
                    $post = ''; 
                }
                if ( $post != $on_forbid_proxy_comments ) {
                    VisitorLog_Utility::update_option_sl( 'forbid_proxy_comments', $post );
                    $on_forbid_proxy_comments = $post;
                    $write_to_htaccess = true;
                }
                //------------------------------------------------1.3.1 Enable comment captcha
                if ( isset($_POST['visitorlog_on_comment_captcha']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_comment_captcha']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                } else {
                    $post = ''; 
                }
                if ( $post != $on_comment_captcha ) {
                    VisitorLog_Utility::update_option_sl( 'on_comment_captcha', $post );
                    $on_comment_captcha = $post;
                }
                //------------------------------------------------1.3.2 Enable comment recaptcha
                if ( isset($_POST['visitorlog_on_comment_recaptcha']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_comment_recaptcha']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                } else {
                    $post = ''; 
                }
                if ( $post != $on_comment_recaptcha ) {
                    VisitorLog_Utility::update_option_sl( 'on_comment_recaptcha', $post );
                    $on_comment_recaptcha = $post;
                }

                if ( !empty($_POST['visitorlog_recaptcha_spam_site_key']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $sitekey = sanitize_text_field(wp_unslash($_POST['visitorlog_recaptcha_spam_site_key']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                    VisitorLog_Utility::update_option_sl( 'recaptcha_site_key', $sitekey );
                } else {
                    $sitekey = '';
                    VisitorLog_Utility::update_option_sl( 'recaptcha_site_key', '' );
                }

                if ( !empty($_POST['visitorlog_recaptcha_spam_secret_key']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $begin = mb_substr(sanitize_text_field(wp_unslash($_POST['visitorlog_recaptcha_spam_secret_key'])), 0, 6);
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                    if ( '******' != $begin ) {
                        $secretkey = $begin;
                        VisitorLog_Utility::update_option_sl( 'recaptcha_secret_key', $secretkey );
                    }
                } else {
                    $secretkey = '';
                    VisitorLog_Utility::update_option_sl( 'recaptcha_secret_key', '' );
                }
                //----------------------------------------------2 IP address blocking options
                if ( isset($_POST['visitorlog_on_block_spam_perm']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_block_spam_perm']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                } else {
                    $post = ''; 
                }
                if ( $post != $on_block_spam_perm ) {
                    VisitorLog_Utility::update_option_sl( 'on_block_spam_perm', $post );
                    $on_block_spam_perm = $post;
                } 
                //----------------------------------------------------
                if ( $write_to_htaccess ) {
                    
                    $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess(); 
                    if ( $res > 0 ) {
                        $msg = __( 'Successful entry of the directives to the file .htaccess', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'green', $msg );
                    } else { 
                        $err = __( 'Could not write to the <.htaccess> file. Please check the file permissions', 'visitorlog' );
                        VisitorLog_Logger::instance()->warning($err);
                    }
                }
            }
        } // end if -------------------

        if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Security_Handler::instance()->handler_htaccess_view();
            exit;
        }    

        //---------------------DISPLAY------

        if ( '' != $secretkey ) {
            $end = mb_substr( $secretkey, -4 );
            $secretkeyview = '**************************' . $end;
        } else {
            $secretkeyview = '';
        }

        switch ( $on_forbid_commentspost ) {
            case 'on':
                $fcp = 'checked';
                break;
            default:
                $fcp = '';
                break;
        }
        switch ( $on_spambot_blocking ) {
            case 'on':
                $esb = 'checked';
                break;
            default:
                $esb = '';
                break;
        }
        switch ( $on_forbid_proxy_comments ) {
            case 'on':
                $fpc = 'checked';
                break;
            default:
                $fpc = '';
                break;
        }
        switch ( $on_comment_captcha ) {
            case 'on':
                $ecc = 'checked';
                break;
            default:
                $ecc = '';
                break;
        }
        switch ( $on_comment_recaptcha ) {
            case 'on':
                $ecr = 'checked';
                break;
            default:
                $ecr = '';
                break;
        }
        switch ( $on_block_spam_perm ) {
            case 'on':
                $ebsp = 'checked';
                break;
            default:
                $ebsp = '';
                break;
        }
        if ( 'on' == $on_spam_protection ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg  = __( 'The anti-spam panel is disabled!', 'visitorlog' );
            $msg .= '<br>';
            $msg .= __( 'Activate the functions on the control panel.', 'visitorlog' );
            VisitorLog_System_View::show_message( 'yellow', $msg );
        } 
        ?>  
        <form method="post">
        <?php wp_nonce_field('visitorlog_nonce');?>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td class="hidden-md-down" width="229">
                                    <input type="checkbox" id="visitorlog_checkbox_select" value="on">
                                            &ensp;<?php esc_html_e('Enable/Disable all','visitorlog');?>
                                            &emsp;&vellip;&emsp;
<?php  
if ( '' != $icon ) {
?>
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_controlpanel')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('This panel with functions is disabled', 'visitorlog');?>!">
    <?php VisitorLog_Utility::render_picture('remove.png');?>&nbsp;
    <?php esc_html_e('This panel with functions is disabled','visitorlog');?>!
</a>
                                            &emsp;&vellip;&emsp;
<?php
}
$title = __('View file .htaccess', 'visitorlog');
VisitorLog_Utility::redirect_html( 'file-code.png', $title, 'visitorlog_spamprotection', '&reg=htaccess' );
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
        <div style="margin-top:-13px;">
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead class="vl-color-thead">
                                    <tr>
                    <td width="62"> <?php esc_html_e('Enable','visitorlog');?></td>
                    <td width="200"><?php esc_html_e('Functions',  'visitorlog');?></td>
                    <td width="222"><?php esc_html_e('Parameters',  'visitorlog');?></td>
                    <td>            <?php esc_html_e('Description', 'visitorlog');?></td> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                                                1.&ensp;
    <?php esc_html_e('Spam in comments', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr>    
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                                                1.1&ensp;
    <?php esc_html_e('Algorithm', 'visitorlog');?>&ensp;1.&ensp;
    <?php esc_html_e('Blocking a spammer when it is detected by WordPress', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr>   
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_forbid_commentspost" name="visitorlog_forbid_commentspost" type="checkbox" value="on" <?php echo esc_html($fcp);?>>
                                                    <label for="visitorlog_forbid_commentspost"></label>
                                                </div>
                                            </div>    
                                        </td>
                                        <td>
<?php esc_html_e('Enable mode','visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Spam report', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_spam' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('In this case, the plugin receives a signal about spam comments from WordPress and enters the spammer\'s data into the table', 'visitorlog');?>.&nbsp;
<?php esc_html_e('In the future, the administrator decides what to do with it - delete it from the quarantine table or permanently block the IP address in the htaccess file', 'visitorlog');?>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                                                1.2&ensp;
    <?php esc_html_e('Algorithm', 'visitorlog');?>&ensp;2.&ensp;
    <?php esc_html_e('Protection against spam comments using instructions in the htaccess file', 'visitorlog');?>
                                            </em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_spambot_blocking" name="visitorlog_on_spambot_blocking" type="checkbox" value="on" <?php echo esc_html($esb);?>>
                                                    <label for="visitorlog_on_spambot_blocking"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                <?php esc_html_e('Forbidding access to the file wp-comments-post.php', 'visitorlog');?>
                                        </td>
                                        <td>
                                        </td>                                        
                                        <td>
<?php esc_html_e('Use the Htaccess file instructions to block bots from accessing the file wp-comments-post.php and redirect them to the address', 'visitorlog');?>&nbsp;http://127.0.0.1
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_forbid_proxy_comments" name="visitorlog_forbid_proxy_comments" type="checkbox" value="on" <?php echo esc_html($fpc);?>>
                                                    <label for="visitorlog_forbid_proxy_comments"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                <?php esc_html_e('Forbid Proxy Comment Posting', 'visitorlog');?>
                                        </td>
                                        <td>
                                        </td>                                        
                                        <td>
<?php esc_html_e('Use the Htaccess file instructions to blacklist the various HTTP protocols used by proxy servers', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                                                1.3&ensp;
                            <?php esc_html_e('Captcha On Comment Forms', 'visitorlog');?>
                                            </em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_comment_captcha" name="visitorlog_on_comment_captcha" type="checkbox" value="on" <?php echo esc_html($ecc);?>>
                                                    <label for="visitorlog_on_comment_captcha"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                            <?php esc_html_e('Enable Captcha On Comment Forms', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
<?php esc_html_e('Check this if you want to insert a captcha field on the comment forms', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_comment_recaptcha" name="visitorlog_on_comment_recaptcha" type="checkbox" value="on" <?php echo esc_html($ecr);?>>
                                                    <label for="visitorlog_on_comment_recaptcha"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                            <?php esc_html_e('Enable Google reCAPTCHA On Comment Forms', 'visitorlog');?>
                                        </td>
                                        <td>
<input type="text" size="25" name="visitorlog_recaptcha_spam_site_key" placeholder="<?php esc_html_e('Site key', 'visitorlog');?>" value="<?php echo esc_html($sitekey);?>" style="margin-bottom:2px;"/>
<input type="text" size="25" name="visitorlog_recaptcha_spam_secret_key" placeholder="<?php esc_html_e('Secret key', 'visitorlog');?>" value="<?php echo esc_html($secretkeyview);?>"/>    
                                        </td>                                        
                                        <td>
                        <?php esc_html_e('Enable it if you want to use Google reCAPTCHA v2', 'visitorlog');?>
                                            <br>
                        -<?php esc_html_e('Site key', 'visitorlog');?>
                                            <br> 
                        -<?php esc_html_e('Secret key', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                                                2.&ensp;
    <?php esc_html_e('Blocking IP addresses for partitions', 'visitorlog');?>&nbsp;1.1
                                            </em>
                                        </td>    
                                    </tr>   
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_block_spam_perm" name="visitorlog_on_block_spam_perm" type="checkbox" value="on" <?php echo esc_html($ebsp);?>>
                                                    <label for="visitorlog_on_block_spam_perm"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>    
<?php esc_html_e('Permanent blocking of IP addresses in Htaccess file', 'visitorlog');?>
                                        </td>    
                                        <td>
                                        </td>
                                        <td>    
<?php esc_html_e('When enabled, the administrator will be able to manually permanently block the IP address using the control instructions in the htaccess file. This is done in the Spam table', 'visitorlog');?>.
                                        </td>
                                    </tr>    
                                </tbody>
                            </table>
                            <div style="margin-top:10px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=spam' );
?>
                            </div>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_save_spam_block" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
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

} // END class