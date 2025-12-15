<?php
/**
  * Server information view
  */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Server_Info class.
 *
 * @method public    static function get_class_name()
 * @method public    static function render_server_info()
 * @method protected static function plugin_row_1()
 * @method protected static function plugin_row_2()
 * @method protected static function plugin_row_3()
 * @method protected static function wp_row_1()
 * @method protected static function wp_row_2()
 * @method protected static function wp_row_3()
 * @method protected static function wp_row_4()
 * @method protected static function php_row_1()
 * @method protected static function php_row_2()
 * @method protected static function php_row_3()
 * @method protected static function php_row_4()
 * @method protected static function php_row_5()
 * @method protected static function php_row_6()
 * @method protected static function php_row_7()
 * @method protected static function php_row_8()
 * @method protected static function php_row_9()
 * @method protected static function php_row_10()
 * @method protected static function php_row_11()
 * @method protected static function php_row_12()
 * @method protected static function php_row_13()
 * @method public    static function check_directory_uploads()
 * @method protected static function render_row_col4()
 * @method public    static function render_row_php2()
 * @method public    static function php_disabled_functions()
 * @method public    static function php_disabled_functions2()
 * @method protected static function kmg()
 * @method protected static function create_server_report_file()
 * @method protected static function delete_report_files()
 * @method protected static function create_html_content()
 */
class VisitorLog_Server_Info { 

	public static function get_class_name()
    {
		return __CLASS__;
	}

