<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods VisitorLog_Utility.
 *
 * @method public static function get_class_name()
 * @method public static function array_merge()
 * @method public static function update_option_sl()
 * @method public static function get_option_sl()
 * @method public static function get_data_select()
 * @method public static function get_row()
 * @method public static function get_data_table_nolimit() 
 * @method public static function generate_alpha_numeric_random_string()
 * @method public static function get_server()
 * @method public static function vl_redirect()
 * @method public static function vl_redirect2()
 * @method public static function get_option_image()
 * @method public static function redirect_html()
 * @method public static function vl_confirm()
 * @method public static function vl_alert()
 * @method public static function link_in_table()
 * @method public static function edit_row_in_table()
 * @method public static function render_picture()
 * @method public static function is_admin_ip()
 * @method public static function check_update_db_version()
 * @method public static function localize_script()
 */
class VisitorLog_Utility
{
    public static $home_site = 'https://itaugustine.com';
    public static $name_firm = '&trade;IT-Augustine';

	public static function get_class_name()
    {
		return __CLASS__;
	}

	/**
	 * Merge two given arrays into one.
	 *
	 * @param mixed $arr1 First array.
	 * @param mixed $arr2 Second array.
	 *
	 * @return array Merged Array.
	 */
	public static function array_merge( $arr1, $arr2 )
    {
		if ( ! is_array( $arr1 ) && ! is_array( $arr2 ) ) {
			return array();
		}
		if ( ! is_array( $arr1 ) ) {
			return $arr2;
		}
		if ( ! is_array( $arr2 ) ) {
			return $arr1;
		}

		$output = array();
		foreach ( $arr1 as $el ) {
			$output[] = $el;
		}
		foreach ( $arr2 as $el ) {
			$output[] = $el;
		}

		return $output;

	} // END func

