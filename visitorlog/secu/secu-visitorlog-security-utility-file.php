<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of the VisitorLog_Security_Utility_File
 *
 * @method public        function __construct()
 * @method public static function backup_and_rename_htaccess()
 * @method public static function get_file_permission() 
 * @method public static function is_file_permission_secure() 
 */
class VisitorLog_Security_Utility_File
{
    public $files_and_dirs_to_check;

    public function __construct()
    {
        // null
    }

    /**
     * Backup and rename htaccess
     *
     * @param string $src_file_path
     * @param string $suffix
     *
     * @uses  VisitorLog_Utility_File::create_dir()
     * @uses  VisitorLog_Utility::generate_alpha_numeric_random_string()
     * @uses  VisitorLog_Utility_File::scan_dir_sort_date()
     * @uses  VisitorLog_Logger::instance()->warning()
     *
     * @return bool true/false
     */
    public static function backup_and_rename_htaccess( $src_file_path, $suffix = 'backup' )
    {
        $dirpath = VISITORLOG_BACKUPS_DIR;
        if ( ! VisitorLog_Utility_File::create_dir( $dirpath ) ) {
            $msg_err = 'backup_and_rename_htaccess()-error create dir: ' . $dirpath;
            VisitorLog_Logger::instance()->warning( $msg_err );
            return false;
        }

        $random_suffix = VisitorLog_Utility::generate_alpha_numeric_random_string( 10 );
        $file_htaccess = $dirpath . '/htaccess-' . current_time( 'Ymd-His' ) . '-' . $random_suffix . '.' . $suffix;

        $files = VisitorLog_Utility_File::scan_dir_sort_date($dirpath);
        foreach ( $files as $file ) {
            if ( strpos( $file, 'htaccess' ) !== false ) {
                VisitorLog_Utility_File::wp_delete_file($dirpath.'/'.$file);
            }
        }
        if ( ! VisitorLog_Utility_File::wp_copy_file($src_file_path, $file_htaccess, true) ) {
            $warning = 'backup_and_rename_htaccess(): Failed to make a backup copy htaccess file';
            VisitorLog_Logger::instance()->warning($warning);
            return false;
        }

        return true;
        
    } // END func

    /*
     * Returns the file's permission value eg, "0755"
     */
    public static function get_file_permission( $filepath )
    { 
        if ( !function_exists('fileperms') ) {
            $perms = '-1';
        } else {
            clearstatcache();
            $perms = substr(sprintf("%o", @fileperms($filepath)), -4);
        }
        return $perms;

    } // END func

    /**
     * This function will compare the current permission value for a file or dir with the recommended value.
     * It will compare the individual "execute", "write" and "read" bits for the "public", "group" and "owner" permissions.
     * If the permissions for an actual bit value are greater than the recommended value it returns '0' (=less secure)
     * Otherwise it returns '1' which means it is secure
     * Accepts permission value parameters in octal, ie, "0777" or "777"
     */
    public static function is_file_permission_secure( $recommended, $actual )
    {
        $result = 1;            //initialize return result

        //Check "public" permissions
        $public_value_actual = substr( $actual, -1,1 );   //get dec value for actual public permission
        $public_value_rec    = substr( $recommended, -1,1 ); //get dec value for recommended public permission

        $pva_bin = sprintf( '%04b', $public_value_actual ); //Convert value to binary
        $pvr_bin = sprintf( '%04b', $public_value_rec ); //Convert value to binary
        //Compare the "executable" bit values for the public actual versus the recommended
        if( substr( $pva_bin, -1,1 ) <= substr( $pvr_bin, -1, 1 ) ) {
            //The "execute" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "execute" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        //Compare the "write" bit values for the public actual versus the recommended
        if ( substr( $pva_bin, -2, 1 ) <= substr( $pvr_bin, -2, 1 ) ) {
            //The "write" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "write" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        //Compare the "read" bit values for the public actual versus the recommended
        if ( substr( $pva_bin, -3, 1 ) <= substr( $pvr_bin, -3, 1 ) ) {
            //The "read" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "read" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        //Check "group" permissions
        $group_value_actual = substr( $actual, -2, 1 );
        $group_value_rec    = substr( $recommended, -2, 1 );
        $gva_bin = sprintf( '%04b', $group_value_actual ); //Convert value to binary
        $gvr_bin = sprintf( '%04b', $group_value_rec );    //Convert value to binary

        //Compare the "executable" bit values for the group actual versus the recommended
        if ( substr( $gva_bin, -1, 1 ) <= substr( $gvr_bin, -1, 1 ) ) {
            //The "execute" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "execute" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        //Compare the "write" bit values for the public actual versus the recommended
        if ( substr( $gva_bin, -2, 1 ) <= substr( $gvr_bin, -2, 1 ) ) {
            //The "write" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "write" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        //Compare the "read" bit values for the public actual versus the recommended
        if ( substr( $gva_bin, -3, 1) <= substr( $gvr_bin, -3, 1 ) ) {
            //The "read" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "read" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        //Check "owner" permissions
        $owner_value_actual = substr( $actual, -3, 1 );
        $owner_value_rec = substr( $recommended, -3, 1 );
        $ova_bin = sprintf( '%04b', $owner_value_actual ); //Convert value to binary
        $ovr_bin = sprintf( '%04b', $owner_value_rec );    //Convert value to binary

        //Compare the "executable" bit values for the group actual versus the recommended
        if ( substr( $ova_bin, -1, 1) <= substr( $ovr_bin, -1, 1 ) ) {
            //The "execute" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "execute" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        //Compare the "write" bit values for the public actual versus the recommended
        if ( substr( $ova_bin, -2, 1) <= substr( $ovr_bin, -2, 1 ) ) {
            //The "write" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "write" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        //Compare the "read" bit values for the public actual versus the recommended
        if ( substr( $ova_bin, -3, 1 ) <= substr( $ovr_bin, -3, 1 ) ) {
            //The "read" bit is the same or less as the recommended value
            $result = 1*$result;
        } else {
            //The "read" bit is switched on for the actual value - meaning it is less secure
            $result = 0*$result;
        }

        return $result; 

    } // END func

    /**
     * Will return an indexed array of files sorted by last modified timestamp
     *
     * @param string $dir
     * @param string $sort (ASC, DESC)
     * @return array
     */
    public static function scan_dir_sort_date( $dir, $sort='DESC' )
    {
        $files = array();
        foreach ( scandir( $dir ) as $file ) {
            $files[$file] = filemtime( $dir . '/' . $file );
        }

        if ( $sort === 'ASC' ) asort( $files );
        else                  arsort( $files );
    
        return array_keys( $files );

    } // END func


} // END class