	/**
	 * Render server info.
	 */
	public static function render_server_info()
    {
        $msg = '';
        $color = '';

        if ( isset($_GET['reg']) && 'send_email' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = self::create_server_report_file();

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
            //--------- Message
            VisitorLog_System_View::show_message( $color, $msg );
            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
            VisitorLog_Utility::vl_redirect( 'visitorlog_serverinfo' );
            exit;
        } 
        //------------- Display

        if ( '' != VisitorLog_Utility::get_option_sl( 'temp_message' ) ) {
            
            $msg   = VisitorLog_Utility::get_option_sl( 'temp_message' );
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }

        //-------------------------------------------Plugins-----------
        self::plugin_row_1( $col_pl1_1, $col_pl1_2, $col_pl1_3, $col_pl1_4 );
        self::plugin_row_2( $col_pl2_1, $col_pl2_2, $col_pl2_3, $col_pl2_4 );
        self::plugin_row_3( $col_pl3_1, $col_pl3_2, $col_pl3_3, $col_pl3_4 );

        //-------------------------------------------WordPress----------> 
        self::wp_row_1( $col_wp1_1, $col_wp1_2, $col_wp1_3, $col_wp1_4, $col_wp1_5 );
        self::wp_row_2( $col_wp2_1, $col_wp2_2, $col_wp2_3, $col_wp2_4, $col_wp2_5 );
        self::wp_row_3( $col_wp3_1, $col_wp3_2, $col_wp3_3, $col_wp3_4, $col_wp3_5 );
        self::wp_row_4( $col_wp4_1, $col_wp4_2, $col_wp4_3, $col_wp4_4, $col_wp4_5 );

        //-------------------------------------------PHP---------->
        self::php_row_1( $col1_1, $col1_2, $col1_3, $col1_4, $col1_5 );
        self::php_row_2( $col2_1, $col2_2, $col2_3, $col2_4, $col2_5 );
        self::php_row_3( $col3_1, $col3_2, $col3_3, $col3_4, $col3_5 );
        self::php_row_4( $col4_1, $col4_2, $col4_3, $col4_4, $col4_5 );
        self::php_row_5( $col5_1, $col5_2, $col5_3, $col5_4, $col5_5 );
        self::php_row_6( $col6_1, $col6_2, $col6_3, $col6_4, $col6_5 );
        self::php_row_7( $col7_1, $col7_2, $col7_3, $col7_4, $col7_5 );
        self::php_row_8( $col8_1, $col8_2, $col8_3, $col8_4, $col8_5 );
        self::php_row_9( $col9_1, $col9_2, $col9_3, $col9_4, $col9_5 );
        self::php_row_10( $col10_1, $col10_2, $col10_3, $col10_4, $col10_5 );
        self::php_row_11( $col11_1, $col11_2, $col11_3, $col11_4, $col11_5 );
        self::php_row_12( $col12_1, $col12_2, $col12_3, $col12_4, $col12_5 );
        self::php_row_13( $col13_1, $col13_2, $col13_3, $col13_4, $col13_5 );

        //-------------------------------------------MySQL---------->
        self::sql_row_1( $col_sql1_1, $col_sql1_2, $col_sql1_3, $col_sql1_4, $col_sql1_5 );

        $string = VisitorLog_Utility::get_option_sl( 'check_sys_msg' );
        $result = explode( ';', $string );
        $result[0] = $col_wp1_5;
        $result[1] = $col_wp2_5;
        $result[2] = $col_wp3_5;
        $result[3] = $col_wp4_5;
        $result[4] = $col1_5;
        $result[5] = $col2_5;
        $result[6] = $col3_5;
        $result[7] = $col4_5;
        $result[8] = $col5_5;
        $result[9] = $col6_5;
        $result[10] = $col7_5;
        $result[11] = $col8_5;
        $result[12] = $col9_5;
        $result[13] = $col10_5;
        $result[14] = $col11_5;
        $result[15] = $col12_5;
        $result[16] = $col13_5;
        $result[17] = $col_sql1_5;
        $string = implode( ';', $result );
        VisitorLog_Utility::update_option_sl( 'check_sys_msg', $string );
		?>
        <div class="block-header">
            <div class="row" >
                <div class="col-lg-7 col-md-6 col-sm-12" >
                    <p style="font-size:18px;">
                        <em>
                            <?php esc_html_e('Server Info', 'visitorlog');?>
                        </em>
                    </p>
                </div>
            </div>
        </div>
        <div class="row clearfix" style="margin-top:-10px;">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td>
<?php
$title = __('Send report by email', 'visitorlog');
VisitorLog_Utility::redirect_html( 'email.png', $title, 'visitorlog_serverinfo', '&reg=send_email' );
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
        <!-------------------------------------------Plugins----------->
        <div style="margin-top:10px;"></div>
        <div class="row clearfix">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="header" style="margin-bottom:15px;">
                    <h3><?php esc_html_e('Information about plugins','visitorlog');?></h3>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>                                       
                                <td width="450"><?php esc_html_e('Name','visitorlog');?></td>
                                <td>            <?php esc_html_e('Required','visitorlog');?></td>
                                <td>            <?php esc_html_e('Detected','visitorlog');?></td>
                                <td width="150"><?php esc_html_e('Status','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">
                            <?php
                            self::render_row_col4( $col_pl1_1, $col_pl1_2, $col_pl1_3, $col_pl1_4 );
                            self::render_row_col4( $col_pl2_1, $col_pl2_2, $col_pl2_3, $col_pl2_4 );
                            self::render_row_col4( $col_pl3_1, $col_pl3_2, $col_pl3_3, $col_pl3_4 );
                            ?>
                            <tr>
                                <td> <?php esc_html_e('Active Plugins', 'visitorlog');?>:</td>
                            	<td></td>
                                <td></td>
                                <td></td>
                            </tr>
                		<?php
                		$all_plugins = get_plugins();
                		foreach ( $all_plugins as $slug => $plugin ) {
                			$active = is_plugin_active( $slug ) ? 'active' : 'inactive';
                			?>
                            <tr>
                            	<td><?php echo esc_html( $plugin['Name'] );?></td> 
                            	<td></td>
                            	<td><?php echo esc_html( $plugin['Version'] );?></td>
                            	<td><?php VisitorLog_System_View::get_badge($active);?></td>
                            </tr>
                		    <?php
                		}
                		?>
                    	</tbody>
                	</table>
                </div>
        	</div>
    	</div>
        <!-------------------------------------------WordPress----------> 
        <div class="row clearfix">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="header" style="margin-top:-15px;margin-bottom:15px;">
                    <h3>WordPress</h3>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>                                       
                                <td width="450"><?php esc_html_e('Name','visitorlog');?></td>
                                <td>            <?php esc_html_e('Required','visitorlog');?></td>
                                <td>            <?php esc_html_e('Detected','visitorlog');?></td>
                                <td width="150"><?php esc_html_e('Status','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">
                            <?php
                            self::render_row_col4( $col_wp1_1, $col_wp1_2, $col_wp1_3, $col_wp1_4 );
                            self::render_row_col4( $col_wp2_1, $col_wp2_2, $col_wp2_3, $col_wp2_4 );
                            self::render_row_col4( $col_wp3_1, $col_wp3_2, $col_wp3_3, $col_wp3_4 );
                            self::render_row_col4( $col_wp4_1, $col_wp4_2, $col_wp4_3, $col_wp4_4 );
                            ?>
                    	</tbody>
                	</table>
            	</div>
        	</div>
    	</div>
        <!-------------------------------------------PHP---------->
        <div class="row clearfix">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="header" style="margin-top:-15px;margin-bottom:15px;">
                    <h3>PHP</h3>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>                                       
                                <td width="450"><?php esc_html_e('Name','visitorlog');?></td>
                                <td>            <?php esc_html_e('Required','visitorlog');?></td>
                                <td>            <?php esc_html_e('Detected','visitorlog');?></td>
                                <td width="150"><?php esc_html_e('Status','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">
                            <?php
                            /*01*/ self::render_row_col4( $col1_1, $col1_2, $col1_3, $col1_4 );
                            /*02*/ self::render_row_col4( $col2_1, $col2_2, $col2_3, $col2_4 );
                            /*03*/ self::render_row_col4( $col3_1, $col3_2, $col3_3, $col3_4 );
                            /*04*/ self::render_row_col4( $col4_1, $col4_2, $col4_3, $col4_4 );
                            /*05*/ self::render_row_col4( $col5_1, $col5_2, $col5_3, $col5_4 );
                            /*06*/ self::render_row_col4( $col6_1, $col6_2, $col6_3, $col6_4 );
                            /*07*/ self::render_row_col4( $col7_1, $col7_2, $col7_3, $col7_4 );
                            /*08*/ self::render_row_col4( $col8_1, $col8_2, $col8_3, $col8_4 );
                            /*09*/ self::render_row_col4( $col9_1, $col9_2, $col9_3, $col9_4 );
                            /*10*/ self::render_row_col4( $col10_1, $col10_2, $col10_3, $col10_4 );
                            /*11*/ self::render_row_col4( $col11_1, $col11_2, $col11_3, $col11_4 );

                            if ( function_exists( 'curl_version' ) ) {
                                /*12*/ self::render_row_col4( $col12_1, $col12_2, $col12_3, $col12_4 );
                                /*13*/ self::render_row_col4( $col13_1, $col13_2, $col13_3, $col13_4 );
                            }
                            ?>
<tr>                                       
    <td><?php esc_html_e('PHP Allow URL fopen','visitorlog');?></td>
    <td colspan="3"><?php VisitorLog_Server_Information_Handler::get_php_allow_url_fopen();?></td>
</tr>
<tr>                                       
    <td><?php esc_html_e('PHP Exif Support','visitorlog');?></td>
    <td colspan="3"><?php VisitorLog_Server_Information_Handler::get_php_exif();?></td>
</tr>
<tr>                                       
    <td><?php esc_html_e('PHP IPTC Support','visitorlog');?></td>
    <td colspan="3"><?php VisitorLog_Server_Information_Handler::get_php_iptc();?></td>
</tr>
<tr>                                       
    <td><?php esc_html_e('PHP XML Support','visitorlog');?></td>
    <td colspan="3"><?php VisitorLog_Server_Information_Handler::get_php_xml();?></td>
</tr>
<tr>                                       
    <td><?php esc_html_e('PHP Disabled Functions','visitorlog');?></td>
    <td colspan="3"><?php self::php_disabled_functions();?></td>
</tr>
<tr>                                       
    <td><?php esc_html_e('PHP Loaded Extensions','visitorlog');?></td>
    <td colspan="3"><?php VisitorLog_Server_Information_Handler::get_loaded_php_extensions();?></td>
</tr>
                    	</tbody>
                	</table>
                </div>    
        	</div>
    	</div>
        <!-------------------------------------------MySQL---------->
        <div class="row clearfix">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="header" style="margin-top:-15px;margin-bottom:15px;">
                    <h3>MySQL</h3>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>                                       
                                <td width="450"><?php esc_html_e('Name','visitorlog');?></td>
                                <td>            <?php esc_html_e('Required','visitorlog');?></td>
                                <td>            <?php esc_html_e('Detected','visitorlog');?></td>
                                <td width="150"><?php esc_html_e('Status','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">
                            <?php
                            self::render_row_col4( $col_sql1_1, $col_sql1_2, $col_sql1_3, $col_sql1_4 );
                            ?>
                            <tr>                                       
<td style="font-weight:normal;"><?php esc_html_e('MySQL Mode','visitorlog');?></td>
<td></td>
<td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_sql_mode();?></td>
<td></td>
                            </tr>
                            <tr>                                       
<td style="font-weight:normal;"><?php esc_html_e('MySQL Client Encoding','visitorlog');?></td>
<td></td>
<td style="font-weight:normal;"><?php echo defined( 'DB_CHARSET' ) ? esc_html(DB_CHARSET) : '';?></td>
<td></td>
                            </tr>
                    	</tbody>
                	</table>
            	</div>
        	</div>
    	</div>
        <!-------------------------------------------Server Info---------->
        <div class="row clearfix">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="header" style="margin-top:-15px;margin-bottom:15px;">
                    <h3><?php esc_html_e( 'Server Info', 'visitorlog' );?></h3>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>                                       
                                <td width="450"><?php esc_html_e('Name','visitorlog');?></td>
                                <td>            <?php esc_html_e('Detected','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('WordPress Root Directory','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_wp_root();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Server Name','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_name();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Server Software','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_software();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Operating System','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_os();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Architecture','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_architecture();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Server IP','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_ip();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Server Protocol','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_protocol();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('HTTP Host','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_http_host();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;">HTTPS</td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_https();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('User agent','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_user_agent();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Server Port','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_port();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Gateway Interface','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_gateway_interface();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Memory Usage','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::memory_usage();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Complete URL','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_complete_url();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Request Time','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_request_time();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Accept Content','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_http_accept();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Accept-Charset Content','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_server_accept_charset();?></td>
</tr>
<tr>                                       
    <th style="font-weight:normal;"><?php esc_html_e('Currently Executing Script Pathname','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_script_file_name();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Current Page URI','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_current_page_uri();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Remote Address','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_remote_address();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Remote Host','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_remote_host();?></td>
</tr>
<tr>                                       
    <td style="font-weight:normal;"><?php esc_html_e('Remote Port','visitorlog');?></td>
    <td style="font-weight:normal;"><?php VisitorLog_Server_Information_Handler::get_remote_port();?></td>
</tr>
                    	</tbody>
                	</table>
            	</div>
        	</div>
    	</div>
	    <?php

	} // END func	

    //-----------------Plugins---------

    protected static function plugin_row_1( &$col_pl1_1, &$col_pl1_2, &$col_pl1_3, &$col_pl1_4 )
    {
/*01*/  $col_pl1_1 = __('VisitorLog Version', 'visitorlog'); 
        $col_pl1_2 = '>=1.0.0';
        $col_pl1_3 = VISITORLOG_PLUGIN_VERSION; 
        if ( version_compare( $col_pl1_3, '1.0.0', '>=' ) ) {
            $col_pl1_4 = 'Pass';
        } else {
            $col_pl1_4 = 'Warning';
        }
    }    

    protected static function plugin_row_2( &$col_pl2_1, &$col_pl2_2, &$col_pl2_3, &$col_pl2_4 )
    {
/*02*/  $col_pl2_1 = __('VisitorLog DB Version', 'visitorlog'); 
        $col_pl2_2 = '>=1.0.1';
        $col_pl2_3 = VISITORLOG_DB_VERSION; 
        if ( version_compare( $col_pl2_3, '1.0.1', '>=' ) ) {
            $col_pl2_4 = 'Pass';
        } else {
            $col_pl2_4 = 'Warning';
        }
    }  

    protected static function plugin_row_3( &$col_pl3_1, &$col_pl3_2, &$col_pl3_3, &$col_pl3_4 )
    {
/*03*/  $col_pl3_1 = __('VisitorLog Upload Directory', 'visitorlog'); 
        $col_pl3_2 = __('Writable', 'visitorlog'); 
        $mes  = '';
        self::check_directory_uploads( $mes, $pas );
        $col_pl3_3 = $mes; 
        if ( true === $pas ) {
            $col_pl3_4 = 'Pass';
        } else {
            $col_pl3_4 = 'Warning';
        }
    }  

    //-----------------WordPress---------

    protected static function wp_row_1( &$col1_1, &$col1_2, &$col1_3, &$col1_4, &$col1_5 )
    {
        global $wp_version;

/*01*/  $col1_1 = __('WordPress Version', 'visitorlog'); 
        $col1_2 = '>=3.6';
        $col1_3 = $wp_version; 
        if ( version_compare( $col1_3, '3.6', '>=' ) ) {
            $col1_4 = 'Pass';
            $col1_5 = 1;
        } else {
            $col1_4 = 'Warning';
            $col1_5 = '';
        }
    }

    protected static function wp_row_2( &$col2_1, &$col2_2, &$col2_3, &$col2_4, &$col2_5 )
    {
/*02*/  $col2_1 = __('WordPress Memory Limit', 'visitorlog');
        $col2_2 = '>=64M';
        $col2_3 = WP_MEMORY_LIMIT; 
        $col2_3_1 = self::kmg($col2_3);            
        if ( $col2_3_1 >= 64000 ) {
            $col2_4 = 'Pass';
            $col2_5 = 1;
        } else {
            $col2_4 = 'Warning';
            $col2_5 = '';
        }
    }

    protected static function wp_row_3( &$col3_1, &$col3_2, &$col3_3, &$col3_4, &$col3_5 )
    {
/*03*/  $col3_1 = __('MultiSite Disabled', 'visitorlog');
        $col3_2 = '=true';
        $isMultisite = !is_multisite() ? true : false;
        if ( $isMultisite ) {
            $col3_3 = 'true';             
            $col3_4 = 'Pass';
            $col3_5 = 1;
        } else {
            $col3_3 = 'false';
            $col3_4 = 'Warning';
            $col3_5 = '';
        }
    }

    protected static function wp_row_4( &$col4_1, &$col4_2, &$col4_3, &$col4_4, &$col4_5 )
    {
/*04*/  $col4_1 = __('FileSystem Method', 'visitorlog');
        $col4_2 = '=direct';
        $col4_3 = VisitorLog_System_Check::get_filesystem_method();
        if ( 'direct' == $col4_3 ) {
            $col4_4 = 'Pass';
            $col4_5 = 1;
        } else {
            $col4_4 = 'Warning';
            $col4_5 = '';
        }
    }

    //-----------------PHP----------

    protected static function php_row_1( &$col1_1, &$col1_2, &$col1_3, &$col1_4, &$col1_5 )
    {
/*01*/  $col1_1 = __('PHP Version', 'visitorlog'); 
        $col1_2 = '>=7.4';
        $col1_3 = phpversion(); 
        if ( version_compare( $col1_3, '7.4', '>=' ) ) {
            $col1_4 = 'Pass';
            $col1_5 = 1;
        } else {
            $col1_4 = 'Warning';
            $col1_5 = '';
        }
    }

    protected static function php_row_2( &$col2_1, &$col2_2, &$col2_3, &$col2_4, &$col2_5 )
    {
/*02*/  $col2_1 = __('PHP Safe Mode Disabled', 'visitorlog'); 
        $col2_2 = '=true';
        $col2_3 = VisitorLog_Server_Information_Handler::get_php_safe_mode(); 
        if ( true === $col2_3 ) {
            $col2_3 = 'true';
            $col2_4 = 'Pass';
            $col2_5 = 1;
        } else {
            $col2_3 = 'false';
            $col2_4 = 'Warning';
            $col2_5 = '';
        }
    }    

    protected static function php_row_3( &$col3_1, &$col3_2, &$col3_3, &$col3_4, &$col3_5 )
    {
/*03*/  $col3_1 = __('PHP Max Execution Time', 'visitorlog'); 
        $col3_2 = '>=30 seconds';
        $col3_3 = ini_get( 'max_execution_time' ); 
        if ( $col3_3 >= 30 ) {
            $col3_4 = 'Pass';
            $col3_5 = 1;
        } else {
            $col3_4 = 'Warning';
            $col3_5 = '';
        }
    }    

    protected static function php_row_4( &$col4_1, &$col4_2, &$col4_3, &$col4_4, &$col4_5 )
    {
/*04*/  $col4_1 = __('PHP Max Input Time', 'visitorlog'); 
        $col4_2 = '>=30 seconds';
        $col4_3 = ini_get( 'max_input_time' ); 
        if ( -1 == $col4_3 ) $col4_3 = 300; 
        if ( 0  == $col4_3 ) $col4_3 = 999999;      
        if ( $col4_3 >= 30 ) {
            $col4_4 = 'Pass';
            $col4_5 = 1;
        } else {
            $col4_4 = 'Warning';
            $col4_5 = '';
        }
    }

    protected static function php_row_5( &$col5_1, &$col5_2, &$col5_3, &$col5_4, &$col5_5 )
    {
/*05*/  $col5_1 = __('PHP Memory Limit', 'visitorlog'); 
        $col5_2 = '>=128M';
        $col5_3 = ini_get( 'memory_limit' ); 
        if ( -1 == $col5_3 || 0  == $col5_3 ) {
            $col5_3_1 = 999999999;
        } else {
            $col5_3_1 = self::kmg($col5_3);            
        }  
        if ( $col5_3_1 >= 128000 ) {
            $col5_4 = 'Pass';
            $col5_5 = 1;
        } else {
            $col5_4 = 'Warning';
            $col5_5 = '';
        }
    }

    protected static function php_row_6( &$col6_1, &$col6_2, &$col6_3, &$col6_4, &$col6_5 )
    {
/*06*/  $col6_1 = __('PCRE Backtracking Limit', 'visitorlog'); 
        $col6_2 = '>=10000';
        $col6_3 = ini_get( 'pcre.backtrack_limit' );
        if ( $col6_3 >= 10000 ) {
            $col6_4 = 'Pass';
            $col6_5 = 1;
        } else {
            $col6_4 = 'Warning';
            $col6_5 = '';
        }
    }
    
    protected static function php_row_7( &$col7_1, &$col7_2, &$col7_3, &$col7_4, &$col7_5 )
    {
/*07*/  $col7_1 = __('PHP Upload Max Filesize', 'visitorlog'); 
        $col7_2 = '>=2M';
        $col7_3 = ini_get( 'upload_max_filesize' );
        $col7_3_1 = self::kmg($col7_3);            
        if ( $col7_3_1 >= 2000 ) {
            $col7_4 = 'Pass';
            $col7_5 = 1;
        } else {
            $col7_4 = 'Warning';
            $col7_5 = '';
        }
    }
    
    protected static function php_row_8( &$col8_1, &$col8_2, &$col8_3, &$col8_4, &$col8_5 )
    {
/*08*/  $col8_1 = __('PHP Post Max Size', 'visitorlog'); 
        $col8_2 = '>=2M';
        $col8_3 = ini_get( 'post_max_size' );
        $col8_3_1 = self::kmg($col8_3);            
        if ( $col8_3_1 >= 2000 ) {
            $col8_4 = 'Pass';
            $col8_5 = 1;
        } else {
            $col8_4 = 'Warning';
            $col8_5 = '';
        }
    }
    
    protected static function php_row_9( &$col9_1, &$col9_2, &$col9_3, &$col9_4, &$col9_5 )
    {
        global $visitorlog_users_resident_parameters;

/*09*/  $col9_1 = __('SSL Extension Enabled', 'visitorlog'); 
        $col9_2 = 'SSL=true';
        $ip = $visitorlog_users_resident_parameters['ip'];
        if ( is_ssl() ) {
            $col9_3 = 'SSL';
            $col9_4 = VisitorLog_System_View::get_html_text('Pass');
            $col9_5 = 1;
        } else {
            if (substr($ip, 0, 4) == '127.' || $ip == '::1') {
                $col9_3 = 'HTTP localhost';
                $col9_4 = 'Pass';
                $col9_5 = 1;
            } else {
                $col9_3 = 'HTTP';
                $col9_4 = 'Warning';
                $col9_5 = '';
            }
        }
    }    

    protected static function php_row_10( &$col10_1, &$col10_2, &$col10_3, &$col10_4, &$col10_5 )
    {
/*10*/  $col10_1 = __('cURL Extension Enabled', 'visitorlog'); 
        $col10_2 = '=true';
        $curl_version = function_exists( 'curl_version' );
        if ( $curl_version ) {
            $col10_3 = 'true';
            $col10_4 = 'Pass';
            $col10_5 = 1;
        } else {
            $col10_3 = 'false';
            $col10_4 = 'Warning';
            $col10_5 = '';
        }
    }
    
    protected static function php_row_11( &$col11_1, &$col11_2, &$col11_3, &$col11_4, &$col11_5 )
    {
/*11*/  $col11_1 = __('cURL Timeout', 'visitorlog'); 
        $col11_2 = '>=60 seconds';
        $col11_3 = ini_get( 'default_socket_timeout' );
        if ( $col11_3 >= 60 ) {
            $col11_4 = 'Pass';
            $col11_5 = 1;
        } else {
            $col11_4 = 'Warning';
            $col11_5 = '';
        }
    }

    protected static function php_row_12( &$col12_1, &$col12_2, &$col12_3, &$col12_4, &$col12_5 )
    {
/*12*/  $col12_1 = __('cURL Version', 'visitorlog'); 
        $col12_2 = '>=7.18.1';
        $curlversion = curl_version();
        $col12_3 = $curlversion['version'];
        if ( version_compare( $col12_3, '7.18.1', '>=' ) ) {
            $col12_4 = 'Pass';
            $col12_5 = 1;
        } else {
            $col12_4 = 'Warning';
            $col12_5 = '';
        }
    }
    
    protected static function php_row_13( &$col13_1, &$col13_2, &$col13_3, &$col13_4, &$col13_5 )
    {
/*13*/  $col13_1 = __('cURL SSL Version', 'visitorlog'); 
        $col13_2 = '>=OpenSSL/0.9.8l';
        $curlversion = curl_version();
        $col13_3 = $curlversion['ssl_version'];
        if ( version_compare( $col13_3, 'OpenSSL/0.9.8l', '>=' ) ) {
            $col13_4 = 'Pass';
            $col13_5 = 1;
        } else {
            $col13_4 = 'Warning';
            $col13_5 = '';
        }
    }

    //----------------MySQL-------------------

    protected static function sql_row_1( &$col_sql1_1, &$col_sql1_2, &$col_sql1_3, &$col_sql1_4, &$col_sql1_5 )
    {
/*01*/  $col_sql1_1 = __('MySQL Version', 'visitorlog'); 
        $col_sql1_3 = VisitorLog_DB_Base::instance()->get_my_sql_version();
        if ( false === strpos($col_sql1_3, 'MariaDB') ) {
            $col_sql1_2 = '>=5.7';
            if ( version_compare( $col_sql1_3, 5.7, '>=' ) ) {
                $col_sql1_4 = 'Pass';
                $col_sql1_5 = 1;
            } else {
                $col_sql1_4 = 'Warning';
                $col_sql1_5 = '';
            }
        } else {
            $col_sql1_2 = '>=10.4';
            if ( version_compare( $col_sql1_3, 10.4, '>=' ) ) {
                $col_sql1_4 = 'Pass';
                $col_sql1_5 = 1;
            } else {
                $col_sql1_4 = 'Warning';
                $col_sql1_5 = '';
            }
        }
    }

	/**
	 * Checks if the ../wp-content/uploads/ directory is writable.
	 *
	 * @return bool True if writable, false if not.
	 */
	public static function check_directory_uploads( &$mess, &$passed )
    {
        $path = VISITORLOG_CONTENT_DIR . '/uploads/'; 
        $mess = __('Writable', 'visitorlog');
		$passed = true;

		if ( ! is_dir( dirname( $path ) ) ) {
			$mess   = __('Not Found', 'visitorlog');
			$passed = false;
		}

		if ( ! VisitorLog_Utility_File::is_writable( $path ) ) {
			$mess   = __('Not Writable', 'visitorlog');
			$passed = false;
		}

	} // END func

	/**
	 * Renders the row.
	 *
	 */
    protected static function render_row_col4( $col1, $col2, $col3, $col4 )
    {
        ?>
        <tr>
            <td><?php echo esc_html($col1);?></td>
            <td><?php echo esc_html($col2);?></td>
            <td><?php echo esc_html($col3);?></td>
            <td><?php VisitorLog_System_View::get_badge($col4);?></td>
        </tr>
        <?php

    } // END func   

	public static function render_row_php2( $compare, $version, $getter, &$currentVersion, &$extraText, $whatType )
    {
		$currentVersion = call_user_func( array(VisitorLog_Server_Information_Handler::get_class_name(), $getter) );

		if ( ( 'get_max_input_time' === $getter || 'get_max_execution_time' === $getter ) && 
				-1 == $currentVersion ) {

			$currentVersion = (string)$currentVersion;
			$extraText = __( 'Pass', 'visitorlog' );
			return;
		}
		if ( 'filesize' == $whatType ) {
			$extraText = VisitorLog_Server_Information_Handler::filesize_compare($currentVersion, $version, $compare) ? __('Pass', 'visitorlog') : __('Warning', 'visitorlog');
			return; 
		}
		elseif ( 'curlssl' == $whatType ) { 
			$extraText = VisitorLog_Server_Information_Handler::curlssl_compare($version, $compare) ? __('Pass', 'visitorlog') : __('Warning', 'visitorlog'); 
			return;
		}
		if ( true === $version ) {
			if ( ( true === $currentVersion ) || ( 1 == $currentVersion ) ) {
				$currentVersion = 'true';
				$extraText = __( 'Pass', 'visitorlog' );
			} else {
				$currentVersion = 'false';
				$extraText = __( 'Warning', 'visitorlog' );
			}
		} else {
			if ( $currentVersion >= $version ) {
				$currentVersion = (string)$currentVersion;
				$extraText = __( 'Pass', 'visitorlog' );
			} else {
				$currentVersion = (string)$currentVersion;
				$extraText = __( 'Warning', 'visitorlog' );
			}
		}
        
	} // END func

	/**
	 * Checks for disable PHP Functions.
	 *
	 * @return void
	 */
	public static function php_disabled_functions()
    {
		$disabled_functions = ini_get( 'disable_functions' );
		if ( '' !== $disabled_functions ) {
			$arr = explode( ',', $disabled_functions );
			sort( $arr );
			$_count = count( $arr );
			for ( $i = 0; $i < $_count; $i ++ ) {
				echo esc_html($arr[ $i ]) . ', ';
			}
		} else {
			esc_html_e( 'No functions disabled', 'visitorlog' );
		}

	} // END func

	public static function php_disabled_functions2()
    {
		$disabled_functions = ini_get( 'disable_functions' );
		if ( '' !== $disabled_functions ) {
			$arr = explode( ',', $disabled_functions );
			sort( $arr );
			$_count = count( $arr );
			$arr_render = '';
			for ( $i = 0; $i < $_count; $i ++ ) {
				if ( $i == ($_count - 1) ) $arr_render .= $arr[ $i ];
				else                       $arr_render .= $arr[ $i ] . ', ';
			}
		} else 	return __( 'No functions disabled', 'visitorlog' );
		
		return $arr_render;

	} // END func

    protected static function kmg( $value )
    {
        $cut_value = substr($value, 0, -1);
        $last      = substr($value, -1);  

        switch ( $last ) {
            case 'K':
                $cut_value_out = $cut_value;
                break;
            case 'M':
                $cut_value_out = $cut_value * 1000;
                break;
            case 'G':
                $cut_value_out = $cut_value * 1000000;
                break;
            default:
                $cut_value_out = 0;
        }
        return $cut_value_out;

    } // END func

    /**
     * Create a report file
     *
     * @uses VisitorLog_Utility_File::create_dir()
     * @uses VisitorLog_Logger::instance()->warning()
     * @uses VisitorLog_Utility::generate_alpha_numeric_random_string()
     * @uses VisitorLog_Send_Email::send_email()
     */    
    protected static function create_server_report_file()
    { 
        $dirpath = VISITORLOG_BACKUPS_DIR;
        $random_suffix = VisitorLog_Utility::generate_alpha_numeric_random_string( 10 );
        $file = 'server-info-' . current_time( 'Ymd-His' ) . '-' . $random_suffix . '.html';
        $attachment = $dirpath . '/' . $file;

        $result[0] = 0;

        if ( ! VisitorLog_Utility_File::create_dir() ) {
            $result[0] = -1;
            $result[1] = __METHOD__;
            $msg_err = 'create_server_report_file()-error create dir or file';
            VisitorLog_Logger::instance()->warning($msg_err);
            return $result;
        }

        $delete = self::delete_report_files($dirpath);
        $content = self::create_html_content();

        if ( ! VisitorLog_Utility_File::wp_put_contents( $attachment, $content ) ) {
            $result[0] = -2;
            $result[1] = $attachment; 
            $msg_err = 'create_server_report_file()-error create file: ' . $attachment;
            VisitorLog_Logger::instance()->warning( $msg_err );
            return $result;
        }

        $date = gmdate( 'l, F jS, Y \a\\t H:i', current_time( 'timestamp' ) );
        $subject = 'VisitorLog - Server info - ' . ' ' . $date;

        $msg = 'The application contains a table with information about the server and not only';
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

            if ( strpos( $file, 'server-info' ) !== false ) {

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
	 * @return $HTML_body - Creating a HTML file
	 */
	protected static function create_html_content()
    {
        self::plugin_row_1( $col_pl1_1, $col_pl1_2, $col_pl1_3, $col_pl1_4 );
        self::plugin_row_2( $col_pl2_1, $col_pl2_2, $col_pl2_3, $col_pl2_4 );
        self::plugin_row_3( $col_pl3_1, $col_pl3_2, $col_pl3_3, $col_pl3_4 );

        self::wp_row_1( $col_wp1_1, $col_wp1_2, $col_wp1_3, $col_wp1_4, $col_wp1_5 );
        self::wp_row_2( $col_wp2_1, $col_wp2_2, $col_wp2_3, $col_wp2_4, $col_wp2_5 );
        self::wp_row_3( $col_wp3_1, $col_wp3_2, $col_wp3_3, $col_wp3_4, $col_wp3_5 );
        self::wp_row_4( $col_wp4_1, $col_wp4_2, $col_wp4_3, $col_wp4_4, $col_wp4_5 );

        self::php_row_1( $col1_1, $col1_2, $col1_3, $col1_4, $col1_5 );
        self::php_row_2( $col2_1, $col2_2, $col2_3, $col2_4, $col2_5 );
        self::php_row_3( $col3_1, $col3_2, $col3_3, $col3_4, $col3_5 );
        self::php_row_4( $col4_1, $col4_2, $col4_3, $col4_4, $col4_5 );
        self::php_row_5( $col5_1, $col5_2, $col5_3, $col5_4, $col5_5 );
        self::php_row_6( $col6_1, $col6_2, $col6_3, $col6_4, $col6_5 );
        self::php_row_7( $col7_1, $col7_2, $col7_3, $col7_4, $col7_5 );
        self::php_row_8( $col8_1, $col8_2, $col8_3, $col8_4, $col8_5 );
        self::php_row_9( $col9_1, $col9_2, $col9_3, $col9_4, $col9_5 );
        self::php_row_10( $col10_1, $col10_2, $col10_3, $col10_4, $col10_5 );
        self::php_row_11( $col11_1, $col11_2, $col11_3, $col11_4, $col11_5 );
        self::php_row_12( $col12_1, $col12_2, $col12_3, $col12_4, $col12_5 );
        self::php_row_13( $col13_1, $col13_2, $col13_3, $col13_4, $col13_5 );

        self::sql_row_1( $col_sql1_1, $col_sql1_2, $col_sql1_3, $col_sql1_4, $col_sql1_5 );

        $Name     = __( 'Name', 'visitorlog' );
        $Required = __( 'Required', 'visitorlog' );
        $Detected = __( 'Detected', 'visitorlog' );
        $Status   = __( 'Status', 'visitorlog' );

        $HTML_body  = VisitorLog_Reports_View::render_header_file();
        $HTML_body .= '<div class="table-caption">';
		$HTML_body .= __( 'System information report', 'visitorlog' );
		$HTML_body .=
        '</div>	
            <table id="table">
            	<thead><tr>
            	    <th style="width:20%;">' . $Name . '</th>
            	    <th style="width:20%;">' . $Required . '</th>
            	    <th style="width:40%;">' . $Detected . '</th>
            	    <th style="width:20%;">' . $Status . '</th>
            	    </tr></thead><tbody>';
		$HTML_body .= '<td style="width:20%;"><h3>';
		$HTML_body .= __('Information about plugins', 'visitorlog');
		$HTML_body .= '</h3></td><td style="width:20%;">';
		$HTML_body .= '';
		$HTML_body .= '</td><td style="width:40%;">';
		$HTML_body .= '';
		$HTML_body .= '</td><td style="width:20%;">';
		$HTML_body .= '';
		$HTML_body .= '</td>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= $col_pl1_1;
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= $col_pl1_2;
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= $col_pl1_3;
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= $col_pl1_4;
		$HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col_pl2_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col_pl2_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col_pl2_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col_pl2_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col_pl3_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col_pl3_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col_pl3_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col_pl3_4;
        $HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __('Active Plugins', 'visitorlog') . ':';
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= '';
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= '';
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= '';
		$HTML_body .= '</td></tr>';

		$all_plugins    = get_plugins();
		foreach ( $all_plugins as $slug => $plugin ) {

            $active = is_plugin_active( $slug ) ? 'active' : 'inactive';

			$HTML_body .= '<tr><td data-label="Name">';
			$HTML_body .= $plugin['Name'];
			$HTML_body .= '</td><td data-label="Required">';
			$HTML_body .= '';
			$HTML_body .= '</td><td data-label="Detected">';
			$HTML_body .= $plugin['Version'];
			$HTML_body .= '</td><td data-label="Status">';
			$HTML_body .= $active;
			$HTML_body .= '</td></tr>';
		}

		$HTML_body .= '<th style="line-height: 14px;"><h3>';
		$HTML_body .= 'WordPress';
		$HTML_body .= '</h3></th><td>';
		$HTML_body .= '';
		$HTML_body .= '</td><td>';
		$HTML_body .= '';
		$HTML_body .= '</td><td>';
		$HTML_body .= '';
		$HTML_body .= '</td>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= $col_wp1_1;
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= $col_wp1_2;
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= $col_wp1_3;
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= $col_wp1_4;
		$HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col_wp2_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col_wp2_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col_wp2_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col_wp2_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col_wp3_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col_wp3_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col_wp3_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col_wp3_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col_wp4_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col_wp4_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col_wp4_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col_wp4_4;
        $HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><th><h3>';
		$HTML_body .= 'PHP';
		$HTML_body .= '</h3></th><td>';
		$HTML_body .= '';
		$HTML_body .= '</td><td>';
		$HTML_body .= '';
		$HTML_body .= '</td><td>';
		$HTML_body .= '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= $col1_1;
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= $col1_2;
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= $col1_3;
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= $col1_4;
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= $col2_1;
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= $col2_2;
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= $col2_3;
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= $col2_4;
		$HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col3_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col3_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col3_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col3_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col4_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col4_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col4_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col4_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col5_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col5_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col5_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col5_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col6_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col6_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col6_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col6_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col7_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col7_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col7_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col7_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col8_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col8_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col8_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col8_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col9_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col9_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col9_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col9_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col10_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col10_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col10_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col10_4;
        $HTML_body .= '</td></tr>';

        $HTML_body .= '<tr><td data-label="Name">';
        $HTML_body .= $col11_1;
        $HTML_body .= '</td><td data-label="Required">';
        $HTML_body .= $col11_2;
        $HTML_body .= '</td><td data-label="Detected">';
        $HTML_body .= $col11_3;
        $HTML_body .= '</td><td data-label="Status">';
        $HTML_body .= $col11_4;
        $HTML_body .= '</td></tr>';

		if ( function_exists( 'curl_version' ) ) {

            $HTML_body .= '<tr><td data-label="Name">';
            $HTML_body .= $col12_1;
            $HTML_body .= '</td><td data-label="Required">';
            $HTML_body .= $col12_2;
            $HTML_body .= '</td><td data-label="Detected">';
            $HTML_body .= $col12_3;
            $HTML_body .= '</td><td data-label="Status">';
            $HTML_body .= $col12_4;
            $HTML_body .= '</td></tr>';

            $HTML_body .= '<tr><td data-label="Name">';
            $HTML_body .= $col13_1;
            $HTML_body .= '</td><td data-label="Required">';
            $HTML_body .= $col13_2;
            $HTML_body .= '</td><td data-label="Detected">';
            $HTML_body .= $col13_3;
            $HTML_body .= '</td><td data-label="Status">';
            $HTML_body .= $col13_4;
            $HTML_body .= '</td></tr>';
		}

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'PHP Allow URL fopen', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= '';
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_php_allow_url_fopen2();
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'PHP Exif Support', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= '';
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_php_exif2();
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'PHP IPTC Support', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= '';
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_php_iptc2();
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'PHP XML Support', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= '';
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_php_xml2();
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'PHP Disabled Functions', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= '';
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= self::php_disabled_functions2();
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '</tbody></table>';
		$HTML_body .= '<tbody><table>';

		$HTML_body .= '<tr><td data-label="Name" style="width:20%;">';
		$HTML_body .= __( 'PHP Loaded Extensions', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required" style="width:80%;">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_loaded_php_extensions2();
		$HTML_body .= '</td></tr>';

		$HTML_body .= '</tbody></table>';
		$HTML_body .= '<tbody><table>';

		$HTML_body .= '<td style="width:20%;"><h3>';
		$HTML_body .= 'MySQL';
		$HTML_body .= '</h3></td><td style="width:20%;">';
		$HTML_body .= '';
		$HTML_body .= '</td><td style="width:40%;">';
		$HTML_body .= '';
		$HTML_body .= '</td><td style="width:20%;">';
		$HTML_body .= '';
		$HTML_body .= '</td>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= $col_sql1_1;
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= $col_sql1_2;
		$HTML_body .= '</td><td data-label="Detected">';
		$HTML_body .= $col_sql1_3;
		$HTML_body .= '</td><td data-label="Status">';
		$HTML_body .= $col_sql1_4;
		$HTML_body .= '</td></tr>';

		$HTML_body .= '</tbody></table>';
		$HTML_body .= '<tbody><table>';

		$HTML_body .= '<tr><td data-label="Name" style="width:20%;">';
		$HTML_body .= __( 'MySQL Mode', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required" style="width:80%;">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_sql_mode2();
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name" style="width:20%;">';
		$HTML_body .= __( 'MySQL Client Encoding', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required" style="width:80%;">';
		$HTML_body .= defined( 'DB_CHARSET' ) ? DB_CHARSET : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '</tbody></table>';
		$HTML_body .= '<tbody><table>';

		$HTML_body .= '<td style="width:20%;"><h3>';
		$HTML_body .= __('Server Information','visitorlog');
		$HTML_body .= '</h3></td><td style="width:80%;">';
		$HTML_body .= '';
		$HTML_body .= '</td>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'WordPress Root Directory', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= get_home_path();
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Server Name', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_server_name( true );
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Server Software', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_server_software( true );
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Operating System', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= PHP_OS;
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Architecture', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= PHP_INT_SIZE * 8 . '  bit';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Server IP', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field(wp_unslash( $_SERVER['SERVER_ADDR'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Server Protocol', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['SERVER_PROTOCOL'] ) ? sanitize_text_field(wp_unslash( $_SERVER['SERVER_PROTOCOL'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'HTTP Host', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field(wp_unslash( $_SERVER['HTTP_HOST'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= 'HTTPS';
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_https2();
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'User agent', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field(wp_unslash( $_SERVER['HTTP_USER_AGENT'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Server Port', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field(wp_unslash( $_SERVER['SERVER_PORT'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Gateway Interface', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['GATEWAY_INTERFACE'] ) ? sanitize_text_field(wp_unslash( $_SERVER['GATEWAY_INTERFACE'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Memory Usage', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= VisitorLog_Server_Information_Handler::memory_usage2();
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Complete URL', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field(wp_unslash( $_SERVER['HTTP_REFERER'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Request Time', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['REQUEST_TIME'] ) ? sanitize_text_field(wp_unslash( $_SERVER['REQUEST_TIME'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Accept Content', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['HTTP_ACCEPT'] ) ? sanitize_text_field(wp_unslash( $_SERVER['HTTP_ACCEPT'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Accept-Charset Content', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= !isset( $_SERVER['HTTP_ACCEPT_CHARSET'] ) || ( '' == $_SERVER['HTTP_ACCEPT_CHARSET'] ) ? 'N/A' : sanitize_text_field(wp_unslash( $_SERVER['HTTP_ACCEPT_CHARSET'] ));
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Currently Executing Script Pathname', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['SCRIPT_FILENAME'] ) ? sanitize_text_field(wp_unslash( $_SERVER['SCRIPT_FILENAME'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Current Page URI', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field(wp_unslash( $_SERVER['REQUEST_URI'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Remote Address', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field(wp_unslash( $_SERVER['REMOTE_ADDR'] )) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Remote Host', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= VisitorLog_Server_Information_Handler::get_remote_host2();
		$HTML_body .= '</td></tr>';

		$HTML_body .= '<tr><td data-label="Name">';
		$HTML_body .= __( 'Remote Port', 'visitorlog' );
		$HTML_body .= '</td><td data-label="Required">';
		$HTML_body .= isset($_SERVER['REMOTE_PORT']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_PORT'])) : '';
		$HTML_body .= '</td></tr>';

		$HTML_body .= '</tbody></table></body></html>';

        return $HTML_body;

	} // END func 

} // END class