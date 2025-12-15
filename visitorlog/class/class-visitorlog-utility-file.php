<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods VisitorLog_Utility_File.
 *
 * @method public static function create_dir()
 * @method public static function backup_and_rename_file()
 * @method public static function delete_backup_files()
 * @method public static function scan_dir_sort_date()
 * @method public static function sanitize_db_field()
 * @method public static function get_wp_config_file_path()
 * @method public static function is_writable()
 * @method public static function wp_delete_file()
 * @method public static function wp_put_contents()
 * @method public static function wp_get_contents()
 * @method public static function load_class_filesystem_direct()
 * @method public static function copy_dir()
 * @method public static function copy_images()
 */
class VisitorLog_Utility_File
{
    /**
     * Checks if a directory exists and creates one if it does not
     *
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function create_dir( $dirpath='' )
    {
        self::load_class_filesystem_direct();
        $WP_Filesystem_Direct = new \WP_Filesystem_Direct($dirpath);

        $chmod_dir = ( 0755 & ~ umask() );
        if ( defined( 'FS_CHMOD_DIR' ) ) {
            $chmod_dir = FS_CHMOD_DIR;
        }

        if( '' == $dirpath ) {

            $dirpath = VISITORLOG_UPLOADS_DIR;
            if ( ! $WP_Filesystem_Direct->exists($dirpath) ) {

                if ( false === $WP_Filesystem_Direct->mkdir($dirpath, $chmod_dir) ) {
                    $err = 'create_dir()-error create dir: ' . $dirpath;
                    VisitorLog_Logger::instance()->warning($err);
                    return false;
                }
                if ( false === $WP_Filesystem_Direct->put_contents($dirpath.'/index.php', '<?php //Silence is golden', '0644') ) {
                    $err = 'create_dir()-error create file: '.$dirpath.'/index.php';
                    VisitorLog_Logger::instance()->warning($err);
                    return false;
                }                
            }
            $dirpath = VISITORLOG_BACKUPS_DIR;
            if ( ! $WP_Filesystem_Direct->exists($dirpath) ) {

                if ( false === $WP_Filesystem_Direct->mkdir($dirpath, $chmod_dir) ) {
                    $err = 'create_dir()-error create dir: ' . $dirpath;
                    VisitorLog_Logger::instance()->warning($err);
                    return false;
                }
                if ( false === $WP_Filesystem_Direct->put_contents($dirpath.'/index.php', '<?php //Silence is golden', '0644') ) {
                    $err = 'create_dir()-error create file: '.$dirpath.'/index.php';
                    VisitorLog_Logger::instance()->warning($err);
                    return false;
                }                
            }
            return true;
        }     

        if ( $WP_Filesystem_Direct->exists($dirpath) ) return true;

        if ( false === $WP_Filesystem_Direct->mkdir($dirpath, $chmod_dir) ) {
            $err = 'create_dir()-error create dir: ' . $dirpath;
            VisitorLog_Logger::instance()->warning($err);
            return false;
        }
        if ( false === $WP_Filesystem_Direct->put_contents($dirpath.'/index.php', '<?php //Silence is golden', '0644') ) {
            $err = 'create_dir()-error create file: '.$dirpath.'/index.php';
            VisitorLog_Logger::instance()->warning($err);
            return false;
        }
        return true;

    } // END func

    /**
     * Backup and rename file
     *
     * @param string $src_file_path, $mask
     * @param string $mask
     *
     * @uses  VisitorLog_Utility::generate_alpha_numeric_random_string()
     * @uses  VisitorLog_Logger::instance()->warning()
     *
     * @return bool true/false
     */
    public static function backup_and_rename_file( $src_file_path, $mask )
    {
        $dirpath = VISITORLOG_BACKUPS_DIR;

        if ( false === self::create_dir() ) {
            $err = 'backup_and_rename_file()-error create dir: ' . $dirpath;
            VisitorLog_Logger::instance()->warning($err);
            return false;
        }
        if ( false === self::delete_backup_files($dirpath, $mask) ) {
            $err = 'backup_and_rename_file()-error delete files: ' . $dirpath;
            VisitorLog_Logger::instance()->warning($err);
        }    

        $random_suffix = VisitorLog_Utility::generate_alpha_numeric_random_string(10);
        $backup_file_name = $mask . '-' . current_time('Ymd-His') . '-' . $random_suffix;
        $backup_file_path = $dirpath . '/' . $backup_file_name;

        if ( ! self::wp_copy_file($src_file_path, $backup_file_path, true) ) {
            $err = 'backup_and_rename_file(): Failed to make a backup copy file';
            VisitorLog_Logger::instance()->warning($err);
            return false;
        }
        return true;
        
    } // END func

