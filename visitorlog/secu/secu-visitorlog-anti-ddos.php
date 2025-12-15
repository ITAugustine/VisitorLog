<?php
/**
 * VisitorLog_Anti_Ddos
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of the VisitorLog_Anti_Ddos class 
 * 
 * @method public     function __construct()
 * @method protected  function checking_for_attack()
 * @method protected  function checking_for_blocks_ddos()
 * @method protected  function insert_bot_ddos()
 */
class VisitorLog_Anti_Ddos
{
	public function __construct()
	{
		$this->checking_for_attack();
	}

	/**
	 * Checking for an DDoS attack
	 * 
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 * @uses VisitorLog_Logger::instance()->warning()
	 * @uses VisitorLog_Utility::get_option_sl()
	 * @uses VisitorLog_Security_Utility::redirect_to_url()
	 * @uses VisitorLog_Registration()
	 * @uses VisitorLog_Statistics_Utility()
	 */
	protected function checking_for_attack()
	{
		global $visitorlog_users_resident_parameters;

		$ip        = $visitorlog_users_resident_parameters['ip'];
		$timestamp = $visitorlog_users_resident_parameters['timestamp'];
		$category  = $visitorlog_users_resident_parameters['category'];

        if ( 'WP'    == $category  || 
             'Admin' == $category )  return;

		$check = $this->checking_for_blocks_ddos();
		$visitorlog_users_resident_parameters['timerelease'] = $check['timerelease'];

		if ( 'ddosip' == $check['event'] ) {    /* DDoS attack check for a single IP */

	        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) ) {

		        if ( 'perm' == $check['block'] ) {

					$visitorlog_users_resident_parameters['block']    = 'perm';
					$visitorlog_users_resident_parameters['category'] = 'blockddos';

					$this->insert_bot_ddos( 'ddosip' );
		    		VisitorLog_Registration::instance()->registration();
					VisitorLog_Statistics_Utility::instance()->statistics_handler();
					$visitorlog_users_resident_parameters['event'] = 'eventddos';
			    	return;
		        }	

				if ( 'temp' == $check['block'] ) {

					$insert = $this->insert_bot_ddos( 'ddosip' );

					if ( 'perm' == $insert ) {
						$visitorlog_users_resident_parameters['block']    = 'perm';
						$visitorlog_users_resident_parameters['category'] = 'blockddos';
					} else {
						$visitorlog_users_resident_parameters['block']    = 'temp';
						$visitorlog_users_resident_parameters['category'] = 'blockddos';
					}
		    		VisitorLog_Registration::instance()->registration();
					VisitorLog_Statistics_Utility::instance()->statistics_handler();
					$visitorlog_users_resident_parameters['event'] = 'eventddos';
			    	return;
				}
	        }
				
