<?php
/**
 * VisitorLog-Statistics-Utility
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Statistics_Utility class. 
 *
 * @method public    static function instance()
 * @method public           function __construct()
 * @method public           function start_statistics_handler()
 * @method public           function statistics_handler()
 * @method protected static function increment_value()
 * @method protected static function increment()
 * @method protected static function reset_value()
 */
class VisitorLog_Statistics_Utility
{
    private static $instance = null;    

    public static function instance()
    {
        if ( null == self::$instance ) self::$instance = new self();
        return self::$instance;
        
    } // END func

    public function __construct()
    {
        $this->start_statistics_handler();
    } 

    public function start_statistics_handler()
    {
        global $visitorlog_users_resident_parameters;

        $url      = $visitorlog_users_resident_parameters['url'];
        $category = $visitorlog_users_resident_parameters['category'];
        $event    = $visitorlog_users_resident_parameters['event'];

        if ( '' != $event ) return;
        if ( is_404() ) return;
        if ( 'CRON'      == $category ||
             'WP'        == $category ||
             'BotLogin'  == $category ||
             'BotDdos'   == $category ||
             'BotDdosIP' == $category ||
             'Bot404'    == $category ) return;

        if ( false !== strpos( $url, 'wp-login.php' ) ||
             false !== strpos( $url, 'xmlrpc.php' )   ||
             false !== strpos( $url, 'wp-cron.php' )  ||
             false !== strpos( $url, 'favicon.ico' )  ||
             false !== strpos( $url, 'admin-ajax' )   ||
             false !== strpos( $url, 'assets' ) )  return;

        $this->statistics_handler();

    } // END func

    /**
     * Visitors statistics handler
     *
     * @uses VisitorLog_Utility::get_option_sl()
     * @uses VisitorLog_Utility::update_option_sl() 
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     */
    public function statistics_handler()
    {
        global $visitorlog_users_resident_parameters;

        $event = $visitorlog_users_resident_parameters['event'];
        if ( '' != $event ) return;

        $datetime = $visitorlog_users_resident_parameters['datetime'];
        $current_hour = substr( $datetime, 11, 2 );
        $current_day  = substr( $datetime, 8, 2 );
        $previous_day = VisitorLog_Utility::get_option_sl( 'previous_readings_day' );

        if ( '' == $previous_day ) {
            $previous_day = $current_day;
            VisitorLog_Utility::update_option_sl( 'previous_readings_day', $current_day );
        }    

        $diff_d = (int)$current_day - (int)$previous_day;

        if ( 0 == $diff_d ) {
            $result = self::increment_value( $current_hour );
            if ( !$result ) return false;
        } else {
            VisitorLog_Utility::update_option_sl( 'previous_readings_day', $current_day );
            $result = self::reset_value( 'hour' );
            if ( !$result ) return false;
            $result = self::increment_value( $current_hour );
            if ( !$result ) return false;
        }
        return true;

    } // END func

    /**
     * Increment of the field value
     * 
     */
    protected static function increment_value( $CurrentHour )
    {
        $result = self::increment( 'hour',  'hour',  $CurrentHour );
        if ( !$result ) return false;

    } // END func