    /**
     * Delete backup files
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function delete_backup_files( $backups_dir, $mask )
    {
        self::load_class_filesystem_direct();

        if ( 'database-backup' == $mask ) {
            $files_to_keep = (int)VisitorLog_Utility::get_option_sl('number_backup_instances');
        } else {
            $files_to_keep = 1;
        }

        $files = self::scan_dir_sort_date($backups_dir);
        $WP_Filesystem_Direct = new \WP_Filesystem_Direct($backups_dir);
        $count = 1;

        foreach ( $files as $file ) {

            if ( false !== strpos($file, $mask) ) {

                if ( $count >= $files_to_keep ) {

                    if ( false === $WP_Filesystem_Direct->delete( $backups_dir . '/' . $file ) ) {
                        $msg_err = 'delete_backup_files()-error delete file: '.$backups_dir.'/'.$file;
                        VisitorLog_Logger::instance()->warning( $msg_err );
                        return false;                        
                    }
                }
                $count++;
            }
        }
        return $count;

    } // END func 

    /**
     * Will return an indexed array of files sorted by last modified timestamp
     *
     * @param string $dir
     * @param string $sort (ASC, DESC)
     * @return array
     */
    public static function scan_dir_sort_date( $dir, $sort = 'DESC' )
    {
        $files = array();
        foreach ( scandir($dir) as $file ) {
            $files[$file] = filemtime( $dir . '/' . $file );
        }

        if ( $sort === 'ASC' ) asort( $files );
        else                  arsort( $files );
    
        return array_keys( $files );

    } // END func

    /**
     * Add slashes, sanitize end-of-line characters (?), wrap $value in quotation marks.
     *
     * @param string $value
     * @return string
     */
    public static function sanitize_db_field( $value )
    {
        if ( '' == $value ) return '';

        return '"' . preg_replace( "/".PHP_EOL."/", "\n", addslashes( $value ) ) . '"';

    } // END func


    public static function get_wp_config_file_path()
    {
        $wp_config_file = VISITORLOG_HOME_DIR . 'wp-config.php';
        if ( file_exists($wp_config_file) ) {
            return $wp_config_file;
        }
        if ( file_exists(dirname(get_home_path()) . '/wp-config.php') ) {
            return dirname(get_home_path()) . '/wp-config.php';
        }
        return $wp_config_file;

    } // END func

