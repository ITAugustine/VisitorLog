<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Bots_Table Class 
 * 
 * @method public    static function render_bots_table()
 * @method public    static function render_table_insert_bots()
 * @method protected static function submit_save()
 * @method protected static function check_ip_duble()
 */
class VisitorLog_Bots_Table
{
    /**
     * Render table bots contents.
     *
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Security_Handler::instance()->truncate_table()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Handler::instance()->delete_rows()
     * @uses VisitorLog_Security_Handler::instance()->ban_unban_ip()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::get_data_select()
     */
    public static function render_bots_table()
    {
        $write_htaccess = 0;
        $msg = '';
        $color = '';

        $on_log_blocking_visitor = VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' );
        $on_block_ip_perm        = VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' );

        if ( isset($_GET['ins']) && 0 < $_GET ['ins'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            self::render_table_insert_bots(); 
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
        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $clear_tbl = VisitorLog_Security_Handler::instance()->truncate_table( 'bots_table' );
            if ( $clear_tbl > 0 ) {
                $msg = __('Data has been deleted from the table', 'visitorlog');
                $color = 'blue';
                $write_htaccess = 1;
            }  
        } //--------------------------
        if ( isset($_GET['reg']) && 'del' == $_GET ['reg'] && isset($_GET['data']) ) {

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
                $res = VisitorLog_Security_Handler::delete_rows( 'bots_table', $data_parse, $is_block, $count_del );
                if ( $res > 0 ) {
                    /* translators: 1: value. */
                    $msg = sprintf( __('Rows in the amount of %d have been deleted from the table', 'visitorlog'), $count_del );
                    $color = 'green';
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
                    $msg = __( 'An error occurred while locking rows', 'visitorlog' );
                    $color = 'blue';
                }
            }    
        } //---------------------------
        if ( isset($_GET['reg']) && 'ban' == $_GET ['reg'] && isset($_GET['data']) ) {

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
                $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'bots_table', $data_parse, 'ban', $select_block, $num_ban ); 
                if ( $res > 0 ) {
                    if ( $num_ban > 0 && 'perm' == $select_block ) {
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
        if ( isset($_GET['reg']) && 'unban' == $_GET ['reg'] && isset($_GET['data']) ) {

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
                $res = VisitorLog_Security_Handler::instance()->ban_unban_ip( 'bots_table', $data_parse, 'unban', $select_block, $num_unban ); 
                if ( $res > 0 ) {
                    if ( $num_unban > 0 && '' == $select_block ) {
                        /* translators: 1: value. */
                        $msg = sprintf( __( 'In the table has been unblocked %d line(s)', 'visitorlog' ), $num_unban );
                        $color = 'green';
                        $write_htaccess = 1;
                    }
                } else {
                    $msg = __( 'An error occurred while locking rows', 'visitorlog' );
                    $color = 'blue';                    
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
                $color = 'blue';
            }
        } //--------------------------
        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_botstable' );
            exit;
        }
        //------------- Display
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
        }
        $color_view1=$color_view2=$color_view3=$color_view4=$color_view5='';
        $style1 = 'color:green;font-weight:bold;'; 
        $err_ = '';       

        if ( isset($_GET['reg']) ) {
            if ( 'viewall' == $_GET ['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $result = VisitorLog_Utility::get_data_select( 'bots_table', 'viewall', $n_rows, $err_ );
                $color_view1 = $style1;
            } elseif ( 'viewban' == $_GET ['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $result = VisitorLog_Utility::get_data_select( 'bots_table', 'viewban', $n_rows, $err_ );
                $color_view3 = $style1;
            } elseif ( 'viewunban' == $_GET ['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $result = VisitorLog_Utility::get_data_select( 'bots_table', 'viewunban', $n_rows, $err_ );
                $color_view2 = $style1;
            } elseif ( 'notblock' == $_GET ['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $result = VisitorLog_Utility::get_data_select( 'bots_table', 'notblock', $n_rows, $err_ );
                $color_view4 = $style1;
            } elseif ( 'type' == $_GET ['reg'] ) {

                if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $result = VisitorLog_Utility::get_data_select( 'bots_table', 'type', $n_rows, $err_ );
                $color_view5 = $style1;
            } else {
                $result = VisitorLog_Utility::get_data_select( 'bots_table', 'viewall', $n_rows, $err_ );
                $color_view1 = $style1;
            }   
        } else {
            $result = VisitorLog_Utility::get_data_select( 'bots_table', 'viewall', $n_rows, $err_ ); 
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
<?php esc_html_e('The administrator can add a new IP address, delete, copy, edit, or permanently block one or a group of addresses','visitorlog');?>
.<br>2.&nbsp;
<?php esc_html_e('To permanently block the IP address in the htaccess file, the appropriate mode must be enabled in the registration and blocking management module','visitorlog');?>
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
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide&addr=bots_table' );
?>
                    </div>
                    <div style="margin-top:4px;"></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody style="background-color:#F2F2F2;">
                                    <tr>
                                        <td>
                                            <?php esc_html_e( 'Select view', 'visitorlog' );?>:
                                            &emsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_botstable')).'&reg=viewall';?>" title="<?php esc_html_e('All', 'visitorlog');?>" style="<?php echo esc_html($color_view1);?>">&nbsp;<?php esc_html_e('All', 'visitorlog');?>
</a> 
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_botstable')).'&reg=viewban';?>" title="<?php esc_html_e('Ban', 'visitorlog');?>" style="<?php echo esc_html($color_view3);?>">&nbsp;<?php esc_html_e('Ban', 'visitorlog');?>
</a>
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_botstable')).'&reg=viewunban';?>" title="<?php esc_html_e('Unban', 'visitorlog');?>" style="<?php echo esc_html($color_view2);?>">&nbsp;<?php esc_html_e('Unban', 'visitorlog');?>
</a> 
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_botstable')).'&reg=notblock';?>" title="<?php esc_html_e('Not block', 'visitorlog');?>" style="<?php echo esc_html($color_view4);?>">&nbsp;<?php esc_html_e('Not block', 'visitorlog');?>
</a> 
                                            &nbsp;&nbsp; 
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_botstable')).'&reg=type';?>" title="<?php esc_html_e('Type', 'visitorlog');?>" style="<?php echo esc_html($color_view5);?>">&nbsp;<?php esc_html_e('Type', 'visitorlog');?>
</a> 
                                            &nbsp; &vellip; &nbsp;
<?php
$title = __('View file .htaccess', 'visitorlog');
VisitorLog_Utility::redirect_html( 'code.png', $title, 'visitorlog_botstable', '&reg=htaccess' );
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
                                            &ensp;&vellip;&ensp;
<?php
$title = __('Row insert', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_insrow.png', $title, 'visitorlog_botstable', '&ins=1&reg=insert' );
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

        <div style="margin-top: 10px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
            <td class="vl-thead" width="165"><?php esc_html_e('Select/Edit/Copy','visitorlog');?></td>
            <td class="vl-thead">            <?php esc_html_e('Name', 'visitorlog');?></td>
            <td class="vl-thead">            <?php esc_html_e('Type', 'visitorlog');?></td>
            <td class="vl-thead" width="55"> <?php esc_html_e('IP Addr (ipv4, ipv6)', 'visitorlog');?></td>
            <td class="vl-thead">            <?php esc_html_e('Short Name', 'visitorlog');?></td>
            <td class="vl-thead">            <?php esc_html_e('Block', 'visitorlog');?></td> 
            <td class="vl-thead">            <?php esc_html_e('Comment','visitorlog');?></td> 
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
            <td class="vl-thead" width="165"><?php esc_html_e('Select/Edit/Copy','visitorlog');?></td>
            <td class="vl-thead">            <?php esc_html_e('Name', 'visitorlog');?></td>
            <td class="vl-thead">            <?php esc_html_e('Type', 'visitorlog');?></td>
            <td class="vl-thead" width="55"> <?php esc_html_e('IP Addr (ipv4, ipv6)', 'visitorlog');?></td>
            <td class="vl-thead">            <?php esc_html_e('Short Name', 'visitorlog');?></td>
            <td class="vl-thead">            <?php esc_html_e('Block', 'visitorlog');?></td> 
            <td class="vl-thead">            <?php esc_html_e('Comment','visitorlog');?></td> 
                                    </tr>
                                </tfoot>
                                <tbody>
        <?php
        $i = 0;
        while ( $i < $n_rows ) {
            $id         = $result[$i]->id;
            $type       = $result[$i]->type;
            $bot_name   = $result[$i]->bot_name;
            $bot_ip     = $result[$i]->bot_ip;
            $short_name = $result[$i]->short_name;
            $block      = $result[$i]->block;
            $comment    = $result[$i]->comment;
        ?>
                                    <tr style="font-size:13px;">
                                        <td>
<input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" value="<?php echo esc_html($id);?>">
                                            &nbsp; &vellip; &nbsp;
<?php
$url1 = 'visitorlog_botstable';
$url2 = '&ins=2&reg=editrow&row=' . esc_html($id);
$msg  = __('Edit and block row', 'visitorlog');
VisitorLog_Utility::edit_row_in_table( 'line_edit.png', $msg, $url1, $url2 );
?>
                                            &nbsp; &vellip; &nbsp;
<?php
$url1 = 'visitorlog_botstable';
$url2 = '&ins=3&reg=copyrow&row=' . esc_html($id);
$msg  = __('Copy row', 'visitorlog');
VisitorLog_Utility::edit_row_in_table( 'b_insrow.png', $msg, $url1, $url2 );
?>
                                        </td>
                                        <td><?php echo esc_html($bot_name);?></td>
                                        <td><?php echo esc_html($type);?></td>
                                        <td><?php echo esc_html($bot_ip);?></td>
                                        <td><?php echo esc_html($short_name);?></td>
                                        <td><?php echo esc_html($block);?></td>
                                        <td><?php echo esc_html($comment);?></td>
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
        VisitorLog_Utility::localize_script( 'visitorlog_botstable' );

    } // END func   


    /**
     * Render table insert bots.
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_row()
     * @uses VisitorLog_Utility::get_option_sl()
     */
    public static function render_table_insert_bots()
    {
        $textarea_name    = '';
        $textarea_addr    = '';
        $textarea_comment = '';
        $short_name = '';
        $bot_type = '';
        $block = '';
        $err = '';

        $on_log_blocking_visitor = VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' );
        $on_block_ip_perm        = VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' );

        if ( isset( $_POST['visitorlog_bot_insert_data'] ) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Utility::vl_redirect( 'visitorlog_botstable' );
        } 
        if ( isset( $_POST['visitorlog_bot_insert_data_save'] ) ) {
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
                    if ( 'on' == $on_log_blocking_visitor && 'on' == $on_block_ip_perm ) {
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
        //------------------------------------------------------------- COPY EDIT

        if ( isset($_GET['reg']) && ('copyrow' == $_GET['reg'] || 'editrow' == $_GET['reg']) ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            if ( isset($_GET['row']) ) {

                if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                    $getrow = sanitize_text_field(wp_unslash($_GET['row']));
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $res = VisitorLog_Utility::get_row( 'bots_table', $getrow, $n_rows, $err_ );
                if ( $err_ )  VisitorLog_System_View::show_message( 'orange', $err_, $err_ );

                $textarea_name    = $res[0]->bot_name;
                $bot_type         = $res[0]->type;
                $textarea_addr    = $res[0]->bot_ip;
                $short_name       = $res[0]->short_name;
                $block            = $res[0]->block;
                $textarea_comment = $res[0]->comment;
            }
        }

        //------------- Display

        $bot_type_disp = 0;
        if ( 'useful' == $bot_type ) $bot_type_disp = 1;

        $on_off_block = 0;
        if ( 'perm'     == $block ) $on_off_block = 1;
        if ( 'notblock' == $block ) $on_off_block = 2;
   
        $title = __('Enter the bot\'s data in the table', 'visitorlog');
        if ( isset($_GET['reg']) && 'editrow' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $title = __('Edit the bot\'s data in the table', 'visitorlog');
        }
        if ( isset($_GET['reg']) && 'copyrow' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $title = __('Copy and edit the bot\'s data in the table', 'visitorlog');
        }

        $sel_0=$sel_1=$sel_2='';
        $bot_type_0=$bot_type_1='';

        switch ( $on_off_block ) {
            case 0:
                $sel_0 = 'selected';
                break;
            case 1:
                $sel_1 = 'selected';
                break;
            case 2:
                $sel_2 = 'selected';
                break;
            default:
                $sel_0 = 'selected';
                break;                
        }
        switch ( $bot_type_disp ) {
            case 0:
                $bot_type_0 = 'selected';
                break;
            case 1:
                $bot_type_1 = 'selected';
                break;
            default:
                $bot_type_0 = 'selected';
                break;   
        }
        ?>  
        <form method="post" id="form_bots_insert">
        <?php wp_nonce_field('visitorlog_nonce');?>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="">
                    <div class="header">
                        <p style="font-size:14px;">
                            <?php echo esc_html($title);?>
                        </p>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <thead style="color:#4169E1; background-color:#ffffff;">
                                    <tr>
                                        <td width="120"><?php esc_html_e('Designation', 'visitorlog');?></td>
                                        <td><?php esc_html_e('Parameters', 'visitorlog');?></td>
                                        <td><?php esc_html_e('Description', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody style="background-color:#F2F2F2;">
                                    <tr>
                                        <td style="color:blue;">IP</td>
                                        <td>
<textarea name="visitorlog_textarea_addr" style="width:300px;height:30px;"><?php echo esc_html($textarea_addr);?></textarea>&emsp;(ipv4, ipv6)
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
<textarea name="visitorlog_textarea_name" style="width:400px;height:30px;" autofocus><?php echo esc_html($textarea_name);?></textarea> 
                                        </td>
                                        <td>
<?php esc_html_e('A set of characters for identifying the bot in the user agent string. Maximum of 150 characters', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
<?php esc_html_e('Bot Type', 'visitorlog');?>
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                                <select name="visitorlog_bot_type">
<option value="0" <?php echo esc_html($bot_type_0);?> ></option>
<option value="1" <?php echo esc_html($bot_type_1);?> ><?php esc_html_e('Useful', 'visitorlog');?></option>
                                                </select>
                                            </label>
                                        </td>
                                        <td>
<?php esc_html_e('If the name belongs to a search bot that indexes your site\'s pages for display in the browser, set the value to "Useful" and "Do not block" in the next field', 'visitorlog');?>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
<?php esc_html_e('IP address blocking mode', 'visitorlog');?>
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                                <select name="visitorlog_mode_block">
<option value="0" <?php echo esc_html($sel_0);?> ><?php esc_html_e('Is no blocking', 'visitorlog');?></option>
<option value="1" <?php echo esc_html($sel_1);?> ><?php esc_html_e('Block', 'visitorlog');?></option>
<option value="2" <?php echo esc_html($sel_2);?> ><?php esc_html_e('Do not block', 'visitorlog');?></option>
                                                </select>
                                            </label>
                                        </td>
                                        <td>
                                            &#8226;
<?php esc_html_e('The Not Blocked mode is set by default, but the plugin algorithm can automatically change the lock if it detects an attack from this address', 'visitorlog');?>.
                                            <br>&#8226;
<?php esc_html_e('Turn on Block mode if you want to block the address permanently immediately using the instructions in the htaccess file', 'visitorlog');?>.
                                            <br>&#8226;
<?php esc_html_e('Enable the "Do not block" mode, then the plugin will not be able to automatically block the IP address in the htaccess file', 'visitorlog');?>.&nbsp;
<?php esc_html_e('This is necessary to avoid blocking search bots such as GoogleBot', 'visitorlog');?>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php esc_html_e('Short Name', 'visitorlog');?></td>
                                        <td>
<textarea name="visitorlog_short_name" style="width:200px;height:30px;"><?php echo esc_html($short_name);?></textarea>
                                        </td>
                                        <td>
<?php esc_html_e('The short name of the bot to display on the screen in tables. Maximum of 10 characters', 'visitorlog');?> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?php esc_html_e('Comment', 'visitorlog');?></td>
                                        <td>
<textarea name="visitorlog_textarea_comment" style="width:400px;height:30px;"><?php echo esc_html($textarea_comment);?></textarea>
                                        </td>
                                        <td><?php esc_html_e('150 characters', 'visitorlog');?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="margin-top: 15px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide&addr=bots_table' );
?>
                            </div>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end;">
                            <div style="margin-right:30px;"></div>
<input type="submit" name="visitorlog_bot_insert_data" class="btn btn__secondary" value="<?php esc_html_e('Return', 'visitorlog');?>"/>
                            <div style="margin-right:30px;"></div>
<input type="submit" name="visitorlog_bot_insert_data_save" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
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
     * @uses VisitorLog_Logger::instance()->debug()
     * @uses VisitorLog_Utility::get_row()
     * @uses VisitorLog_System_View::show_message()
     */ 
    protected static function submit_save( &$block_check )
    {
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'bots_table';
        $bot_type = '';
        $block = '';
        $block_check = '';
        $err = '';

        if ( isset( $_POST['visitorlog_bot_type'] ) ) { 

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $type = sanitize_text_field(wp_unslash($_POST['visitorlog_bot_type'])); 
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( 1 == $type ) $bot_type = 'useful';
        } 
        if ( isset( $_POST['visitorlog_mode_block'] ) ) { 

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $on_off_block = sanitize_text_field(wp_unslash($_POST['visitorlog_mode_block'])); 
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            if ( 1 == $on_off_block ) $block = 'perm';
            if ( 2 == $on_off_block ) $block = 'notblock';
        } 
        if ( isset( $_POST['visitorlog_textarea_addr'] ) ) { 

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $textarea_addr = trim(sanitize_text_field(wp_unslash($_POST['visitorlog_textarea_addr'])));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
        } 
        if ( isset( $_POST['visitorlog_textarea_name'] ) ) {

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $textarea_name = trim(sanitize_text_field(wp_unslash($_POST['visitorlog_textarea_name'])));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
        } 
        if ( isset( $_POST['visitorlog_short_name'] ) ) {

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $short_name = trim(sanitize_text_field(wp_unslash($_POST['visitorlog_short_name'])));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
        } 
        if ( isset( $_POST['visitorlog_textarea_comment'] ) ) {

            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                $textarea_comment = trim(sanitize_text_field(wp_unslash($_POST['visitorlog_textarea_comment'])));
            } else {    
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
        } 

        if ( isset($_GET['reg']) && 'editrow' != $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $err = VisitorLog_Security_Utility::checking_data_entry( $textarea_addr, $textarea_name, $short_name, $textarea_comment );
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
                'bot_name'   => $textarea_name,
                'type'       => $bot_type,
                'bot_ip'     => $textarea_addr,
                'short_name' => $short_name,
                'block'      => $block,
                'comment'    => $textarea_comment 
            );
            $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'submit_save()-Error insert into table ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->debug( $err );
                return -2;
            }
        }  
        if ( isset($_GET['reg']) && 'copyrow' == $_GET['reg'] ) {        
        //----------------------------------------------------- COPY 

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            if ( isset($_GET['row']) ) {

                if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                    $getrow = sanitize_text_field(wp_unslash($_GET['row']));
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $res = VisitorLog_Utility::get_row( 'bots_table', $getrow, $n_rows, $err_ );
                if ( $err_ ) { 
                    VisitorLog_System_View::show_message( 'orange', $err_, $err_ );
                    return -3;
                }    
                if ( !('' == $block && '' == $res[0]->block) ) {
                    $block_check = 1;
                }
                $data = array(
                    'bot_name'   => $textarea_name,
                    'type'       => $bot_type,
                    'bot_ip'     => $textarea_addr,
                    'short_name' => $short_name,
                    'block'      => $block,
                    'comment'    => $textarea_comment,
                );
                $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
                if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                    $err = 'submit_save()-Error insert into table-' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                    VisitorLog_Logger::instance()->debug( $err );
                    return -4;
                }
            }  
        }

        if ( isset($_GET['reg']) && 'editrow' == $_GET['reg'] ) {        
        //----------------------------------------------------- EDIT 

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            
            if ( isset($_GET['row']) ) {

                if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                    $getrow = sanitize_text_field(wp_unslash($_GET['row']));
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $res = VisitorLog_Utility::get_row( 'bots_table', $getrow, $n_rows, $err_ );
                if ( $err_ )  {
                    VisitorLog_Logger::instance()->debug($err_);
                    return -5;
                }
                if ( !('' == $block && '' == $res[0]->block) ) {
                    $block_check = 1;
                }
                $data = array(
                    'bot_name'   => $textarea_name,
                    'type'       => $bot_type,
                    'bot_ip'     => $textarea_addr,
                    'short_name' => $short_name,
                    'block'      => $block,
                    'comment'    => $textarea_comment,
                );
                $result = VisitorLog_DB_Base::$wpdb_vl->update( $table_name, $data, ['id' => $getrow] );
                if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                    $err = 'submit_save()-Error update into table-' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                    VisitorLog_Logger::instance()->debug( $err );
                    return -6;
                }
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
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'bots_table';

        if ( '' != $addr ) {
            $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `bot_ip` = %s", $addr));
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
            $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `bot_name` = %s", $name));
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