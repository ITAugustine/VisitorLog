<?php
/**
 * Backup Handler.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods VisitorLog_Backup_Handler.
 *
 * @method public static function instance()
 * @method public static function get_class_name()
 * @method public static function execute_backup()
 * @method public static function create_db_backup_file()
 * @method public        function scheduled_backup_handler()
 * @method public static function send_email_auto()
 */

class VisitorLog_Backup_Handler
{
	private static $instance = null;		        // instance.

	public static function instance()
    {
		if ( null == self::$instance ) self::$instance = new self;
		return self::$instance;

	} // END func

	public static function get_class_name()
    {
		return __CLASS__;

	} // END func
		
   /**
    * This function will perform a database backup
    *
    * @return int -1 / -5 error
    *         int 1 good
    *
    * @uses VisitorLog_Logger::instance()->warning()
    * @uses VisitorLog_Utility_File::create_dir()
    * @uses VisitorLog_Utility::generate_alpha_numeric_random_string()
    * @uses VisitorLog_Utility_File::delete_backup_files()
    * @uses VisitorLog_Utility_File::wp_delete_file()
    * @uses VisitorLog_Utility_File::wp_put_contents()
    */
    public static function execute_backup( &$attachment )
    {
        $attachment = '';  
        $tables = VisitorLog_DB_Base::$wpdb_vl->get_results( 'SHOW TABLES', ARRAY_N );

        if ( empty( $tables ) ) {
            $msg_err = 'execute_backup()-empty SHOW TABLES';
            VisitorLog_Logger::instance()->warning( $msg_err );
            return -1;
        }

        $dirpath = VISITORLOG_BACKUPS_DIR;
        if ( false === VisitorLog_Utility_File::create_dir($dirpath) ) {
            $msg_err = 'execute_backup()-error create dir: ' . $dirpath;
            VisitorLog_Logger::instance()->warning( $msg_err );
            return -2;
        }
        if ( false === VisitorLog_Utility_File::delete_backup_files($dirpath, 'database-backup') ) {
            $err = 'execute_backup()-error delete backup files';
            VisitorLog_Logger::instance()->warning( $err );
            return -3;
        }

        $random_suffix = VisitorLog_Utility::generate_alpha_numeric_random_string( 10 );
        $file = 'database-backup-' . current_time( 'Ymd-His' ) . '-' . $random_suffix;
        $contents = self::create_db_backup_file( $tables );
        if ( false === $contents ) {
            $err = 'execute_backup()-error create db backup body';
            VisitorLog_Logger::instance()->warning( $err );
            return -4;
        }

        $file_sql = $dirpath . DIRECTORY_SEPARATOR . $file . '.sql';
        $put = VisitorLog_Utility_File::wp_put_contents( $file_sql, $contents );
        if ( false === $put ) {
            $err = 'execute_backup()-error put contents';
            VisitorLog_Logger::instance()->warning( $err );
            return -5;
        }

        //zip the file
        if ( class_exists( 'ZipArchive' ) ) {
            $zip = new \ZipArchive();
            $archive = $zip->open( $dirpath . DIRECTORY_SEPARATOR . $file . '.zip', \ZipArchive::CREATE );
            $zip->addFile( $file_sql, $file . '.sql' );
            $zip->close();

            //delete .sql and keep zip
            $delete_file = VisitorLog_Utility_File::wp_delete_file( $file_sql );
            if ( false === $delete_file ) {
                $err = 'execute_backup()-error delete file';
                VisitorLog_Logger::instance()->warning( $err );
                return -6;
            }
            $fileext = '.zip';
        } else {
            $fileext = '.sql';
        }
        $attachment = $dirpath . DIRECTORY_SEPARATOR . $file . $fileext;

        return 1;

    } // END func

