<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods VisitorLog_Statistics_Regions class.
 *
 * @method public static function handler_ip_session()
 */
class VisitorLog_Statistics_Regions
{
    /**
     * Function handler_ip_session
     *
     * @param  int $time_statistic
     * @uses   VisitorLog_Utility::get_option_sl()
     * @uses   VisitorLog_DB_Base::$table_prefix_sl
     * @uses   VisitorLog_Logger::instance()->warning()
     * @return array $result / false
     */
    public static function handler_ip_session( $time_statistic )
    {
        global $visitorlog_users_resident_parameters;

        $time_session = 3600; // 1h
        $number_rows_limit = VisitorLog_Utility::get_option_sl( '#_display_limit' );
        $timestamp_now = $visitorlog_users_resident_parameters['timestamp'];
        $point_statistic = $timestamp_now - $time_statistic;

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'users';

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `timestamp` > %d ORDER BY id DESC", $point_statistic));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'handler_ip_session()-Error select data into Users: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        }   
        
        $num_rows = VisitorLog_DB_Base::$wpdb_vl->num_rows;

        if ( empty($array) ) {
           $result = $array; 
        } else {
            $result[0] = $array[0];
            $k = 0;
            foreach ($array as $arr) {
                $arr_ip      = $arr->user_ip;
                $arr_session = $arr->session;
                $count = count($result);
                $equ_ip = 0;
                $equ_ses = 0;
                $j = 0;

                while ( $j < $count ) {
                    $res_ip      = $result[$j]->user_ip;
                    $res_session = $result[$j]->session;

                    if ( $arr_ip == $res_ip ) {
                        $equ_ip = 1;
                        if ( $arr_session == $res_session ) {
                            $equ_ses = 1;
                        }
                    }  
                    $j++;    
                }
                if ( 0 == $equ_ip || (1 == $equ_ip && 0 == $equ_ses ) ) {
                    $k++;
                    $result[$k] = $arr;
                }
            }
        }
        return $result;

    } // END func


} // END class