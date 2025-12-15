<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Failed_Logins class
 * 
 * @method public    static function render_failed_logins()
 */
class VisitorLog_Failed_Logins
{
    /**
     * Render failed logins.
     *
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Security_Handler::instance()->truncate_table()
     * @uses VisitorLog_Security_Handler::instance()->delete_rows()
     * @uses VisitorLog_Security_Handler::instance()->ban_unban_ip()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Utility::get_data_select()
     */
    public static function render_failed_logins()
    {
        $write_htaccess = 0;
        $msg = '';
        $color = '';

        $admin_panel_protection  = VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' );
        $enable_login_lockdown   = VisitorLog_Utility::get_option_sl( 'on_login_lockdown' );
        $use_htaccess_file_login = VisitorLog_Utility::get_option_sl( 'use_htaccess_file_login' );

        if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Security_Handler::instance()->handler_htaccess_view();
            return;
        } //--------------------------------  
        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $del_spam = VisitorLog_Security_Handler::instance()->truncate_table( 'events_failed' );
            if ( $del_spam > 0 ) {
                $msg = __('Data has been deleted from the table', 'visitorlog');
                VisitorLog_System_View::show_message( 'blue', $msg );
                $write_htaccess = 1;
            }    
        } //--------------------------------
        if ( isset($_GET['reg']) && 'del' == $_GET ['reg'] && isset($_GET['data']) ) { 

            if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                $data_parse = json_decode( sanitize_text_field(wp_unslash( $_GET['data'] )) );
            } else {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $res = VisitorLog_Security_Handler::delete_rows( 'events_failed', $data_parse, $is_block, $count_del );
            if ( $res > 0 ) {
                /* translators: 1: value. */
                $msg = sprintf( __('Rows in the amount of %d have been deleted from the table', 'visitorlog'), $count_del );
                if ( $is_block > 0 ) {
                    $msg .= '<br>';
                    /* translators: 1: value. */
                    $msg .= sprintf( __('Among the rows deleted from the table, %d were deleted from the Htaccess file', 'visitorlog'), $is_block );
                    $color = 'green';
                    $write_htaccess = 1;
                } else {
                    $msg .= '<br>';
                    $msg .= __('The deleted IP addresses were not blocked in the Htaccess file', 'visitorlog');
                    $color = 'blue';
                }
            } elseif ( $res < 0 ) {
                $msg = __('An error occurred while locking rows', 'visitorlog');
                $color = 'blue';
            } 
        } //-------------------------------- 
        if ( isset($_GET['reg']) && 'ban' == $_GET['reg'] && isset($_GET['data']) ) {

            if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                $data_parse = json_decode( sanitize_text_field(wp_unslash( $_GET['data'] )) );
            } else {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( empty( $data_parse ) ) {
                $color = 'blue';
                $msg = __( 'There are no selected lines', 'visitorlog' );
            } else {
                $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'events_failed', $data_parse, 'ban', $select_block, $num_ban );
                if ( $res > 0 ) {
                    if ( $num_ban > 0 && $select_block == 'perm' ) {
                        /* translators: 1: value. */
                        $msg = sprintf( __( 'In the table has been blocked %d line(s)', 'visitorlog' ), $num_ban );
                        $color = 'green';
                        $write_htaccess = 1;
                    }
                } else {
                    $msg = __('An error occurred while locking rows', 'visitorlog');
                    $color = 'blue';
                }
            } 
        } //--------------------------------
        if ( isset($_GET['reg']) && 'unban' == $_GET['reg'] && isset($_GET['data']) ) {

            if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                $data_parse = json_decode( sanitize_text_field(wp_unslash( $_GET['data'] )) );
            } else {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( empty( $data_parse ) ) {
                $color = 'blue';
                $msg = __( 'There are no selected lines', 'visitorlog' );
            } else {
                $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'events_failed', $data_parse, 'unban', $select_block, $num_unban );
                if ( $res > 0 ) {
                    if ( $num_unban > 0 && $select_block == '' ) {
                        /* translators: 1: value. */
                        $msg = sprintf( __( 'In the table has been unblocked %d line(s)', 'visitorlog' ), $num_unban );
                        $color = 'green';
                        $write_htaccess = 1;
                    }
                } else {
                    $msg = __('An error occurred while locking rows', 'visitorlog');
                    $color = 'blue';
                }
            } 
        }
        //-------------------------------
        if ( 1 == $write_htaccess ) {

            if ( 'on' == $admin_panel_protection && 'on' == $enable_login_lockdown && 'on' == $use_htaccess_file_login ) {
                $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess(); 
                if ($res) {
                    if ( '' != $msg ) $msg .= '<br>';
                    $msg .= __( 'Successful entry of the directives to the file .htaccess', 'visitorlog' );
                    $color = 'green';
                }
            } else {
                if ( '' != $msg ) $msg .= '<br>';
                $msg .= __( 'Enable permissions to write directives to the htaccess file', 'visitorlog' );
                $color = 'blue';
            }   
        }
        //--------------------- Message
        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_failedlogins' );
            exit;
        }
        //--------------------- Display
        if ( '' != VisitorLog_Utility::get_option_sl( 'temp_message' ) ) {
            $msg   = VisitorLog_Utility::get_option_sl( 'temp_message' );
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
            if ( 'green' == $color || 'blue' == $color) {
                $icon = '';
            } else {
                $icon = 'off-icon';
            }
        } elseif ( 'on' == $admin_panel_protection && 'on' == $enable_login_lockdown ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg  = __( 'Registration and blocking of bots and their IP addresses when logging into the admin panel is not activated!', 'visitorlog' );
            $msg .= '<br>';
            $msg .= __( 'Check the activation of the function on the control panel and in the security management module of the administrative panel', 'visitorlog' );
            VisitorLog_System_View::show_message( 'yellow', $msg );
        }
        $color_view1 = $color_view2 = $color_view3 = '';
        $style1 = 'color:green;font-weight:bold;'; 
        $err_ = '';       

        if ( isset($_GET['reg']) && 'viewall' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_Utility::get_data_select( 'events_failed', 'viewall', $n_rows, $err_ );
            $color_view1 = $style1;
        } elseif ( isset($_GET['reg']) && 'viewban' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_Utility::get_data_select( 'events_failed', 'viewban', $n_rows, $err_ );
            $color_view3 = $style1;
        } elseif ( isset($_GET['reg']) && 'viewunban' == $_GET ['reg'] ) {
 
            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_Utility::get_data_select( 'events_failed', 'viewunban', $n_rows, $err_ );
            $color_view2 = $style1;
        } else {
            $result = VisitorLog_Utility::get_data_select( 'events_failed', 'viewall', $n_rows, $err_ ); 
            $color_view1 = $style1;
        }
        if ( $err_ ) VisitorLog_System_View::show_message( 'orange', $err_ );
        ?>  
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <p style="font-size:14px;">
1.&nbsp;
<?php esc_html_e('The data in the table is generated automatically when a spammer tries to log into the admin panel','visitorlog');?>
.<br>2.&nbsp;
<?php esc_html_e('At the first stage, the IP address is temporarily blocked. If the number of temporary locks exceeds the set limit, a permanent lock occurs in the Htaccess file','visitorlog');?>
.<br>3.&nbsp;
<?php esc_html_e('If desired, the administrator can delete an entry from the table, permanently block one address or a group of IP addresses in the htaccess file','visitorlog');?>
.<br>4.&nbsp;
<?php esc_html_e('IP address blocking parameters are managed in the security management module of the admin panel','visitorlog');?>
&nbsp;
<?php
$title = __('Admin panel security features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_adminpanel' );
?>
                        </p>
                    </div>
                    <div style="margin-top:-10px;font-size:12px;display:flex;flex-direction:row;justify-content: flex-end;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide&addr=failed_logins_table' );
