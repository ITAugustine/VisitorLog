<?php
/**
 * Security DB
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security_DB class
 * 
 * @method public static function instance() 
 * @method public static function database_protection()
 * @method public        function change_db_prefix()
 * @method public        function get_mysql_tables()
 * @method public static function send_email_manual()
 */
class VisitorLog_Security_DB
{
    public static $feature_db_prefix;
    public static $feature_db_backup;
    public static $instance = null;

    public static function instance()
    {
        if ( null == self::$instance ) self::$instance = new self;
        return self::$instance;
    }

    /**
     * Render Database protection.
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility_File::get_wp_config_file_path()
     * @uses VisitorLog_Utility_File::is_writable()
     * @uses VisitorLog_Backup_Handler::execute_backup()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Logger::instance()->log_backup()
     */
    public static function database_protection()
    {
        $old_db_prefix = VisitorLog_DB_Base::$wpdb_vl->prefix;
        $new_db_prefix = '';
        $n1=$n2= '';
        $period1=$period2=$period3=$period4='';
        $debug = '';

        $on_db_protection        = VisitorLog_Utility::get_option_sl( 'on_db_protection' );
        $on_auto_backups         = VisitorLog_Utility::get_option_sl( 'on_auto_backups' );
        $backups_period          = VisitorLog_Utility::get_option_sl( 'backups_period' );
        $number_backup_instances = VisitorLog_Utility::get_option_sl( 'number_backup_instances' );
        $on_input_email_manual   = VisitorLog_Utility::get_option_sl( 'on_input_email_manual' );

        if ( isset($_POST['visitorlog_change_db_prefix'])    || 
             isset($_POST['visitorlog_start_backup_manual']) ||
             isset($_POST['visitorlog_save_backup_auto']) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            if ( '' == $on_db_protection ) {
               $err = __( 'Turn on the database protection feature on the control panel', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err );
            } else {
		        //---------------------------------------------------- DB prefix
		        if ( isset($_POST['visitorlog_change_db_prefix']) ) {

                    if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }

                    $msg1  = __( 'Before starting the automatic database prefix change, make sure once again that you have the option to manually restore the system file wp-config.php and the database:', 'visitorlog' );
                    $msg1 .= '\n- ';
                    $msg1 .= __( 'you have access to the system files and folders on the server', 'visitorlog' );
                    $msg1 .= '\n- ';
                    $msg1 .= __( 'you have access to the database through PHPMYADMIN', 'visitorlog' );
                    $msg1 .= '\n- ';
                    $msg1 .= __( 'you have made a copy of the database and the file wp-config.php', 'visitorlog' );
                    $msg1 .= '\n';
                    $msg1 .= __( 'The plugin must have write permissions to the file wp-config.php and to the wp-content folder.', 'visitorlog' );
                    $msg1 .= ' ';
                    $msg1 .= __( 'Read the plugin documentation on this topic.', 'visitorlog' );
                    $msg1 .= ' ';
                    $msg1 .= __( 'Shall we begin?', 'visitorlog' );

                    VisitorLog_Utility::vl_confirm( $msg1, 'visitorlog_dbprotection' );

	                $config_file = VisitorLog_Utility_File::get_wp_config_file_path();
	                $file_write  = VisitorLog_Utility_File::is_writable( $config_file );

	                if ( !$file_write ) {
                        VisitorLog_System_Check::update_data_of_system_check( 20, '' );
	                    $err = __( 'The plugin has found that it cannot write to the file wp-config.php to change the prefix. The plugin may not have enough permissions to access this file.', 'visitorlog' );
                        VisitorLog_Utility::vl_alert( $err, 'visitorlog_dbprotection' );
                        exit;
	                } else {
                        VisitorLog_System_Check::update_data_of_system_check( 20, 1 );
	                    if ( empty($_POST['visitorlog_input_table_prefix']) ) {
	                       $err = __( 'Please enter a value for the DB prefix', 'visitorlog' );
	                        VisitorLog_System_View::show_message( 'yellow', $err );
                            VisitorLog_Utility::update_option_sl( 'temp_value', 'yellow' );
                            VisitorLog_Utility::update_option_sl( 'temp_message', $err );
                            VisitorLog_Utility::vl_redirect( 'visitorlog_dbprotection' );
                            exit;
	                    } else {
                            if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                                $new_db_prefix = trim(sanitize_text_field(wp_unslash($_POST['visitorlog_input_table_prefix'])));
                            } else {    
                                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                                VisitorLog_System_View::show_message( 'orange', $err, $err );
                                wp_die();
                            }
	                        $new_db_prefix = $new_db_prefix . '_';
	                        $error = VisitorLog_DB_Base::$wpdb_vl->set_prefix( $new_db_prefix ); //validate the user chosen prefix
	                        if ( is_wp_error($error) ) {
                                echo '<strong>ERROR</strong>:&nbsp;';
	                            wp_die( esc_html__('The table prefix can only contain numbers, letters, and underscores', 'visitorlog') );
	                        }
	                        VisitorLog_DB_Base::$wpdb_vl->set_prefix( $old_db_prefix );
                            $result = self::instance()->change_db_prefix( $old_db_prefix, $new_db_prefix ); 
                            if ( false === $result ) {
                               $err = __( 'The DB prefix cannot be changed programmatically. Try to do it manually. The steps can be found in the plugin documentation.', 'visitorlog' );
                                VisitorLog_System_View::show_message( 'orange', $err );
                            } elseif ( '' != $result ) {
                                $err = __( 'The DB prefix cannot be changed programmatically. Try to do it manually. The steps can be found in the plugin documentation.', 'visitorlog' );
                                VisitorLog_System_View::show_message( 'yellow', $err );
                            }    
                            $msg = __( 'The plugin has been stopped. Reload the page.', 'visitorlog' );
                            VisitorLog_System_View::show_message( 'blue', $msg );
                            exit;
	                    }
	                }    
		        }
		        //---------------------------------------------------- DB backup manual
		        if ( isset($_POST['visitorlog_start_backup_manual']) ) {

                    if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                    $result_file = $result_mail = '';
			        $res = VisitorLog_Backup_Handler::execute_backup( $attachment );

                    if ( $res < 0 ) {
                        $result_file = -1;
                        switch ($res) {
                            case -2:
                                $msg = __('Error create dir', 'visitorlog');
                                break;
                            case -3:
                                $msg = __('Error create dir of multisite', 'visitorlog');
                                break;
                            case -4:
                                $msg = __('Error delete backup files', 'visitorlog');
                                break;
                            case -5:
                                $msg = __('Error create db backup body', 'visitorlog');
                                break;
                            case -6:
                                $msg = __('Error put contents', 'visitorlog');
                                break;
                            case -7:
                                $msg = __('Error delete file', 'visitorlog');
                                break;
                            default:
                                $msg = __('Error creating a backup file', 'visitorlog');
                                break;
                        }
                        $msg .= '. ';
                        $msg .= __('Check the plugin\'s rights to write to the wp-content folder', 'visitorlog');
                        $msg .= '.';
                        VisitorLog_System_View::show_message( 'blue', $msg );
                    } else {
                        $result_file = 1;
                        $current_time = current_time( 'timestamp' );
                        VisitorLog_Utility::update_option_sl( 'last_backup_time', $current_time );
                        $msg = __('Successful creation of the backup file', 'visitorlog');
                        VisitorLog_System_View::show_message( 'green', $msg ); 
                        if ( 'on' == $on_input_email_manual ) {

                            $result = self::send_email_manual( $attachment );

                            if ( $result[0] == 1 ) {
                                $msg = __('The message with the report file has been sent successfully', 'visitorlog');
                                $color = 'green';
                                $result_mail = 1;
                            } 
                            elseif ( $result[0] == -3 ) {
                                $msg = __('An error occurred when sending mail. Check the error log', 'visitorlog');
                                $color = 'blue';
                                $result_mail = -1;
                            } 
                            VisitorLog_System_View::show_message( $color, $msg, $msg );
                            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
                            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
                            VisitorLog_Logger::instance()->log_backup( $result_file, $result_mail, 'manual' );
                            VisitorLog_Utility::vl_redirect( 'visitorlog_dbprotection' );
                            exit;
                        } else {
                            $msg = __('Enable the permission to send a message by mail', 'visitorlog');
                        }
                        VisitorLog_System_View::show_message( 'blue', $msg, $msg );
                        VisitorLog_Logger::instance()->log_backup( $result_file, $result_mail, 'manual' );
                    } 
		        }
		        //---------------------------------------------------- DB backup auto
		        if ( isset ($_POST['visitorlog_save_backup_auto']) ) {

                    if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }

                    if ( isset($_POST['visitorlog_on_auto_backups']) ) {

                        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                            $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_auto_backups']));
                        } else {
                            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                            VisitorLog_System_View::show_message( 'orange', $err, $err );
                            wp_die();
                        }
                    } else {
                        $post = ''; 
                    }
                    if ( $on_auto_backups != $post ) {
                        VisitorLog_Utility::update_option_sl( 'on_auto_backups', $post );
                        $on_auto_backups = $post;
                    }

