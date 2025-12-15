<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Class VisitorLog_Locked_IP
 * 
 * @method public    static function render_locked_ip_addresses()
 * @method public    static function render_locked_ip_insert()
 * @method protected static function submit_save()
 * @method protected static function check_ip_duble()
 */
class VisitorLog_Locked_IP
{
    public static function class_name()
    {
        return __CLASS__;
    }

    /**
     * Render Locked IP addresses.
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
    public static function render_locked_ip_addresses()
    {
        $write_htaccess = 0;
        $select_block = '';
        $msg = '';
        $color = '';

        $on_log_blocking_visitor = VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' );
        $on_block_ip_perm        = VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' );

        if ( isset($_GET['ins']) && 0 < $_GET['ins'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            self::render_locked_ip_insert(); 
            return;       
        } //--------------------------
        if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Security_Handler::instance()->handler_htaccess_view();
            exit;
        } //--------------------------
        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $clear_tbl = VisitorLog_Security_Handler::instance()->truncate_table( 'lockdown_ip' );
            if ( $clear_tbl > 0 ) {
                $msg = __('Data has been deleted from the table', 'visitorlog');
                $color = 'green';
                $write_htaccess = 1;
            }  
        } //--------------------------
        if ( isset($_GET['reg']) && 'del' == $_GET['reg'] && isset($_GET['data']) ) {

            if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                $data_parse = json_decode( sanitize_text_field(wp_unslash( $_GET['data'] )) );
            } else {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( empty( $data_parse ) ) {
                $color = 'yellow';
                $msg = __( 'There are no selected lines', 'visitorlog' );
            } else {
                $res = VisitorLog_Security_Handler::delete_rows( 'lockdown_ip', $data_parse, $is_block, $count_del );
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
                        $color = 'yellow';
                    }
                } elseif ( $res < 0 ) {
                    $msg = __('An error occurred while locking rows', 'visitorlog');
                    $color = 'yellow';
                } 
            }   
        } //--------------------------
        if ( isset($_GET['reg']) && 'ban' == $_GET['reg'] && isset($_GET['data']) ) {

            if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                $data_parse = json_decode( sanitize_text_field(wp_unslash( $_GET['data'] )) );
            } else {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( empty( $data_parse ) ) {
                $color = 'yellow';
                $msg = __( 'There are no selected lines', 'visitorlog' );
            } else {
                $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'lockdown_ip', $data_parse, 'ban', $select_block, $num_ban );
                if ( $res > 0 ) {
                    if ( $num_ban > 0 && $select_block == 'perm' ) {
                        /* translators: 1: value. */
                        $msg = sprintf( __( 'In the table has been blocked %d line(s)', 'visitorlog' ), $num_ban );
                        $color = 'green';
                        $write_htaccess = 1;
                    }
                } else {
                    $msg = __('An error occurred while locking rows', 'visitorlog');
                    $color = 'yellow';
                }
            }    
        } //--------------------------
        if ( isset($_GET['reg']) && 'unban' == $_GET['reg'] && isset($_GET['data']) ) {

            if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                $data_parse = json_decode( sanitize_text_field(wp_unslash( $_GET['data'] )) );
            } else {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( empty( $data_parse ) ) {
                $color = 'yellow';
                $msg = __( 'There are no selected lines', 'visitorlog' );
            } else {
                $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'lockdown_ip', $data_parse, 'unban', $select_block, $num_unban );
                if ( $res > 0 ) {
                    if ( $num_unban > 0 && $select_block == '' ) {
                        /* translators: 1: value. */
                        $msg = sprintf( __( 'In the table has been unblocked %d line(s)', 'visitorlog' ), $num_unban );
                        $color = 'green';
                        $write_htaccess = 1;
                    }
                } else {
                    $msg = __('An error occurred while locking rows', 'visitorlog');
                    $color = 'yellow';
                }
            }    
        } //--------------------------
        if ( 1 == $write_htaccess ) { 

            if ( 'on' == $on_log_blocking_visitor && 'on' == $on_block_ip_perm ) {
                $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess();
                if ( $res ) {
                    if ( '' != $msg ) $msg .= '<br>';
                    $msg .= __( 'Successful entry of the directives to the file .htaccess', 'visitorlog' );
                    $color = 'green';
                } 
            } else {
                if ( '' != $msg ) $msg .= '<br>';
                $msg .= __( 'Enable permissions to write directives to the htaccess file', 'visitorlog' );
                $color = 'yellow';
            }
        }
        //------------------------- Message
        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_lockedaddr' );
            exit;
        }
        //------------------------- Display
        if ( '' != VisitorLog_Utility::get_option_sl( 'temp_message' ) ) {
            $msg   = VisitorLog_Utility::get_option_sl( 'temp_message' );
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
            if ( 'green' == $color ) {
                $icon = '';
            } else {
                $icon = 'off-icon';
            }
        } elseif ( 'on' == $on_log_blocking_visitor && 'on' == $on_block_ip_perm ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg  = __( 'Writing control instructions to a file .htaccess is blocked!', 'visitorlog' );
            $msg .= '<br>';
            $msg .= __( 'Activate registration and blocking of visitors on the control panel and enable permanent blocking of IP addresses on it.', 'visitorlog' );
            VisitorLog_System_View::show_message( 'yellow', $msg );
        }  

        $color_view1 = $color_view2 = $color_view3 = '';
        $style1 = 'color:green;font-weight:bold;';        

        if ( isset($_GET['reg']) && 'viewall' == $_GET ['reg'] ) {
 
            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_Utility::get_data_select( 'lockdown_ip', 'viewall', $n_rows, $err_ );
            $color_view1 = $style1;
        } elseif ( isset($_GET['reg']) && 'viewban' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_Utility::get_data_select( 'lockdown_ip', 'viewban', $n_rows, $err_ );
            $color_view3 = $style1;
        } elseif ( isset($_GET['reg']) && 'viewunban' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_Utility::get_data_select( 'lockdown_ip', 'viewunban', $n_rows, $err_ );
            $color_view2 = $style1;
        } else {
            $result = VisitorLog_Utility::get_data_select( 'lockdown_ip', 'viewall', $n_rows, $err_ ); 
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
<?php esc_html_e('The data is entered into the table automatically from the DDoS protection module, as well as manually by the site administrator','visitorlog');?>
.<br>2.&nbsp;
<?php esc_html_e('IP addresses are not automatically blocked, this is implemented in the full version of the plugin','visitorlog');?>
.<br>3.&nbsp;
<?php esc_html_e('The administrator can add a new IP address, delete, permanently block one or a group of addresses','visitorlog');?>
.<br>4.&nbsp;
<?php esc_html_e('IP address blocking parameters are managed in the user registration and blocking module','visitorlog');?>
&nbsp;
<?php
$title = __('Logging & blocking functions', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_blockvisitor' );
?>
                        </p>
                    </div>
                    <div style="margin-top:-10px;font-size:12px;display:flex;flex-direction:row;justify-content: flex-end;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide&addr=blacklist' );
?>
                    </div>
                    <div style="margin-top:4px;"></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody style="background-color:#F2F2F2;font-size:13px;">
                                    <tr>
                                        <td>
<?php  
if ( '' != $icon ) {
?>
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_blockvisitor')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('This function is disabled', 'visitorlog');?>!">
    <?php VisitorLog_Utility::render_picture('remove.png');?>&nbsp;
    <?php esc_html_e('This function is disabled','visitorlog');?>!
</a>
                                            &nbsp; &vellip; &nbsp;
<?php
}
?>
                                            <?php esc_html_e( 'Select view', 'visitorlog' );?>:
                                            &emsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_lockedaddr')).'&reg=viewall&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('All', 'visitorlog');?>" style="<?php echo esc_html($color_view1);?>">&nbsp;<?php esc_html_e('All', 'visitorlog');?>
</a> 
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_lockedaddr')).'&reg=viewban&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('Ban', 'visitorlog');?>" style="<?php echo esc_html($color_view3);?>">&nbsp;<?php esc_html_e('Ban', 'visitorlog');?>
</a>
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_lockedaddr')).'&reg=viewunban&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('Unban', 'visitorlog');?>" style="<?php echo esc_html($color_view2);?>">&nbsp;<?php esc_html_e('Unban', 'visitorlog');?>
</a> 
                                            &nbsp; &vellip; &nbsp;
<?php
$title = __('View file .htaccess', 'visitorlog');
VisitorLog_Utility::redirect_html( 'code.png', $title, 'visitorlog_lockedaddr', '&reg=htaccess' );
?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>  
<input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" value="on">
                                            &ensp;
                                            <?php esc_html_e('Check all','visitorlog');?>
                                            &ensp;&vellip;&ensp;
                                            <?php esc_html_e('With selected','visitorlog');?>
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
                                            &ensp;&vellip;&ensp;
<?php
$title = __('Row insert', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_insrow.png', $title, 'visitorlog_lockedaddr', '&ins=1&reg=insert' );
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
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
            <td class="vl-thead" width="60"><?php esc_html_e('Select','visitorlog');?></td>
            <td class="vl-thead" width="150"><?php esc_html_e('Date Write/Release', 'visitorlog');?></td>
            <td class="vl-thead">IP</td>
            <td class="vl-thead" width="45"><?php esc_html_e('Block', 'visitorlog');?></td>
            <td class="vl-thead" width="45"><?php esc_html_e('Canal', 'visitorlog');?></td>
            <td class="vl-thead" width="55"><?php esc_html_e('Category', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('User agent', 'visitorlog');?></td>
            <td class="vl-thead">URL</td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
            <td class="vl-thead" width="60"><?php esc_html_e('Select','visitorlog');?></td>
            <td class="vl-thead" width="150"><?php esc_html_e('Date Write/Release', 'visitorlog');?></td>
            <td class="vl-thead">IP</td>
            <td class="vl-thead" width="45"><?php esc_html_e('Block', 'visitorlog');?></td>
            <td class="vl-thead" width="45"><?php esc_html_e('Canal', 'visitorlog');?></td>
            <td class="vl-thead" width="55"><?php esc_html_e('Category', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('User agent', 'visitorlog');?></td>
            <td class="vl-thead">URL</td>
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
            $col3   = $result[$i]->block;
            $col4   = $result[$i]->canal;
            $col5   = $result[$i]->category;
            $col6   = $result[$i]->user_name;
            $col7   = $result[$i]->url;

            $col1 = date_i18n( 'd M, Y H:i:s', $col1 );
        ?>
        <tr style="font-size:13px;">
            <td width="70">
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
            <td><?php echo esc_html($col2);?></td>
            <td><?php echo esc_html($col3);?></td>
            <td><?php echo esc_html($col4);?></td>
            <td>
                <span <?php VisitorLog_System_View::show_badge($col5);?>>
                    <?php echo esc_html($col5);?>
                </span>
            </td>
            <td>
                <?php
                if ( strlen($col6) > 20 ) {
                    $col61 = substr($col6, 0, 19) . ' ...';
                    echo '<a href="#" title="'.esc_html($col6).'">'.esc_html($col61).'</a>';
                } else {
                    echo esc_html($col6);
                }  
                ?>
            </td> 
            <td>
                <?php
                if ( strlen($col7) > 20 ) {
                    $col71 = substr($col7, 0, 19) . ' ...';
                    echo '<a href="#" title="'.esc_html($col7).'">'.esc_html($col71).'</a>';
                } else {
                    echo esc_html($col7);
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
        VisitorLog_Utility::localize_script( 'visitorlog_lockedaddr' );

    } // END func   

    /**
     * Render Locked IP (insert rows).
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Utility::get_row()
     */
    public static function render_locked_ip_insert()
    {
        $timestamp = current_time('timestamp');
        $count = 0;
        $block = '';
        $textarea_addr = '';
        $textarea_name = '';
        $category = '';

        // Limit block
        $on_log_blocking_visitor = VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' );
        $on_block_ip_perm        = VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' );

        if ( isset( $_POST['visitorlog_locked_ip_insert'] ) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Utility::vl_redirect( 'visitorlog_lockedaddr' );
            exit;
        } 
        if ( isset( $_POST['visitorlog_select_type'] ) ) {

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $category = sanitize_text_field(wp_unslash($_POST['visitorlog_select_type']));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
        }    

        if ( isset( $_POST['visitorlog_locked_ip_insert_save'] ) ) {
        //----------------------------------------------------------- SUBMIT save    

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $err = self::submit_save( $block_check );

            if ( $err < -1 ) {
                $msg_err = __( 'Database error', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $msg_err );
            } else {
                $msg = __( 'Successful writing of data to the table', 'visitorlog' );
                VisitorLog_System_View::show_message( 'green', $msg );

                if ( 1 == $block_check ) {

                    if ( 'on' == $on_log_blocking_visitor  && 
                         'on' == $on_block_ip_perm ) {

                        $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess();

                        if ( $res ) {
                            $msg = __( 'Successful entry of the directives to the file .htaccess', 'visitorlog' );
                            VisitorLog_System_View::show_message( 'green', $msg );
                        } else {
                            $err = __( 'Enable permissions to write directives to the htaccess file', 'visitorlog' );
                            VisitorLog_System_View::show_message( 'orange', $err );
                        }   
                    } else {
                        $err = __( 'Enable permissions to write directives to the htaccess file', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err );
                    }
                } 
            }
        }    
        //------------- Display

        if ( 'perm' == $block ) {
            $on_off_block = 1;
        } else {
            $on_off_block = 0;
        }

        $timestamp = date_i18n( 'd M, Y H:i:s', $timestamp );
        $title1 = __('Create a new entry in the table', 'visitorlog');
        $title2 = __('Blocking of the administrator\'s IP is prohibited', 'visitorlog');

        $sel_0=$sel_1=''; 
        $sel_2_0=$sel_2_1=$sel_2_2='';

        switch ( $on_off_block ) {
            case 0:
                $sel_0 = 'selected';
                break;
            case 1:
                $sel_1 = 'selected';
                break;
        }
        switch ( $category ) {
            case '':
                $sel_2_0 = 'selected';
                break;
            case 'User':
                $sel_2_1 = 'selected';
                break;
            case 'Bot':
                $sel_2_2 = 'selected';
                break;
        }
        ?>  
        <form method="post" id="form_locked_ip_insert">
        <?php wp_nonce_field('visitorlog_nonce');?>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div>
                        <p style="font-size:14px;">
                            <?php echo esc_html($title1).'<br>'.esc_html($title2);?>
                        </p>
                    </div>
                    <div style="margin-top:5px;"></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead style="color:#4169E1; background-color:#ffffff;">
                                    <tr>
                                        <td width="120"><?php esc_html_e('Designation', 'visitorlog');?></td>
                                        <td><?php esc_html_e('Parameters', 'visitorlog');?></td>
                                        <td><?php esc_html_e('Description', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody style="background-color:#F2F2F2;">
                                    <tr>
                                        <td>
                                            <?php esc_html_e('Date of registration', 'visitorlog');?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($timestamp);?>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:blue;">IP</td>
                                        <td>
<textarea name="visitorlog_textarea_ip" style="width:300px;height:30px;" autofocus><?php echo esc_html($textarea_addr);?></textarea>&emsp;(ipv4, ipv6)
                                        </td>
                                        <td>
<?php esc_html_e('IP address, maximum of 50 characters', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="color:blue;">
                                            <?php esc_html_e('User agent', 'visitorlog');?>
                                        </td>
                                        <td>
<textarea name="visitorlog_textarea_username" style="width:400px;height:30px;"><?php echo esc_html($textarea_name);?></textarea> 
                                        </td>
                                        <td>
<?php esc_html_e('User agent, maximum of 150 characters', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
<?php esc_html_e('Blocking of IP address', 'visitorlog');?>
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                <select name="visitorlog_select_block_ip">
<option value="0" <?php echo esc_html($sel_0);?> ><?php esc_html_e('Off', 'visitorlog');?></option>
<option value="1" <?php echo esc_html($sel_1);?> ><?php esc_html_e('On', 'visitorlog');?></option>
                                </select>
                                            </label>
                                        </td>
                                        <td>
<?php esc_html_e('Enable it if you want to permanently block the address immediately using the instructions in the htaccess file', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?php esc_html_e('Category', 'visitorlog');?>
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                <select name="visitorlog_select_type">
<option value=""          <?php echo esc_html($sel_2_0);?>></option>
<option value="User"      <?php echo esc_html($sel_2_1);?>><?php esc_html_e('User', 'visitorlog');?></option>
<option value="BotDdosIP" <?php echo esc_html($sel_2_2);?>><?php esc_html_e('Bot', 'visitorlog');?></option>
                                </select>
                                            </label>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="margin-top:15px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide&addr=blacklist' );
?>
                            </div>
                            <div style="margin-top: -15px;"></div>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_locked_ip_insert" class="btn btn__secondery" value="<?php esc_html_e('Return', 'visitorlog');?>"/>
                                <div style="margin-right:10px;"></div>
<input type="submit" name="visitorlog_locked_ip_insert_save" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
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
     * Processing the save button click
     * 
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Security_Utility::checking_data_entry()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Logger::instance()->warning()
     */ 
    protected static function submit_save( &$block_check )
    {
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'lockdown_ip';
        $count = 0;
        $block = '';
        $block_check = '';
        $err = '';

        if ( isset( $_POST['visitorlog_select_block_ip'] ) ) {

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $on_off_block = sanitize_text_field(wp_unslash($_POST['visitorlog_select_block_ip']));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( 1 == $on_off_block ) $block = 'perm';
        } 
        if ( isset( $_POST['visitorlog_select_type'] ) ) {

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $category = sanitize_text_field(wp_unslash($_POST['visitorlog_select_type']));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
        } 
        if ( isset( $_POST['visitorlog_textarea_ip'] ) ) {

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $textarea_addr = trim(sanitize_text_field(wp_unslash($_POST['visitorlog_textarea_ip'])));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
        } 
        if ( isset( $_POST['visitorlog_textarea_username'] ) ) {

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $textarea_name = trim(sanitize_text_field(wp_unslash($_POST['visitorlog_textarea_username'])));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
        } 
        $err = VisitorLog_Security_Utility::checking_data_entry( $textarea_addr, $textarea_name, '', '' ); 
        if ( '' != $err ) return -1;

        $check = self::check_ip_duble( $textarea_addr, $textarea_name );
         if ( 1 == $check['addr'] ) {
            $msg = __( 'This IP address is already in the table', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $msg );
            return -1;
        }
        if ( 1 == $check['name'] ) {
            $msg = __( 'This name is already in the table', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $msg );
            return -1;
        }           
        if ( isset($_GET['reg']) && 'insert' == $_GET['reg'] ) {  
        //----------------------------------------------------- INSERT 

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            
            if ( 'perm' == $block ) $block_check = 1;
            
            $data = array(
                'timestamp'     => current_time( 'timestamp' ),
                'user_ip'       => $textarea_addr,
                'user_name'     => $textarea_name,
                'block'         => $block,
                'count'         => $count,
                'category'      => $category
            );
            $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err_msg = 'submit_save():Error insert data into table LOCKIP-' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err_msg);
                return -2;
            } 
        }
        return '';

    } // END func

    /*
     * Checking the ip duplicate
     */
    protected static function check_ip_duble( $addr, $name )
    {
        $check_ip   = 0;
        $check_name = 0;
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'lockdown_ip';

        if ( '' != $addr ) {
            $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_ip` = %s", $addr)); 
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err_msg = 'check_ip_duble():Error select data in table ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err_msg);
                $result['addr'] = -1;
                $result['name'] = -1;
                return $result; 
            } 

            if ( 0 != count($array) ) {
                $check_ip = 1;
            }
        }

        if ( '' != $name ) {
            $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_name` = %s", $name)); 
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err_msg = 'check_ip_duble():Error select data in table ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err_msg);
                $result['addr'] = -1;
                $result['name'] = -1;
                return $result; 
            } 

            if ( 0 != count($array) ) {
                $check_name = 1;
            }
        }
        $result['addr'] = $check_ip;
        $result['name'] = $check_name;
        return $result;

    } // END func 


} // END class