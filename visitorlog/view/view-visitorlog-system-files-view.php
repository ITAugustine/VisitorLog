<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_System_Files_View class
 *
 * @method  public static function get_class_name() 
 * @method  public static function wp_config_view() 
 * @method  public static function hide_fields()
 */

class VisitorLog_System_Files_View
{
	public static function get_class_name()
    {
		return __CLASS__;
	}

	/**
	 * Renders the wp comfig page.
	 *
	 * @return void
	 */
	public static function wp_config_view()
    {
        $config_file = VISITORLOG_HOME_DIR . 'wp-config.php';
        $symbols = array(
            'DB_NAME',
            'DB_USER',
            'DB_PASSWORD',
            'DB_HOST',
            'AUTH_KEY',
            'SECURE_AUTH_KEY',
            'LOGGED_IN_KEY',
            'NONCE_KEY',
            'AUTH_SALT',
            'SECURE_AUTH_SALT',
            'LOGGED_IN_SALT',
            'NONCE_SALT',
            'table_prefix'
        );

		?>
		<div id="vl-show-wp-config">
			<?php
			if ( false !== strpos( ini_get( 'disable_functions' ), 'show_source' ) ) {
				esc_html_e( 'File content could not be displayed', 'visitorlog' );
				echo '<br />';
				esc_html_e( 'It appears that the show_source() PHP function has been disabled on the server', 'visitorlog' );
				echo '<br>';
				esc_html_e( 'Please, contact your host support and have them enable the show_source() function for the proper functioning of this feature.', 'visitorlog' );

                $err = 'wp_config_view(): Can\'t read the file config.php';
                VisitorLog_Logger::instance()->warning( $err );
                VisitorLog_System_Check::update_data_of_system_check( 18, '' );

			} else {
				if ( file_exists( $config_file ) ) {
                    $content = show_source( $config_file, true );
                    $i = 0;
                    foreach ( $symbols as $snippet ) {
                        $result = self::hide_fields( $content, $snippet );
                        if ( false !== $result ) $content = $result;
                    }
                    VisitorLog_System_Check::update_data_of_system_check( 18, 1 );  
                    echo wp_kses( $content, 'post' );
				} else {
					$files = get_included_files();
					$configFound = false;
					if ( is_array( $files ) ) {
						foreach ( $files as $file ) {
							if ( stristr( $file, 'wp-config.php' ) ) {
								$configFound = true;
                                VisitorLog_System_Check::update_data_of_system_check( 18, 1 );
								show_source( $file );
								break;
							}
						}
					}
					if ( ! $configFound ) {
                        $err = 'wp_config_view(): wp-config.php not found';
                        VisitorLog_Logger::instance()->warning( $err );
                        esc_html_e( 'wp-config.php not found', 'visitorlog' );
                    }    
				}
			}
			?>
		</div>
		<?php

	} // END func

    public static function hide_fields( $content, $field )
    {
        if ( false === strpos( $content, $field ) ) return false;

        if ( 'table_prefix' == $field ) {

            $pos1 = stripos( $content, $field );        
            $cut1 = substr( $content, $pos1 );

            $pos2 = stripos($cut1, "'");
            $cut2 = substr( $cut1, $pos2 + 1 );

            $pos3 = -1;

            $pos4 = stripos($cut2, "'");
            $cut4 = substr( $cut2, 0, $pos4 );
            $strlen = strlen( $cut4 );
            $pos4--;
        } else {
            $pos1 = stripos( $content, $field );        
            $cut1 = substr( $content, $pos1 );

            $pos2 = stripos($cut1, "'");
            $cut2 = substr( $cut1, $pos2 + 1 );

            $pos3 = stripos($cut2, "'");
            $cut3 = substr( $cut2, $pos3 + 1 );

            $pos4 = stripos($cut3, "'");
            $cut4 = substr( $cut3, 0, $pos4 );
            $strlen = strlen( $cut4 );
        }

        $snippet = '**********';
        if ( 0 != $strlen ) {
            if ( $strlen > 4 ) {
                $cut5 = substr( $cut4, -2 ); 
                $snippet .= $cut5;
            }    

            $pos5 = $pos1 +$pos2 + $pos3 + 2;
            $pos6 = $pos1 +$pos2 + $pos3 + $pos4 + 2;
            $content = substr( $content, 0, $pos5 ) . $snippet . substr( $content, $pos6 );

        } else {
            $pos5 = $pos1 +$pos2 + $pos3 + $pos4 + 2;
            $content = substr( $content, 0, $pos5 ) . $snippet . substr( $content, $pos5 );
        }

        if ( false === $content ) return false;

        return $content;

    } // END func


} // END class