<?php
/**
 * File system protection
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Security_FSP_View class
 * 
 * @method public static file_system_protection()
 */
class VisitorLog_Security_FSP
{
    /**
     * Render File system protection.
     *
     * @uses VisitorLog_System_View::show_message()
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::update_option_sl()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Handler::instance()->handler_htaccess_view()
     */
    public static function file_system_protection()
    {
        $run_scan_data = '';
        $file_types = '';
        $files = '';
        $error = '';

        $n1=$n2=$n3= '';
        $period1=$period2=$period3=$period4= '';
        $debug = '';

        $on_file_system_protection = VisitorLog_Utility::get_option_sl( 'on_file_system_protection' );
        
        $disable_file_edit  = VisitorLog_Utility::get_option_sl( 'disable_file_edit' );
        $on_prevent_wp_file = VisitorLog_Utility::get_option_sl( 'on_prevent_wp_file_access' );

        if ( isset ($_POST['visitorlog_fileprotection_save']) ) {

            if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }

            if ( '' == $on_file_system_protection ) {
               $err = __( 'Turn on the file system protection feature on the control panel', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err );
            } else {
                //---------------------------------------------------- Disable PHP file edit
                if ( isset($_POST['visitorlog_disable_file_edit']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $post = sanitize_text_field(wp_unslash($_POST['visitorlog_disable_file_edit']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                } else {
                    $post = ''; 
                }
                if ( $post != $disable_file_edit ) {
                    $res = VisitorLog_Security_Utility::disable_enable_file_edit( $post );
                    if ( $res > 0 ) {
                        VisitorLog_Utility::update_option_sl( 'disable_file_edit', $post );
                        $disable_file_edit = $post;
                        $msg = __( 'The directive for the DISALLOW_FILE_EDIT constant has been successfully written to the wp-config system file', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'green', $msg );
                    }
                    if ( $res < 0 ) {
                        $msg = __( 'The directive for the DISALLOW_FILE_EDIT constant could not be written to the wp-config system file. Apparently the plugin does not have enough rights to work with files', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $msg, $msg );
                    }
                }
                //---------------------------------------------------- WP file access
                if ( isset($_POST['visitorlog_on_prevent_wp_file_access']) ) {

                    if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
                        $post = sanitize_text_field(wp_unslash($_POST['visitorlog_on_prevent_wp_file_access']));
                    } else {    
                        $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'orange', $err, $err );
                        wp_die();
                    }
                } else {
                    $post = ''; 
                }
                if ( $on_prevent_wp_file != $post ) {
                    VisitorLog_Utility::update_option_sl( 'on_prevent_wp_file_access', $post );
                    $on_prevent_wp_file = $post;

                    $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess();
                    if ( $res > 0 ) {
                        $msg = __( 'You have successfully saved the Prevent Access to Default WP Files configuration', 'visitorlog' );
                        VisitorLog_System_View::show_message( 'green', $msg, $msg );
                    } else { 
                        $err = __( 'Could not write to the <.htaccess> file. Please check the file permissions', 'visitorlog' );
                        VisitorLog_Logger::instance()->warning($err);
                    }
                }  

            }
        }
        //--------------------- Display
        if ( $run_scan_data ) {
            if ( 2 == $run_scan_data ) {
                $msg = __('The plugin has detected that you have made changes to the "File Types To Ignore" or "Files To Ignore" fields. In order to ensure that future scan results are accurate, the old scan data has been refreshed', 'visitorlog');
                VisitorLog_System_View::show_message( 'blue', $msg );
            } else {
                $msg = __('The administrator has started scanning the site\'s file system for changes to files and folders', 'visitorlog');
                VisitorLog_System_View::show_message( 'blue', $msg );                
            }
        }
        if ( isset($_GET['reg']) && 'htaccess' == $_GET['reg'] ) {

            if ( ! (isset($_GET['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce')) ) {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Security_Handler::instance()->handler_htaccess_view();
            exit;
        }    
        switch ( $disable_file_edit ) {
            case 'on':
                $dfe = 'checked';
                break;
            default:
                $dfe = '';
                break;
        }
        switch ( $on_prevent_wp_file ) {
            case 'on':
                $epwfa = 'checked';
                break;
            default:
                $epwfa = '';
                break;
        }
        if ( 'on' == $on_file_system_protection ) {
            $icon = '';
        } else {
            $icon = 'off-icon';
            $msg  = __( 'The panel with file system protection functions is disabled!', 'visitorlog' );
            $msg .= '<br>';
            $msg .= __( 'Activate the functions on the control panel.', 'visitorlog' );
            VisitorLog_System_View::show_message( 'yellow', $msg );
        } 
        ?>  
        <form method="post">
        <?php wp_nonce_field('visitorlog_nonce');?>
        <div class="row clearfix" style="margin-top:-7px;">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="visitorlog_checkbox_select" value="on">
                                            &ensp;<?php esc_html_e('Enable/Disable all','visitorlog');?>
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
$title = __('View file .htaccess', 'visitorlog');
VisitorLog_Utility::redirect_html( 'file-code.png', $title, 'visitorlog_fileprotection', '&reg=htaccess' );
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
        <div class="row clearfix" style="margin-top:-10px;">
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
                            </table>
                        </div>
                    </div>
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
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                    <?php esc_html_e('Disable Ability To Edit PHP Files', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr>    
                                    <tr>
                                        <td width="62">
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_disable_file_edit" name="visitorlog_disable_file_edit" type="checkbox" value="on" <?php echo esc_html($dfe);?>>
                                                    <label for="visitorlog_disable_file_edit"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="hidden-md-down" width="200">
                                            <?php esc_html_e('Disable Ability To Edit PHP Files','visitorlog');?>
                                        </td>
                                        <td width="222">

                                        </td>                                        
                                        <td>
<?php esc_html_e('Check this if you want to remove the ability for people to edit PHP files via the WP dashboard','visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
<?php esc_html_e('Prevent Access to WordPress Install Files', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr> 
                                    <tr>
                                        <td width="62">
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_on_prevent_wp_file_access" name="visitorlog_on_prevent_wp_file_access" type="checkbox" value="on" <?php echo esc_html($epwfa);?>>
                                                    <label for="visitorlog_on_prevent_wp_file_access"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td width="200">
                                <?php esc_html_e('Enable prevent wp file access','visitorlog');?>
                                        </td>
                                        <td width="222">
                                        </td>                                        
                                        <td>
<?php esc_html_e('Check this if you want to prevent access to readme.html, license.txt and wp-config-sample.php','visitorlog');?>.
<?php esc_html_e('To implement this function , the control instructions in the file are used .htaccess', 'visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <em style="font-size:16px;">
                                <?php esc_html_e('File Permissions Scan', 'visitorlog');?>
                                            </em>
                                        </td>    
                                    </tr>
                                    <tr>
                                        <td width="62"></td>
                                        <td width="200">
    <?php esc_html_e('Check the WP core folder and file access settings','visitorlog');?>
                                        </td>
                                        <td class="hidden-md-down" width="222">
<?php
$title = __('Table of WP core folder and file access settings', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_filepermissions' );
?>
                                        </td>                                        
                                        <td>
<?php esc_html_e('This feature will scan the critical WP core folders and files and will highlight any permission settings which are insecure','visitorlog');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="margin-top:10px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=file_protect' );
?>
                            </div>
                            <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_fileprotection_save" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
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


} // END class