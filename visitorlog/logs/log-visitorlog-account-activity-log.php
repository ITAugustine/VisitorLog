<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Account_Activity_Log class
 * 
 * @method public static function render_account_activity_log()
 */
class VisitorLog_Account_Activity_Log
{
    /**
     * Render account activity log.
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_data_select()
     */
    public static function render_account_activity_log()
    {
        $msg = '';

        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            $clear_tbl = VisitorLog_Security_Handler::instance()->truncate_table('login_activity');
            if ( $clear_tbl > 0 ) {
                $msg = esc_html__('Data has been deleted from the table', 'visitorlog');
                $color = 'green';
            }  
        }
        //------------------------- Message
        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_accountactivity' );
            exit;
        }
        //------------------------- Display
        $msg = VisitorLog_Utility::get_option_sl( 'temp_message' );
        if ( '' != $msg ) {
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }

        $result = VisitorLog_Utility::get_data_select( 'login_activity', 'viewall', $n_rows, $err_ );
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
<?php
$msg = __('Clear the table', 'visitorlog');
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
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
    <td class="vl-thead">#</td>  
    <td class="vl-thead" width="160"><?php esc_html_e('Login Date', 'visitorlog');?></td>
    <td class="vl-thead" width="160"><?php esc_html_e('Logout Date','visitorlog');?></td>
    <td class="vl-thead">            <?php esc_html_e('Duration of sessions', 'visitorlog');?></td>
    <td class="vl-thead">            <?php esc_html_e('Name', 'visitorlog');?></td>
    <td class="vl-thead">ID</td>
    <td class="vl-thead">IP</td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
    <td class="vl-thead">#</td>  
    <td class="vl-thead" width="160"><?php esc_html_e('Login Date', 'visitorlog');?></td>
    <td class="vl-thead" width="160"><?php esc_html_e('Logout Date','visitorlog');?></td>
    <td class="vl-thead">            <?php esc_html_e('Duration of sessions', 'visitorlog');?></td>
    <td class="vl-thead">            <?php esc_html_e('Name', 'visitorlog');?></td>
    <td class="vl-thead">ID</td>
    <td class="vl-thead">IP</td>
                                    </tr>
                                </tfoot>
                                <tbody>
        <?php
        $i = 0;
        while ( $i < $n_rows ) {
            $col1 = $result[$i]->login_date;
            $col2 = $result[$i]->logout_date;
            $col3 = $result[$i]->user_login;
            $col4 = $result[$i]->user_id;
            $col5 = $result[$i]->user_ip;

            if ( 0 == $col2 ) {
                $session_time = '';
                $col2 = '';
            } else {
                $time_diff    = $col1 - $col2;
                $session_time = human_time_diff( 0, $time_diff );
                $col2 = date_i18n( 'Y-m-d H:i:s', $col2 );
            }
            $col1 = date_i18n( 'Y-m-d H:i:s', $col1 );

        ?>
        <tr style="font-size:13px;">
            <td><?php echo esc_html($i+1);?></td>   
            <td><?php echo esc_html($col1);?></td>                                     
            <td><?php echo esc_html($col2);?></td>
            <td><?php echo esc_html($session_time);?></td>
            <td><?php echo esc_html($col3);?></td>
            <td><?php echo esc_html($col4);?></td>
            <td><?php echo esc_html($col5);?></td>
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
        VisitorLog_Utility::localize_script( 'visitorlog_accountactivity' );

    } // END func

} // END class