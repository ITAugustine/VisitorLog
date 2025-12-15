<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Visitor_Log class
 * 
 * @method public    static function render_visitor_log()
 * @method protected static function create_visitorlog_report_file()
 * @method protected static function delete_report_files()
 * @method protected static function create_html_content()
 */
class VisitorLog_Visitor_Log
{
    /**
     * Render Visitor Log.
     *
     * @uses VisitorLog_Security_Handler::instance()->truncate_table()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_System_View::show_select_badge()
     */
    public static function render_visitor_log()
    {
        $msg = '';
        $color = '';

        if ( isset($_GET['reg']) && 'clear_tbl' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $del_spam = VisitorLog_Security_Handler::instance()->truncate_table( 'users' );
            if ( $del_spam > 0 ) {
                $msg = __('Data has been deleted from the table', 'visitorlog');
                $color = 'green';
            }    
        } //--------------------------
        if ( isset($_GET['reg']) && 'send_email' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = self::create_visitorlog_report_file();
            if ( $result[0] == 1 ) {
                $msg = __('The message with the report file has been sent successfully', 'visitorlog');
                $color = 'green';
            } 
            elseif ( $result[0] == -1 ) {
                $msg = __('Error create dir', 'visitorlog');
                $color = 'blue';
            }    
            elseif ( $result[0] == -2 ) {
                $msg = __('Error create file', 'visitorlog');
                $color = 'blue';
            }  
            elseif ( $result[0] == -3 ) {
                $msg = __('An error occurred when sending mail. Check the error log', 'visitorlog');
                $color = 'blue';
            } 
        } 
        //--------------------- Message
        if ( '' != $msg ) {
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_visitorlog' );
            exit;
        }
        //--------------------- Display
        if ( '' != VisitorLog_Utility::get_option_sl( 'temp_message' ) ) {
            $msg = VisitorLog_Utility::get_option_sl( 'temp_message' );
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
        }
        if ( '' != $msg && '' != $color ) {
            VisitorLog_System_View::show_message( $color, $msg );
        }

        $result = VisitorLog_Utility::get_data_select( 'users', 'viewall', $n_rows, $err_ );
        if ( $err_ ) VisitorLog_System_View::show_message( 'orange', $err_ );
        ?>  
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <p style="font-size:14px;">
<?php esc_html_e('All site visitors and their parameters are recorded in the log', 'visitorlog');?>.<br>
<?php esc_html_e('The table contains a list of the user\'s sessions and the number of pages he visited on the site','visitorlog');?>.<br>
                        </p>
                    </div>
                    <div style="margin-top:-20px;font-size:12px;display:flex;flex-direction:row;justify-content: flex-end;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=visitor_log' );
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
$msg  = __('Clear the table', 'visitorlog');
VisitorLog_Utility::link_in_table( 'visitorlog_delete_all', $msg, 'deltbl.png' );
?>
                                            &nbsp; &vellip; &nbsp;
<?php
$title = __('Send report by email', 'visitorlog');
VisitorLog_Utility::redirect_html( 'email.png', $title, 'visitorlog_visitorlog', '&reg=send_email' );
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
            <td class="vl-thead">#</td>       
            <td class="vl-thead" width="150"><?php esc_html_e('Date Write/Release', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('UserIP', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Visitor', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Canal', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Pages','visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Session','visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('AllPages','visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('User agent','visitorlog');?></td>
            <td class="vl-thead">URL</td>
            <td class="vl-thead">Refer</td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
            <td class="vl-thead">#</td>       
            <td class="vl-thead" width="150"><?php esc_html_e('Date Write/Release', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('UserIP', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Visitor', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Canal', 'visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Pages','visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('Session','visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('AllPages','visitorlog');?></td>
            <td class="vl-thead"><?php esc_html_e('User agent','visitorlog');?></td>
            <td class="vl-thead">URL</td>
            <td class="vl-thead">Refer</td>
                                    </tr>
                                </tfoot>
                                <tbody>
        <?php
        $i = 0;
        while ( $i < $n_rows ) {
            $col1  = date_i18n( 'd M, Y H:i:s', $result[$i]->timestamp );
            if ( 0 == $result[$i]->timerelease ) {
                $col2 = '';
            } else {
                $col2 = date_i18n( 'd M, Y H:i:s', $result[$i]->timerelease );
            }
            $col3  = $result[$i]->user_ip;
            $col4  = $result[$i]->category;
            $col5  = $result[$i]->canal;
            $col6  = $result[$i]->pages;
            $col7  = $result[$i]->session;
            $col8  = $result[$i]->allpages;
            $col9  = $result[$i]->user_agent;
            $col10 = $result[$i]->url;
            $col11 = $result[$i]->referer_info;
        ?>
        <tr style="font-size:13px;">
            <td><?php echo esc_html($i+1);?></td>   
            <td><?php echo esc_html($col1);?>
                <?php if ('' != $col2) {?><br><?php echo esc_html($col2);}?>
            </td>                                     
            <td><?php echo esc_html($col3);?></td>
            <td>
                <span <?php VisitorLog_System_View::show_badge($col4);?>>
                    <?php echo esc_html($col4);?>
                </span>
            </td>
            <td><?php echo esc_html($col5);?></td>
            <td><?php echo esc_html($col6);?></td>
            <td><?php echo esc_html($col7);?></td>
            <td><?php echo esc_html($col8);?></td>
            <td>
                <?php
                if ( strlen($col9) > 20 ) {
                    echo '<a href="#" title="';
                    echo esc_html($col9);
                    echo '">';
                    echo esc_html(substr($col9, 0, 19));
                    echo '</a>';
                } else {
                    echo esc_html($col9);
                }
                ?>                    
            </td>
            <td>
                <?php
                if ( strlen($col10) > 20 ) {
                    echo '<a href="#" title="';
                    echo esc_html($col10);
                    echo '">';
                    echo esc_html(substr($col10, 0, 19));
                    echo '</a>';
                } else {
                    echo esc_html($col10);
                }
                ?>    
            </td>
            <td>
                <?php
                if ( strlen($col11) > 20 ) {
                    echo '<a href="#" title="';
                    echo esc_html($col11);
                    echo '">';
                    echo esc_html(substr($col11, 0, 19));
                    echo '</a>';
                } else {
                    echo esc_html($col11);
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
        VisitorLog_Utility::localize_script( 'visitorlog_visitorlog' );

    } // END func 

    /**
     * Create a event 404 report file
     *
     * @uses VisitorLog_Utility_File::create_dir()
     * @uses VisitorLog_Logger::instance()->warning()
     * @uses VisitorLog_Utility::generate_alpha_numeric_random_string()
     * @uses VisitorLog_Utility::get_data_select()
     * @uses VisitorLog_System_View::show_message()
     */    
    protected static function create_visitorlog_report_file()
    { 
        global $visitorlog_users_resident_parameters;

        $timestamp = $visitorlog_users_resident_parameters['timestamp'];

        $mail_date = gmdate( 'l, F jS, Y \a\\t H:i', $timestamp );
        $file_date = gmdate( 'Ymd-His', $timestamp );

        $result[0] = 0;

        if ( ! VisitorLog_Utility_File::create_dir() ) {
            $result[0] = -1;
            $result[1] = __METHOD__;
            $msg_err = 'create_visitorlog_report_file()-error create dir or file';
            VisitorLog_Logger::instance()->warning($msg_err);
            return $result;
        }

        $dirpath = VISITORLOG_BACKUPS_DIR;
        $random_suffix = VisitorLog_Utility::generate_alpha_numeric_random_string(10);
        $file = 'visitor-log-report-' . $file_date . '-' . $random_suffix . '.html';
        $attachment = $dirpath . '/' . $file;

        $delete = self::delete_report_files($dirpath);
        $data = VisitorLog_Utility::get_data_select( 'users_report', 'report', $number_rows, $err_ );
        if ( $err_ ) VisitorLog_System_View::show_message( 'orange', $err_, $err_ );

        $content = self::create_html_content($data);

        if ( ! VisitorLog_Utility_File::wp_put_contents( $attachment, $content ) ) {
            $result[0] = -2;
            $result[1] = $attachment; 
            $msg_err = 'create_visitorlog_report_file()-error create file: ' . $attachment;
            VisitorLog_Logger::instance()->warning( $msg_err );
            return $result;
        }
        $subject = 'Visitor Log report - ' . ' ' . $mail_date;

        $msg = 'The application contains a report on visitorlog on the site';
        $headers  = 'From: Visitorlog, site: ' . get_bloginfo('url') . PHP_EOL;
        $headers .= '<br><br>' . $msg . PHP_EOL;

        $send = VisitorLog_Send_Email::send_email( $headers, $subject, $attachment, $err );
        if ( $send ) {
            $result[0] = 1;
        } else {
            $result[0] = -3;
            $result[1] = $err;  
        }

        return $result;   

    } // END func

    /*
     * Delete report files
     */
    protected static function delete_report_files( $path )
    {
        $files  = VisitorLog_Utility_File::scan_dir_sort_date( $path );
        $count  = 0;

        foreach ( $files as $file ) {

            if ( strpos( $file, 'visitor-log-report' ) !== false ) {

                if ( VisitorLog_Utility_File::wp_delete_file( $path.'/'.$file ) ) {
                    $count++;
                } else {
                   return false; 
                }
            }
        }
        return $count;

    } // END func 

    /**
     * Creating a report in HTML file format.
     *
     * @param  array  $data           Data array.
     * @param  string $file           The path to the file.
     * @param  int    $number_rows    Number of rows in the array.
     * @return true/false             Creating a HTML file
     */
    protected static function create_html_content( $data )
    { 
        if ( !is_array( $data ) ) return false;

        $num_rows = count( $data );

        $date_time       = __('Date Write/Release', 'visitorlog');
        $user_ip         = __('User IP', 'visitorlog');
        $visitor         = __('Visitor', 'visitorlog');
        $canal           = __('Canal', 'visitorlog');
        $pagespersession = __('Pages per session', 'visitorlog');
        $session         = __('Session','visitorlog');
        $allpagecount    = __('All Page count','visitorlog');
        $useragent       = __('User agent','visitorlog');
        $refererinfo     = __('Referer info','visitorlog');

        $msg = __( 'VisitorLog', 'visitorlog' );

        $HTML_body  = VisitorLog_Reports_View::render_header_file();
        $HTML_body .= $msg;
        $HTML_body .=
        '</div> 
        <table id="table">
            <thead><tr>
                <th style="width:30px;">#</th>
                <th style="width:220px;">'. $date_time .'</th>
                <th style="width:100px;">'. $user_ip .'</th>
                <th style="width:100px;">'. $visitor .'</th>
                <th style="width:100px;">'. $canal .'</th>
                <th>'. $pagespersession .'</th>
                <th>'. $session .'</th>
                <th>'. $allpagecount .'</th>
                <th>'. $useragent .'</th>
                <th>URL</th>
                <th>'. $refererinfo .'</th>
                </tr></thead><tbody>
        ';
        $i = 0;
        while ( $i < $num_rows ) {
            $field1  = date_i18n( 'd M, Y H:i:s', $data[$i]->timestamp );
            if ( 0 == $data[$i]->timerelease ) {
                $field2 = '';
            } else {
                $field2 = '<br>' . date_i18n( 'd M, Y H:i:s', $data[$i]->timerelease );
            }
            $field3  = $data[$i]->user_ip;
            $field4  = $data[$i]->category;
            $field5  = $data[$i]->canal;
            $field6  = $data[$i]->pages;
            $field7  = $data[$i]->session;
            $field8  = $data[$i]->allpages;
            $field9  = $data[$i]->user_agent;
            $field10 = $data[$i]->url;
            $field11 = $data[$i]->referer_info;

            $HTML_body .= '<tr><td data-label="#">';
            $HTML_body .= $i+1;
            $HTML_body .= '</td><td data-label="'. $date_time .'">';
            $HTML_body .= $field1 . $field2;
            $HTML_body .= '</td><td data-label="'. $user_ip .'">';
            $HTML_body .= $field3;
            $HTML_body .= '</td><td data-label="'. $visitor .'">';
            $HTML_body .= $field4;
            $HTML_body .= '</td><td data-label="'. $canal .'">';
            $HTML_body .= $field5;
            $HTML_body .= '</td><td data-label="'. $pagespersession .'">';
            $HTML_body .= $field6;
            $HTML_body .= '</td><td data-label="'. $session .'">';
            $HTML_body .= $field7;
            $HTML_body .= '</td><td data-label="'. $allpagecount .'">';
            $HTML_body .= $field8;
            $HTML_body .= '</td><td data-label="'. $useragent .'">';
            $HTML_body .= $field9;
            $HTML_body .= '</td><td data-label="URL">';
            $HTML_body .= $field10;
            $HTML_body .= '</td><td data-label="'. $refererinfo .'">';
            $HTML_body .= $field11;

            $HTML_body .= '</td></tr>';
            $i ++;
        }
        $HTML_body .= '</tbody></table></body></html>';
                                    
        return $HTML_body;

    } // END func


} // END class