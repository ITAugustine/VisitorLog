<?php
/**
 * Log & Blocking Visitors
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security_Log_Blocking_Visitor_View class
 * 
 * @method public static log_blocking_visitor()
 */
class VisitorLog_Security_Log_Blocking_Visitor
{
    /**
     * Render Log_Blocking_Visitor. 
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     */
    public static function log_blocking_visitor()
    {
        $write_to_htaccess = false;
        $on_log_blocking_visitor = VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' );
        $on_404_logging = VisitorLog_Utility::get_option_sl( 'on_404_logging' );

        $on_block_ip_perm = VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' );
        $on_block_ip_temp = VisitorLog_Utility::get_option_sl( 'on_block_ip_temp' );

        $delay_time              = VisitorLog_Utility::get_option_sl( 'ddos_delay_time' );
        $ddos_permanent_block_ip = VisitorLog_Utility::get_option_sl( 'ddos_permanent_block_ip' );
        $ddos_redirect_url       = VisitorLog_Utility::get_option_sl( 'ddos_redirect_url' );

        $disable_rest_requests  = VisitorLog_Utility::get_option_sl( 'disable_rest_requests' );
        $on_block_xmlrpc        = VisitorLog_Utility::get_option_sl( 'on_block_xmlrpc' );
        $disable_xmlrpc_methods = VisitorLog_Utility::get_option_sl( 'disable_xmlrpc_methods' );

        if ( isset($_POST['visitorlog_blocking_visitor_save']) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( '' == $on_log_blocking_visitor ) {
               $err = __( 'Turn on the Enable log & blocking of visitors feature on the control panel', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err );
            }
            //---------------------------------------------------- Enable 404 Event Logging

            if ( isset($_POST['visitorlog_on_404_logging']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_404_logging']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_404_logging ) {
                VisitorLog_Utility::update_option_sl( 'on_404_logging', $post );
                $on_404_logging = $post; 
            }
            //---------------------------------------------------- Enable_block_ip_perm
            if ( isset($_POST['visitorlog_on_block_ip_perm']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_block_ip_perm']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_block_ip_perm ) {
                VisitorLog_Utility::update_option_sl( 'on_block_ip_perm', $post );
                $on_block_ip_perm = $post;
            }
            //---------------------------------------------------- Enable_block_ip_temp
            if ( isset($_POST['visitorlog_on_block_ip_temp']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_block_ip_temp']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_block_ip_temp ) {
                VisitorLog_Utility::update_option_sl( 'on_block_ip_temp', $post );
                $on_block_ip_temp = $post; 
            }
            //--------------------------------------------------- Disallow Unauthorized REST Requests
            if ( isset($_POST['visitorlog_disable_rest_requests']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_disable_rest_requests']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $disable_rest_requests ) {
                VisitorLog_Utility::update_option_sl( 'disable_rest_requests', $post );
                $disable_rest_requests = $post; 
            }
            //---------------------------------------------------- Block Access To XMLRPC
            if ( isset($_POST['visitorlog_on_block_xmlrpc']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_block_xmlrpc']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $on_block_xmlrpc ) {
                VisitorLog_Utility::update_option_sl( 'on_block_xmlrpc', $post );
                $on_block_xmlrpc = $post; 
                $write_to_htaccess = true;
            }
            //------------------------------------------ Disable Pingback Functionality From XMLRPC
            if ( isset($_POST['visitorlog_disable_xmlrpc_methods']) ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $post = sanitize_text_field(wp_unslash($_POST['visitorlog_disable_xmlrpc_methods']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
            } else {
                $post = ''; 
            }
            if ( $post != $disable_xmlrpc_methods ) {
                VisitorLog_Utility::update_option_sl( 'disable_xmlrpc_methods', $post );
                $disable_xmlrpc_methods = $post; 
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
            //----------------------------------------------------
            if ( isset($_POST['visitorlog_delay_time']) && $delay_time != $_POST['visitorlog_delay_time'] ) { 

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $delay_time = sanitize_text_field(wp_unslash($_POST['visitorlog_delay_time']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                VisitorLog_Utility::update_option_sl( 'ddos_delay_time', $delay_time );
            }
            if ( isset($_POST['visitorlog_ddos_permanent_block_ip']) && $ddos_permanent_block_ip != $_POST['visitorlog_ddos_permanent_block_ip'] ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $ddos_permanent_block_ip = sanitize_text_field(wp_unslash($_POST['visitorlog_ddos_permanent_block_ip']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                VisitorLog_Utility::update_option_sl( 'ddos_permanent_block_ip', $ddos_permanent_block_ip );
            }

            if ( isset($_POST['visitorlog_ddos_redirect_url']) && $ddos_redirect_url != $_POST['visitorlog_ddos_redirect_url'] ) {

                if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                    $redirect_url = sanitize_url(wp_unslash($_POST['visitorlog_ddos_redirect_url']));
                } else {    
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                if ( $redirect_url == '' || (false === str_contains( $redirect_url, 'https://') && false === str_contains( $redirect_url, 'http://')) ) {
                    $err = __( 'You entered an incorrect format for the "Redirect URL" field. It has been set to the default value', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err );
                } else {
                    VisitorLog_Utility::update_option_sl( 'ddos_redirect_url', $redirect_url );
                    $ddos_redirect_url = $redirect_url;
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

        $dt1=$dt2=$dt3=$dt4=$dt5=$dt6=$dt7=$dt8=$dt9=$dt10=$dt11=$dt12=$dt13=$dt14=$dt15=$dt16=$dt17='';
        $pbi1=$pbi2=$pbi3=$pbi4=$pbi5=$pbi6=$pbi7=$pbi8=$pbi9=$pbi10='';
        $tbs0=$tbs1=$tbs2=$tbs3=$tbs4=$tbs5=$tbs6=$tbs7=$tbs8=$tbs9=$tbs10=$tbs11=$tbs12=$tbs13=$tbs14=$tbs15=$tbs16=$tbs17='';

        switch ( $on_block_ip_perm ) {
            case 'on':
                $ebip = 'checked';
                break;
            default:
                $ebip = '';
                break;
        }    
        switch ( $on_block_ip_temp ) {
            case 'on':
                $ebit = 'checked';
                break;
            default:
                $ebit = '';
                break;
        } 
        switch ( $delay_time ) {
            case '30':
                $dt1 = 'selected';
                break;
            case '60':
                $dt2 = 'selected';
                break;
            case '120':
                $dt3 = 'selected';
                break;
            case '180':
                $dt4 = 'selected';
                break;
            case '300':
                $dt5 = 'selected';
                break;
            case '600':
                $dt6 = 'selected';
                break;
            case '1800':
                $dt7 = 'selected';
                break;
            case '3600':
                $dt8 = 'selected';
                break;
            case '7200':
                $dt9 = 'selected';
                break;
            case '10800':
                $dt10 = 'selected';
                break;
            case '21600':
                $dt11 = 'selected';
                break;
            case '43200':
                $dt12 = 'selected';
                break;
            case '86400':
                $dt13 = 'selected';
                break;
            case '172800':
                $dt14 = 'selected';
                break;
            case '259200':
                $dt15 = 'selected';
                break;
            case '604800':
                $dt16 = 'selected';
                break;
            case '2419200':
                $dt17 = 'selected';
                break;
            default:
                $dt1 = 'selected';
                break; 
        }
        switch ( $ddos_permanent_block_ip ) {
            case '1':
                $pbi1 = 'selected';
                break;
            case '2':
                $pbi2 = 'selected';
                break;
            case '3':
                $pbi3 = 'selected';
                break;
            case '4':
                $pbi4 = 'selected';
                break;
            case '5':
                $pbi5 = 'selected';
                break;
            case '6':
                $pbi6 = 'selected';
                break;
            case '7':
                $pbi7 = 'selected';
                break;
            case '8':
                $pbi8 = 'selected';
                break;
            case '9':
                $pbi9 = 'selected';
                break;
            case '10':
                $pbi10 = 'selected';
                break;
            default:
                $pbi5 = 'selected';
                break; 
        }
        switch ( $disable_rest_requests ) {
            case 'on':
                $rest = 'checked';
                break;
            default:
                $rest = '';
                break;
        }
        switch ( $on_block_xmlrpc ) {
            case 'on':
                $ebx = 'checked';
                break;
            default:
                $ebx = '';
                break;
        }
        switch ( $disable_xmlrpc_methods ) {
            case 'on':
                $dxm = 'checked';
                break;
            default:
                $dxm = '';
                break;
        }
        switch ( $on_404_logging ) {
            case 'on':
                $e404 = 'checked';
                break;
            default:
                $e404 = '';
                break;
        }
        if ( 'on' == $on_log_blocking_visitor ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg  = __( 'The visitors registration and blocking panel is disabled!', 'visitorlog' );
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
                                        <td width="478">
                                            <input type="checkbox" id="visitorlog_checkbox_select" value="on">
                                            &ensp;
                                            <?php esc_html_e('Enable/Disable all','visitorlog');?>
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
VisitorLog_Utility::redirect_html( 'file-code.png', $title, 'visitorlog_blockvisitor', '&reg=htaccess' );
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
                                        <td width="50"> <?php esc_html_e('Enable','visitorlog');?></td>
                                        <td width="160"><?php esc_html_e('Functions','visitorlog');?></td>
                                        <td width="222"><?php esc_html_e('Parameters','visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description','visitorlog');?></td> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td colspan="4">
                                            <div>
                                                <em style="font-size:16px;">
                                <?php esc_html_e('DDoS attacks, 404 events', 'visitorlog');?>
                                                </em>
                                                &emsp;
<?php
$title = __('Blacklist Locked IPs', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_lockedaddr' );
?>
                                                &emsp;
<?php
$title = __('Table of Bots', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_botstable' );
?>
                                            </div>
                                        </td>
                                    </tr>    
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Detection of DDoS attacks', 'visitorlog');?>
                                        </td>
                                        <td>
                                        </td>                                        
                                        <td>
<?php esc_html_e('There is only a determination of the presence of a massive DDoS attack and its reflection in the table with IP addresses', 'visitorlog');?>.
&nbsp;
<?php esc_html_e('Protection against attacks is implemented in the full version of the plugin', 'visitorlog');?><code>PRO</code>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="45">
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_404_logging" name="visitorlog_on_404_logging" type="checkbox" value="on" <?php echo esc_html($e404);?>>
                                                    <label for="visitorlog_on_404_logging"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td width="160">
            <?php esc_html_e('Enable 404 Event Logging & Locking', 'visitorlog');?> 
                                        </td>
                                        <td width="222">
<?php
$title = __('Event 404', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_event404' );
?>
                                        </td>                                        
                                        <td>
            <?php esc_html_e('Check this if you want to enable 404 IP Detection and Lockout', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                                <?php esc_html_e('IP address blocking parameters', 'visitorlog');?>.
                                            </em>
                                        </td>    
                                    </tr>    
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_block_ip_perm" name="visitorlog_on_block_ip_perm" type="checkbox" value="on" <?php echo esc_html($ebip);?>>
                                                    <label for="visitorlog_on_block_ip_perm"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>    
                                <?php esc_html_e('Permanent blocking of IP addresses', 'visitorlog');?>
                                        </td>    
                                        <td>
                                        </td>
                                        <td>    
<?php esc_html_e('Enabling the ability to block unwanted IP addresses by entering control instructions in the file', 'visitorlog');?> <code>htaccess</code>.
                                        </td>
                                    </tr>    
                                    <tr>
                                        <td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_block_ip_temp" name="visitorlog_on_block_ip_temp" type="checkbox" value="on" <?php echo esc_html($ebit);?>>
                                                    <label for="visitorlog_on_block_ip_temp"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>    
                                <?php esc_html_e('Temporary blocking of IP addresses', 'visitorlog');?>
                                        </td>    
                                        <td>
                                        </td>
                                        <td>    
<?php esc_html_e('Enabling the ability to block unwanted IP addresses programmatically using the address table', 'visitorlog');?>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                            <?php esc_html_e('IP address blocking period', 'visitorlog');?>
                                        </td>
                                        <td>
    <label class="form__input1">
        <select name="visitorlog_delay_time">
<option value="30"     <?php echo esc_html($dt1);?>> <?php esc_html_e('30 seconds', 'visitorlog');?></option>
<option value="60"     <?php echo esc_html($dt2);?>> <?php esc_html_e('1 minute', 'visitorlog');?></option>
<option value="120"    <?php echo esc_html($dt3);?>> <?php esc_html_e('2 minutes', 'visitorlog');?></option>
<option value="180"    <?php echo esc_html($dt4);?>> <?php esc_html_e('3 minutes', 'visitorlog');?></option>
<option value="300"    <?php echo esc_html($dt5);?>> <?php esc_html_e('5 minutes', 'visitorlog');?></option>
<option value="600"    <?php echo esc_html($dt6);?>> <?php esc_html_e('10 minutes', 'visitorlog');?></option>
<option value="1800"   <?php echo esc_html($dt7);?>> <?php esc_html_e('30 minutes', 'visitorlog');?></option>
<option value="3600"   <?php echo esc_html($dt8);?>> <?php esc_html_e('1 hour', 'visitorlog');?></option>
<option value="7200"   <?php echo esc_html($dt9);?>> <?php esc_html_e('2 hours', 'visitorlog');?></option>
<option value="10800"  <?php echo esc_html($dt10);?>><?php esc_html_e('3 hours', 'visitorlog');?></option>
<option value="21600"  <?php echo esc_html($dt11);?>><?php esc_html_e('6 hours', 'visitorlog');?></option>
<option value="43200"  <?php echo esc_html($dt12);?>><?php esc_html_e('12 hours', 'visitorlog');?></option>
<option value="86400"  <?php echo esc_html($dt13);?>><?php esc_html_e('1 day', 'visitorlog');?></option>
<option value="172800" <?php echo esc_html($dt14);?>><?php esc_html_e('2 days', 'visitorlog');?></option>
<option value="259200" <?php echo esc_html($dt15);?>><?php esc_html_e('3 days', 'visitorlog');?></option>
<option value="604800" <?php echo esc_html($dt16);?>><?php esc_html_e('7 days', 'visitorlog');?></option>
<option value="2419200"<?php echo esc_html($dt17);?>><?php esc_html_e('4 weeks', 'visitorlog');?></option>
        </select>
    </label>   
                                        </td>                                        
<td>
    <?php esc_html_e('Time for which a blocked IP address will be from visiting your site', 'visitorlog');?>
</td>
                                    </tr>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Limit of temporary locks', 'visitorlog');?>
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                                <select name="visitorlog_ddos_permanent_block_ip">
                                                    <option value="1"  <?php echo esc_html($pbi1);?> > 1 </option>
                                                    <option value="2"  <?php echo esc_html($pbi2);?> > 2 </option>
                                                    <option value="3"  <?php echo esc_html($pbi3);?> > 3 </option>
                                                    <option value="4"  <?php echo esc_html($pbi4);?> > 4 </option>
                                                    <option value="5"  <?php echo esc_html($pbi5);?> > 5 </option>
                                                    <option value="6"  <?php echo esc_html($pbi6);?> > 6 </option>
                                                    <option value="7"  <?php echo esc_html($pbi7);?> > 7 </option>
                                                    <option value="8"  <?php echo esc_html($pbi8);?> > 8 </option>
                                                    <option value="9"  <?php echo esc_html($pbi9);?> > 9 </option>
                                                    <option value="10" <?php echo esc_html($pbi10);?>> 10 </option>
                                                </select>
                                            </label> 
                                        </td>                                        
                                        <td>
<?php esc_html_e('The number of temporary IP address locks. Then a permanent lock is activated. By default, n=5', 'visitorlog');?>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td width="45">
                                        </td>
                                        <td width="160">
                                            <?php esc_html_e('Redirect to this URL', 'visitorlog');?>
                                        </td>
                                        <td width="222">
                                            <p style="margin-top:-5px;">
<input type="text" size="25" name="visitorlog_ddos_redirect_url" placeholder="<?php esc_html_e('Lock redirect url', 'visitorlog');?>" value="<?php echo esc_url($ddos_redirect_url);?>"/>
                                            </p>    
                                        </td>                                        
                                        <td>
    <?php esc_html_e('A blocked visitor will be automatically redirected to this URL', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
            <?php esc_html_e('Control of other entry points to your site', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr> 
                                    <tr>
                                        <td width="45">
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_disable_rest_requests" name="visitorlog_disable_rest_requests" type="checkbox" value="on" <?php echo esc_html($rest);?>>
                                                    <label for="visitorlog_disable_rest_requests"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td width="160">
                    <?php esc_html_e('Disallow Unauthorized REST Requests', 'visitorlog');?>
                                        </td>
                                        <td width="222">
                                        </td>                                        
                                        <td>
<?php esc_html_e('This feature allows you to block WordPress REST API access for unauthorized requests. When enabled this feature will only allow REST requests to be processed if the user is logged in', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="45">
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_block_xmlrpc" name="visitorlog_on_block_xmlrpc" type="checkbox" value="on" <?php echo esc_html($ebx);?>>
                                                    <label for="visitorlog_on_block_xmlrpc"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td width="160">
            <?php esc_html_e('Block Access To XMLRPC', 'visitorlog');?>
                                        </td>
                                        <td width="222">
                                        </td>                                        
                                        <td>
<?php esc_html_e('Enable this if you are not using WP XMLRPC functionality and want to completely block external access to XML RPC', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="45">
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_disable_xmlrpc_methods" name="visitorlog_disable_xmlrpc_methods" type="checkbox" value="on" <?php echo esc_html($dxm);?>>
                                                    <label for="visitorlog_disable_xmlrpc_methods"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td width="160">
            <?php esc_html_e('Disable Pingback Functionality From XMLRPC', 'visitorlog');?>
                                        </td>
                                        <td width="222">
                                        </td>                                        
                                        <td>
<?php esc_html_e('If you use Jetpack or WP iOS or other apps which need WP XMLRPC functionality then check this. This will enable protection against WordPress pingback vulnerabilities' , 'visitorlog');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="margin-top:10px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=block_ip' );
?>
                            </div>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_blocking_visitor_save" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form> 
        <?php
        exit;

    } // END of func


} // END class