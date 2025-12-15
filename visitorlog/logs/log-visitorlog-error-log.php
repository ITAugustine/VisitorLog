<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods VisitorLog_Error_Log class.
 *
 * @method public    static function get_class_name() 
 * @method public    static function error_log_view() 
 * @method protected static function render_error_log() 
 * @method protected static function last_lines()
 */
class VisitorLog_Error_Log
{
	public static function get_class_name()
    {
		return __CLASS__;
	}

    /**
     * Viewing error logs
     * 
     * @uses VisitorLog_System_View::show_message() 
     */
    public static function error_log_view()
    {
        $lines = self::render_error_log( $err );
        if ( $err ) VisitorLog_System_View::show_message( 'orange', $err, $err );
        ?>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <p style="font-size:14px;">
1.&nbsp;
<?php esc_html_e('Check the necessary data write permissions for the wp-content folder and the debug.log file','visitorlog');?>
<br>2.&nbsp;
<?php esc_html_e('Enable debugging mode by setting the WP_DEBUG and WP_DEBUG_LOG constants to true in the configuration file. wp-config.php','visitorlog');?>
                        </p>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped js-basic-example dataTable">
                                <thead>
                                    <tr>
                <td class="vl-thead" width="40">#</td>
                <td class="vl-thead" width="200"><?php esc_html_e('Date / Time', 'visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Error', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                <td class="vl-thead" width="40">#</td>
                <td class="vl-thead" width="200"><?php esc_html_e('Date / Time', 'visitorlog');?></td>
                <td class="vl-thead">            <?php esc_html_e('Error', 'visitorlog');?></td>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php    
                                    if ('' == $err) {
                                        $i = 1;
                                        foreach ( $lines as $line ) {
                                            $error = $line['error'];
                                            $time  = $line['time'];
                                            if ( !empty($error) ) {
                                                ?>
                                                <tr style="font-size:13px;">
                                                    <td><?php echo esc_html($i);?></td>
                                                    <td><?php echo esc_html($time);?></td>
                                                    <td><?php echo esc_html($error);?></td>
                                                </tr>    
                                                <?php
                                                $i++;
                                            }
                                        }
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
	 * Renders error log.
	 *
	 * @return void
	 *
	 * Plugin-Name: Error Log Dashboard Widget
	 * Plugin URI: http://wordpress.org/extend/plugins/error-log-dashboard-widget/
	 * Description: Robust zero-configuration and low-memory way to keep an eye on error log.
	 * Author: Andrey "Rarst" Savchenko
	 * Author URI: http://www.rarst.net/
	 * Version: 1.0.2
	 * License: GPLv2 or later
	 * Includes last_lines() function by phant0m, licensed under cc-wiki and GPLv2+
     *
     * @uses VisitorLog_Server_Information_Handler::last_lines()
     * @uses VisitorLog_Server_Information_Handler::get_class_name()
     *
     * @return array $lines - text system errors
     * @return link $err    - processing errors 
	 */
    protected static function render_error_log( &$err )
    {
        $lines = array();
        $err   = '';
        $log_errors = ini_get( 'log_errors' );

        if ( !$log_errors ) {
            $err = __( 'Error logging disabled', 'visitorlog' )
                 . '<br/>' 
                 /* translators: 1: link, 2: </a>. */
                 . sprintf( __( 'To enable error logging, please check this %1$s help document %2$s', 'visitorlog' ), '<a href="https://codex.wordpress.org/Debugging_in_WordPress" target="_blank">', '</a>' );
            return $lines;
        }

        $error_log = ini_get( 'error_log' );
        $logs      = array( $error_log );
        $count     = 100; //100;

        foreach ( $logs as $log ) {
            if ( is_readable( $log ) ) {
                $lines = array_merge( $lines, self::last_lines( $log, $count ) );
            }
        }
        if ( empty( $lines ) ) {
            $err = __( 'No error log was found', 'visitorlog' );
            return $lines;
        }

        $lines = array_map( 'trim', $lines );
        $lines = array_filter( $lines );

        foreach ( $lines as $key => $line ) {
            if ( false !== strpos( $line, ']' ) ) {
                list( $time, $error ) = explode( ']', $line, 2 );
            } else {
                list( $time, $error ) = array( '', $line );
            }
            $time        = trim( $time, '[]' );
            $error       = trim( $error );
            $lines[$key] = compact( 'time', 'error' );
        }
        
        if ( 1 < count( $lines ) ) {
            uasort( $lines, array( VisitorLog_Server_Information_Handler::get_class_name(), 'time_compare' ) );
            $lines = array_slice( $lines, 0, $count );
        }

        return $lines;

    } // END func

    /**
     * Gets line count.
     *
     * @param mixed $path the error log file location.
     * @param int   $line_count number of lines in the error log.
     *
     * @return string Line Count.
     */
    protected static function last_lines( $path, $line_count )
    {
        $lines = array();
        $lines = VisitorLog_Utility_File::wp_get_contents( $path );
        $replace = str_replace('[', '##[', $lines);
        $lines = array_reverse(explode('##', $replace));
        $lines = array_slice( $lines, 0, - 1 );

        return array_slice( $lines, 0, $line_count );

    } // END func


} // END class