?>
                    </div>
                    <div style="margin-top:4px;"></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody style="background-color:#F2F2F2;">
                                    <tr>
                                        <td>
<?php  
if ( '' != $icon ) {
?>
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_spamprotection')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('This function is disabled', 'visitorlog');?>!">
    <?php VisitorLog_Utility::render_picture('remove.png');?>&nbsp;
    <?php esc_html_e('This function is disabled','visitorlog');?>!
</a>
                                            &nbsp; &vellip; &nbsp;
<?php
}
?>
                                            <?php esc_html_e( 'Select view', 'visitorlog' );?>:
                                            &emsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_failedlogins')).'&reg=viewall&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('All', 'visitorlog');?>" style="<?php echo esc_html($color_view1);?>">&nbsp;<?php esc_html_e('All', 'visitorlog');?>
</a> 
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_failedlogins')).'&reg=viewban&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('Ban', 'visitorlog');?>" style="<?php echo esc_html($color_view3);?>">&nbsp;<?php esc_html_e('Ban', 'visitorlog');?>
</a>
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php')).'?page=visitorlog_failedlogins&reg=viewunban&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('Unban', 'visitorlog');?>" style="<?php echo esc_html($color_view2);?>">&nbsp;<?php esc_html_e('Unban', 'visitorlog');?>
</a> 
                                            &nbsp; &vellip; &nbsp;
<?php
$title = __('View file .htaccess', 'visitorlog');
VisitorLog_Utility::redirect_html( 'code.png', $title, 'visitorlog_failedlogins', '&reg=htaccess' );
?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>  
<input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" value="on">&ensp;
                                            <?php esc_html_e('Check all','visitorlog');?>
                                            &ensp;&vellip;&ensp;<?php esc_html_e('With selected','visitorlog');?>
                                            :&emsp;
