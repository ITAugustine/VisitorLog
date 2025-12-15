<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Spam class
 * 
 * @method public static function render_spam()
 */
class VisitorLog_Spam
{
    /**
     * Render Spam.
     *
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Security_Handler::instance()->truncate_table()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Handler::instance()->delete_rows_in_table()
     * @uses VisitorLog_Security_Handler::instance()->ban_unban_ip_table()
     * @uses VisitorLog_Utility::get_data_select()
     */
    public static function render_spam()
    { 
        $write_htaccess = '';
        $msg = '';
        $color = '';
        $color_view1 = $color_view2 = $color_view3 = $color_view4 = $color_view5 = '';
        $style1 = 'color:green;font-weight:bold;'; 

        $on_spam_protection     = VisitorLog_Utility::get_option_sl( 'on_spam_protection' );
        $on_forbid_commentspost = VisitorLog_Utility::get_option_sl( 'on_forbid_commentspost' ); // 1
        $on_block_spam_perm     = VisitorLog_Utility::get_option_sl( 'on_block_spam_perm' );

        if ( isset($_GET['reg']) ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            if ( 'htaccess' == $_GET['reg'] ) {
 
                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                VisitorLog_Security_Handler::instance()->handler_htaccess_view();
                exit;
            }

            if ( 'clear_tbl' == $_GET['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $del_spam = VisitorLog_Security_Handler::instance()->truncate_table( 'spam' );
                if ( $del_spam > 0 ) {
                    $msg = __('Data has been deleted from the table', 'visitorlog');
                    $color = 'blue';
                    $write_htaccess = 1;
                }  
            }

            if ( 'del' == $_GET['reg'] && isset($_GET['data']) ) { 

                if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                    $data_parse = json_decode(sanitize_text_field(wp_unslash($_GET['data']))); 
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                if ( empty( $data_parse ) ) {
                    $color = 'blue';
                    $msg = __( 'There are no selected lines', 'visitorlog' );
                } else {
                    $res = VisitorLog_Security_Handler::delete_rows( 'spam', $data_parse, $is_block, $count_del );
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
                }  
            }

            if ( 'ban' == $_GET['reg'] && isset($_GET['data']) ) {

                if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                    $data_parse = json_decode(sanitize_text_field(wp_unslash($_GET['data']))); 
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                if ( empty( $data_parse ) ) {
                    $color = 'blue';
                    $msg = __( 'There are no selected lines', 'visitorlog' );
                } else {
                    $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'spam', $data_parse, 'ban', $select_block, $num_ban );
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
            }

            if ( 'unban' == $_GET['reg'] && isset($_GET['data']) ) {

                if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                    $data_parse = json_decode(sanitize_text_field(wp_unslash($_GET['data']))); 
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                if ( empty( $data_parse ) ) {
                    $color = 'blue';
                    $msg = __( 'There are no selected lines', 'visitorlog' );
                } else {
                    $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'spam', $data_parse, 'unban', $select_block, $num_unban );
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

            if ( 'viewall' == $_GET ['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $result = VisitorLog_Utility::get_data_select( 'spam', 'viewall', $n_rows, $err_ );
                $color_view1 = $style1;
            } elseif ( 'viewban' == $_GET ['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $result = VisitorLog_Utility::get_data_select( 'spam', 'viewban', $n_rows, $err_ );
                $color_view3 = $style1;
            } elseif ( 'viewunban' == $_GET ['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $result = VisitorLog_Utility::get_data_select( 'spam', 'viewunban', $n_rows, $err_ );
                $color_view2 = $style1;
            } else {
                $result = VisitorLog_Utility::get_data_select( 'spam', 'viewall', $n_rows, $err_ ); 
                $color_view1 = $style1;  
            }
        } else {
            $result = VisitorLog_Utility::get_data_select( 'spam', 'viewall', $n_rows, $err_ ); 
            $color_view1 = $style1;            
        }

        if ( 1 == $write_htaccess ) { 

            if ( 'on' == $on_spam_protection && 'on' == $on_block_spam_perm && 'on' == $on_forbid_commentspost ) {

                $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess();
                if ( $res ) {
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
            VisitorLog_Utility::vl_redirect( 'visitorlog_spam' );
            exit;
        }
        //--------------------- Display

        if ( '' != VisitorLog_Utility::get_option_sl( 'temp_message' ) ) {
            $msg   = VisitorLog_Utility::get_option_sl( 'temp_message' );
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
            if ( 'green' == $color || 'blue' == $color ) {
                $icon = '';
            } else {
                $icon = 'off-icon';
            }
        } elseif ( 'on' == $on_spam_protection && 'on' == $on_forbid_commentspost ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg = __( 'Activate registration and blocking of visitors on the control panel and enable spam recognition mode', 'visitorlog' );
            VisitorLog_System_View::show_message( 'yellow', $msg );
        } 
        if ( $err_ ) VisitorLog_System_View::show_message( 'orange', $err_ );
        ?>  
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <p style="font-size:14px;">
1.&nbsp;
<?php esc_html_e('The data in the table is received automatically when Wordpress detects spam in comments','visitorlog');?>
.<br>2.&nbsp;
<?php esc_html_e('If desired, the administrator can delete an entry from the table, permanently block one address or a group of IP addresses in the htaccess file','visitorlog');?>
.<br>3.&nbsp;
<?php esc_html_e('IP address blocking parameters are managed in the anti-spam module','visitorlog');?>
&nbsp;
<?php
$title = __('Spam protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_spamprotection' );
?>
                        </p>
                    </div>
                    <div style="margin-top:-10px;font-size:12px;display:flex;flex-direction:row;justify-content: flex-end;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide&addr=spam_table' );
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
<a href="<?php echo esc_url(admin_url('admin.php')).'?page=visitorlog_spam&reg=viewall&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('All', 'visitorlog');?>" style="<?php echo esc_html($color_view1);?>">&nbsp;<?php esc_html_e('All', 'visitorlog');?>
</a> 
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php')).'?page=visitorlog_spam&reg=viewban&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('Ban', 'visitorlog');?>" style="<?php echo esc_html($color_view3);?>">&nbsp;<?php esc_html_e('Ban', 'visitorlog');?>
</a>
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php')).'?page=visitorlog_spam&reg=viewunban&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('Unban', 'visitorlog');?>" style="<?php echo esc_html($color_view2);?>">&nbsp;<?php esc_html_e('Unban', 'visitorlog');?>
</a> 
                                            &nbsp; &vellip; &nbsp;
<?php
$title = __('View file .htaccess', 'visitorlog');
VisitorLog_Utility::redirect_html( 'code.png', $title, 'visitorlog_spam', '&reg=htaccess' );
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
        <div class="row clearfix" style="margin-top:10px;">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
                    <td class="vl-thead" width="30"></td>
                    <td class="vl-thead" width="150"><?php esc_html_e('Date/Time', 'visitorlog');?></td>
                    <td class="vl-thead">IP</td>
                    <td class="vl-thead" width="45"><?php esc_html_e('Block', 'visitorlog');?></td>
                    <td class="vl-thead" width="55"><?php esc_html_e('Reason', 'visitorlog');?></td>
                    <td class="vl-thead">URL</td>
                    <td class="vl-thead"><?php esc_html_e('Content', 'visitorlog');?></td>
                    <td class="vl-thead"><?php esc_html_e('User agent', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                    <td class="vl-thead" width="30"></td>
                    <td class="vl-thead" width="150"><?php esc_html_e('Date/Time', 'visitorlog');?></td>
                    <td class="vl-thead">IP</td>
                    <td class="vl-thead" width="45"><?php esc_html_e('Block', 'visitorlog');?></td>
                    <td class="vl-thead" width="55"><?php esc_html_e('Reason', 'visitorlog');?></td>
                    <td class="vl-thead">URL</td>
                    <td class="vl-thead"><?php esc_html_e('Content', 'visitorlog');?></td>
                    <td class="vl-thead"><?php esc_html_e('User agent', 'visitorlog');?></td>
                                    </tr>
                                </tfoot>
                                <tbody>
        <?php
        $i = 0;
        while ( $i < $n_rows ) {
            $id     = $result[$i]->id;
            $field1 = $result[$i]->timestamp;
            $field2 = $result[$i]->author_ip;
            $field3 = $result[$i]->block;
            $field4 = $result[$i]->reason;
            $field5 = $result[$i]->author_url;
            $field6 = $result[$i]->content;
            $field7 = $result[$i]->agent;

            $field1 = date_i18n( 'd M, Y H:i:s', $field1 );

            if ( strlen($field5) > 20 ) {
                $field51 = substr($field5, 0, 19) . ' ...';
            } else {
                $field51 = $field5;
            }
            if ( strlen($field7) > 20 ) {
                $field71 = substr($field7, 0, 19) . ' ...';
            } else {
                $field71 = $field7;
            }
        ?> 
        <tr style="font-size:13px;">
            <td>
<input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" title="<?php esc_html_e('Select row', 'visitorlog');?>" value="<?php echo esc_html($id);?>">
            </td>
            <td><?php echo esc_html($field1);?></td> 
            <td><?php echo esc_html($field2);?></td>                                       
            <td><?php echo esc_html($field3);?></td>
            <td><?php echo esc_html($field4);?></td>
            <td><a href="#" title="<?php echo esc_html($field5);?>"><?php echo esc_html($field51);?></a></td>
            <td><?php echo wp_kses($field6, 'post');?></td> 
            <td><a href="#" title="<?php echo esc_html($field7);?>"><?php echo esc_html($field71);?></a></td>
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
        VisitorLog_Utility::localize_script( 'visitorlog_spam' );

    } // END func  


} // END class