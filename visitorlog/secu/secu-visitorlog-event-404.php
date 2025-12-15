<?php
/**
 * Event 404.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Event_404 class
 *
 * @method public static function get_class_name()
 * @method public        function __construct()
 * @method public static function start_registration_404()
 * @method public static function registration_404()
 * @method public static function insert_data_into_event_404()
 */
class VisitorLog_Event_404
{ 
    public static function get_class_name()
    {
        return __CLASS__;
    }

    public function __construct()
    {
        add_filter( 'template_redirect', array( self::get_class_name(), 'start_registration_404' ) );
    }

    public static function start_registration_404()
    {
        if ( !is_404() ) return;
        self::registration_404();

    } // END func

    /**
     * Function for `template_redirect` filter-hook.
     * 
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Logger::instance()->warning()
     * @uses VisitorLog_Statistics_Utility::instance()->statistics_handler()
     * @uses VisitorLog_Registration::instance()->registration()
     * @uses VisitorLog_Utility_Htaccess::write_rules_to_htaccess()
     * @uses VisitorLog_Security_Utility::redirect_to_url()
     */
    public static function registration_404()
    {
        global $visitorlog_users_resident_parameters;
   
        $category = $visitorlog_users_resident_parameters['category'];
        $event    = $visitorlog_users_resident_parameters['event'];

        if ( 'event404' == $event ) return; 

        if ( 'Admin' == $category ) {
            $visitorlog_users_resident_parameters['category'] = 'Admin404';
            $visitorlog_users_resident_parameters['block'] = '';
            $visitorlog_users_resident_parameters['timerelease'] = 0;
            self::insert_data_into_event_404();
            VisitorLog_Registration::instance()->registration();
            VisitorLog_Statistics_Utility::instance()->statistics_handler();
            $visitorlog_users_resident_parameters['event'] = 'event404';
            return;
        }

        $block = self::insert_data_into_event_404();
        $visitorlog_users_resident_parameters['timerelease'] = $block['timerelease'];

        if ( 'perm' == $block['block'] ) {   /* ----- need for permanent blocking */

            if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) &&
                 'on' == VisitorLog_Utility::get_option_sl( 'on_block_ip_perm' )         &&
                 'on' == VisitorLog_Utility::get_option_sl( 'on_404_logging' ) ) {

                $visitorlog_users_resident_parameters['block'] = 'perm';
                $visitorlog_users_resident_parameters['category'] = 'block404';
                VisitorLog_Registration::instance()->registration();
                VisitorLog_Statistics_Utility::instance()->statistics_handler();
                $visitorlog_users_resident_parameters['event'] = 'event404';
                $ip = $visitorlog_users_resident_parameters['ip'];

                $res = VisitorLog_Utility_Htaccess::write_rules_to_htaccess(); 
                if ( $res > 0 ) {
                    $msg = 'registration_404(): Successfully written to the htaccess file, IP-'.$ip;
                    VisitorLog_Logger::instance()->warning($msg);
                } else { 
                    $err = 'registration_404(): Could not write to the htaccess file, IP-'.$ip;
                    VisitorLog_Logger::instance()->warning($err);
                }
                exit;
            }
            /* Temporary blocking */
            $visitorlog_users_resident_parameters['block'] = 'temp';
            $visitorlog_users_resident_parameters['category'] = 'block404';
            VisitorLog_Registration::instance()->registration();
            VisitorLog_Statistics_Utility::instance()->statistics_handler();
            $visitorlog_users_resident_parameters['event'] = 'event404';

