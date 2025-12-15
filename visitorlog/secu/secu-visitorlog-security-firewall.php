<?php
/**
 * Firewall
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security_Firewall class
 * 
 * @method public static firewall()
 */
class VisitorLog_Security_Firewall
{
    /**
     * Render Firewall.
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     */
    public static function firewall()
    {
        $write_to_htaccess = false;

        $on_firewall                 = VisitorLog_Utility::get_option_sl( 'on_firewall' );
        $on_firewall_g7              = VisitorLog_Utility::get_option_sl( 'on_firewall_g7' );
        $on_firewall_g6              = VisitorLog_Utility::get_option_sl( 'on_firewall_g6' );
        $on_protect_wp_config        = VisitorLog_Utility::get_option_sl( 'on_protect_wp_config' );
        $disable_server_signature    = VisitorLog_Utility::get_option_sl( 'disable_server_signature' );
        $on_max_file_upload_size     = VisitorLog_Utility::get_option_sl( 'on_max_file_upload_size' );
        $max_file_upload_size        = VisitorLog_Utility::get_option_sl( 'max_file_upload_size' );
        $disable_index_views         = VisitorLog_Utility::get_option_sl( 'disable_index_views' );
        $on_prevent_hotlinking       = VisitorLog_Utility::get_option_sl( 'on_prevent_hotlinking' );
        $disable_trace_and_track     = VisitorLog_Utility::get_option_sl( 'disable_trace_and_track' );
        $deny_bad_query_strings      = VisitorLog_Utility::get_option_sl( 'deny_bad_query_strings' );
        $advanced_char_string_filter = VisitorLog_Utility::get_option_sl( 'advanced_char_string_filter' );

        if ( '' == $on_firewall ) {
           $err = __( 'Turn on the firewall protection feature on the control panel', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err );
        }
        if ( isset($_POST['visitorlog_firewall_protection_save']) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            //-1--------------------------------------------------- Enable firewall G7
            if ( isset($_POST['visitorlog_on_firewall_g7']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_firewall_g7']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_firewall_g7 ) {
                VisitorLog_Utility::update_option_sl( 'on_firewall_g7', $post );
                $on_firewall_g7 = $post;
                $write_to_htaccess = true;
            }
            //-2--------------------------------------------------- Enable firewall G6
            if ( isset($_POST['visitorlog_on_firewall_g6']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_firewall_g6']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_firewall_g6 ) {
                VisitorLog_Utility::update_option_sl( 'on_firewall_g6', $post );
                $on_firewall_g6 = $post;
                $write_to_htaccess = true;

                if ( 'on' == $on_firewall_g7 && 'on' == $on_firewall_g6 ) {
                    VisitorLog_Utility::update_option_sl( 'on_firewall_g6', '' );
                    $on_firewall_g6 = '';
                    $msg = __( 'Only one mode can be enabled: G7 or G6', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'blue', $msg );
                }
            }
            //-3------------------------------------------------- Enable protect the wp-config.php file
            if ( isset($_POST['visitorlog_on_protect_wp_config']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_protect_wp_config']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_protect_wp_config ) {
                VisitorLog_Utility::update_option_sl( 'on_protect_wp_config', $post );
                $on_protect_wp_config = $post;
                $write_to_htaccess = true;
            }
            //-4--------------------------------------------------- Disable the server signature
            if ( isset($_POST['visitorlog_off_server_signature']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_off_server_signature']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $disable_server_signature ) {
                VisitorLog_Utility::update_option_sl( 'on_protect_wp_config', $post );
                $disable_server_signature = $post;
                $write_to_htaccess = true;
            }
            //-5---------------------------------------------------Enable Max file upload size
            if ( isset($_POST['visitorlog_max_file_upload_size']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_max_file_upload_size']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_max_file_upload_size ) {
                VisitorLog_Utility::update_option_sl( 'on_max_file_upload_size', $post );
                $on_max_file_upload_size = $post;
                $write_to_htaccess = true;
            }
            //-6--------------------------------------------------- Disable Index Views
            if ( isset($_POST['visitorlog_off_index_views']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_off_index_views']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $disable_index_views ) {
                VisitorLog_Utility::update_option_sl( 'disable_index_views', $post );
                $disable_index_views = $post;
                $write_to_htaccess = true;
            }
            //-7--------------------------------------------------- Prevent Image Hotlinking
            if ( isset($_POST['visitorlog_prevent_hotlinking']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $post = sanitize_text_field(wp_unslash($_POST['visitorlog_prevent_hotlinking']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_prevent_hotlinking ) {
                VisitorLog_Utility::update_option_sl( 'on_prevent_hotlinking', $post );
                $on_prevent_hotlinking = $post;
                $write_to_htaccess = true;
            }
            //-8--------------------------------------------------- Disable Trace and Track
            if ( isset($_POST['visitorlog_off_trace_and_track']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_off_trace_and_track']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $disable_trace_and_track ) {
                VisitorLog_Utility::update_option_sl( 'disable_trace_and_track', $post );
                $disable_trace_and_track = $post;
                $write_to_htaccess = true;
            }
            //-9--------------------------------------------------- Deny Bad Query Strings
            if ( isset($_POST['visitorlog_deny_bad_query_strings']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_deny_bad_query_strings']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $deny_bad_query_strings ) {
                VisitorLog_Utility::update_option_sl( 'deny_bad_query_strings', $post );
                $deny_bad_query_strings = $post;
                $write_to_htaccess = true;
            }
            //-10--------------------------------------------------- Advanced char string filter
            if ( isset($_POST['visitorlog_char_string_filter']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_char_string_filter']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $advanced_char_string_filter ) {
                VisitorLog_Utility::update_option_sl( 'advanced_char_string_filter', $post );
                $advanced_char_string_filter = $post;
                $write_to_htaccess = true;
            }
            //-5--------------------------------------------------- Max file upload size
            if ( isset($_POST['visitorlog_max_file_upload_size']) &&
                 '' != $_POST['visitorlog_max_file_upload_size']  &&
                  0 != $_POST['visitorlog_max_file_upload_size']
               ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $upload_size = absint($_POST['visitorlog_max_file_upload_size']);
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $max_allowed = 250;
            
                if ( $upload_size > $max_allowed ) {
                    $upload_size = $max_allowed;
                } elseif ( 0 == $upload_size ) {
                    $upload_size = 10;
                }
                VisitorLog_Utility::update_option_sl( 'max_file_upload_size', $upload_size );
                $max_file_upload_size = $upload_size;
            }
            //----------------------------------------------------
            if ( $write_to_htaccess ) {

                $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess(); 
                if ( $res > 0 ) {
                    $msg = __( 'Settings were successfully saved', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'green', $msg );
                } else { 
                    $err = __( 'Could not write to the <.htaccess> file. Please check the file permissions', 'visitorlog' );
                    VisitorLog_Logger::instance()->warning($err); 
                }
            }
        } // end if

        if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Security_Handler::instance()->handler_htaccess_view();
            exit;
        }    
        switch ( $on_firewall_g7 ) {
            case 'on':
                $efg7 = 'checked';
                break;
            default:
                $efg7 = '';
                break;
        }
        switch ( $on_firewall_g6 ) {
            case 'on':
                $efg6 = 'checked';
                break;
            default:
                $efg6 = '';
                break;
        }
        switch ( $on_protect_wp_config ) {
            case 'on':
                $epwc = 'checked';
                break;
            default:
                $epwc = '';
                break;
        }
        switch ( $disable_server_signature ) {
            case 'on':
                $dss = 'checked';
                break;
            default:
                $dss = '';
                break;
        }
        switch ( $on_max_file_upload_size ) {
            case 'on':
                $emfus = 'checked';
                break;
            default:
                $emfus = '';
                break;
        }
        switch ( $disable_index_views ) {
            case 'on':
                $div = 'checked';
                break;
            default:
                $div = '';
                break;
        }
        switch ( $disable_trace_and_track ) {
            case 'on':
                $dtat = 'checked';
                break;
            default:
                $dtat = '';
                break;
        }
        switch ( $deny_bad_query_strings ) {
            case 'on':
                $dbqs = 'checked';
                break;
            default:
                $dbqs = '';
                break;
        }
        switch ( $advanced_char_string_filter ) {
            case 'on':
                $acsf = 'checked';
                break;
            default:
                $acsf = '';
                break;
        }
        switch ( $on_prevent_hotlinking ) {
            case 'on':
                $eph = 'checked';
                break;
            default:
                $eph = '';
                break;
        }
        if ( 'on' == $on_firewall ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg  = __( 'The Firewall panel is disabled!', 'visitorlog' );
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
                                        <td>
<input type="checkbox" id="visitorlog_checkbox_select" value="on">
                                            &ensp;<?php esc_html_e('Enable/Disable all','visitorlog');?>
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
VisitorLog_Utility::redirect_html( 'file-code.png', $title, 'visitorlog_firewall', '&reg=htaccess' );
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
                                        <td width="240"><?php esc_html_e('Functions', 'visitorlog');?></td>
                                        <td width="120"><?php esc_html_e('Parameters', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                    <?php esc_html_e('Protection from perishablepress.com', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_firewall_g7" name="visitorlog_on_firewall_g7" type="checkbox" value="on" <?php echo esc_html($efg7);?>>
                                                    <label for="visitorlog_on_firewall_g7"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Enable Firewall G7', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
                                            &#8226;&#8201;
<?php esc_html_e('Check this if you want to apply the 7G firewall protection from perishablepress.com to your site', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_firewall_g6" name="visitorlog_on_firewall_g6" type="checkbox" value="on" <?php echo esc_html($efg6);?>>
                                                    <label for="visitorlog_on_firewall_g6"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Enable Firewall G6', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
                                            &#8226;&#8201;
<?php esc_html_e('Check this if you want to apply the 6G firewall protection from perishablepress.com to your site', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                    <?php esc_html_e('Basic Firewall Protection', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_protect_wp_config" name="visitorlog_on_protect_wp_config" type="checkbox" value="on" <?php echo esc_html($epwc);?>>
                                                    <label for="visitorlog_on_protect_wp_config"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                    <?php esc_html_e('Enable protect the wp-config.php file', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
        <?php esc_html_e('Protect your wp-config.php file by denying access to it', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_off_server_signature" name="visitorlog_off_server_signature" type="checkbox" value="on" <?php echo esc_html($dss);?>>
                                                    <label for="visitorlog_off_server_signature"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                    <?php esc_html_e('Disable the server signature', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
                    <?php esc_html_e('Disable the server signature', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_max_file_upload_size" name="visitorlog_max_file_upload_size" type="checkbox" value="on" <?php echo esc_html($emfus);?>>
                                                    <label for="visitorlog_max_file_upload_size"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                    <?php esc_html_e('Max File Upload Size (MB)', 'visitorlog');?>:
                                        </td>
                                        <td>
<input type="text" size="10" name="visitorlog_max_file_upload_size" placeholder="<?php echo esc_html($max_file_upload_size);?>" />
                                        </td>                                        
                                        <td>
<?php esc_html_e('The value for the maximum file upload size used in the .htaccess file. Defaults to 10MB', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_off_index_views" name="visitorlog_off_index_views" type="checkbox" value="on" <?php echo esc_html($div);?>>
                                                    <label for="visitorlog_off_index_views"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                    <?php esc_html_e('Disable Index Views', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
<?php esc_html_e('Check this if you want to disable directory and file listing', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_prevent_hotlinking" name="visitorlog_prevent_hotlinking" type="checkbox" value="on" <?php echo esc_html($eph);?>>
                                                    <label for="visitorlog_prevent_hotlinking"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                    <?php esc_html_e('Prevent Image Hotlinking', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
<?php esc_html_e('Check this if you want to prevent hotlinking to images on your site', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                    <?php esc_html_e('Protection cross site scripting attacks (XSS)', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_off_trace_and_track" name="visitorlog_off_trace_and_track" type="checkbox" value="on" <?php echo esc_html($dtat);?>>
                                                    <label for="visitorlog_off_trace_and_track"></label>
                                                </div>
                                            </div>   
                                        </td>
                                        <td>
                    <?php esc_html_e('Disable Trace and Track', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
                    <?php esc_html_e('Check this if you want to disable trace and track', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_deny_bad_query_strings" name="visitorlog_deny_bad_query_strings" type="checkbox" value="on" <?php echo esc_html($dbqs);?>>
                                                    <label for="visitorlog_deny_bad_query_strings"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                    <?php esc_html_e('Deny Bad Query Strings', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
        <?php esc_html_e('This will help protect you against malicious queries via XSS', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_char_string_filter" name="visitorlog_char_string_filter" type="checkbox" value="on" <?php echo esc_html($acsf);?>>
                                                    <label for="visitorlog_char_string_filter"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                    <?php esc_html_e('Enable Advanced Character String Filter', 'visitorlog');?>
                                        </td>
                                        <td></td>                                        
                                        <td>
                <?php esc_html_e('This will block bad character matches from XSS', 'visitorlog');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="margin-top:10px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=firewall' );
?>
                            </div>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_firewall_protection_save" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
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