			$visitorlog_users_resident_parameters['timerelease'] = 0;
			$visitorlog_users_resident_parameters['category'] = 'BotDdosIP';
			$visitorlog_users_resident_parameters['block'] = '';
			$this->insert_bot_ddos( 'ddosip' );
    		VisitorLog_Registration::instance()->registration();
			VisitorLog_Statistics_Utility::instance()->statistics_handler();
			$visitorlog_users_resident_parameters['event'] = 'eventddos';
        	return;
		}	

		if ( 'ddos' == $check['event'] ) {      /* DDoS attack check for multi IP addresses */

	        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) ) {
				$visitorlog_users_resident_parameters['category'] = 'blockddos';
				$visitorlog_users_resident_parameters['block']    = 'perm';
        	} else {
				$visitorlog_users_resident_parameters['category'] = 'BotDdos';
				$visitorlog_users_resident_parameters['block']    = '';        		
        	}

			$this->insert_bot_ddos( 'ddos' );
	   		VisitorLog_Registration::instance()->registration();
			VisitorLog_Statistics_Utility::instance()->statistics_handler();
			$visitorlog_users_resident_parameters['event'] = 'eventddos';
        	return;
		}

		if ( 'templock' == $check['event'] ) {  /* checking the blocking time */

	        if ( 'on' == VisitorLog_Utility::get_option_sl( 'on_log_blocking_visitor' ) ) {
				$visitorlog_users_resident_parameters['category'] = 'blockddos';
				$visitorlog_users_resident_parameters['block']    = 'temp';
	        } else {
		        $visitorlog_users_resident_parameters['category'] = 'BotDdosIP';
		        $visitorlog_users_resident_parameters['block']    = '';	        	
	        }

			$this->insert_bot_ddos( 'templock' );
	    	VisitorLog_Registration::instance()->registration();
			VisitorLog_Statistics_Utility::instance()->statistics_handler();
			$visitorlog_users_resident_parameters['event'] = 'eventddos';
        	return;
		}	

	} // END func

    /**
     * Event checking for DDoS attacks. 
     *
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->warning()
     * @return boolean
     */
	protected function checking_for_blocks_ddos()
	{
        global $visitorlog_users_resident_parameters;

		$ip        = $visitorlog_users_resident_parameters['ip'];
		$timestamp = $visitorlog_users_resident_parameters['timestamp'];
	    $bot       = $visitorlog_users_resident_parameters['bot'];
	    $botblock  = $visitorlog_users_resident_parameters['botblock'];

		$check['event'] = '';
		$check['block'] = '';
		$check['timerelease'] = 0;

		if ( '' != $bot && 'notblock' == $botblock ) return $check;

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'users';

        $period_check_ip = 2;            // period check ip - sec
		$check_count_ip  = 3;            // 3 per 2 sec
		$check_count_ip_perm = 6;        // 6 per 2 sec
		$timelimit_ip = $timestamp - $period_check_ip;

        $period_check_multi_ip = 2;      // period check ip - sec
		$check_count_multi_ip  = 10;     // 10 per 2 sec
		$timelimit_multi_ip = $timestamp - $period_check_multi_ip;

		/*****************************
		 * DDoS attack check for a single IP
		 */
		$array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare( "SELECT * FROM $table_name WHERE `user_ip` = %s AND `timestamp` > %d 	ORDER BY `id` DESC", $ip, $timelimit_ip));
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$err = 'checking_for_blocks_ddos(1)-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
			VisitorLog_Logger::instance()->warning( $err );
			return $check;
		}

		if ( !empty($array) ) {

			if ( $array[0]->category == 'blockddos' && 
				 $array[0]->timerelease != 0        && 
				 $array[0]->pages > 6 ) {

				$check['event'] = 'ddosip';
				$check['block'] = 'perm';
				$check['timerelease'] = $array[0]->timerelease;
				return $check;
			}

			if ( count($array) > $check_count_ip ) {

		        $lock_seconds = VisitorLog_Utility::get_option_sl( 'ddos_delay_time' );
				$timerelease  = $timestamp + $lock_seconds;

				if ( 0 == $array[0]->timerelease ) {
					$check['event'] = 'ddosip';
					$check['block'] = 'temp';
					$check['timerelease'] = $timerelease;
					return $check;
				} else {
					if ( $timestamp < $array[0]->timerelease ) {
						$check['event'] = 'templock';
						$check['block'] = 'temp';
						$check['timerelease'] = $array[0]->timerelease;
						return $check;
					} else {
						$check['event'] = 'ddosip';
						$check['block'] = 'temp';
						$check['timerelease'] = $timerelease;
						return $check;
					}
				}
				return $check;
			}
		}

		/*****************************
		 * DDoS attack check for multi IP addresses
		 */
		$array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `timestamp`>%d", $timelimit_multi_ip));
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$err = 'checking_for_blocks_ddos(2)-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
			VisitorLog_Logger::instance()->warning( $err );
			return $check;
		}

		if ( count($array) > $check_count_multi_ip ) {
			$check['event'] = 'ddos';
			$check['block'] = 'perm';
			return $check;
		}	

		return $check;

	} // END func

    /**
     * Adds an entry to the table. 
     *
     * @param  string $mode
     * @uses   VisitorLog_DB_Base::$table_prefix_sl
     * @uses   VisitorLog_Logger::instance()->warning()
     * @return int $block
     */
    protected function insert_bot_ddos( $mode )
    {
        global $visitorlog_users_resident_parameters;

        $timestamp   = $visitorlog_users_resident_parameters['timestamp'];
        $timerelease = $visitorlog_users_resident_parameters['timerelease'];
        $ip          = $visitorlog_users_resident_parameters['ip'];
        $username    = $visitorlog_users_resident_parameters['username'];
        $url         = $visitorlog_users_resident_parameters['url'];
        $category    = $visitorlog_users_resident_parameters['category'];
        $block       = $visitorlog_users_resident_parameters['block'];
        $canal       = $visitorlog_users_resident_parameters['canal'];

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . 'lockdown_ip';

		$array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `user_ip`=%s ORDER BY `id` DESC LIMIT 1", $ip));
		if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
			$err = 'insert_bot_ddos()-Error select data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
			VisitorLog_Logger::instance()->warning( $err );
			return '';
		}

		if ( empty($array) ) {
    		$count = 0;
		} else {
    		$count = $array[0]->count;
		}

		if ( 'ddosip'    == $mode   && 
			 'temp'      == $block  && 
			 'blockddos' == $category ) {

			$count++;
			if ( $count < VisitorLog_Utility::get_option_sl( 'ddos_permanent_block_ip' ) ) {
				$block = 'temp';
			} else {
				$block = 'perm';
			}
		}

        $data = array( 'timestamp'   => $timestamp,
                       'timerelease' => $timerelease,
                       'user_ip'     => $ip,
                       'count'       => $count,
                       'user_name'   => $username, 
                       'block'       => $block,
                       'canal'       => $canal,
                       'category'    => $category,
                       'url'         => $url
                     );
        $result = VisitorLog_DB_Base::$wpdb_vl->insert( $table_name, $data );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $error_msg = 'insert_bot_ddos()-Error insert data: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($error_msg);
            return $block;      
        }

        $array  = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare( "SELECT COUNT(id) FROM $table_name WHERE `id`!=%d", 0), ARRAY_A);
        $c_rows = $array[0]['COUNT(id)'];
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'insert_bot_ddos()-Error select table: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning($err);
            return $block;
        }  

        $rows_record_limit = (int)VisitorLog_Utility::get_option_sl( '#_record_limit' );

        if ( $c_rows > $rows_record_limit ) {
            $delete = $c_rows - $rows_record_limit;
            $res = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("DELETE FROM $table_name WHERE `id`!=%d LIMIT %d", 0, $delete));
            if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
                $err = 'insert_bot_ddos()-Error delete rows: ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
                VisitorLog_Logger::instance()->warning($err);
                return $block;
            } 
        }

        return $block;

    } // END func


} // END class