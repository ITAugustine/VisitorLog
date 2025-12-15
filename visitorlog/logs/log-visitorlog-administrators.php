<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Administrators class
 * 
 * @method public static function render_administrators()
 */
class VisitorLog_Administrators
{
    /**
     * Render account activity log.
     *
     * @uses VisitorLog_Security_Handler::instance()->handler_delete_table_rows()
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_System_View::show_message()
     */
    public static function render_administrators()
    {
        $msg = '';

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
                $res = VisitorLog_Security_Handler::delete_rows( 'admin_ip', $data_parse, $is_block, $count_del );
                if ( $res > 0 ) {
                    /* translators: 1: quantity. */
                    $msg = sprintf( __('Rows in the amount of %d have been deleted from the table', 'visitorlog'), $count_del );
                    $color = 'green';
                } elseif ( $res < 0 ) {
                    $msg = __('An error occurred while locking rows', 'visitorlog');
                    $color = 'yellow';
                } 
            } 
        } //--------------------------
        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $clear_tbl = VisitorLog_Security_Handler::instance()->truncate_table( 'admin_ip' );
            if ( $clear_tbl > 0 ) {
                $msg = __('Data has been deleted from the table', 'visitorlog');
                $color = 'blue';
            }  
        }
        //------------------------- Message
        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_administrators' );
            exit;
        }
        //------------------------- Display
        $msg = VisitorLog_Utility::get_option_sl( 'temp_message' );
        if ( '' != $msg ) {
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }

        $result = VisitorLog_Utility::get_data_select( 'admin_ip', 'viewall', $n_rows, $err_ );
        if ( $err_ ) VisitorLog_System_View::show_message( 'orange', $err_ );
        ?>  
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody style="background-color:#F2F2F2;">
                                    <tr>
                                        <td>
<input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" value="on">&ensp;
                                            <?php esc_html_e('Check all','visitorlog');?>
                                            &nbsp; &vellip; &nbsp;
                                            <?php esc_html_e('With selected','visitorlog');?>:&ensp;
<?php
$msg = __('Delete', 'visitorlog');
VisitorLog_Utility::link_in_table( 'visitorlog_delete_select', $msg, 'b_drop.png' );
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
                        <td class="vl-thead" width="60"> <?php esc_html_e('Select','visitorlog');?></td>
                        <td class="vl-thead" width="45">#</td>
                        <td class="vl-thead"><?php esc_html_e('Date of the first login', 'visitorlog');?></td>
                        <td class="vl-thead"><?php esc_html_e('Administrators','visitorlog');?></td>
                        <td class="vl-thead"><?php esc_html_e('IP address', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                        <td class="vl-thead" width="60"> <?php esc_html_e('Select','visitorlog');?></td>
                        <td class="vl-thead" width="45">#</td>
                        <td class="vl-thead"><?php esc_html_e('Date of the first login', 'visitorlog');?></td>
                        <td class="vl-thead"><?php esc_html_e('Administrators','visitorlog');?></td>
                        <td class="vl-thead"><?php esc_html_e('IP address', 'visitorlog');?></td>
                                    </tr>
                                </tfoot>
                                <tbody>
        <?php
        $admin = __('Administrators','visitorlog');
        $i = 0;
        while ( $i < $n_rows ) {
            $id   = $result[$i]->id;
            $col1 = $result[$i]->date_time;
            $col2 = $admin . '-' . $id;
            $col3 = $result[$i]->user_ip;
        ?>
        <tr style="font-size:13px;">
            <td>
            <input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" value="<?php echo esc_html($id);?>">
            </td>
            <td><?php echo esc_html($i+1);?></td>   
            <td><?php echo esc_html($col1);?></td>                                     
            <td><?php echo esc_html($col2);?></td>
            <td><?php echo esc_html($col3);?></td>
        </tr>
        <?php
            $i++;
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
        VisitorLog_Utility::localize_script( 'visitorlog_administrators' );

    } // END func

} // END class