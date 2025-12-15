<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Action_Log class.
 *
 * @method public    static function get_class_name() 
 * @method public    static function action_log_view() 
 */
class VisitorLog_Action_Log
{
	public static function get_class_name()
    {
		return __CLASS__;
	}

	/**
	 * Viewing activity logs.
     * 
     * @uses VisitorLog_Security_Handler::instance()->truncate_table() 
     * @uses VisitorLog_System_View::show_message() 
     * @uses VisitorLog_Utility::get_option_sl() 
     * @uses VisitorLog_Utility::update_option_sl() 
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_System_View::show_select_badge()      
	 */
	public static function action_log_view()
    {
        $msg = '';

        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            $clear_tbl = VisitorLog_Security_Handler::instance()->truncate_table('action_log');
            if ( $clear_tbl > 0 ) {
                $msg = __('Data has been deleted from the table', 'visitorlog');
                $color = 'green';
            }  
        }
        //------------------------- Message
        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_actionlog' );
            exit;
        }
        //------------------------- Display
        $msg = VisitorLog_Utility::get_option_sl( 'temp_message' );
        if ( '' != $msg ) {
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }

        $result = VisitorLog_Utility::get_data_select( 'action_log', 'viewall', $n_rows, $err_ );
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
                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
                            <td class="vl-thead" width="15">#</td>
                            <td class="vl-thead" width="150"><?php esc_html_e('Date / Time','visitorlog');?></td>
                            <td class="vl-thead" width="50"> <?php esc_html_e('User','visitorlog');?></td>
                            <td class="vl-thead">            <?php esc_html_e('Text','visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                            <td class="vl-thead" width="15">#</td>
                            <td class="vl-thead" width="150"><?php esc_html_e('Date / Time','visitorlog');?></td>
                            <td class="vl-thead" width="50"> <?php esc_html_e('User','visitorlog');?></td>
                            <td class="vl-thead">            <?php esc_html_e('Text','visitorlog');?></td>
                                    </tr>
                                </tfoot>
                                <tbody>
                                <?php
                                $i = 0; $row = 0;
                                while ( $i < $n_rows ) {
                                    $id        = $result[$i]->id;
                                    $date_time = $result[$i]->log_timestamp;
                                    $log_user  = $result[$i]->log_user;
                                    $log_text  = $result[$i]->log_content;
                        			$date_time = date_i18n('d M, Y H:i:s',$date_time);
                                ?> 
                                    <tr style="font-size:13px;">
            <td><?php echo esc_html($i+1);?></td>
            <td><?php echo esc_html($date_time);?></td>
            <td>
                <span <?php VisitorLog_System_View::show_badge($log_user);?>>
                <?php echo esc_html($log_user);?>
                </span>
            </td> 
            <td><?php echo esc_html($log_text);?></td>
                                    </tr>
                                <?php
                                    $i++;
                                } //--------- end while
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        VisitorLog_Utility::localize_script( 'visitorlog_actionlog' );

	} // END func

} // END class