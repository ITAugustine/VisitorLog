<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods VisitorLog_Overview_view class
 * 
 * @method public static function render_server_info()
 */
class VisitorLog_Overview_view
{
    /**
     * Render server info.
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_System_Check::handler_system_check_common()
     * @uses VisitorLog_Utility::get_server()
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->debug()
     * @uses VisitorLog_Statistics_Regions::handler_ip_session()
     * @uses VisitorLog_System_View::get_html_text()
     */
    public static function render_server_info()
    {  
        global $wp_version;
        global $visitorlog_users_resident_parameters;

        if ( '' == VisitorLog_Utility::get_option_sl('check_sys_msg') ) {
            VisitorLog_System_Check::handler_system_check_common();
        }
        // ---------- Statistics of visits
        $current_time = $visitorlog_users_resident_parameters['datetime'];
        $current_stmp = $visitorlog_users_resident_parameters['timestamp'];

        //--------------Today Yesterday ...
        $сurrent_time_disp = date_i18n( 'd M, Y H:i', $current_stmp );
        $Today_users=$Today_admins=$Today_bots=$Today_block=$Today_total=0;

        $result = VisitorLog_Utility::get_data_table_nolimit( 'hour', $num_rows, $err );
        if ( $err ) {
            $msg_err = __('Error reading data from a database table', 'visitorlog');
            VisitorLog_System_View::show_message( 'orange', $msg_err, $msg_err.': '.$err );
        } else {
            foreach ($result as $res) {
                $Today_users  = $Today_users  + $res->user;
                $Today_admins = $Today_admins + $res->admin;
                $Today_bots   = $Today_bots   + $res->bot;
                $Today_block  = $Today_block  + $res->block;
                $Today_total  = $Today_users  + $Today_admins + $Today_bots;
            }
         }
        $current_day = substr($current_time, 8, 2); 

        // ---------- Server Info / Plugin Info

        $pluginData = get_plugin_data( VISITORLOG_PLUGIN_FILE, true, false );
        $plugin_name = $pluginData['Name'];
        $plugin_edition = VISITORLOG_PLUGIN_VERSION;

        $db_version = VISITORLOG_DB_VERSION;
        $warning = 0;

        $for_htaccess = VisitorLog_Utility::get_server( $server );
        $apache_or_litespeed = $for_htaccess == 'apache' || $for_htaccess == 'litespeed';
        if ( $apache_or_litespeed ) { 
            $htaccess_use = __('Use','visitorlog');
        } else {
            $htaccess_use = '';
        }
         //------------------ 1 Log & blocking of visitors 
        $warning_1 = 0;
        //------------------ 2 Admin panel protection 
        $warning_2 = 0;
        //-------------------- 3 DataBase protection 
        $old_db_prefix = VisitorLog_DB_Base::$wpdb_vl->prefix;
        $limit_days = 60*60*24*15; // 15 days
        $warning_3 = 0;
        $old_db_prefix_msg = '';
        $diff_time_msg = '';
        $last_time = VisitorLog_Utility::get_option_sl('last_backup_time');
        //------------------ 4 File system protection 
        $warning_4 = 0;
        //------------------ 5 Spam protection 
        $link_site = VisitorLog_Utility::$home_site;
        $warning_5 = 0;
        //------------------ 6 Firewall 
        $max_file_upload_size = VisitorLog_Utility::get_option_sl( 'max_file_upload_size' );
        $warning_6 = 0;
        $warning = $warning_1 + $warning_2 + $warning_3 + $warning_4 + $warning_5 + $warning_6;
        //------------------ Settings
        $check = VisitorLog_Security_Utility::check_file_config_debug();

        $check_debug         = $check['debug'];
        $check_debug_log     = $check['debug_log'];
        $check_debug_display = $check['debug_display'];
        ?> 
        <div class="block-header">
            <div class="row" >
                <font style="font-size:16px;">
                    <em>
                        <b>
                        <?php esc_html_e('Overview', 'visitorlog');?>,&nbsp;&nbsp;&nbsp;
                        </b>
                        <?php echo esc_html($сurrent_time_disp);?>
                    </em>
                </font>
            </div>
        </div>
        <!-------------------- Now on the site --------------------------------> 
        <div class="row clearfix" style="margin-top:-7px;">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td><?php esc_html_e('Now on the site','visitorlog');?></td>
                                <td>IP</td>
                                <td><?php esc_html_e('Object','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
        <?php
        $time_period = 3600; // 1h
        $array_now = VisitorLog_Statistics_Regions::handler_ip_session( $time_period );
        $number_rows = count( $array_now );

        $i = 0;
        while ( $i < $number_rows ) {
            $addr     = $array_now[$i]->user_ip;
            $category = $array_now[$i]->category;

            $class = VisitorLog_System_View::show_select_badge( $category ); 
        ?>
            <tr>
                <td><?php echo esc_html($i + 1);?></td>
                <td><?php echo esc_html($addr);?></td>  
                <td>
                    <span 
                        <?php VisitorLog_System_View::show_badge($category);?>><?php echo esc_html($category);?>
                    </span>
                </td>
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
        <!-------------------- Statistics of visits --------------------------------> 
        <div class="row clearfix" style="margin-top:-7px;">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td>
<?php esc_html_e('Statistics of visits','visitorlog');?>&ensp;
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_visitshour')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('more', 'visitorlog');?>" style="font-size:11px"><?php esc_html_e('more', 'visitorlog');?>...
</a>
                                </td>
                                <td><?php esc_html_e('Total','visitorlog');?></td>
                                <td><?php esc_html_e('Users','visitorlog');?></td>
                                <td><?php esc_html_e('Admins','visitorlog');?></td>
                                <td><?php esc_html_e('Bots','visitorlog');?></td>
                                <td><?php esc_html_e('Block','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td><?php esc_html_e('Today','visitorlog');?></td>
                                <td><?php echo esc_html($Today_total);?></td>
                                <td><?php echo esc_html($Today_users);?></td>
                                <td><?php echo esc_html($Today_admins);?></td>
                                <td><?php echo esc_html($Today_bots);?></td>
                                <td><?php echo esc_html($Today_block);?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div> 
        <!-------------------- Server info --------------------------------> 
        <div class="row clearfix" style="margin-top:-7px;">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td colspan="6">
<?php esc_html_e('Server info','visitorlog');?>&ensp;
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_serverinfo')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e( 'more', 'visitorlog' );?>" style="font-size:11px"><?php esc_html_e('more', 'visitorlog');?>...
</a>                                    
                                </td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td width="120"><?php esc_html_e('Server Software','visitorlog');?></td>
                                <td width="100"><?php echo esc_html($server);?></td>
                                <td width="120"><?php esc_html_e('Server Protocol','visitorlog');?></td>
                                <td width="100"><?php VisitorLog_Server_Information_Handler::get_server_protocol();?></td>
                                <td width="120"><?php esc_html_e('WordPress vers','visitorlog');?></td>
                                <td width="100"><?php echo esc_html($wp_version);?></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Architecture','visitorlog');?></td>
                                <td><?php VisitorLog_Server_Information_Handler::get_architecture();?></td>
                                <td><?php esc_html_e('Server Name','visitorlog');?></td>
                                <td><?php VisitorLog_Server_Information_Handler::get_server_name();?></td>
                                <td><?php esc_html_e('PHP Version','visitorlog');?></td>
                                <td><?php echo esc_html(phpversion());?></td>
                            </tr>
                            <tr>
                                <td><?php esc_html_e('Operating System','visitorlog');?></td>
                                <td><?php VisitorLog_Server_Information_Handler::get_os();?></td>
                                <td><?php esc_html_e('Server IP','visitorlog');?></td>
                                <td><?php VisitorLog_Server_Information_Handler::get_server_ip();?></td>
                                <td><?php esc_html_e('MySQL Version','visitorlog');?></td>
                                <td><?php echo esc_html(VisitorLog_DB_Base::instance()->get_my_sql_version());?></td>
                            </tr>
                            <tr>
                                <td width="120"><?php esc_html_e('Plugin Info','visitorlog');?></td>
                                <td width="100"><?php echo esc_html($plugin_name);?></td>
                                <td><?php esc_html_e('Plugin Edition','visitorlog');?></td>
                                <td><?php echo esc_html($plugin_edition);?></td>
                                <td><?php esc_html_e('Plugin Version','visitorlog');?></td>
                                <td>
                                    <?php
                                    VisitorLog_Utility::render_picture('nokey.png');
                                    echo esc_html('&nbsp;');
                                    esc_html_e('shortened', 'visitorlog');
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div> 
        <!-------------------- Security - Control panel --------------------------------> 
        <div class="row clearfix" style="margin-top:-20px;">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="header">
                    <font style="font-size:16px;">
                        <em>
                            <?php esc_html_e('Security','visitorlog');?>.&nbsp;
                            <?php esc_html_e('Control panel','visitorlog');?>
                        </em>
                    </font>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="100"><?php esc_html_e('On/Off','visitorlog');?></td>
                                <td width="380"><?php esc_html_e('Site protection functions','visitorlog');?></td>
                                <td>            <?php esc_html_e('Reports','visitorlog');?></td>
                                <td width="120">
                                                <?php esc_html_e('Warnings','visitorlog');?>
                                                <?php if( 0 != $warning ) echo (' : ' . esc_html($warning));?>
                                </td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>1</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                 </td>
                                <td>
<?php
$title = __( 'Log & blocking of visitors', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_blockvisitor' );
?>
                                </td>
                                <td>
<?php
$title = __( 'Blacklist Locked IPs', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_lockedaddr' );
?>
<br>
<?php
$title = __( 'Event 404', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_event404' );
?>
<br>
<?php
$title = __( 'Table of Bots', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_botstable' );
?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_admin_panel_protection' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td>
<?php
$title = __( 'Admin panel protection', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_adminpanel' );
?>
                                </td>
                                <td>
<?php
$title = __( 'Failed Logins', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_failedlogins' );
?>
<br>
<?php
$title = __( 'Account activity', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_accountactivity' );
?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_db_protection' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td>
<?php
$title = __( 'DataBase protection', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_dbprotection' );
?>
                                </td>
                                <td>
<?php
$title = __( 'Backups Report', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_backupreport' );
?>
                                </td>
                                <td>
                                    <?php if(0 != $warning_3) {
                                        esc_html_e('Warnings','visitorlog');
                                        echo (' : ' . esc_html($warning_3));
                                    }?>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_file_system_protection' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td>
<?php
$title = __( 'File system protection', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_fileprotection' );
?>
                                </td>
                                <td>
<?php
$title = __( 'File Permissions Report', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_filepermissions' );
?>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_spam_protection' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td>
<?php
$title = __( 'Spam Protection', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_spamprotection' );
?>
                                </td>
                                <td>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_firewall' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td>
<?php
$title = __( 'Firewall', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_firewall' );
?>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-------------------- 1 Log & blocking of visitors --------------------------------> 
                <div class="header">
                    <font style="font-size:16px;">
                        <em><?php esc_html_e('Security','visitorlog');?>
                            .&nbsp;&nbsp;1&nbsp;
                            <?php esc_html_e('Log & blocking of visitors','visitorlog');?>
                        </em>
                    </font>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="100"><?php esc_html_e('On/Off','visitorlog');?></td>
                                <td width="380"><?php esc_html_e('Functions','visitorlog');?></td>
                                <td><?php esc_html_e('Reports','visitorlog');?></td>
                                <td width="85">htaccess</td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>1.1</td>
                                <td></td>
                                <td><?php esc_html_e('Registration of DDoS attacks','visitorlog');?></td>
                                <td>
<?php
$title = __( 'Blacklist Locked IPs', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_lockedaddr' );
?>
&nbsp;(<?php esc_html_e('the lock is only manual, automatic in the full version','visitorlog');?>)
                                </td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>1.2</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_404_logging' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('404 Event Logging & Locking','visitorlog');?></td>
                                <td>
<?php
$title = __( 'Event 404', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_event404' );
?>
                                </td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>1.3</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Permanent blocking of IP addresses','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>1.4</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_block_ip_temp' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Temporary blocking of IP addresses','visitorlog');?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1.5</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'disable_rest_requests' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Disallow Unauthorized REST Requests','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>1.6</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_block_xmlrpc' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Block Access To XMLRPC','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>1.7</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'disable_xmlrpc_methods' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Disable Pingback Functionality From XMLRPC','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>1.8</td>
                                <td></td>
                                <td>
                                    <?php esc_html_e('Creating your own database of unwanted addresses', 'visitorlog');?>
                                </td>
                                <td>
<?php
$title = __( 'Table of Bots', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_botstable' );
?>
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-------------------- 2 Admin panel protection -------------------------------->
                <div class="header">
                    <font style="font-size:16px;">
                         <em><?php esc_html_e('Security','visitorlog');?>
                            .&nbsp;&nbsp;2&nbsp;
                            <?php esc_html_e('Admin panel protection','visitorlog');?>
                        </em>
                    </font>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="100"><?php esc_html_e('On/Off','visitorlog');?></td>
                                <td width="380"><?php esc_html_e('Functions','visitorlog');?></td>
                                <td><?php esc_html_e('Reports/Parameters','visitorlog');?></td>
                                <td width="85">htaccess</td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>2.1</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_login_lockdown' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Blocking an invalid username when logging in','visitorlog');?></td>
                                <td>
<?php
$title = __( 'Failed Logins', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_failedlogins' );
?>
                                </td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>2.2</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_login_captcha' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Captcha On Login Page','visitorlog');?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2.3</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_login_recaptcha' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Google reCAPTCHA On Login Page','visitorlog');?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2.4</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_forced_logout' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Force User Logout','visitorlog');?></td>
                                <td>
<?php
$logout_time_period = VisitorLog_Utility::get_option_sl( 'logout_time_period' );
switch ($logout_time_period) {
    case 60:
        esc_html_e('1 minute','visitorlog');
        break;
    case 120:
        esc_html_e('2 minutes','visitorlog');
        break;
    case 300:
        esc_html_e('5 minutes','visitorlog');
        break;
    case 600:
        esc_html_e('10 minutes','visitorlog');
        break;
    case 1800:
        esc_html_e('30 minutes','visitorlog');
        break;
    case 3600:
        esc_html_e('1 hour','visitorlog');
        break;
    case 7200:
        esc_html_e('2 hours','visitorlog');
        break;
    case 10800:
        esc_html_e('3 hours','visitorlog');
        break;
    case 21600:
        esc_html_e('6 hours','visitorlog');
        break;
    case 43200:
        esc_html_e('12 hours','visitorlog');
        break;
    case 86400:
        esc_html_e('1 day','visitorlog');
        break;
    case 172800:
        esc_html_e('2 days','visitorlog');
        break;
    case 259200:
        esc_html_e('3 days','visitorlog');
        break;
    case 604800:
        esc_html_e('7 days','visitorlog');
        break;
}
?>
<br>
<?php
$title = __( 'Account activity log', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_accountactivity' );
?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5">
<?php esc_html_e('Notes','visitorlog');?>:&ensp;
<?php esc_html_e('Additional functions for protecting the admin panel by applying a white list of IP addresses and renaming the login page are presented in the full version of the plugin','visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                </td>
                            </tr>    
                        </tbody>
                    </table>
                </div>
                <!-------------------- 3 DataBase protection -------------------------------->
                <div class="header">
                    <font style="font-size:16px;">
                         <em><?php esc_html_e('Security','visitorlog');?>
                            .&nbsp;&nbsp;3&nbsp;
                            <?php esc_html_e('DataBase protection','visitorlog');?>
                        </em>
                    </font>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="100"><?php esc_html_e('On/Off','visitorlog');?></td>
                                <td width="380"><?php esc_html_e('Functions','visitorlog');?></td>
                                <td><?php esc_html_e('Parameters','visitorlog');?></td>
                                <td><?php esc_html_e('Warnings','visitorlog');?>
                                    <?php if( 0 != $warning_3 ) echo (' : ' . esc_html($warning_3));?>
                                </td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>3.1</td>
                                <td></td>
                                <td><?php esc_html_e('Change Database Prefix','visitorlog');?></td>
                                <td><?php esc_html_e('Current DB Table Prefix','visitorlog');?>:&nbsp;
                                    <strong style="font-size:16px;">
                                        <?php echo esc_html($old_db_prefix);?>
                                    </strong>
                                </td>
                                <td>
<?php
if ( 'wp_' == $old_db_prefix ) {
    echo '<font style="background-color:yellow">';
    esc_html_e('DB prefix is better to change','visitorlog');
    echo '</font>';    
    $warning_3++;
}
?>
                                </td>
                            </tr>
                            <tr>
                                <td>3.2</td>
                                <td></td>
                                <td><?php esc_html_e('Manual database backup','visitorlog');?>
                                    <br>
<?php
$title = __( 'Backups Report', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_backupreport' );
?>
                                </td>
                                <td>
<?php
    if ( '' == $last_time ) {
        esc_html_e('DB backups have never been performed','visitorlog');
    } else {
        $date_i18n = date_i18n( 'Y-m-d H:i:s', $last_time );
        $diff_time_int = $current_stmp - $last_time;
        $diff_time  =  esc_html__('Last backup','visitorlog') . ':<br>';
        $diff_time .= $date_i18n . '<br>';          
        $diff_time .= human_time_diff( 0, $diff_time_int );

        esc_html_e('Last backup','visitorlog');
        echo ':<br>';
        echo esc_html($date_i18n);
        echo '<br>';          
        echo esc_html(human_time_diff(0, $diff_time_int));
    }
?>
                                </td>
                                <td>
<?php
if ( '' == $last_time ) {
    echo '<font style="background-color:yellow">';
    esc_html_e('Be nice to create a backup db','visitorlog');
    echo '</font>';
    $warning_3++;
} else {
    $diff_time_int = $current_stmp - $last_time;
    if ( $diff_time_int > $limit_days ) {
        echo '<font style="background-color:yellow">';
        esc_html_e('Be nice to create a backup db','visitorlog');
        echo '</font>';
        $warning_3++;
    }
}
?>
                                </td>
                            </tr>
                            <tr>
                                <td>3.3</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_auto_backups' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Auto db backups','visitorlog');?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>3.4</td>
                                <td></td>
                                <td><?php esc_html_e('Backup period','visitorlog');?></td>
                                <td>
<?php
$backups_period = VisitorLog_Utility::get_option_sl( 'backups_period' );
switch ( $backups_period ) {
    case 1:
        esc_html_e('Monthly','visitorlog');   break;
    case 2:
        esc_html_e('Weekly','visitorlog');    break;
    case 3:
        esc_html_e('Daily','visitorlog');     break;
    case 4:
        esc_html_e('5minutely','visitorlog'); break;
    case '':
        esc_html_e('Monthly','visitorlog');   break;
}
?>
                                </td>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>3.5</td>
                                <td></td>
                                <td><?php esc_html_e('Number backup instances','visitorlog');?></td>
                                <td>
<?php
echo esc_html(VisitorLog_Utility::get_option_sl('number_backup_instances'));
?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>3.6</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_input_email_manual' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Sending backup to email','visitorlog');?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-------------------- 4 File system protection --------------------------------> 
                <div class="header">
                    <font style="font-size:16px;">
                         <em><?php esc_html_e('Security','visitorlog');?>
                            .&nbsp;&nbsp;4&nbsp;
                            <?php esc_html_e('File system protection','visitorlog');?>
                        </em>
                    </font>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="100"><?php esc_html_e('On/Off','visitorlog');?></td>
                                <td width="380"><?php esc_html_e('Functions','visitorlog');?></td>
                                <td><?php esc_html_e('Parameters','visitorlog');?></td>
                                <td width="85">htaccess</td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>4.1</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'disable_file_edit' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Disable Ability To Edit PHP Files','visitorlog');?></td>
                                <td></td>
                                <td>wp config</td>
                            </tr>
                            <tr>
                                <td>4.2</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_prevent_wp_file_access' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Prevent wp files access','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>4.3</td>
                                <td></td>
                                <td>
<?php
$title = __( 'Check the WP core folder and file access settings', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_filepermissions' );
?>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5">
<?php esc_html_e('Notes','visitorlog');?>:&ensp;
<?php esc_html_e('Additional functions to protect system files and folders by scanning and monitoring against unauthorized changes are provided in the full version of the plugin','visitorlog');?>
&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-------------------- 5 SPAM Protection --------------------------------> 
                <div class="header">
                    <font style="font-size:16px;">
                         <em><?php esc_html_e('Security','visitorlog');?>
                            .&nbsp;&nbsp;5&nbsp;
                            <?php esc_html_e('Spam Protection','visitorlog');?>
                        </em>
                    </font>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="100"><?php esc_html_e('On/Off','visitorlog');?></td>
                                <td width="380"><?php esc_html_e('Functions','visitorlog');?></td>
                                <td><?php esc_html_e('Parameters','visitorlog');?></td>
                                <td width="85">htaccess</td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">  
                            <tr>
                                <td>5.1</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_forbid_commentspost' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Algorithm','visitorlog');?>&nbsp;1.&nbsp;
<?php esc_html_e('Blocking a spammer when it is detected by WordPress', 'visitorlog');?>
                                </td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>5.2</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_spambot_blocking' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Algorithm','visitorlog');?>&nbsp;2.&nbsp;
<?php esc_html_e('Forbidding access to the file wp-comments-post.php', 'visitorlog');?>
                                </td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>5.3</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'forbid_proxy_comments' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Forbid Proxy Comment Posting', 'visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>5.4</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_comment_captcha' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Captcha On Comment Forms','visitorlog');?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>5.5</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_comment_recaptcha' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Google reCAPTCHA On Comment Forms','visitorlog');?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>5.6</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_block_spam_perm' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Permanent blocking of IP addresses','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>5.7</td>
                                <td></td>
                                <td>
<?php
$title = __( 'Spam report', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_spam' );
?>
                                </td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td colspan="5">
<?php esc_html_e('Notes','visitorlog');?>:&ensp;
<?php esc_html_e('More effective spam protection in comments and feedback forms using shortcodes is provided in the full version of the plugin','visitorlog');?>
&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-------------------- 6 Firewall --------------------------------> 
                <div class="header">
                    <font style="font-size:16px;">
                         <em><?php esc_html_e('Security','visitorlog');?>
                            .&nbsp;&nbsp;6&nbsp;
                            <?php esc_html_e('Firewall','visitorlog');?>
                        </em>
                    </font>
                </div>
                <div class="body table-responsive">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="100"><?php esc_html_e('On/Off','visitorlog');?></td>
                                <td width="380"><?php esc_html_e('Functions','visitorlog');?></td>
                                <td><?php esc_html_e('Parameters','visitorlog');?></td>
                                <td width="85">htaccess</td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>6.1</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_firewall_g7' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
}
?>
                                </td>
                                <td><?php esc_html_e('Firewall G7', 'visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.2</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_firewall_g6' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Firewall G6', 'visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.3</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_protect_wp_config' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Protect the wp-config.php file', 'visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.4</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'disable_server_signature' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Disable the server signature', 'visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.5</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_max_file_upload_size' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Max File Upload Size (MB)','visitorlog');?></td>
                                <td><?php echo esc_html($max_file_upload_size);?></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.6</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'disable_index_views' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Disable Index Views','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.7</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'on_prevent_hotlinking' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Prevent Image Hotlinking','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.8</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'disable_trace_and_track' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
 ?>
                                </td>
                                <td><?php esc_html_e('Disable Trace and Track','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.9</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'deny_bad_query_strings' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                         
                                </td>
                                <td><?php esc_html_e('Deny Bad Query Strings','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                            <tr>
                                <td>6.10</td>
                                <td>
<?php
if ( '' == VisitorLog_Utility::get_option_sl( 'advanced_char_string_filter' ) ) {
    VisitorLog_System_View::get_badge('off');
} else {
    VisitorLog_System_View::get_badge('on');
} 
?>
                                </td>
                                <td><?php esc_html_e('Enable Advanced Character String Filter','visitorlog');?></td>
                                <td></td>
                                <td><?php echo esc_html($htaccess_use);?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-------------------- Reports, Tables, Logs--------------------->
        <div class="row clearfix" id="reports">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="header">
                    <font style="font-size:18px;">
                        <em><?php esc_html_e('Reports, Tables, Logs','visitorlog');?></em>
                    </font>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="250"><?php esc_html_e('Name','visitorlog');?></td>
                                <td>            <?php esc_html_e('Description','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>1</td>
                                <td><?php esc_html_e('Reports', 'visitorlog');?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>1.1</td>
                                <td>
<?php
$title = __( 'Backups Report', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_backupreport' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'The report on the creation of database backups contains the date and time of the creation of the report, automatic or manual mode, sending a copy of the DB to email.', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>1.2</td>
                                <td>
<?php
$title = __( 'Server info', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_serverinfo' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'Information about the server, DB, PHP, WP, plugins', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>1.3</td>
                                <td>
<?php
$title = __( 'File Permissions Scan Report', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_filepermissions' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'This feature will scan the critical WP core folders and files and will highlight any permission settings which are insecure.', 'visitorlog' );?>&ensp;
<?php esc_html_e( 'There are no recommendations for Windows OS.', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>1.4</td>
                                <td>
<?php
$title = __( 'Updates', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_updates' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'Report on updates of this plugin. Includes the update date, the latest version of the plugin and the plugin tables.', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><?php esc_html_e( 'Tables', 'visitorlog' );?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>2.1</td>
                                <td>
<?php
$title = __( 'Blacklist Locked IPs', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_lockedaddr' );
?>
                                </td>
                                <td>
<?php esc_html_e('The data is entered into the table automatically from the DDoS protection module, as well as manually by the site administrator','visitorlog');?>.&nbsp;
<?php esc_html_e('IP addresses are not automatically blocked, this is implemented in the full version of the plugin','visitorlog');?>.&nbsp;
<?php esc_html_e('The administrator can add a new IP address, delete, permanently block one or a group of addresses','visitorlog');?>.
                                </td>
                            </tr>
                            <tr>
                                <td>2.2</td>
                                <td>
<?php
$title = __( 'Failed Logins', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_failedlogins' );
?>
                                </td><td>
<?php esc_html_e( 'Contains data from users who have made unsuccessful attempts to log in to the site\'s admin panel', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>2.3</td>
                                <td>
<?php
$title = __( 'Spam', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_spam' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'The plugin enters data about spam in the comments in the table', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>2.4</td>
                                <td>
<?php
$title = __( 'Event 404', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_event404' );
?>
                                </td><td>
<?php esc_html_e( 'Contains the data of users who tried to access non-existent pages of the site and the 404 error occurred', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>2.5</td>
                                <td>
<?php
$title = __( 'Table of Bots', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_botstable' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'Creating your own database of unwanted addresses', 'visitorlog' );?>.&ensp;
<?php esc_html_e( 'The administrator can manually add, delete, edit table entries, as well as block IP addresses in the htaccess file', 'visitorlog' );?>.&ensp;
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td><?php esc_html_e( 'Logs', 'visitorlog' );?></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>3.1</td>
                                <td>
<?php
$title = __( 'Visitor Log', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_visitorlog' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'The log of registration of site visitors. Contains the date of the visit, IP address, visitor type(Admin, User, Bot, WP, Cron), receipt channel(get, post, user, authorization), URL, Referer info.', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>3.2</td>
                                <td>
<?php
$title = __( 'Administrators', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_administrators' );
?>
                                </td><td>
<?php esc_html_e( 'The table contains the IP addresses of the site administrators', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>3.3</td>
                                <td>
<?php
$title = __( 'Account activity log', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_accountactivity' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'Log of session opening and closing for administrators. Contains the ID and IP of the administrators.', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>3.4</td>
                                <td>
<?php
$title = __( 'Action Log', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_actionlog' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'The log of registration of active actions. Operations are made by this plugin.', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>3.5</td>
                                <td>
<?php
$title = __( 'Error Log', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_errorlog' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'The log contains system errors that occur during the operation of the site, as well as the date and time of their occurrence in UTF format', 'visitorlog' );?>.&ensp;
<?php esc_html_e( 'Uses php.ini', 'visitorlog' );?>.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
        <!-------------------- Statistics -------------------------------->
        <div style="margin-top:-20px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="header">
                    <font style="font-size:18px;">
                        <em><?php esc_html_e('Statistics','visitorlog');?></em>
                    </font>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="250"><?php esc_html_e('Name','visitorlog');?></td>
                                <td><?php esc_html_e('Description','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>1</td>
                                <td>
<?php
$title = __( 'Report on the current day\'s visits', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_visitshour' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'A report in the form of a table and a graph of visits by the hour in the context of administrators, users, bots', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
<?php esc_html_e('Notes','visitorlog');?>:&ensp;
<?php esc_html_e('Additional statistics functions in the form of tables and graphs for the current month, year, by region, summary report are presented in the full version of the plugin','visitorlog');?>
&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                </td>
                            </tr> 
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
        <!-------------------- Parameters settings -------------------------------->
        <div style="margin-top:-20px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="header">
                    <font style="font-size:18px;">
                        <em><?php esc_html_e('Parameters settings','visitorlog');?></em>
                    </font>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="250"><?php esc_html_e('Name','visitorlog');?></td>
                                <td><?php esc_html_e('Description','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview"> 
                            <tr>
                                <td>1</td>
                                <td>
<?php
$title = __( 'Parameters settings', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_settings' );
?>
                                </td>
                                <td>
                                    <?php esc_html_e( 'Parameters settings', 'visitorlog' );?>.
                                </td>
                            </tr>
                            <tr>
                                <td>1.1</td>
                                <td>
<?php
if ( 'true' == $check_debug ) {
    VisitorLog_System_View::get_badge('on');
} else {
    VisitorLog_System_View::get_badge('off');
} 
?>
&nbsp;WP_DEBUG
                                </td>
                                <td>
<?php esc_html_e('When you enable define( \'WP_DEBUG\', true ), all error levels will be included in the error report', 'visitorlog');?>
                                </td>
                            </tr>
                            <tr>
                                <td>1.2</td>
                                <td>
<?php
if ( 'true' == $check_debug_display ) {
    VisitorLog_System_View::get_badge('on');
} else {
    VisitorLog_System_View::get_badge('off');
} 
?>
&nbsp;WP_DEBUG_DISPLAY
                                </td>
                                <td>
<?php esc_html_e('When define( \'WP_DEBUG\', true ) is enabled, all errors will be displayed because WP_DEBUG_DISPLAY = true by default', 'visitorlog');?>
                                </td>
                            </tr>
                            <tr>
                                <td>1.3</td>
                                <td>
<?php
if ( 'true' == $check_debug_log ) {
    VisitorLog_System_View::get_badge('on');
} else {
    VisitorLog_System_View::get_badge('off');
} 
?>
&nbsp;WP_DEBUG_LOG
                                </td>
                                <td>
<?php esc_html_e('With define enabled (\'WP_DEBUG\', true ), errors will be logged to the log file specified in php.ini, because WP_DEBUG_LOG = false by default', 'visitorlog');?> 
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
        <!-------------------- Cron -------------------------------->
        <div style="margin-top:-20px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="header">
                    <font style="font-size:18px;">
                        <em>Cron</em>
                    </font>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="250"><?php esc_html_e('Name','visitorlog');?></td>
                                <td><?php esc_html_e('Description','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">  
                            <tr>
                                <td>1</td>
                                <td>
<?php
$title = __( 'Scheduled tasks', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_schedultasks' );
?>
                                </td>
                                <td>
                                    <?php esc_html_e( 'Report on scheduled tasks.', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
<?php
$title = __( 'Cron activity test in WordPress', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_crontest' );
?>
                                </td>
                                <td>
                                    <?php esc_html_e( 'Cron activity test in WordPress', 'visitorlog' );?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
        <!-------------------- Other Features -------------------------------->
        <div style="margin-top:-20px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="header">
                    <font style="font-size:18px;">
                        <em><?php esc_html_e('Other Features','visitorlog');?></em>
                    </font>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="250"><?php esc_html_e('Name','visitorlog');?></td>
                                <td><?php esc_html_e('Description','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">  
                            <tr>
                                <td>1</td>
                                <td>
<?php
$title = __( 'Maintenance', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_maintenance' );
?>
                                </td>
                                <td>
                                    <?php esc_html_e( 'Maintenance', 'visitorlog' );?>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>
<?php
$title = __( 'Hide notifications', 'visitorlog' );
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_hidenotifications' );
?>
                                </td>
                                <td>
                                    <?php esc_html_e('Hiding notifications of new WordPress version in admin panel', 'visitorlog');?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
        <!-------------------- Guide -------------------------------->
        <div style="margin-top:-20px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="header">
                    <font style="font-size:18px;">
                        <em><?php esc_html_e('Guide','visitorlog');?></em>
                    </font>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-bordered">
                        <thead class="vl-color-thead-overview">
                            <tr>
                                <td width="50">#</td>
                                <td width="250"><?php esc_html_e('Name','visitorlog');?></td>
                                <td><?php esc_html_e('Description','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody class="vl-color-tbody-overview">  
                            <tr>
                                <td>1</td>
                                <td>
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_guide' );
?>
                                </td>
                                <td>
<?php esc_html_e( 'A more detailed description of all the functions of the plugin', 'visitorlog' );?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div>
        <?php

    } // END func

} // END class