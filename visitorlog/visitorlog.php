<?php
/**
 * Plugin Name:  VisitorLog 
 * Plugin URI:   https://itaugustine.com
 * Description:  Security management, Anti DDoS, Admin panel protection, Database protection, File system protection, Anti-spam, Statistics, Reports, Tables, Graphs, Logs of your site. A lot of what you need.	
 *
 * Version:      1.0.5
 * Author:       IT-Augustine
 * Author URI:   https://profiles.wordpress.org/itaugustine/
 * License:      GPL-2.0+
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:  visitorlog
 * Domain Path:  /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'VISITORLOG_PLUGIN_VERSION' ) ) define( 'VISITORLOG_PLUGIN_VERSION', '1.0.5' );
if ( ! defined( 'VISITORLOG_DB_VERSION' ) )     define( 'VISITORLOG_DB_VERSION',     '1.0.1' );
if ( ! defined( 'VISITORLOG_PLUGIN_FILE' ) )    define( 'VISITORLOG_PLUGIN_FILE', __FILE__ );
if ( ! defined( 'VISITORLOG_PLUGIN_DIR' ) )     define( 'VISITORLOG_PLUGIN_DIR',  plugin_dir_path( VISITORLOG_PLUGIN_FILE ) );
if ( ! defined( 'VISITORLOG_PLUGIN_URL' ) )     define( 'VISITORLOG_PLUGIN_URL',  plugin_dir_url( VISITORLOG_PLUGIN_FILE ) );
if ( ! defined( 'VISITORLOG_PLUGIN_SLUG' ) )    define( 'VISITORLOG_PLUGIN_SLUG', plugin_basename( VISITORLOG_PLUGIN_FILE ) );
$slug = explode( '/', VISITORLOG_PLUGIN_SLUG );
if ( ! defined( 'VISITORLOG_PLUGIN_SLUG0' ) ) define( 'VISITORLOG_PLUGIN_SLUG0', $slug[0] );
if ( ! defined( 'VISITORLOG_PLUGIN_SLUG1' ) ) define( 'VISITORLOG_PLUGIN_SLUG1', $slug[1] );
if ( ! defined( 'VISITORLOG_DIR' ) )          define( 'VISITORLOG_DIR', 'visitorlog' );

if ( ! function_exists( 'visitorlog_autoload' ) ) {

	function visitorlog_autoload ( $class_name ) {

		if ( 0 !== strpos( $class_name, 'SecurityLine\VisitorLog' ) ) return;
		$class_name = substr( $class_name, 24 );
		if ( 0 !== strpos( $class_name, 'VisitorLog_' ) ) return;
		 
		$autoload_types = array(
			'class'  => 'class',
			'pages'  => 'page',
			'view'   => 'view',
			'tables' => 'table',
			'others' => 'other',
			'secu'   => 'secu',
			'stat'   => 'stat',
			'logs'   => 'log',
			'inst'   => 'inst',
			'report' => 'report'
		);
		foreach ( $autoload_types as $type => $prefix ) {
			$autoload_dir  = \trailingslashit( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $type );
			$autoload_path = sprintf( '%s%s-%s.php', $autoload_dir, $prefix, strtolower( str_replace( '_', '-', $class_name ) ) );

			if ( file_exists( $autoload_path ) ) {
				require_once $autoload_path;
				break;
			}
		}
	}
}
spl_autoload_register( 'visitorlog_autoload' );
add_action( 'plugins_loaded', array( 'SecurityLine\VisitorLog\VisitorLog_System', 'run' ) );