                    if ( isset($_POST['visitorlog_on_input_email_manual']) ) {

                        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                            $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_input_email_manual']));
                        } else {
                            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                            VisitorLog_System_View::show_message( 'orange', $err, $err );
                            wp_die();
                        }
                    } else {
                        $post = ''; 
                    }
                    if ( $on_input_email_manual != $post ) {
                        VisitorLog_Utility::update_option_sl( 'on_input_email_manual', $post );
                        $on_input_email_manual = $post;
                    }

                    if ( isset($_POST['visitorlog_backup_instances']) ) {

                        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                            $post = sanitize_text_field(wp_unslash($_POST['visitorlog_backup_instances']));
                        } else {
                            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                            VisitorLog_System_View::show_message( 'orange', $err, $err );
                            wp_die();
                        }
                    } else {
                        $post = ''; 
                    }
                    if ( $number_backup_instances != $post ) {
                        VisitorLog_Utility::update_option_sl( 'number_backup_instances', $post );
                        $number_backup_instances = $post;
                    }

                    if ( isset($_POST['visitorlog_backup_period']) ) {

                        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                            $post = sanitize_text_field(wp_unslash($_POST['visitorlog_backup_period']));
                        } else {
                            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                            VisitorLog_System_View::show_message( 'orange', $err, $err );
                            wp_die();
                        }
                    } else {
                        $post = ''; 
                    }
                    if ( $backups_period != $post ) {
                        VisitorLog_Utility::update_option_sl( 'backups_period', $post );
                        $backups_period = $post;
                        $sched1 = wp_next_scheduled( 'visitorlog_cron_backups_action' );
                        wp_unschedule_event( $sched1, 'visitorlog_cron_backups_action' );
                    }
		        }	
			}
        }
        //--------------------- Display
        switch ( $backups_period ) {
            case '1':
                $period1 = 'selected';
                break;
            case '2':
                $period2 = 'selected';
                break;
            case '3':
                $period3 = 'selected';
                break;
             case '4':
                $period4 = 'selected';
                break;
            default:
                $period1 = 'selected';
                break; 
        }
        switch ( $number_backup_instances ) {
            case '1':
                $n1 = 'selected';
                break;
            case '2':
                $n2 = 'selected';
                break;
            default:
                $n1 = 'selected';
                break;
        }
        switch ( $on_input_email_manual ) {
            case 'on':
                $eiem = 'checked';
                break;
            default:
                $eiem = '';
                break;
        }
        switch ( $on_auto_backups ) {
            case 'on':
                $eab = 'checked';
                break;
            default:
                $eab = '';
                break;
        }
        if ( 'on' == $on_db_protection ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg  = __( 'The panel with database protection functions is disabled!', 'visitorlog' );
            $msg .= '<br>';
            $msg .= __( 'Activate the functions on the control panel.', 'visitorlog' );
            VisitorLog_System_View::show_message( 'yellow', $msg );
        } 
        $temp_message = VisitorLog_Utility::get_option_sl( 'temp_message' );
        if ( '' != $temp_message ) {
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_System_View::show_message( $color, $temp_message );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
        }
        ?>  
        <form method="post">
        <?php wp_nonce_field('visitorlog_nonce');?>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="visitorlog_checkbox_select" value="on">
                                            &ensp;
                                            <?php esc_html_e('Enable/Disable all','visitorlog');?>
                                            &emsp;&vellip;&emsp;
