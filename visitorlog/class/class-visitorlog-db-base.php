<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_DB_Base class
 *
 * @method public        function __construct()
 * @method public        function table_name()
 * @method public        function get_my_sql_version()
 * @method public        function insert_update_log()
 * @method public static function checking_table()
 */
class VisitorLog_DB_Base
{
	private  static $instance = null;
	public   static $table_prefix_sl;
	public   static $table_prefix;
	public   static $wpdb_vl;               // mixed $wpdb WordPress Database.

	public static function instance()
	{
		if ( null == self::$instance ) self::$instance = new self();
		return self::$instance;

	} // END func

	public function __construct()
	{

		global $wpdb;

		self::$wpdb_vl = &$wpdb;
		self::$table_prefix = $wpdb->prefix;
		self::$table_prefix_sl = $wpdb->prefix . 'visitorlog_';

	} // END __construct

	/**
	 * Create entire table name.
	 *
	 * @param  mixed $suffix Table suffix.
	 * @param  null  $tablePrefix Table prefix.
	 * @return string Table name.
	 */
	public function table_name_sl( $suffix, $tablePrefix = null )
	{
		return ( null == $tablePrefix ? self::$table_prefix_sl : $tablePrefix ) . $suffix;

	} // END func

	/**
	 * Get MySQL Version.
	 *
	 * @return mixed MySQL vresion.
	 */
	public function get_my_sql_version()
	{
		return VisitorLog_DB_Base::$wpdb_vl->get_var( 'SHOW VARIABLES LIKE "version"', 1 );

	} // END func

	/**
	 * Insert update log.
	 *
	 * @param array $data log data.
	 * @return void
	 */
	public function insert_update_log( $data )
	{
		$result = VisitorLog_DB_Base::$wpdb_vl->insert( $this->table_name_sl( 'update' ), $data );
		return $result;
		
	} // END func

	/**
	 * Checking for a table in the database
	 *
	 * @return boolean
	 */
	public static function checking_table( $suffix='' )
	{
		if ( '' == $suffix ) $suffix = 'options';

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . $suffix;
		$result = VisitorLog_DB_Base::$wpdb_vl->query( "SHOW TABLES LIKE '" . $table_name . "'" );

		if ( null == $result ) return false;
		return true;

	} // END func


} // END class