            $redirect_url = VisitorLog_Utility::get_option_sl( 'ddos_redirect_url' );
            VisitorLog_Security_Utility::redirect_to_url( $redirect_url );
            exit;
        } //----------------------

        if ( 'temp' == $block['block'] ) {   /* ----- need for temporary blocking */

            if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) &&
                 'on' == VisitorLog_Utility::get_option_sl( 'on_block_ip_temp' )         &&            
                 'on' == VisitorLog_Utility::get_option_sl( 'on_404_logging' ) ) {

                /* Temporary blocking */
                $visitorlog_users_resident_parameters['block'] = 'temp';
                $visitorlog_users_resident_parameters['category'] = 'block404';
                VisitorLog_Registration::instance()->registration();
                VisitorLog_Statistics_Utility::instance()->statistics_handler();
                $visitorlog_users_resident_parameters['event'] = 'event404';

                $redirect_url = VisitorLog_Utility::get_option_sl( 'ddos_redirect_url' );
                VisitorLog_Security_Utility::redirect_to_url( $redirect_url );
                return; 
            }  
        } //----------------------

        /* No block */
        $visitorlog_users_resident_parameters['block'] = '';
        $visitorlog_users_resident_parameters['category'] = 'Bot404';
        $visitorlog_users_resident_parameters['timerelease'] = 0;
        VisitorLog_Registration::instance()->registration();
        VisitorLog_Statistics_Utility::instance()->statistics_handler();
        $visitorlog_users_resident_parameters['event'] = 'event404';
        return;
        
    } // END func

    /**
     * Writing to the event_404 table
     *
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Logger::instance()->warning()
     */
    public static function insert_data_into_event_404()
    {
        global $visitorlog_users_resident_parameters;

        $timestamp   = $visitorlog_users_resident_parameters['timestamp'];
        $timerelease = $visitorlog_users_resident_parameters['timerelease'];
        $ip          = $visitorlog_users_resident_parameters['ip'];        
        $useragent   = $visitorlog_users_resident_parameters['username'];
        $category    = $visitorlog_users_resident_parameters['category'];
        $canal       = $visitorlog_users_resident_parameters['canal'];
        $url         = $visitorlog_users_resident_parameters['url'];
        $refinfo     = $visitorlog_users_resident_parameters['refinfo'];

        $block['block'] = '';
        $block['timerelease'] = 0;
        $count = 0;

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'event_404';

        if ( 'Admin404' != $category ) {

            $limit_block  = VisitorLog_Utility::get_option_sl( 'ddos_permanent_block_ip' );
            $period_block = VisitorLog_Utility::get_option_sl( 'ddos_delay_time' );

            $max_attempts = 3;
            $per_period   = 10;
            $analyze_period = $timestamp - $per_period; 

            $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_ip` = %s AND `timestamp` > $analyze_period ORDER BY `id` DESC", $ip));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'insert_data_into_event_404()-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error ;
                VisitorLog_Logger::instance()->warning( $err );
                return $block;
            }

            if ( !empty($array) ) {

                $timerelease = $array[0]->release_tstmp;
                $count = $array[0]->count;

                if ( $timestamp > $timerelease ) {

                    if ( count($array) > $max_attempts ) {
                        $count++;
                        if ( $count > $limit_block ) {
                            $block['block'] = 'perm';
                        } else {
                            $block['block'] = 'temp';
                            $block['timerelease'] = $timestamp + $period_block;
                        }
                    } else {
                        $block['timerelease'] = 0;
                        $block['block'] = '';                         
                    }
                } else {
                    $block['block'] = 'temp'; 
                    $block['timerelease'] = $timerelease;
                }
            }    
        }
        $data = array(
           'timestamp'     => $timestamp, 
           'release_tstmp' => $block['timerelease'],
           'user_ip'       => $ip,
           'count'         => $count,
           'block'         => $block['block'],
           'canal'         => $canal,
           'category'      => $category,
           'user_agent'    => $useragent,
           'url'           => $url,
           'refinfo'       => $refinfo 
        );
         $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_data_into_event_404()-Error insert to table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        }  

        $array  = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT COUNT(id) FROM $table_name WHERE `id` != %d", 0), ARRAY_A);
        $c_rows = $array[0]['COUNT(id)'];
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_data_into_event_404()-Error select table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        }  

        $rows_record_limit = (int)VisitorLog_Utility::get_option_sl( '#_record_limit' );
        if ( $c_rows > $rows_record_limit ) {

            $delete = $c_rows - $rows_record_limit;
            $res = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name  WHERE `id` != %d LIMIT $delete", 0));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'insert_data_into_event_404()-Error delete rows: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return $block;
            } 
        }
        return $block;

    } // END func


} // END class