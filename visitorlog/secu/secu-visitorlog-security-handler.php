<?php
/**
 * Security Handler.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods VisitorLog_Security_Handler.
 *
 * @method public static function instance()
 * @method public        function validate_ip_list()
 * @method protected     function validate_user_agent_list()
 * @method public        function handler_htaccess_view()
 * @method public        function handler_delete_table_rows()
 * @method public        function ban_unban_ip()
 * @method public        function truncate_table()
 * @method public        function delete_rows_in_table()
 * @method public static function delete_rows()
 * @method public        function handler_blocking_selected_field()
 * @method public static function block_field_update()
 * @method protected     function get_user_ip_address()
 */
class VisitorLog_Security_Handler
{
    private static $instance = null;    

    public static function instance()
    {
        if ( null == self::$instance ) self::$instance = new self;
        return self::$instance;
    }

    /**
     * Validate ip list
     *
     * @param array  $ip_list_array
     * @param string $ban_your_ip
     *
     * @uses WP_Http::is_ip_address($item)
     */
    public function validate_ip_list( $ip_list_array, $ban_your_ip = '' )
    { 
        $list = array();
        $errors = '';

        if( !empty($ip_list_array) ) {

            foreach( $ip_list_array as $item ) {

                $item = filter_var( $item, FILTER_SANITIZE_STRING );
                if ( strlen($item) > 0 ) {

                    //ipv6 - for now we will support only whole ipv6 addresses, NOT ranges
                    if( strpos($item, ':') !== false ) {

                        //possible ipv6 addr
                        $res = \WP_Http::is_ip_address($item);
                        if( FALSE === $res ) {

                            $errors .= "\n" . $item . " ";
                            $errors .= __('is not a valid ip address format', 'visitorlog');
                        } else if( $res == '6' ) {

                            $list[] = trim($item);
                        }
                        continue;
                    }

                    $ipParts = explode('.', $item);
                    $isIP = 0;
                    $partcount = 1;
                    $goodip    = true;
                    $foundwild = false;
                   
                    if (count($ipParts) < 2) {

                        $errors .= "\n" . $item . " ";
                        $errors .= __('is not a valid ip address format', 'visitorlog');
                        continue;
                    }
                    foreach ($ipParts as $part) {

                        if ($goodip == true) {

                            if ( (is_numeric(trim($part)) && trim($part) <= 255 && trim($part) >= 0)
                                 || trim($part) == '*' ) 
                            {
                                $isIP++;
                            }

                            switch ($partcount) {
                                case 1:
                                    if (trim($part) == '*') {
                                        $goodip = false;
                                        $errors .= "\n" . $item . " ";
                                        $errors .= __('is not a valid ip address format', 'visitorlog');
                                    }
                                    break;
                                case 2:
                                    if (trim($part) == '*') {
                                        $foundwild = true;
                                    }
                                    break;
                                default:
                                    if (trim($part) != '*') {
                                        if ($foundwild == true) {
                                            $goodip = false;
                                            $errors .= "\n" . $item . " ";
                                            $errors .= __('is not a valid ip address format', 'visitorlog');
                                        }
                                    } else {
                                        $foundwild = true;  
                                    }
                                    break;
                            }

                            $partcount++;
                        }
                    }
                    if ( ip2long( trim(str_replace('*', '0', $item)) ) == false ) { 

                        //invalid ip 
                        $errors .= "\n" . $item . " ";
                        $errors .= __('is not a valid ip address format', 'visitorlog');
                    } 
                    elseif ( strlen($item) > 4 && !in_array($item, $list) ) {

                        $current_user_ip = $this->get_user_ip_address();
                        if ( $current_user_ip == $item && $ban_your_ip == 'ban_your_ip' ) {

                            //You can't ban your own IP
                            $errors .= "\n" . __('You cannot ban your own IP address: ', 'visitorlog') . $item;
                        }
                        else {
                            $list[] = trim($item);
                        }
                    }
                }
            }
        }
        else {
            //This function was called with an empty IP address array list
        }

        if ( strlen($errors) > 0 ) {

            $return_payload = array( -1, array($errors) );
            return $return_payload;
        }
        if ( sizeof($list) >= 1 ) {

            sort($list);
            $list = array_unique( $list, SORT_STRING );
            $return_payload = array( 1, $list );
            return $return_payload;
        }

        $return_payload = array( 1, array() );
        return $return_payload;

    } // END fun