<?php
$msg  = __('Delete', 'visitorlog');
VisitorLog_Utility::link_in_table( 'visitorlog_delete_select', $msg, 'b_drop.png' );
?>
                                            &ensp;&ensp; 
<?php
$msg  = __('Ban', 'visitorlog');
VisitorLog_Utility::link_in_table( 'visitorlog_ban_select', $msg, 'ban.png' );
?>
                                            &ensp;&ensp; 
<?php
$msg  = __('Unban', 'visitorlog');
VisitorLog_Utility::link_in_table( 'visitorlog_unban_select', $msg, 'unban.png' );
?>
                                            &ensp;&vellip;&ensp;
<?php
$msg  = __('Clear the table', 'visitorlog');
VisitorLog_Utility::link_in_table( 'visitorlog_delete_all', $msg, 'deltbl.png' );
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
   
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped js-basic-example dataTable">
                                <thead>
                                    <tr>
            <td class="vl-thead" width="30"></td>
            <td class="vl-thead" width="150"><?php esc_html_e('Date Write/Release', 'visitorlog');?></td>
            <td class="vl-thead">IP</td>
            <td class="vl-thead" width="55"><?php esc_html_e('Count', 'visitorlog');?></td>
            <td class="vl-thead" width="45"><?php esc_html_e('Block', 'visitorlog');?></td>
            <td class="vl-thead" width="55"><?php esc_html_e('Canal', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Author', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Login', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Password', 'visitorlog');?></td>
            <td class="vl-thead">URL</td>
            <td class="vl-thead"><?php esc_html_e('Refinfo', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
            <td class="vl-thead" width="30"></td>
            <td class="vl-thead" width="150"><?php esc_html_e('Date Write/Release', 'visitorlog');?></td>
            <td class="vl-thead">IP</td>
            <td class="vl-thead" width="55"><?php esc_html_e('Count', 'visitorlog');?></td>
            <td class="vl-thead" width="45"><?php esc_html_e('Block', 'visitorlog');?></td>
            <td class="vl-thead" width="55"><?php esc_html_e('Canal', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Author', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Login', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Password', 'visitorlog');?></td>
            <td class="vl-thead">URL</td>
            <td class="vl-thead"><?php esc_html_e('Refinfo', 'visitorlog');?></td>
                                    </tr>
                                </tfoot>
                                <tbody>
        <?php
        $i = 0;
        while ( $i < $n_rows ) {
            $id     = $result[$i]->id;
            $col1   = $result[$i]->timestamp;
            $col1_1 = $result[$i]->timerelease;
            $col2   = $result[$i]->user_ip;
            $col3   = $result[$i]->count;
            $col4   = $result[$i]->block;
            $col5   = $result[$i]->canal;
            $col6   = $result[$i]->category;
            $col7   = $result[$i]->login;
            $col8   = $result[$i]->pswd;
            $col9   = $result[$i]->url;
            $col10  = $result[$i]->referer_info;

            $col1 = date_i18n( 'd M, Y H:i:s', $col1 );
        ?>
        <tr style="font-size:13px;">
            <td>
<input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" title="<?php esc_html_e('Select row', 'visitorlog');?>" value="<?php echo esc_html($id);?>">
            </td>
            <td>
                <?php
                if(0 == $col1_1) {
                    echo esc_html($col1);
                } else {
                    echo esc_html($col1).'<br>'. esc_html(date_i18n( 'd M, Y H:i:s', $col1_1 ));
                }    
                ?>
            </td>                                   
            <td><?php echo esc_html($col2); ?></td>
            <td><?php echo esc_html($col3); ?></td>
            <td><?php echo esc_html($col4); ?></td>
            <td><?php echo esc_html($col5); ?></td>
            <td>
                <span <?php VisitorLog_System_View::show_badge($col6);?>>
                    <?php echo esc_html($col6);?>
                </span>
            </td>
            <td><?php echo esc_html($col7); ?></td>
            <td><?php echo esc_html($col8); ?></td>
            <td>
                <?php
                if ( strlen($col9) > 20 ) {
                    $col91 = substr($col9, 0, 19) . ' ...';
                    echo '<a href="#" title="'.esc_html($col9).'">'.esc_html($col91).'</a>';
                } else {
                    echo esc_html($col9);
                }  
                ?>
            </td> 
            <td>
                <?php
                if ( strlen($col10) > 20 ) {
                    $col101 = substr($col10, 0, 19) . ' ...';
                    echo '<a href="#" title="'.esc_html($col10).'">'.esc_html($col101).'</a>';
                } else {
                    echo esc_html($col10);
                }  
                ?>
            </td>
        </tr>
        <?php
            $i ++;
        } // END while ----------
        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        VisitorLog_Utility::localize_script( 'visitorlog_failedlogins' );

    } // END func 


} // END class