    /**
     * Check if the path is writable. To make the check.
     *
     * @param  string $path
     * @return boolean
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function is_writable( $path )
    {
        self::load_class_filesystem_direct();

        $WP_Filesystem_Direct = new \WP_Filesystem_Direct($path);
        $res = $WP_Filesystem_Direct->is_writable($path);

        if ( false === $res ) {    
            $err = 'is_writable()-error writable: ' . $path;
            VisitorLog_Logger::instance()->warning($err);
            return false;
        }
        return true;

    } // END func

    /**
     * Delete the file using the WP library.
     *
     * @param string $path
     * @return boolean
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function wp_delete_file( $path )
    {
        self::load_class_filesystem_direct();

        $WP_Filesystem_Direct = new \WP_Filesystem_Direct($path);
        $del = @$WP_Filesystem_Direct->delete($path);

        if ( false === $del ) {    
            $err = 'wp_delete_file()-error delete file: ' . $path;
            VisitorLog_Logger::instance()->warning($err);
            return false;
        }
        return true;

    } // END func

    /**
     * Writing to a file using the WP library.
     *
     * @param string $path, $content, $mode
     * @return boolean
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function wp_put_contents( $file, $contents, $mode = 0644 )
    {
        self::load_class_filesystem_direct();

        $WP_Filesystem_Direct = new \WP_Filesystem_Direct($file);

        if ( false === $WP_Filesystem_Direct->put_contents( $file, $contents, $mode ) ) {
            $err = 'wp_put_contents()-error writing to the file: ' . $file;
            VisitorLog_Logger::instance()->warning($err);
            return false;
        }
        
        return true;

    } // END func

    /**
     * Reading from a file to a string using the WP library.
     *
     * @param string $path
     * @return $contents/boolean
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function wp_get_contents( $path )
    {
        self::load_class_filesystem_direct();

        $WP_Filesystem_Direct = new \WP_Filesystem_Direct($path);
        $get = $WP_Filesystem_Direct->get_contents($path);

        if ( false === $get ) {
            $err = 'wp_get_contents()-error reading from the file: ' . $path;
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        }
        return $get;

    } // END func      

    /**
     * Copies a file.
     *
     * @param string $source, $destination, $overwrite, $mode
     * @return $contents/boolean
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function wp_copy_file( $source, $destination, $overwrite=false, $mode=false )
    {
        self::load_class_filesystem_direct();

        $WP_Filesystem_Direct = new \WP_Filesystem_Direct($source);
        $get = $WP_Filesystem_Direct->copy( $source, $destination, $overwrite, $mode );

        if ( false === $get ) {
            $err = 'wp_copy_file()-error copy the file: ' . $source;
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        }
        
        return $get;

    } // END func  

    /*
     * Direct loading of the file system class
     *
     * Since Wordpress policy requires using its own functions when working with the file system, 
     * we load the file system class and use its functions.
     */
    public static function load_class_filesystem_direct()
    {
        if ( ! class_exists('WP_Filesystem_Direct') ) {
            require_once VISITORLOG_HOME_DIR . 'wp-' . 'admin/includes/class-wp-filesystem-base.php';
            require_once VISITORLOG_HOME_DIR . 'wp-' . 'admin/includes/class-wp-filesystem-direct.php';
        }

    } // END func          

    /**
     * Copy dir
     *
     * @param  string $from, $to
     * @return true|WP_Error
     */
    public static function copy_dir( $from, $to )
    {
        global $wp_filesystem;

        if ( ! $wp_filesystem ) {
            require_once VISITORLOG_HOME_DIR . '/wp-' . 'admin/includes/file.php';
            WP_Filesystem();
        }

        $res = copy_dir( $from, $to );

        return $res;

    } // END func  

