<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Backups_Report class
 * 
 * @method public static function render_backups_report()
 */
class VisitorLog_Backups_Report
{
    /**
     * Render backups report.
     *
     * @uses VisitorLog_Security_Handler::instance()->handler_delete_table_rows()
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_System_View::show_select_badge()
     */
    public static function render_backups_report()
    {
        $msg = '';

        if ( isset($_GET['reg']) && 'del' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            if ( isset($_GET['data']) ) {

                if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                    $data = str_replace( "\\", "", sanitize_text_field(wp_unslash($_GET['data'])) );
                } else {
                    $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                    VisitorLog_System_View::show_message( 'orange', $err, $err );
                    wp_die();
                }
                $data_parse = json_decode( $data ); 
                if ( empty( $data_parse ) ) {
                    $color = 'yellow';
                    $msg = esc_html__( 'There are no selected lines', 'visitorlog' );
                } else {
                    $res = VisitorLog_Security_Handler::delete_rows( 'backups', $data_parse, $is_block, $count_del );
                    if ( $res > 0 ) {
                       /* translators: 1: amount. */ 
                        $msg = sprintf( esc_html__('Rows in the amount of %d have been deleted from the table', 'visitorlog'), $count_del );
                        $color = 'green';
                    } elseif ( $res < 0 ) {
                        $msg = esc_html__('An error occurred while locking rows', 'visitorlog');
                        $color = 'yellow';
                    } 
                }
            } 
        } //--------------------------

        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET ['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            $clear_tbl = VisitorLog_Security_Handler::instance()->truncate_table( 'backups' );
            if ( $clear_tbl > 0 ) {
                $msg = esc_html__('Data has been deleted from the table', 'visitorlog');
                $color = 'blue';
            } 
        } //--------------------------

        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_backupreport' );
            exit;
        }
        //------------------------- Display
        $msg = VisitorLog_Utility::get_option_sl( 'temp_message' );
        if ( '' != $msg ) {
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }

        $result = VisitorLog_Utility::get_data_select( 'backups', 'viewall', $n_rows, $err_ );
        if ( $err_ ) VisitorLog_System_View::show_message( 'orange', $err_ );
        ?>  
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td>
<input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" value="on">
                                            &ensp;
                                            <?php esc_html_e('Check all','visitorlog');?>
                                            &emsp;&vellip;&emsp;
                                            <?php esc_html_e('With selected','visitorlog');?>:
                                            &emsp;
<?php
$msg  = __('Delete', 'visitorlog');
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
        <div style="margin-top: 10px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <thead class="vl-color-thead">
                                    <tr> 
                <td class="vl-thead" width="62"> <?php esc_html_e('Select','visitorlog');?></td>
                <td class="vl-thead" width="130"><?php esc_html_e('Date / Time','visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Initiator','visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Mode','visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Report file','visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Send email','visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tfoot class="vl-color-thead">
                                    <tr>
                <td class="vl-thead" width="62"> <?php esc_html_e('Select','visitorlog');?></td>
                <td class="vl-thead" width="130"><?php esc_html_e('Date / Time','visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Initiator','visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Mode','visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Report file','visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Send email','visitorlog');?></td>
                                    </tr>
                                </tfoot>
                                <tbody class="vl-color-tbody">
                                    <?php
                                    if ( !empty($result) ) {
                                        $i = 0;
                                        while ( $i<$n_rows ) {
                                            $id           = $result[$i]->id;
                                            $date_time    = $result[$i]->date_time;
                                            $initiator    = $result[$i]->initiator;
                                            $mode         = $result[$i]->mode;
                                            $success      = $result[$i]->success;
                                            $success_mail = $result[$i]->success_mail;
     
                                            $date_time = gmdate('Y/m/d H:i:s', $date_time);
                                            $i++;
                                    ?> 
                                    <tr style="font-size:13px;">
<td><input type="checkbox" name="visitorlog_checkbox_select" id="visitorlog_checkbox_select" value="<?php echo esc_html($id);?>"></td>
<td><?php echo esc_html($date_time);?></td>
<td><span <?php VisitorLog_System_View::show_badge( $initiator );?>>
    <?php echo esc_html($initiator);?>
    </span>
</td>
<td><span <?php VisitorLog_System_View::show_badge( $mode );?>>
    <?php echo esc_html($mode);?>
    </span>
</td>
<td><span <?php VisitorLog_System_View::show_badge( $success );?>>
    <?php echo esc_html($success);?>
    </span>
</td>
<td><span <?php VisitorLog_System_View::show_badge( $success_mail );?>>
    <?php echo esc_html($success_mail);?>
    </span>
</td>
                                    </tr>
                                    <?php 
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        VisitorLog_Utility::localize_script( 'visitorlog_backupreport' );

    } // END func  


} // END class