<?php
/**
 * WP Cron.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods VisitorLog_Cron.
 *
 * @method public    static function get_class_name()
 * @method public    static function init_menu()
 * @method protected static function init_left_menu() 
 * @method public    static function scheduled_tasks() 
 * @method protected static function render_scheduled_tasks()
 * @method public    static function cron_test()
 * @method protected static function render_cron_test()
 */
class VisitorLog_Cron
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Initiate menu.
	 *
	 * @uses VisitorLog_Menu::is_disable_menu_item()
	 */
	public static function init_menu()
	{
		add_submenu_page(
			'visitorlog_tab',
			__( 'Cron', 'visitorlog' ),
			__( 'Cron', 'visitorlog' ),
			'read',
			'visitorlog_cron', 
			array ( self::get_class_name(), 'scheduled_tasks' )
		);

		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'scheduledtasks' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Scheduled tasks', 'visitorlog' ),
				__( 'Scheduled tasks', 'visitorlog' ),
				'read',
				'visitorlog_schedultasks',
				array( self::get_class_name(), 'scheduled_tasks' )
			);
		}
		if ( ! VisitorLog_Menu::is_disable_menu_item( 3, 'crontest' ) ) {
			add_submenu_page(
				'visitorlog_tab',
				__( 'Cron test', 'visitorlog' ),
				__( 'Cron test', 'visitorlog' ),
				'read',
				'visitorlog_crontest',
				array( self::get_class_name(), 'cron_test' )
			);
		}

		self::init_left_menu();

	} // END func

	/**
	 * Initiate left Sites menu.
	 *
	 * @uses VisitorLog_Menu::add_left_menu()
	 * @uses VisitorLog_Menu::is_disable_menu_item()
	 */
	protected static function init_left_menu()
	{
		global $visitorlog_global_parameters;		

		$menu_active_slugs = $visitorlog_global_parameters['menu_active_slugs'];
		$menu_active_slugs['сron'] = 'сron';
		$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;

		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Cron', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_cron',
				'href'       => 'admin.php?page=visitorlog_cron&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		$init_sub_subleftmenu = array(
			array(
				'title'      => __( 'Scheduled tasks', 'visitorlog' ),
				'parent_key' => 'visitorlog_cron',
				'href'       => 'admin.php?page=visitorlog_schedultasks&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_schedultasks',
				'right'      => '',
			),
			array(
				'title'      => __( 'WP Cron test', 'visitorlog' ),
				'parent_key' => 'visitorlog_cron',
				'href'       => 'admin.php?page=visitorlog_crontest&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'slug'       => 'visitorlog_crontest',
				'right'      => '',
			),
		);

		foreach ( $init_sub_subleftmenu as $item ) {
			if ( VisitorLog_Menu::is_disable_menu_item( 3, $item['slug'] ) ) {
				continue;
			}
			VisitorLog_Menu::add_left_menu( $item, 2 );
		}

	} // END func

	/**
	 * Scheduled tasks.
	 *
	 * @uses VisitorLog_UI::render_top_header()
	 * @uses VisitorLog_View::render_preloader()
	 */
	public static function scheduled_tasks()
	{
		$params = array( 'title' => __( 'Scheduled tasks', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<?php VisitorLog_View::render_preloader();?> 
						<section class="content home" >
						    <div class="block-header" style="margin-top:-15px;">
						        <div class="row" >
						            <div class="col-lg-7 col-md-6 col-sm-12" >
						                <h2><em>
						                		<?php esc_html_e('Scheduled tasks', 'visitorlog');?>
						                	</em>
						            	</h2>
						            </div>
						        </div>
						    </div>
						    <div class="container-fluid" >
					    		<?php self::render_scheduled_tasks();?>
						    </div>
						</section>
					</body>
				</div>	
		    </div>
		</div>
		<?php

	} // END func

    /**
     * Render Scheduled tasks.
     *
     * @uses VisitorLog_System_View::show_select_badge()
     */
    protected static function render_scheduled_tasks()
    {
		$current_time = current_time( 'timestamp', 1 ); // GMT/UTC (Unix format)
		$timezone = (int)get_option( 'gmt_offset' ) * 3600;
		$events_array = array();

		// protected events registered by WordPress core.
		$protected_events = array(
			'wp_privacy_delete_old_export_files',
			'wp_version_check',
			'wp_update_plugins',
			'wp_update_themes',
			'wp_site_health_scheduled_check',
			'recovery_mode_clean_expired_keys',
			'wp_scheduled_delete',
			'delete_expired_transients',
			'wp_scheduled_auto_draft_delete',
			'recovery_mode_clean_expired_keys',
			'wp_update_user_counts',
			'wp_delete_temp_updater_backups'
		);

		$this_plugin_events = array(
			'visitorlog_cron_backups',
			'visitorlog_login_captcha',
			'visitorlog_check_system',
			'visitorlog_activity_test'
		);

		$i = 0;
		foreach ( _get_cron_array() as $timestamp => $events ) {

			foreach ( $events as $event_hook => $event_args ) {

				$attributes = '';
				$attributEN = '';
				if ( in_array( $event_hook, $protected_events, true ) ) {
					$attributes = __('system','visitorlog');
					$attributEN = 'system';
				} elseif ( in_array( $event_hook, $this_plugin_events, true ) ) {
					$attributes = 'Visitorlog';
					$attributEN = 'this plugin';
				}

				foreach ( $event_args as $event ) {

					$interval = isset( $event['interval'] ) ? $event['interval'] : 0;
					$schedule = empty( $event['schedule'] ) ? 0 : $event['schedule'];

					switch ($schedule) {
						case 'monthly':
							$schedule = __( 'Monthly', 'visitorlog' );
							break;
						case 'weekly':
							$schedule = __( 'Weekly', 'visitorlog' );
							break;
						case 'daily':
							$schedule = __( 'Daily', 'visitorlog' );
							break;	
						case 'twicedaily':
							$schedule = __( 'Twicedaily', 'visitorlog' );
							break;
						case 'hourly':
							$schedule = __( 'Once an hour', 'visitorlog' );
							break;
						case '5minutely':
							$schedule = __( 'Once every 5 minutes', 'visitorlog' );
							break;			
					}

					$diff = $timestamp - $current_time;

					$int_day = (int)($diff / 86400);    // integer days
					$rem_day = ($diff % 86400);         // remainder days

					$int_hour = (int)($rem_day / 3600); // integer hours
					$rem_hour = ($rem_day % 3600);      // remainder hours

					$int_min = (int)($rem_hour / 60);   // integer minutes
					$rem_min = ($rem_hour % 60);        // remainder minutes

					$time_left_view = '';
					if ( 0 != $int_day ) {
						$time_left_view .= ' ' . (string)$int_day . __('d','visitorlog');
					}
					if ( 0 != $int_hour ) {
						$time_left_view .= ' ' . (string)$int_hour . __('h','visitorlog');
					}					
					$time_left_view .= ' ' . (string)$int_min . __('min','visitorlog');

					$events_array[$i]['eventhook']  = $event_hook;
					$events_array[$i]['attributes'] = $attributes;
					$events_array[$i]['attributen'] = $attributEN;
					$events_array[$i]['schedule']   = $schedule;
					$events_array[$i]['timestamp']  = gmdate('d/m/Y H:i', $timestamp+$timezone);
					$events_array[$i]['timeleft']   = $time_left_view;

    				$i++;
				}
			}
		}
        ?>
        <div class="row clearfix">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="header" style="margin-bottom:-20px;">
                    <h4>
                    	<p style="text-align:right;">
                        rows:&nbsp;&nbsp;&nbsp;<?php echo esc_html($i);?>
                        </p>
                    </h4>
                </div>
                <div class="body table-responsive card1 project_list">
                    <table class="table table-hover" style="line-height: 16px;">
                        <thead style="background-color:#F2F0E6;font-size:14px;">
                            <tr>                                       
                                <td><?php esc_html_e('Tasks','visitorlog');?></td>
                                <td><?php esc_html_e('Attributes','visitorlog');?></td>
                                <td><?php esc_html_e('Schedule','visitorlog');?></td>   
                                <td><?php esc_html_e('Start time','visitorlog');?></td>
                                <td><?php esc_html_e('Remaining time','visitorlog');?></td>
                            </tr>
                        </thead>
                        <tbody style="line-height:10px;font-size:14px;background-color:#F2F2F2;line-height:1.25;">
                            <tr>
                            <?php
                            $number_rows = $i;
                            $i = 0;
                            while ( $i < $number_rows ) {
								$event_hook = $events_array[$i]['eventhook'];
								$attributes = $events_array[$i]['attributes'];
								$attributEN = $events_array[$i]['attributen'];
								$schedule   = $events_array[$i]['schedule'];
								$date_time  = $events_array[$i]['timestamp'];
								$time_left  = $events_array[$i]['timeleft'];
                                $i++;
                                ?>
                                <td><?php echo esc_html($event_hook);?></td>
                                <td>
                                	<span <?php VisitorLog_System_View::show_badge($attributEN);?>>
                                		<?php echo esc_html($attributes);?>
                                	</span>
                                </td>
                                <td><?php echo esc_html($schedule);?></td>
                                <td><?php echo esc_html($date_time);?></td>
                                <td><?php echo esc_html($time_left);?></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>      
        <?php

    } // END func 

    /**
     * Render Cron test.
     *
     * @uses 
     */
    public static function cron_test()
    {
		$params = array( 'title' => __( 'Cron activity test', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
        ?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
						    <div class="block-header" style="margin-top:-15px;">
						        <div class="row" >
						            <div class="col-lg-7 col-md-6 col-sm-12" >
						                <h2><em>
						            <?php esc_html_e( 'Cron activity test in WordPress', 'visitorlog' );?>
						                	</em>
						            	</h2>
						            </div>
						        </div>
						    </div>
						    <div class="container-fluid" >
					    		<?php self::render_cron_test();?>
						    </div>
						</section>
					</body>
				</div>	
		    </div>
		</div>
		<?php

    } // END func	

	/**
	 * Render Cron test.
	 *
	 * @uses VisitorLog_System_View::show_message()
	 * @uses VisitorLog_Utility::get_option_sl()
	 * @uses VisitorLog_Utility::update_option_sl()
	 * @uses VisitorLog_Logger::instance()->warning()
	 */
	protected static function render_cron_test()
	{
	   	$msg = '';
		$cron_test      = VisitorLog_Utility::get_option_sl( 'cron_test' );
		$cron_test_time = VisitorLog_Utility::get_option_sl( 'cron_test_time' );

		if ( '' != $cron_test_time ) {
	        $date_time = gmdate( 'd-m-Y H:i:s', $cron_test_time );
    		$msg = __( 'Cron WP - successful activity test', 'visitorlog' );
	        $msg .= '  Time: ' . $date_time;
			// Unset Cron Schedules.
			$sched = wp_next_scheduled( 'visitorlog_activity_test' );
			if ( $sched ) wp_unschedule_event( $sched, 'visitorlog_activity_test' );

			VisitorLog_Utility::update_option_sl( 'cron_test', '' ); 
			VisitorLog_Utility::update_option_sl( 'cron_test_time', '' );
		} else {
			if ( $cron_test > 1 ) {
				VisitorLog_Utility::update_option_sl( 'cron_test', '' ); 
				VisitorLog_Utility::update_option_sl( 'cron_test_time', '' );
	            $err = __( 'It looks like the CRON health test failed. Try again at the beginning.', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
			}
			if  ( 1 == $cron_test ) {
				$cron_test = 2;
				VisitorLog_Utility::update_option_sl( 'cron_test', $cron_test ); 
				$msg = __( 'The CRON WP test is running - refresh the page to get the result on the screen', 'visitorlog' );
			}	
		}

		if ( isset( $_POST['visitorlog_cron_test_start'] ) ) {

	        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
				$sched = wp_next_scheduled( 'visitorlog_activity_test' );
	        } else {    
	            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
	            wp_die();
	        }

			if ( false == $sched ) {
				$time = current_time( 'timestamp', 1 ); // GMT/UTC (Unix format)
				wp_schedule_single_event( $time, 'visitorlog_activity_test' );
			}
			VisitorLog_Utility::update_option_sl( 'cron_test', 1 ); 
			VisitorLog_Utility::update_option_sl( 'cron_test_time', '' );
			$msg = __( 'The CRON WP test is running - refresh the page to initialize the task', 'visitorlog' );
		}	
		?>
        <div class="row clearfix" style="margin-top:40px;">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody style="background-color:#F2F2F2;">
                                    <tr>
										<td>
	    									<div style="font-size:16px;">
<?php esc_html_e( 'To perform the test, do the following', 'visitorlog' );?>:
<br><br>
1.&ensp;<?php esc_html_e( 'Press the "Cron test start" button', 'visitorlog' );?>
<br>
2.&ensp;<?php esc_html_e( 'Click the Refresh Page button to initialize the CRON task and write the result to the ACTION LOG', 'visitorlog' );?>
<br>
3.&ensp;<?php esc_html_e( 'Click the Refresh Page button to display the test result on the screen', 'visitorlog' );?>
<br><br>
<br>
<?php
$title = __('Action Log', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_actionlog' );
?>
<br><br>
<p style="font-size:16px;">
	<b><?php echo esc_html($msg);?></b>
</p>
	    									</div>
										</td>
                                    </tr>
                                </tbody>
                            </table>
							<div style="margin-top:30px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=cron' );
?>
							</div>
							<form method="post">
							<?php wp_nonce_field( 'visitorlog_nonce' );?>
						    <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_cron_test_start" class="btn btn__primary" value="<?php esc_html_e('Cron test start', 'visitorlog');?>"/>
							</div>
						    <div style="display:flex;flex-direction:row;justify-content: flex-end;">
<input type="submit" name="visitorlog_refresh_page" class="btn btn__secondary" value="<?php esc_html_e('Refresh the page', 'visitorlog');?>" style="font-size:12px"/>
							</div>
							</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php

	} // END func	

} // END class