<?php  
if ( '' != $icon ) {
?>
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_controlpanel')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>" title="<?php esc_html_e('This panel with functions is disabled', 'visitorlog');?>!">
    <?php VisitorLog_Utility::render_picture('remove.png');?>&nbsp;
    <?php esc_html_e('This panel with functions is disabled','visitorlog');?>!
</a>
                                            &emsp;&vellip;&emsp;
<?php
}
$title = __('Backups Report', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_backupreport' );
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
        <div style="margin-top:-13px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead class="vl-color-thead">
                                    <tr>
                                        <td width="62"> <?php esc_html_e('Enable','visitorlog');?></td>
                                        <td width="200"><?php esc_html_e('Functions',  'visitorlog');?></td>
                                        <td width="222"><?php esc_html_e('Parameters',  'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
                                    <tr>    
                                        <td colspan="4">
                                            <em>
                                                <span style="font-size:16px;">
                                                    <?php esc_html_e('Change Database Prefix', 'visitorlog');?>
                                                </span>
                                            </em>
                                        </td>
                                    </tr>    
                                    <tr>
                                        <td></td>
                                        <td>
                                            <?php esc_html_e('Current DB Table Prefix','visitorlog');?>:
                                            <br>
                                            <?php esc_html_e('New DB Table Prefix','visitorlog');?>:
                                        </td>
                                        <td>
                                            <p>
                                                <strong style="font-size:16px;">
                                                    <?php echo esc_html($old_db_prefix);?>
                                                </strong>
                                            </p>
                                            <div style="margin-top:-7px;">
<input type="text" size="15" name="visitorlog_input_table_prefix" placeholder="<?php esc_html_e('Example: secure','visitorlog');?>"/>
                                                <strong style="font-size:16px;"> _</strong>
                                            </div>    
                                        </td>                                        
                                        <td>
<?php esc_html_e('Choose your own DB prefix by specifying a string which contains letters and/or numbers','visitorlog');?>.<br>
<?php esc_html_e('Create a backup copy of the database and file before using this feature wp-config.php','visitorlog');?>!
<?php esc_html_e('Read the description of this process in the plugin documentation.','visitorlog');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_change_db_prefix" class="btn btn__primary" value="<?php esc_html_e('Change DB Prefix', 'visitorlog'); ?>" style="font-size:13px;"/>
                            </div>
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
                            <table class="table table-bordered table-hover dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>    
                                        <td colspan="4">
                                            <em>
                                                <span style="font-size:16px;">
                                                    <?php esc_html_e('Database Backups', 'visitorlog');?>
                                                </span>
                                            </em>
                                        </td>
                                    </tr>
                                    <tr>    
                                        <td colspan="4">
                                            1.&nbsp;
                                            <?php esc_html_e('Manual Database Backups', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>    
                                        <td colspan="4">
<?php esc_html_e('Click on the "Start Backup" button and create a backup copy of the database', 'visitorlog');?>
                                            .&nbsp;
<?php esc_html_e('The backup file with the zip extension will be placed in the folder', 'visitorlog');?>
                                            :&nbsp;
                                            <code>wp-content/uploads/visitorlog</code>
                                            <br>
<?php esc_html_e('The plugin must have write permissions set to this folder.', 'visitorlog');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_start_backup_manual" class="btn btn__primary" value="<?php esc_html_e('Start Backup', 'visitorlog');?>"/>
                            </div>
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
                            <table class="table table-bordered table-hover dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>    
                                        <td colspan="4">
                                            2.&nbsp;
                                            <?php esc_html_e('Auto Database Backups', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="62">
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_auto_backups" name="visitorlog_on_auto_backups" type="checkbox" value="on" <?php echo esc_html($eab);?>>
                                                    <label for="visitorlog_on_auto_backups"></label>
                                                </div>
                                            </div>
                                        </td>    
                                        <td colspan="3">
                            <?php esc_html_e('Enable automatic scheduled database backup','visitorlog');?>
                                            .&nbsp;
                            <?php esc_html_e('The report file will be located in the directory', 'visitorlog');?>
                                            :&nbsp;
                                            <code>wp-content/uploads/visitorlog</code>
                                            <br>
<?php esc_html_e('The plugin must have write permissions set to this folder.', 'visitorlog');?>
                                        </td>
                                    </tr>    
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Backup period','visitorlog');?>:
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                                <select name="visitorlog_backup_period">
<option value="1" <?php echo esc_html($period1);?>><?php esc_html_e('Monthly','visitorlog');?></option>
<option value="2" <?php echo esc_html($period2);?>><?php esc_html_e('Weekly','visitorlog');?></option>
<option value="3" <?php echo esc_html($period3);?>><?php esc_html_e('Daily','visitorlog');?></option>
<?php if ( $debug ) { ?>
<option value="4" <?php echo esc_html($period4);?>><?php esc_html_e('5minutely','visitorlog');?></option>
<?php } ?>
                                                </select>
                                            </label> 
                                        </td>
                                        <td>
<?php esc_html_e('Set the value for how often you would like an automated backup to occur','visitorlog');?>
                                        </td>
                                    </tr>   
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <?php esc_html_e('Number backup instances','visitorlog');?>:
                                        </td>
                                        <td>
                                            <label class="form__input1">
                                                <select name="visitorlog_backup_instances">
                                                    <option value="1" <?php echo esc_html($n1);?>>1</option>
                                                    <option value="2" <?php echo esc_html($n2);?>>2</option>
                                                </select>
                                            </label> 
                                        </td>
                                        <td>
<?php esc_html_e('Select the number of backup files that you would like to additionally save in the backup directory. By default, 0, it means that only one copy file of the database will be stored','visitorlog');?>
                                        </td>
                                    </tr>    
                                    <tr>    
                                        <td colspan="4">
                                            3.&nbsp;
                                            <?php esc_html_e('Sending backup to email', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="62">
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_input_email_manual" name="visitorlog_on_input_email_manual" type="checkbox" value="on" <?php echo esc_html($eiem);?>>
                                                    <label for="visitorlog_on_input_email_manual"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td colspan="3">
                            <?php esc_html_e('Enable sending backup to email','visitorlog');?>
                                            .&nbsp;
<?php esc_html_e('The email settings are set in the Settings section', 'visitorlog');?> 
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div style="margin-top:10px;"></div>
                    <div class="body">
                        <div class="table-responsive">
                            <div>
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=db_protect' );
?>
                            </div>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end; margin-top:-15px;">
<input type="submit" name="visitorlog_save_backup_auto" class="btn btn__primary" value="<?php esc_html_e( 'Save', 'visitorlog' );?>"/>
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
     * Changes the DB prefix
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility_File::get_wp_config_file_path()
     * @uses VisitorLog_Logger::instance()->warning()
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility_File::backup_and_rename_file()
     * @uses VisitorLog_Security_Utility::is_multisite_install()
     * @uses VisitorLog_Security_Utility::get_blog_ids()
     * @uses VisitorLog_Utility_File::wp_put_contents()
     */
    public function change_db_prefix( $table_old_prefix, $table_new_prefix )
    {
        $error = '';
        $old_prefix_length = strlen( $table_old_prefix );
        $config_file = VisitorLog_Utility_File::get_wp_config_file_path();
        $result = self::instance()->get_mysql_tables( DB_NAME ); 

        if( !$result ) {
            $err = __( 'DB connection error', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err );
            return false;
        }    

        if( is_array( $result ) && count( $result ) > 0 ) {
            $num_rows = count( $result );
        } else {
            $err = __( 'Error - Could not get tables or no tables found', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err );
            return false;
        }
        $table_count = 0;

        $info_msg  = __( 'Starting DB prefix change...', 'visitorlog' ) . '</br>';
        $info_msg .= sprintf( /* translators: 1: number rows, 2: table new prefix. */
            __( 'WordPress has a total of %1$s tables. New DB prefix will be: %2$s', 'visitorlog' ), $num_rows, $table_new_prefix );
        VisitorLog_System_View::show_message( 'blue', $info_msg );

        if ( ! VisitorLog_Utility_File::backup_and_rename_file( $config_file, 'wp-config' ) ) {
            $err = __( 'Failed to make a backup of the wp-config.php file. This operation will not go ahead', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $err, $err );
            return false;
        } else {
            $msg = __( 'A backup copy of your wp-config.php file was created successfully', 'visitorlog' );
            VisitorLog_System_View::show_message( 'green', $msg );
        }

        foreach ( $result as $table_old_name ) {

            if ( strpos( $table_old_name, $table_old_prefix ) == 0 ) {

                $table_new_name = $table_new_prefix . substr( $table_old_name, $old_prefix_length );
                $res = VisitorLog_DB_Base::$wpdb_vl->query("RENAME TABLE `$table_old_name` TO `$table_new_name`");

                if ( false === $res ) {
                    $error = sprintf( /* translators: 1: table old name. */
                        __( '%s table name update failed', 'visitorlog' ), $table_old_name );
                    VisitorLog_System_View::show_message( 'orange', $error, $error );
                } else {
                    $table_count++;
                }
            } else {
                continue;
            }
        } //------------
 
        if ( '' != $error ) {
            $error = sprintf( /* translators: 1: table new prefix. */
                __( 'Please change the prefix manually for the above tables to: %s', 'visitorlog' ), $table_new_prefix );
            VisitorLog_System_View::show_message( 'orange', $error );
        } else {
            $msg = sprintf( /* translators: 1: table count. */
                __( '%s tables had their prefix updated successfully', 'visitorlog' ), $table_count );
            VisitorLog_System_View::show_message( 'green', $msg );
        }

        //Let's check for mysql tables of type "view"
        $msg = __( 'Checking for MySQL tables of type "view".....', 'visitorlog' );
        VisitorLog_System_View::show_message( 'green', $msg );

        $config_contents = file( $config_file );
        $prefix_match_string = '$table_prefix=';

        foreach ( $config_contents as $line_num => $line ) {

            $no_ws_line = preg_replace( '/\s+/', '', $line );           //Strip white spaces
            if( strpos( $no_ws_line, $prefix_match_string ) !== FALSE ) {

                $prefix_parts    = explode( "=", $config_contents[$line_num] );
                $prefix_parts[1] = str_replace( $table_old_prefix, $table_new_prefix, $prefix_parts[1] );
                $config_contents[$line_num] = implode( "=", $prefix_parts );
                break;
            }
        } //------------

        $config_txt = '';
        foreach ( $config_contents as $line ) {
           $config_txt .= $line;
        }    

        if ( VisitorLog_Utility_File::wp_put_contents( $config_file, $config_txt ) ) {
            $msg = __( 'wp-config.php file was updated successfully', 'visitorlog' );
            VisitorLog_System_View::show_message( 'green', $msg );
        } else {
            $error = sprintf( /* translators: 1: table new prefix. */
                __( 'The "wp-config.php" file was not able to be modified. Please modify this file manually using your favourite editor and search for variable "$table_prefix" and assign the following value to that variable: %s', 'visitorlog' ), $table_new_prefix );
            VisitorLog_System_View::show_message( 'orange', $error );
        }

        $sql = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE {$table_new_prefix}options SET option_name='{$table_new_prefix}user_roles' WHERE option_name='{$table_old_prefix}user_roles' LIMIT %d", 1));

        if ( VisitorLog_DB_Base::$wpdb_vl->last_error || false === $sql ) {
            $error = sprintf( /* translators: 1: table new prefix, 2: table old prefix, 1: table new prefix. */
                __('Update of table %1$s failed: unable to change %2$s to %3$s', 'visitorlog'), $table_new_prefix . 'options', $table_old_prefix . 'user_roles', $table_new_prefix . 'user_roles');
            VisitorLog_System_View::show_message( 'orange', $error );
        } else {
            $msg = __( 'The options table records which had references to the old DB prefix were updated successfully', 'visitorlog' );
            VisitorLog_System_View::show_message( 'green', $msg );
        }

        $old_prefix = $table_old_prefix . '%';

        $meta_keys = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT user_id, meta_key FROM {$table_new_prefix}usermeta WHERE `meta_key` LIKE '%s'", $old_prefix));

        if ( VisitorLog_DB_Base::$wpdb_vl->last_error || false === $meta_keys ) {
            $error = __( 'Error updating user_meta table', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $error );
        }

        foreach ( $meta_keys as $meta_key ) {

            $new_meta_key = $table_new_prefix . substr( $meta_key->meta_key, $old_prefix_length );
            $key     = $meta_key->meta_key;
            $user_id = $meta_key->user_id;

            $sql = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE {$table_new_prefix}usermeta SET meta_key='%s' WHERE meta_key LIKE '$key' AND user_id LIKE $user_id", $new_meta_key));

            if ( VisitorLog_DB_Base::$wpdb_vl->last_error || false === $sql ) {
                $error = __( 'Error updating user_meta table', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $error );
            } else {
                $msg = __( 'The usermeta table records which had references to the old DB prefix were updated successfully', 'visitorlog' );
                VisitorLog_System_View::show_message( 'green', $msg );                
            }
        } //------------

        if ( '' == $error ) {
            $msg = __( 'DB prefix change tasks have been completed', 'visitorlog' );
            VisitorLog_System_View::show_message( 'green', $msg );
        }

        return $error;

    } // END func 

    /**
    * Returns an array of table names
    *
    */
    public function get_mysql_tables( $database='' )
    {
        $tables = array();

        $result = VisitorLog_DB_Base::$wpdb_vl->get_results("SHOW TABLES");

        foreach ($result as $res) {

            foreach ($res as $tab) {
                $tables[] = $tab;
            }
        }
        return $tables;

    } // END func

    /**
     * send email manual
     * 
     * @param string $attachment
     * @uses VisitorLog_Send_Email::send_email()
     */
    public static function send_email_manual( $attachment )
    {
        $date    = gmdate( 'l, F jS, Y \a\\t H:i', current_time( 'timestamp' ) );
        $subject = 'VisitorLog - Manual Backups Report - ' . ' ' . $date;

        $msg = 'The application contains the data of manual database backup';
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


} // END class