    /**
     * Handler htaccess view.
     *
     * @uses VisitorLog_System_View::show_message()
     */
    public function handler_htaccess_view()
    {
        if ( !is_admin() ) return;

        if ( false !== strpos( ini_get( 'disable_functions' ), 'show_source' ) ) {

            $msg_err = __( 'File content could not be displayed', 'visitorlog' ) . '. '
                     . __( 'It appears that the show_source() PHP function has been disabled on the server', 'visitorlog' ) . '. '
                     . __( 'Please, contact your host support and have them enable the show_source() function for the proper functioning of this feature', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $msg_err );
            $err = 'handler_htaccess_view(): Can\'t read the file htaccess';
            VisitorLog_Logger::instance()->warning( $err );
            VisitorLog_System_Check::update_data_of_system_check( 19, '' );
            return;
        }    

        $filename = VISITORLOG_HOME_DIR . '.htaccess';

        if ( !file_exists($filename) ) {
            $msg_err = __( 'The plugin did not find the Htaccess system file', 'visitorlog' );
            VisitorLog_System_View::show_message( 'orange', $msg_err );
            $err = 'handler_htaccess_view(): Can\'t read the file htaccess';
            VisitorLog_Logger::instance()->warning( $err );
            VisitorLog_System_Check::update_data_of_system_check( 19, '' );
            return;
        }
        $contents = VisitorLog_Utility_File::wp_get_contents( $filename );
        $text = @highlight_string( $contents, true );

        if ( null == $text ) {
            $err = 'handler_htaccess_view(): Can\'t read the file htaccess';
            VisitorLog_Logger::instance()->warning( $err );
            VisitorLog_System_Check::update_data_of_system_check( 19, '' );
            return;
        }

        VisitorLog_System_Check::update_data_of_system_check( 19, 1 );
        $html  = '<div style="font-size:14px;color:blue;">';
        $html .= $text;
        $html .= '</div>';

        echo wp_kses( $html, 'post' );

    } // END func

    /**
     * Handler delete table rows.
     *
     * @param array/int/string $data_parse - rows for delete. 
     * @param string $table                - table name.
     *
     * @uses   VisitorLog_DB_Base::$table_prefix_sl
     * @return <0/0/>0  - fail/0/success
     */
    public function handler_delete_table_rows( $data_parse, $table )
    { 
        if ( null == $data_parse ) return 0;

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;

        if ( 'delete_row' == $data_parse && isset($_GET['row']) ) {

            if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                $get_row = sanitize_text_field(wp_unslash($_GET['row']));
            } else {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            $result = VisitorLog_DB_Base::$wpdb_vl->delete( $table_name, array('id' => $get_row) );

            switch ( $result ) { 
                case '1':
                    return 1;
                case '0':
                    return -1;
                default:
                    return -2;
            }
            return 0;        
        }   

        if ( 'on' == $data_parse[0] || 'delete_all' == $data_parse ) {

            $result = VisitorLog_DB_Base::$wpdb_vl->query( "TRUNCATE TABLE $table_name" );

            if ( $result ) {
                return 10;
            } else {
                return -10;
            }
        }

        for ( $i = 0; $i < count($data_parse); $i++ ) {

            $id = $data_parse[$i];
            $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE id = %d", $id));
            if ( !$result ) {
                return -20;
            }    
        }

        return 20;

    } // END func