    /**
     * Write sql dump of $tables to backup file identified by $handle.
     *
     * @param   array    $tables
     * @return  boolean True, if database tables dump have been successfully written to the backup file, false otherwise.
     */
    public static function create_db_backup_file( $tables )
    {
        $current_time = current_time( 'mysql' ); 
        $version = VISITORLOG_PLUGIN_VERSION;
        $body = '';
        $preamble
            = "-- VisitorLog {$version}" . PHP_EOL
            . '-- MySQL dump' . PHP_EOL
            . '-- ' . $current_time . PHP_EOL . PHP_EOL
              // When importing the backup, tell database server that our data is in UTF-8...
              // ...and that foreign key checks should be ignored.
            . "SET NAMES utf8;" . PHP_EOL
            . "SET foreign_key_checks = 0;" . PHP_EOL . PHP_EOL
        ;
        $table_name_pass = VisitorLog_DB_Base::$table_prefix_sl . 'pass';
        foreach ( $tables as $table ) {

            $table_name = $table[0];
            if ( $table_name == $table_name_pass ) continue;

            $result_create_table = VisitorLog_DB_Base::$wpdb_vl->get_row("SHOW CREATE TABLE `$table_name`", ARRAY_N );

            if ( empty( $result_create_table ) ) {
                VisitorLog_Logger::instance()->warning( __METHOD__ . " - get_row returned NULL for table: " . $table_name );
                return false; 
            }

            // Drop/create table preamble
            $body .= 'DROP TABLE IF EXISTS `' . $table_name . '`;' . PHP_EOL . PHP_EOL;
            $body .= $result_create_table[1] . ";" . PHP_EOL . PHP_EOL;

            // Dump table contents
            // Fetch results as row of objects to spare memory.
            $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM `$table_name` LIMIT %d", 1000000));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
              $err = 'create_db_backup_file()-Error SELECT into table: '.$table_name.' - '.VisitorLog_DB_Base::$wpdb_vl->last_error;
              VisitorLog_Logger::instance()->warning($err);
            }
            foreach ( $result as $object_row ) {
                // Convert object row to array row: this is what $wpdb->get_results()
                // internally does when invoked with ARRAY_N param, but in the process
                // it creates new copy of entire results array that eats a lot of memory.

                $row = array_values( get_object_vars($object_row) );

                // Start INSERT statement
                $body .= "INSERT INTO `" . $table_name . "` VALUES(";

                // Loop through all fields and echo them out
                foreach ( $row as $idx => $field ) {

                    if ( $idx > 0 ) $body .= ',';
                    // Echo field content (sanitized)
                    $body .= VisitorLog_Utility_File::sanitize_db_field($field);
                }

                // Finish INSERT statement
                $body .= ');' . PHP_EOL;
            }
            // Place two-empty lines after table data
            $body .= PHP_EOL . PHP_EOL;

        } // end foreach

        return $preamble . $body;

    } // END func

    /**
     * Scheduled backup handler.
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Logger::instance()->warning()
     * @uses VisitorLog_Logger::instance()->log_backup()
     */
    public function scheduled_backup_handler()
    {
        $send_to_email = VisitorLog_Utility::get_option_sl( 'on_input_email_manual' );
        $result_file = '';
        $result_mail = '';

        $result = self::execute_backup( $attachment );

        if ( $result < 0 ) {
            $result_file = -1;
            $msg = __('Error creating a backup file', 'visitorlog');
            VisitorLog_Logger::instance()->warning( $msg );
        } else {
            $result_file = 1;
            $current_time = current_time( 'timestamp' );
            VisitorLog_Utility::update_option_sl( 'last_backup_time', $current_time );
            $msg = __('Successful creation of the backup file', 'visitorlog');
            VisitorLog_Logger::instance()->warning( $msg ); 

            if ( 'on' == $send_to_email ) {
                $result_mail = self::send_email_auto( $attachment );
                if ( $result_mail < 0 ) {
                    $msg = __('An error occurred when sending mail. Check the error log', 'visitorlog');
                    VisitorLog_Logger::instance()->warning( $msg );
                } else {
                    $msg = __('The message with the report file has been sent successfully', 'visitorlog');
                    VisitorLog_Logger::instance()->warning( $msg );
                }
            } else {
                $msg = __('Enable the permission to send a message by mail', 'visitorlog');
                VisitorLog_Logger::instance()->warning( $msg );
            }
        } 
        VisitorLog_Logger::instance()->log_backup( $result_file, $result_mail, 'auto' );

    } // END func  

    /**
     * Send email auto.
     *
     * @param string $attachment
     * @uses VisitorLog_Send_Email::Send_email()
     */
    public static function send_email_auto( $attachment )
    {
        $date    = gmdate( 'l, F jS, Y \a\\t H:i', current_time( 'timestamp' ) );
        $subject = 'Visitor Log - Automatic Backup report - ' . ' ' . $date;

        $msg = 'The application contains the data of automatic database backup';
        $headers  = 'From: Visitorlog, site: ' . get_bloginfo('url') . PHP_EOL;
        $headers .= '<br><br>' . $msg . PHP_EOL;

        $send = VisitorLog_Send_Email::Send_email( $headers, $subject, $attachment, $err );
        if ( $send ) {
            return 1;
        } else {
            return -1;
        }

    } // END func


} // END class