    /**
     * Copying images to a media library
     *
     * @return true|error
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function copy_images()
    {
        $pathimg = VISITORLOG_PLUGIN_URL . 'assets/images/img/';
        $pathico = VISITORLOG_PLUGIN_URL . 'assets/images/icons/';

        require_once VISITORLOG_HOME_DIR . 'wp-' . 'admin/includes/image.php';
        require_once VISITORLOG_HOME_DIR . 'wp-' . 'admin/includes/file.php';
        require_once VISITORLOG_HOME_DIR . 'wp-' . 'admin/includes/media.php';

        $images = array(
            array('url' => $pathico . 'book.png'),
            array('url' => $pathico . 'chart-line.png'),
            array('url' => $pathico . 'email.png'),
            array('url' => $pathico . 'file-code.png'),
            array('url' => $pathico . 'gear.png'),
            array('url' => $pathico . 'globe.png'),
            array('url' => $pathico . 'list-repo-com.png'),
            array('url' => $pathico . 'right-from-bracket.png'),
            array('url' => $pathico . 'right-from-bracket-inv.png'),
            array('url' => $pathico . 'shield-halved.png'),
            array('url' => $pathico . 'table-list.png'),
            array('url' => $pathico . 'update.png'),
            array('url' => $pathico . 'warning.png'),
            array('url' => $pathico . 'wrench.png'),

            array('url' => $pathico . 'accept.png'),    
            array('url' => $pathico . 'b_browse.png'),  
            array('url' => $pathico . 'b_drop.png'),  
            array('url' => $pathico . 'b_insrow.png'),
            array('url' => $pathico . 'ban.png'),    
            array('url' => $pathico . 'code.png'),  
            array('url' => $pathico . 'deltbl.png'),  
            array('url' => $pathico . 'help.png'),
            array('url' => $pathico . 'install.png'),    
            array('url' => $pathico . 'line_edit.png'),  
            array('url' => $pathico . 'link.png'),  
            array('url' => $pathico . 'logo.png'),  
            array('url' => $pathico . 'logoload.png'),
            array('url' => $pathico . 'minus.png'),
            array('url' => $pathico . 'nokey.png'),
            array('url' => $pathico . 'plus.png'),  
            array('url' => $pathico . 'remove.png'),  
            array('url' => $pathico . 'save.png'),
            array('url' => $pathico . 'sl-icon.png'),
            array('url' => $pathico . 'unban.png'),  
            array('url' => $pathico . 'warning-c.png'),  

            array('url' => $pathimg . 'plugin_update.png'), 
            array('url' => $pathimg . 'total_counter.png'), 
            array('url' => $pathimg . 'update_menu.png'), 
            array('url' => $pathimg . 'version_footer.png'),
            array('url' => $pathimg . 'widget_console.png'),  
            array('url' => $pathimg . 'wp_available_console.png'),
        );

        $table_posts  = VisitorLog_DB_Base::$table_prefix . 'posts';
        $table_images = VisitorLog_DB_Base::$table_prefix_sl . 'images';
        foreach ($images as $image) {

            $tmp  = download_url( $image['url'] );
            $name = basename( $image['url'] );

            $arr_post = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_posts WHERE `post_title` = %s", $name));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'copy_images()-Error select data in table POSTS: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return $err;
            }
            $arr_images = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_images WHERE `name` = %s", $name));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'copy_images()-Error select data in table IMAGES: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return $err;
            }

            if ( empty($arr_post) ) {        /* adding files to the library */
                $file_array = array(
                    'name'     => $name,
                    'tmp_name' => $tmp
                );
                $id = media_handle_sideload( $file_array, 0, $name );
            } else {                          /* checking for duplicate files in media library */
                $id = $arr_post[0]->ID;
            }

            wp_delete_file( $tmp );
            if ( is_wp_error($id) ) {
                $err = 'copy_images()-Error delete the file: ' . $tmp;
                VisitorLog_Logger::instance()->warning( $err );
                return $err;
            }

            if ( empty($arr_images) ) {
                $data = array(
                    'name'    => $name,
                    'id_post' => $id,
                );
                $insert = VisitorLog_DB_Base::$wpdb_vl->insert( $table_images, $data );
                if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                    $err = 'copy_images()-Error insert into table IMAGES: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                    VisitorLog_Logger::instance()->warning( $err );
                    return $err;
                }
            } else {
                $data = array(
                    'id_post' => $id,
                );
                $result = VisitorLog_DB_Base::$wpdb_vl->update( $table_images, $data, ['name'=>$name] );
                if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                    $err = 'copy_images()-Error update into table IMAGES: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                    VisitorLog_Logger::instance()->warning( $err );
                    return $err;
                }
            }
        } // end foreach

        return true;

    } // END func 


} // END class