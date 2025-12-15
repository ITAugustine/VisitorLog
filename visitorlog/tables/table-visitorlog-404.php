<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_404 class
 * 
 * @method public static function render_404()
 */
class VisitorLog_404
{
    /**
     * Render Spam.
     *
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Security_Handler::instance()->truncate_table()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Handler::instance()->delete_rows()
     * @uses VisitorLog_Security_Handler::instance()->ban_unban_ip()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_System_View::show_select_badge()
     */
    public static function render_404()
    { 
        $write_htaccess = 0;
        $msg = '';
        $color = '';

        $on_log_blocking_visitor = VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' );
        $on_block_ip_perm        = VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' );
        $on_404_logging          = VisitorLog_Utility::get_option_sl( 'on_404_logging' );

        if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Security_Handler::instance()->handler_htaccess_view();
            exit;
        }    
        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET['reg'] ) {


            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }            
            $del_spam = VisitorLog_Security_Handler::instance()->truncate_table( 'event_404' );
            if ( $del_spam > 0 ) {
                $msg = __('Data has been deleted from the table', 'visitorlog');
                $color = 'blue';
                $write_htaccess = 1;
            }    
        } //----------------------------
        if ( isset($_GET['reg']) && 'del' == $_GET['reg'] && isset($_GET['data']) ) { 

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
                $res = VisitorLog_Security_Handler::delete_rows( 'event_404', $data_parse, $is_block, $count_del );
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
        } //---------------------------- 
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
                $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'event_404', $data_parse, 'ban', $select_block, $num_ban );
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
        } //----------------------------
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
                $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'event_404', $data_parse, 'unban', $select_block, $num_unban );
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
        } //----------------------------
        if ( 1 == $write_htaccess ) { 

            if ( 'on' == $on_log_blocking_visitor  && 
                 'on' == $on_block_ip_perm         && 
                 'on' == $on_404_logging ) {

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
        //------------------------- Message
        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_event404' );
            exit;
        }
        //------------------------- Display
        
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
        } elseif( 'on' == $on_log_blocking_visitor && 'on' == $on_block_ip_perm && 'on' == $on_404_logging ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg = __( 'Activate registration and blocking of visitors on the control panel and enable event recognition mode 404', 'visitorlog' );
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
            $result = VisitorLog_Utility::get_data_select( 'event_404', 'viewall', $n_rows, $err_ );
            $color_view1 = $style1;
        } elseif ( isset($_GET['reg']) && 'viewban' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_Utility::get_data_select( 'event_404', 'viewban', $n_rows, $err_ );
            $color_view3 = $style1;
        } elseif ( isset($_GET['reg']) && 'viewunban' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_Utility::get_data_select( 'event_404', 'viewunban', $n_rows, $err_ );
            $color_view2 = $style1;
        } else {
            $result = VisitorLog_Utility::get_data_select( 'event_404', 'viewall', $n_rows, $err_ ); 
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
<?php esc_html_e('The data in the table is generated automatically if a visitor or bot tries to go to a non-existent page of the site','visitorlog');?>
.<br>2.&nbsp;
<?php esc_html_e('At the first stage, the IP address is temporarily blocked. If the number of temporary locks exceeds the set limit, a permanent lock occurs in the Htaccess file','visitorlog');?>
.<br>3.&nbsp;
<?php esc_html_e('If desired, the administrator can delete an entry from the table, permanently block one address or a group of IP addresses in the htaccess file','visitorlog');?>
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
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide&addr=event404' );
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
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_blockvisitor'));?>" title="<?php esc_html_e('This function is disabled', 'visitorlog');?>!">
    <?php VisitorLog_Utility::render_picture('remove.png');?>&nbsp;
    <?php esc_html_e('This function is disabled','visitorlog');?>!
</a>
                                            &nbsp; &vellip; &nbsp;
<?php
}
?>
                                            <?php esc_html_e( 'Select view', 'visitorlog' );?>:
                                            &emsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_event404')).'&reg=viewall';?>" title="<?php esc_html_e('All', 'visitorlog');?>" style="<?php echo esc_html($color_view1);?>">&nbsp;<?php esc_html_e('All', 'visitorlog');?>
</a> 
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_event404')).'&reg=viewban';?>" title="<?php esc_html_e('Ban', 'visitorlog');?>" style="<?php echo esc_html($color_view3);?>">&nbsp;<?php esc_html_e('Ban', 'visitorlog');?>
</a>
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php')).'?page=visitorlog_event404&reg=viewunban';?>" title="<?php esc_html_e('Unban', 'visitorlog');?>" style="<?php echo esc_html($color_view2);?>">&nbsp;<?php esc_html_e('Unban', 'visitorlog');?>
</a> 
                                            &nbsp; &vellip; &nbsp;
<?php
$title = __('View file .htaccess', 'visitorlog');
VisitorLog_Utility::redirect_html( 'code.png', $title, 'visitorlog_event404', '&reg=htaccess' );
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
            <td class="vl-thead" width="60"><?php esc_html_e('Select', 'visitorlog');?></td>
            <td class="vl-thead" width="150"><?php esc_html_e('Date Write/Release', 'visitorlog');?></td>
            <td class="vl-thead">IP</td>
            <td class="vl-thead" width="55"><?php esc_html_e('Count', 'visitorlog');?></td>
            <td class="vl-thead" width="45"><?php esc_html_e('Block', 'visitorlog');?></td>
            <td class="vl-thead" width="45"><?php esc_html_e('Canal', 'visitorlog');?></td>
            <td class="vl-thead" width="55"><?php esc_html_e('Category', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('User agent', 'visitorlog');?></td>
            <td class="vl-thead">URL</td>
            <td class="vl-thead"><?php esc_html_e('Refinfo', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
            <td class="vl-thead" width="60"><?php esc_html_e('Select', 'visitorlog');?></td>
            <td class="vl-thead" width="150"><?php esc_html_e('Date Write/Release', 'visitorlog');?></td>
            <td class="vl-thead">IP</td>
            <td class="vl-thead" width="55"><?php esc_html_e('Count', 'visitorlog');?></td>
            <td class="vl-thead" width="45"><?php esc_html_e('Block', 'visitorlog');?></td>
            <td class="vl-thead" width="45"><?php esc_html_e('Canal', 'visitorlog');?></td>
            <td class="vl-thead" width="55"><?php esc_html_e('Category', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('User agent', 'visitorlog');?></td>
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
            $col1_1 = $result[$i]->release_tstmp;
            $col3   = $result[$i]->user_ip;
            $col4   = $result[$i]->count;
            $col5   = $result[$i]->block;
            $col6   = $result[$i]->canal;
            $col7   = $result[$i]->category;
            $col8   = $result[$i]->user_agent;
            $col9   = $result[$i]->url;
            $col10  = $result[$i]->refinfo;

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
            <td><?php echo esc_html($col3);?></td>
            <td><?php echo esc_html($col4);?></td>
            <td><?php echo esc_html($col5);?></td>
            <td><?php echo esc_html($col6);?></td>
            <td>
                <span <?php VisitorLog_System_View::show_badge($col7);?>>
                    <?php echo esc_html($col7);?>
                </span>
            </td>
            <td>
                <?php
                if ( strlen($col8) > 20 ) {
                    $col81 = substr($col8, 0, 19) . ' ...';
                    echo '<a href="#" title="'.esc_html($col8).'">'.esc_html($col81).'</a>';
                } else {
                    echo esc_html($col8);
                }  
                ?>
            </td> 
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
        VisitorLog_Utility::localize_script( 'visitorlog_event404' );

    } // END func  


} // END class