	/**
	 * Updating the parameter value in the database table
	 *
	 * @param  $option_name - option key   (text)
	 * @param  $value       - option value (text)
	 *
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 */
	public static function update_option_sl( $option_name, $value )
    {
		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'options';

		$result = VisitorLog_DB_Base::$wpdb_vl->update($table_name, ['option_value'=>$value], ['option_name'=>$option_name]);
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) return VisitorLog_DB_Base::$wpdb_vl->last_error;
		return $result;

	} // END func

	/**
	 * Reading option from a DB table
	 * 
	 * @param  text $option_name - option key
	 * @return text                option value/false
	 *
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->debug()
	 */
	public static function get_option_sl( $option_name )
    {
		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'options';
		$array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `option_name` = %s", $option_name));
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error || empty($array) ) {
			$err  = 'get_option_sl()-Error select data. Option name=' . $option_name;
            $err .= ', Err: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
			VisitorLog_Logger::instance()->debug( $err );
			return false;
		}

		return $array[0]->option_value;

	} // END func

    /**
     * Get data from a table for htaccess file
     * 
     * @param $table  - table name
     *
     * @return  $result - array of objects            
     * @return  $number_rows - link
     * @return  $err - link
     *
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->debug()
     */
    public static function get_data_for_htaccess( $table, &$num_rows, &$err, $spam='' )
    {
        $err = '';
        $num_rows = 0;
        $rows_limit = 200;
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;

        switch( $spam ) {

            case 'Comment':
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `block`=%s AND `reason`='Comment' ORDER BY `id` DESC LIMIT %d", 'perm', $rows_limit));
                break;
            case 'ContactForm':
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `block`=%s AND `reason`='ContactForm' ORDER BY `id` DESC LIMIT %d", 'perm', $rows_limit));
                break;            
            default:
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `block`=%s ORDER BY `id` DESC LIMIT %d", 'perm', $rows_limit));
                break;
        }
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error )  {
            $err = __( 'Database error', 'visitorlog' );
            $msg_err = 'get_data_for_htaccess()-Error select data in table: ' . $table . ' - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->debug( $msg_err );
            return false;
        }
    
        $num_rows = VisitorLog_DB_Base::$wpdb_vl->num_rows;
        return $result;

    } // END func

    /**
     * Get data from a table by condition
     * 
	 * @param $table  - table name
	 * @param $select - viewing mode
	 *
     * @return  $result - array of objects            
     * @return  $number_rows - link
     * @return  $err - link
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->debug()
     */
    public static function get_data_select( $table, $select, &$number_rows, &$err )
    {
        $err = '';
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;
       	$limit = 0;

        switch( $table ) {
        /*** Tables ***/	
            case 'lockdown_ip':
                $limit = VisitorLog_Utility::get_option_sl( 'ddos_permanent_block_ip' );
    		   	$rows_limit = VisitorLog_Utility::get_option_sl( '#_display_limit' );
                break;
            case 'bots_table':
                $limit = VisitorLog_Utility::get_option_sl( 'ddos_permanent_block_ip' );
                $rows_limit = VisitorLog_Utility::get_option_sl( '#_display_limit' );
                break;
            case 'event_404':
                $limit = VisitorLog_Utility::get_option_sl( 'ddos_permanent_block_ip' );
                $rows_limit = VisitorLog_Utility::get_option_sl( '#_display_limit' );
                break;                    
            case 'spam':
                $rows_limit = VisitorLog_Utility::get_option_sl( '#_display_limit' );
                break;
        /*** Logs ***/    
            case 'users_report':
            	$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'users';
                $rows_limit = VisitorLog_Utility::get_option_sl( 'row_report_file_limit' );
                break;
            case 'users':
                $rows_limit = VisitorLog_Utility::get_option_sl( 'log_display_limit' );
                break;
            case 'admin_ip':
                $rows_limit = VisitorLog_Utility::get_option_sl( 'log_display_limit' );
                break;
            case 'login_activity':
                $rows_limit = VisitorLog_Utility::get_option_sl( 'log_display_limit' );
                break;
            case 'action_log':
                $rows_limit = VisitorLog_Utility::get_option_sl( 'log_display_limit' );
                break;
            default:
                $rows_limit = VisitorLog_Utility::get_option_sl( '#_display_limit' );
        }
        switch( $select ) { 
            case 'viewall':
                $param = 0;
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `id` != %d ORDER BY `id` DESC LIMIT %d", $param, $rows_limit));
                break;
            case 'viewban':
                $param = 'perm';
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `block` = %s ORDER BY `id` DESC LIMIT %d", $param, $rows_limit));
                break;
            case 'viewunban':
                $param = 'perm';
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `block` != %s OR `block` IS NULL ORDER BY `id` DESC LIMIT %d", $param, $rows_limit));
                break;                    
            case 'notblock':
                $param = 'notblock';
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `block` = %s OR `block` IS NULL ORDER BY `id` DESC LIMIT %d", $param, $rows_limit));
                break; 
            case 'type':
                $param = 'useful';
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `type` = %s ORDER BY `id` DESC LIMIT %d", $param, $rows_limit));
                break; 
            case 'Comment':
                $param = 'Comment';
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `reason` = %s ORDER BY `id` DESC LIMIT %d", $param, $rows_limit));
                break; 
            case 'ContactForm':
                $param = 'ContactForm';
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `reason` = %s ORDER BY `id` DESC LIMIT %d", $param, $rows_limit));
                break; 
            case 'report':
                $rows_limit = VisitorLog_Utility::get_option_sl( 'row_report_file_limit' );
                $param = 0;
                $result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `id` != %d ORDER BY `id` DESC LIMIT %d", $param, $rows_limit));
                break;
        }
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error )  {
            $err = __( 'Database error', 'visitorlog' );
            $msg_err = 'get_data_select()-Error select data in table: ' . $table . ' - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->debug( $msg_err );
            return false;
        }
	
        $number_rows = VisitorLog_DB_Base::$wpdb_vl->num_rows;

        return $result;

    } // END func

    /**
     * Get row from a table by condition
     * 
	 * @param $table  - table name
	 * @param $row_id - line id
	 *
     * @return  $result      - array of objects            
     * @return  $number_rows - link
     * @return  $err         - error
     *
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->debug()
     */
    public static function get_row( $table, $row_id, &$number_rows, &$err )
    {
        $err = '';
		$table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;

		$result = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `id` = %d", $row_id));
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error )  {
			$err = VisitorLog_DB_Base::$wpdb_vl->last_error;
			$msg_err = 'get_row()-Error select data in table: ' . $table . ' - ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
			VisitorLog_Logger::instance()->debug( $msg_err );
		}
			
        $number_rows = VisitorLog_DB_Base::$wpdb_vl->num_rows;

        return $result;

    } // END func

    /**
     * Get data from the table
	 *
	 * @param   string $table - name table 
     * 
     * @return  array $result   - data of objects            
     * @return  link  $num_rows - number of rows
     * @return  link  $err      - error
     *
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->debug()
     */
    public static function get_data_table_nolimit( $table, &$num_rows, &$err )
    {
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;
        $num_rows = 0;
       	$err = '';

        $result = VisitorLog_DB_Base::$wpdb_vl->get_results( VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE id != %d", 0));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
        	$err = VisitorLog_DB_Base::$wpdb_vl->last_error;
            $msg_err = 'Get_data_table_nolimit()-Err select data into: ' . $err;
            VisitorLog_Logger::instance()->debug( $msg_err );
            return false;
        }
	
        $num_rows = VisitorLog_DB_Base::$wpdb_vl->num_rows;
        return $result;

    } // END func

    /**
     * Generates a random alpha-numeric number
     *
     * @param  type    $string_length
     * @return string
     */
    public static function generate_alpha_numeric_random_string( $string_length )
    {
        $allowed_chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = '';
        for ( $i = 0; $i < $string_length; $i++ ) {
            $string .= $allowed_chars[wp_rand( 0, strlen( $allowed_chars ) - 1 )];
        }
        return $string;

    } // END func 

    public static function get_server( &$server )
    {
		$server = '';

		if ( !isset($_SERVER['SERVER_SOFTWARE']) ) return '';

    	$server = strtolower( sanitize_text_field(wp_unslash($_SERVER['SERVER_SOFTWARE'])) );

        if ( strstr($server, 'apache') )    return 'apache';
		if ( strstr($server, 'nginx') )     return 'nginx';
		if ( strstr($server, 'litespeed') ) return 'litespeed';
		if ( strstr($server, 'iis') )       return 'iis';

        return 'other';

    } // END func

    /**
     *  Note:
     *  We redirect to updating the page using js tools when it is necessary 
     *  to do this after the headers are issued.
     */
    public static function vl_redirect( $url1, $url2='' )
    {
        echo '<s' . 'cript>location.href="';
        echo esc_url(admin_url('admin.php?page=')) . esc_html($url1) . esc_html($url2);
        echo '&_wpnonce=' . esc_html(wp_create_nonce('visitorlog_nonce')) . '"';
        echo '</s' . 'cript>';

    } // END func

    /**
     *  Note:
     *  We redirect to updating the page using js tools when it is necessary 
     *  to do this after the headers are issued.
     */
    public static function vl_redirect2( $addr )
    {
        echo '<s' . 'cript>location.href="#';
        echo esc_html($addr) . '"';
        echo '</s' . 'cript>';

    } // END func

    /**
     * Reading option image from a DB table
     * 
     * @param  text $name - option key
     * @return int          option id_post/false
     * @return text         option $class
     *
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->debug()
     */
    public static function get_option_image( $name, &$class )
    {
        $class = '';
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'images';

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `name` = %s", $name));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error || empty($array) ) {
            $err  = 'get_option_image()-Error select data. Option name=' . $name;
            $err .= ', Err: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->debug( $err );
            return false;
        }
        $class = $array[0]->class;

        return $array[0]->id_post;

    } // END func

    /**
     *  Note:
     *  As an exception, we redirect to refresh the page using js tools 
     *  when necessary to do so after formatting the headings.
     */
    public static function redirect_html( $name, $title, $url1, $url2='' )
    {
        $size = array(16,16);
        $id = VisitorLog_Utility::get_option_image( $name, $class );

        echo '<a href="';
        echo esc_url(admin_url('admin.php?page=')) . esc_html($url1) . esc_html($url2);
        echo '&_wpnonce=' . esc_html(wp_create_nonce('visitorlog_nonce')) . '"';
        echo 'title="' . esc_html($title) . '">';
        echo wp_get_attachment_image($id, $size) . '&ensp;' . esc_html($title);
        echo '</a>';

    } // END func

    /**
     *  Note:
     *  We redirect to updating the page using js tools when it is necessary 
     *  to do this after the headers are issued.
     */
    public static function vl_confirm( $msg, $url )
    {
        echo '<s' . 'cript>if (!confirm("' . esc_html($msg) . '")) {location.href="';
        echo esc_url(admin_url('admin.php?page=' . esc_html($url)));
        echo '&_wpnonce=' . esc_html(wp_create_nonce('visitorlog_nonce')) . '"}';
        echo '</s' . 'cript>';

    } // END func

    /**
     *  Note:
     *  We redirect to updating the page using js tools when it is necessary 
     *  to do this after the headers are issued.
     */
    public static function vl_alert( $msg, $url )
    {
        echo '<s' . 'cript>alert("' . esc_html($msg) . '"); location.href="';
        echo esc_url(admin_url('admin.php?page=' . esc_html($url)));
        echo '&_wpnonce=' . esc_html(wp_create_nonce('visitorlog_nonce')) . '"';
        echo '</s' . 'cript>';

    } // END func

    /**
     *  Note:
     *  We redirect to updating the page using js tools when it is necessary 
     *  to do this after the headers are issued.
     *  We use the plugin's catalog to store pictures.
     */
    public static function edit_row_in_table( $name, $title, $url1, $url2='' )
    {
        $id = VisitorLog_Utility::get_option_image( $name, $class );

        echo '<a href="';
        echo esc_url(admin_url('admin.php?page=')) . esc_html($url1) . esc_html($url2);
        echo '&_wpnonce=' . esc_html(wp_create_nonce('visitorlog_nonce'));
        echo '" title="' . esc_html($title) . '">';
        echo wp_get_attachment_image($id);
        echo '</a>';

    } // END func

    /**
     *  Note:
     *  We redirect to updating the page using js tools when it is necessary 
     *  to do this after the headers are issued.
     *  We use the plugin's catalog to store pictures.
     */
    public static function link_in_table( $id, $msg, $name )
    {
        $id_image = VisitorLog_Utility::get_option_image( $name, $class );

        echo '<a href="javascript:void(0)" id="' . esc_html($id);
        echo '" title="' . esc_html($msg) . '">';
        echo wp_get_attachment_image($id_image) . '&nbsp;';
        echo esc_html($msg) . '</a>';

    } // END func

    public static function render_picture( $name, $size='', $attr='' )
    {
        $id = VisitorLog_Utility::get_option_image( $name, $class );

        echo wp_get_attachment_image( $id, $size, '', $attr );

    } // END func

    public static function is_admin_ip()
    {
        $param = VisitorLog_Detection::instance()->get_user_ip();
        $ip = $param['ip'];

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'admin_ip';

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_ip` = %s", $ip));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'is_admin_ip()-Error select data in table ADMIN_IP: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->debug( $err );
            return false;
        }

        if ( empty($array) ) return false;

        return true;

    } // END func

    /**
     * @return true  - Update the database to the new version
     *         false - A new version of the database has been installed
     */
    public static function check_update_db_version()
    {
        $db_version = VisitorLog_Utility::get_option_sl('db_version');

        if ( version_compare(VISITORLOG_DB_VERSION, $db_version) > 0 ) {
            return true;
        }    
        return false;

    } // END func  

    /**
     * Adds additional data before the specified script, which is in the output queue.
     *         
     */
    public static function localize_script( $slug )
    {
        wp_localize_script( 'visitorlog-action', 'visitorlog_obj', array( 
            'msg1'    => __( 'Confirm your action', 'visitorlog' ),
            'msg2'    => __( 'Yes', 'visitorlog' ),
            'msg3'    => __( 'Cancel', 'visitorlog' ),
            'action1' => $slug,
            'action2' => '&reg=clear_tbl',
            'action3' => '&reg=del',
            'action4' => '&data=',
            'action5' => '&reg=ban',
            'action6' => '&reg=unban',
            'action7' => '&_wpnonce=' . esc_html(wp_create_nonce('visitorlog_nonce')),
        ) );

    } // END func 


} // END class