<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_File_Permissions class
 * 
 * @method public    static function render_file_permissions_report() 
 * @method protected static function show_wp_filesystem_permission_status()
 * @method protected static function permission_verification_files()
 */
class VisitorLog_File_Permissions
{
    /**
     * Render file permissions report.
     *
     * @uses VisitorLog_Security_Utility_File
     * @uses VisitorLog_System_View::show_message()
     */
    public static function render_file_permissions_report()
    {
        $msg = '';
        $msg_server = '';
        $detected_os = strtoupper(PHP_OS);

        if ( strpos($detected_os, "WIN") !== false && $detected_os != "DARWIN" ) {
            $msg_server = __('For Windows server, this plugin does not change the set permissions of WP system files', 'visitorlog');
        } 
        ?>  
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <p><?php echo esc_html($msg_server);?></p>
                    </div>
                    <div></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody style="background-color:#F2F2F2;">
                                    <tr>
                                        <td class="hidden-md-down" style="display:flex;flex-direction:row;justify-content: flex-end;">
<?php
$title = __('Other file system protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_fileprotection', '' );
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
        <div style="margin-top:-2px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead>
                                    <tr> 
        <td class="vl-thead">            <?php esc_html_e('File/Folder','visitorlog');?></td>
        <td class="vl-thead" width="130"><?php esc_html_e('Current Permissions','visitorlog');?></td>
        <td class="vl-thead" width="130"><?php esc_html_e('Recommended Permissions','visitorlog');?></td>
        <td class="vl-thead">            <?php esc_html_e('Recommended Action','visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $files_dirs_to_check = self::permission_verification_files();
                                $i = 0;
                                foreach ( $files_dirs_to_check as $file_or_dir ) {

                                    $path        = $file_or_dir['path'];
                                    $permissions = $file_or_dir['permissions'];
                                    $result = self::show_wp_filesystem_permission_status( $path, $permissions );

                                    $style       = $result[0];
                                    $configmod   = $result[1]; 
                                    $recommended = $result[2];
                                    $fix         = $result[3];

                                    if ( $fix ) {
                                        $msg = __('Set Recommended Permissions', 'visitorlog');
                                    } else {
                                        $msg = __('No Action Required', 'visitorlog');
                                    }
                                    ?>
                                    <tr style="font-size:13px;">
                                        <td><span><?php echo esc_html($path);?></span></td>
                                        <td>
                                            <span style="color:<?php echo esc_html($style);?>">
                                                <?php echo esc_html($configmod);?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color:<?php echo esc_html($style);?>">
                                                <?php echo esc_html($recommended);?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="color:<?php echo esc_html($style);?>">
                                                <?php echo esc_html($msg);?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php            
                                    $i++;
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
        
    } // END func

    /**
     * Scans WP key core files and directory permissions and populates a wp wide_fat table
     * Displays a red entry with a "Fix" button for permissions which are "777"
     * Displays a orange entry with a "Fix" button for permissions which are less secure than the recommended
     * Displays a green entry for permissions which are as secure or better than the recommended
     *
     * @uses VisitorLog_Security_Utility_File::get_file_permission()
     * @uses VisitorLog_Security_Utility_File::is_file_permission_secure()
     */
    protected static function show_wp_filesystem_permission_status( $path, $recommended )
    {
        $fix = 0;
        $configmod = VisitorLog_Security_Utility_File::get_file_permission( $path );

        if ( $configmod == "0777" ) {
            $trclass = "red";
            $fix = 1;
        } elseif ( $configmod != $recommended ) {
            $res = VisitorLog_Security_Utility_File::is_file_permission_secure( $recommended, $configmod );
            if ($res) {
                $trclass = "green";
                $fix = 1;
            } else {
                $trclass = "orange";
                $fix = 1;
            }
        } else {
            $trclass = "green";
        }
        $result[0] = $trclass;
        $result[1] = $configmod;
        $result[2] = $recommended;
        $result[3] = $fix;

        return $result;

    } // END func

    protected static function permission_verification_files()
    {
        $config_path = VisitorLog_Utility_File::get_wp_config_file_path();

        $files_and_dirs = array(
            array('path'=>VISITORLOG_HOME_DIR,                            'permissions'=>'0755'),
            array('path'=>VISITORLOG_HOME_DIR . 'wp-' . 'includes',       'permissions'=>'0755'),
            array('path'=>VISITORLOG_HOME_DIR . 'wp-' . 'admin/js/',      'permissions'=>'0755'),
            array('path'=>VISITORLOG_HOME_DIR . 'wp-' . 'content/themes', 'permissions'=>'0755'),
            array('path'=>VISITORLOG_HOME_DIR . 'wp-' . 'content/plugins','permissions'=>'0755'),
            array('path'=>VISITORLOG_HOME_DIR . 'wp-' . 'admin',          'permissions'=>'0755'),
            array('path'=>VISITORLOG_HOME_DIR . 'wp-' . 'content',        'permissions'=>'0755'),
            array('path'=>VISITORLOG_HOME_DIR . '.htaccess',              'permissions'=>'0644'),
            array('path'=>VISITORLOG_HOME_DIR . 'wp-' . 'admin/index.php','permissions'=>'0644'),
            array('path'=>$config_path,                                   'permissions'=>'0640')
        );

        return $files_and_dirs;

    } // END func


} // END class