    /**
     * Increment 
     * 
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->warning()
     */
    protected static function increment( $t_name, $key, $current_key )
    {
        global $visitorlog_users_resident_parameters;

        $category = $visitorlog_users_resident_parameters['category'];
        $block    = $visitorlog_users_resident_parameters['block'];

        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $t_name;
        $bot = $botblock = $admin = $err = '';

        switch ($category) {
            case 'User':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `user` = user + 1 WHERE `hour` = %s", $current_key));
                break;
            case 'Admin':
                $admin = 'active';
                break;
            case 'AdminLogin':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `adminlogin` = adminlogin + 1 WHERE `hour` = %s", $current_key));
                $admin = 'active';
                break;
            case 'Admin404':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `admin404` = admin404 + 1 WHERE `hour` = %s", $current_key));
                $admin = 'active';
                break;
            case 'BotDdos':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botddos` = botddos + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                break;
            case 'BotDdosIP':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botddos` = botddos + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                break;
            case 'BotLogin':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botlogin` = botlogin + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                break;
            case 'botspamcf':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botspamcf` = botspamcf + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                break;
            case 'botspamco':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botspamco` = botspamco + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                break;
            case 'Bot404':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `bot404` = bot404 + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                break;
            case 'BotOther':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botother` = botother + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                break;
            case 'Bot':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botother` = botother + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                break;

            // --- Blocked Bot ---  
            case 'blockddos':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `blockddos` = blockddos + 1 WHERE `hour` = %s", $current_key));
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botddos` = botddos + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                $botblock = 'blocked';
                break;
            case 'blocklogin':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `blocklogin` = blocklogin + 1 WHERE `hour` = %s", $current_key));
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botlogin` = botlogin + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                $botblock = 'blocked';
                break;
            case 'blockspamcf':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `blockcf` = blockcf + 1 WHERE `hour` = %s", $current_key));
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botspamcf` = botspamcf + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                $botblock = 'blocked';
                break;
            case 'blockspamco':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `blockco` = blockco + 1 WHERE `hour` = %s", $current_key));
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botspamco` = botspamco + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                $botblock = 'blocked';
                break;
            case 'block404':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `block404` = block404 + 1 WHERE `hour` = %s", $current_key));
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `bot404` = bot404 + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                $botblock = 'blocked';
                break;
            case 'blockother':
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `blockother` = blockother + 1 WHERE `hour` = %s", $current_key));
                $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `botother` = botother + 1 WHERE `hour` = %s", $current_key));
                $bot = 'active';
                $botblock = 'blocked';
                break;
        }

        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) $err = VisitorLog_DB_Base::$wpdb_vl->last_error;

        if ( 'active' == $bot ) {
            $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `bot` = bot + 1 WHERE `hour` = %s", $current_key));
        }        
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) $err .= ', ' . VisitorLog_DB_Base::$wpdb_vl->last_error;

        if ( 'blocked' == $botblock ) {
            $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `block` = block + 1 WHERE `hour` = %s", $current_key));
        }
        if ( 'active' == $admin ) {
            $result = VisitorLog_DB_Base::$wpdb_vl->query(VisitorLog_DB_Base::$wpdb_vl->prepare("UPDATE $table_name SET `admin` = admin + 1 WHERE `hour` = %s", $current_key));
        }        

        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) $err .= ', ' . VisitorLog_DB_Base::$wpdb_vl->last_error;
        if ( $err ) {
            $msg_err  = sprintf('increment()-UPDATE table %s failed operation, Key = %s', $table_name, $current_key);
            $msg_err .= $err;
            VisitorLog_Logger::instance()->warning( $msg_err );
            return false;
        }

        return true;

    } // END func 

    /**
     * Reset the hour values to zero
     * 
     * @uses VisitorLog_DB_Base::$table_prefix_sl
     * @uses VisitorLog_Logger::instance()->warning()
     */
    protected static function reset_value( $t_name )
    {
        $table_name = VisitorLog_DB_Base::$table_prefix_sl . $t_name;

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `id` != %d", 0));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $msg_err = sprintf( 'reset_value()-error select data in table %s: ', $table_name );
            $msg_err .= VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning( $msg_err );
            return false;
        }

        $data = array(
            'user'       => 0, 
            'admin'      => 0,
            'adminlogin' => 0,
            'admin404'   => 0,
            'bot'        => 0,
            'block'      => 0,
            'botddos'    => 0,
            'botlogin'   => 0,
            'botspamcf'  => 0,
            'botspamco'  => 0,
            'bot404'     => 0,
            'botother'   => 0,
            'blockddos'  => 0,
            'blocklogin' => 0,
            'blockcf'    => 0,
            'blockco'    => 0,
            'block404'   => 0,
            'blockother' => 0
        );
        $i = 1;
        foreach( $array as $row ) {
            $result = VisitorLog_DB_Base::$wpdb_vl->update( $table_name, $data, ['id' => $i] );
            $i++;
        }
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $msg_err = sprintf( 'reset_value()-error update data in table %s: ', $table_name );
            $msg_err .= VisitorLog_DB_Base::$wpdb_vl->last_error;
            VisitorLog_Logger::instance()->warning( $msg_err );
            return false;
        }
        return true;

    } // END func

} // END class