    public function ban_unban_ip( $table, $data_parse, $ban, &$block, &$j )
    {  
        $block = '';
        $j = 0;
        if ( empty($data_parse) ) return 0;

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;

        for ($i = 0; $i < count($data_parse); $i++) {

            if ( 'on' != $data_parse[$i] ) {

                $id = $data_parse[$i];
                $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE id = %d ORDER BY id DESC LIMIT 1", $id));

                if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                    $err = 'ban_unban_ip()-Error select data in table ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                    VisitorLog_Logger::instance()->debug( $err );
                    return false;
                }
                if ( empty($array) ) continue;

                if ( 'ban' == $ban ) { 
                    if ( 'perm' == $array[0]->block ) continue;
                    $block = 'perm';
                    $j++; 
                }
                if ( 'unban' == $ban )  {
                    if ( '' == $array[0]->block )  continue;
                    $block = '';
                    $j++; 
                }    
                $result = VisitorLog_DB_Base::$wpdb_vl->update($table_name, ['block'=>$block], ['id'=>$id]);
                if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                    $err = 'ban_unban_ip()-Error update in table ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                    VisitorLog_Logger::instance()->debug( $err );
                    return false;
                } 
            }
        }
        return true;  

    } // END func 

    /**
     * Deleting all rows from the table. 
     *
     * @param string $table - table name 
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->debug()
     * @return 60/-60 - success/fail
     */
    public function truncate_table( $table )
    {  
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;

        $clear = VisitorLog_DB_Base::$wpdb_vl->query( "TRUNCATE TABLE $table_name" );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'truncate_table()-Error TRUNCATE TABLE: ' . $table . '-' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->debug( $err );
            return -60;
        }
        return 60;

    } // END func

    /**
     * Delete rows in the lockdown_ip table. 
     *
     * @param       string $table      - table name.
     * @param array/string $data_parse - id for delete. 
     *
     * @uses   VisitorLog_DB_Base::$table_prefix_sl
     * @uses   VisitorLog_Utility::get_option_sl()
     * @uses   VisitorLog_Logger::instance()->debug()
     *
     * @return -1/0/1 - fail/0/success
     * @return $block - the presence of blocked addresses
     * @return $j - the counter of deleted rows
     */
    public static function delete_rows( $table, $data_parse, &$block, &$j )
    {  
        $j = 0;
        $block = 0;
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;

        if ( is_array($data_parse) ) {
            if ( empty($data_parse) ) return 0;

        } elseif ( 'all' == $data_parse ) {
            $clear = VisitorLog_DB_Base::$wpdb_vl->query("TRUNCATE TABLE $table_name");
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'delete_rows()-Error TRUNCATE TABLE ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->debug( $err );
                return -1;
            }
            $j = 1;
            return 1;
        }
        $num_arr = count($data_parse);

        for ( $i = 0; $i < $num_arr; $i++ ) {

            if ( 'on' != $data_parse[$i] ) {

                $id = $data_parse[$i];
                $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE id LIKE %d", $id));
                if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                    $err = 'delete_rows()-Error select data in table '.VisitorLog_DB_Base::$wpdb_vl->last_error;
                    VisitorLog_Logger::instance()->debug( $err );
                    return -1;
                }
                if ( 'fcd_scan' != $table ) {
                    if ( 'whitelist' == $table ) {
                        $block++;
                    } else {
                        if ( $array[0]->block == 'perm' ) {
                            $block++;
                        }
                    }
                }

                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE id LIKE %d", $id));
                if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                    $err = 'delete_rows()-Error delete data in table '.VisitorLog_DB_Base::$wpdb_vl->last_error;
                    VisitorLog_Logger::instance()->debug( $err );
                    return -1;
                }
                $j++;
            }
        }
        return 1;

    } // END func

    /**
     * Delete rows in the table. 
     *
     */
    public function delete_rows_in_table( $table, $data_parse, &$block_ip, &$block_name )
    {
        $block_ip = $block_name = $i = 0;

        if ( empty($data_parse) ) return 0;

        $limit_temporary_locks = VisitorLog_Utility::get_option_sl( 'ddos_permanent_block_ip' );
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;
        $count = count($data_parse);

        while ( $i < $count ) {

            $id = $data_parse[$i];
            $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE id LIKE %d", $id));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'delete_rows_in_table()-Error select into table-' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->debug( $err );
                return -1;
            }

            if ( empty($array) ) continue;
            $block = $array[0]->block;
            if ( $block > $limit_temporary_locks ) {

                switch( $table ) {
                    case 'bots_table':
                        $ip   = $array[0]->bot_ip;
                        $name = $array[0]->bot_name;
                        break;
                    case 'event_404':
                        $ip   = $array[0]->user_ip;
                        $name = $array[0]->user_agent;
                        break;                    
                    default: 
                        break;
                }

                if ( null != $ip )   $block_ip++;
                if ( null != $name ) $block_name++;
            }

            $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE id = %d", $id));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'delete_rows_in_table()-Error delete into table-' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->debug( $err );
                return -1;
            }
            $i++;
        }
        return $i;

    } // END func

    /**
     * Handler for blocking selected bots.
     *
     */
    public function handler_blocking_selected_field( $data_parse, $ban, $table )
    {
        if ( self::block_field_update( $data_parse, $ban, $table ) ) {
            $msg = __( 'The selected data has been successfully updated', 'visitorlog' );
            VisitorLog_System_View::show_message( 'green', $msg );
            return true;
        } else {
            $err_log = 'handler_blocking_selected_field() - UPDATE row failed operation';
            $err = __( 'Failed operation to update a row from table', 'visitorlog' );
            visitorlog_System_View::show_message( 'orange', $err, $err_log );
            return false;
        }

    } // END func    

    public static function block_field_update( $data_parse, $ban, $table, $limit_temporary_locks = '' )
    {  
        if ( '' == VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' ) ) return 0;

        if ( '' == $limit_temporary_locks ) {
            $limit_temporary_locks = VisitorLog_Utility::get_option_sl( 'ddos_permanent_block_ip' );
        } 
        $limit_temporary_locks++;
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $table;

        if ( 'unbanall' == $ban || 'banall' == $ban ) {   

            switch ( $ban ) {
                case 'banall':
                    $block = $limit_temporary_locks;
                    $block_invers = '';
                    break;
                case 'unbanall':
                    $block = 'none';
                    $block_invers = $limit_temporary_locks;
                    break;
            }

            $result = VisitorLog_DB_Base::$wpdb_vl->update($table_name, ['block'=>$block], ['block'=>$block_invers]);
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) return -30;
            return 30;
        }

        if ( 'unban' == $ban || 'ban' == $ban ) {  

            switch ( $ban ) {
                case 'ban':
                    $block = $limit_temporary_locks;
                    break;
                case 'unban':
                    $block = 'none';
                    break;
            }
            $return = 0;
            for ($i = 0; $i < count($data_parse); $i++) {

                if ( 'on' != $data_parse[$i] ) {

                    $id = $data_parse[$i];
                    $result = VisitorLog_DB_Base::$wpdb_vl->update($table_name, ['block'=>$block], ['id'=>$id]);
                    if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) return -30;
 
                    if ( $result > 0 ) $return = 30;
                }
            }
        }
        return $return;  

    } // END func   

    protected function get_user_ip_address()
    {
        $visitor_ip = !empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']) ) : '';

        return $visitor_ip;

    } // END func


} // END class