<?php
/**
 * Settings Options
 *
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit('Silens is gold'); 

/**
 * Methods of VisitorLog_Settings class.
 *
 * @method public    static function get_class_name()
 * @method public    static function init_menu()
 * @method public    static function init_left_menu()
 * @method public    static function render_settings()
 * @method protected static function settings_options_page()
 */
class VisitorLog_Settings
{
	/**
	 * Get Class Name
	 * @return __CLASS__
	 */
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Instantiate the Settings Menu.
	 *
	 */
	public static function init_menu()
	{
		add_submenu_page(
			'visitorlog_tab',
			__( 'Settings options', 'visitorlog' ),
			__( 'Settings', 'visitorlog' ),
			'read',
			'visitorlog_settings',
			array( self::get_class_name(), 'render_settings' )
		);

		self::init_left_menu();

	} // END func

	/**
	 * Instantiate left menu
	 *
	 * @uses VisitorLog_Menu::add_left_menu()
	 */
	public static function init_left_menu()
	{
		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Settings', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_settings',
				'href'       => 'admin.php?page=visitorlog_settings&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => '',
			),
			1
		);
		
	} // END func

	/**
	 * Render settings.
	 *
	 * @uses VisitorLog_UI::render_top_header()
	 * @uses VisitorLog_View::render_preloader()
	 */
	public static function render_settings()
	{
		$params = array( 'title' => __( 'Settings options', 'visitorlog' ) );
		VisitorLog_UI::render_top_header( $params );
		?>
			<div class="ui segment">
				<div class="vl-primary-content-wrap"> 
					<body class="theme-cyan">
						<section class="content home" >
						    <div class="block-header" style="margin-top:-15px;">
						        <div class="row" >
						            <div class="col-lg-7 col-md-6 col-sm-12" >
						                <h2><em><?php esc_html_e('Settings options', 'visitorlog');?></em></h2>
						            </div>
						        </div>
						    </div>
						    <div class="container-fluid" >
						        <?php self::settings_options_page();?>
						    </div>
						</section>
		            </body>
				</div>
			</div>
		</div>
        <?php

	} // END func	

	/**
	 * Render Settings options page.
	 *
	 * @uses VisitorLog_Utility::get_option_sl()
	 * @uses VisitorLog_System_View::show_message()
	 * @uses VisitorLog_Utility::update_option_sl()
	 */
	protected static function settings_options_page()
	{
		$msg = $err = '';
		$update_file_config = false;

		$record_limit          = VisitorLog_Utility::get_option_sl( '#_record_limit' );
		$display_limit         = VisitorLog_Utility::get_option_sl( '#_display_limit' );
		$log_record_limit      = VisitorLog_Utility::get_option_sl( 'log_record_limit' );
		$log_display_limit     = VisitorLog_Utility::get_option_sl( 'log_display_limit' );
		$row_report_file_limit = VisitorLog_Utility::get_option_sl( 'row_report_file_limit' );

		$smtphost     = VisitorLog_Utility::get_option_sl( 'smtphost' );
		$smtpport     = VisitorLog_Utility::get_option_sl( 'smtpport' );
		$smtpusername = VisitorLog_Utility::get_option_sl( 'SMTPusername' );
		$smtppass     = self::get_option_pass( 'smtppass' );

		$send_email1 = VisitorLog_Utility::get_option_sl( 'send_stat_email1' );
		$send_email2 = VisitorLog_Utility::get_option_sl( 'send_stat_email2' );
		$send_email3 = VisitorLog_Utility::get_option_sl( 'send_stat_email3' );

		$check = VisitorLog_Security_Utility::check_file_config_debug();

        $check_debug         = $check['debug'];
        $check_debug_log     = $check['debug_log'];
        $check_debug_display = $check['debug_display'];

        if ( isset( $_POST['visitorlog_submit_execute'] ) ) {

	        if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
	            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
	            VisitorLog_System_View::show_message( 'orange', $err, $err );
	            wp_die();
	        }

			if ( isset($_POST['visitorlog_record_limit']) && 
				 $record_limit != $_POST['visitorlog_record_limit'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$record_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_record_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( '#_record_limit', $record_limit );
				$msg .= __('The parameter for limiting the size of database tables has been successfully entered', 'visitorlog');
			}
			if ( isset($_POST['visitorlog_display_limit']) && 
				 $display_limit != $_POST['visitorlog_display_limit'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$display_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_display_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( '#_display_limit', $display_limit );
				$msg .= __('The parameter for limiting table rows to display on the screen has been successfully entered', 'visitorlog');
				$msg .= '. ';
			}
			if ( isset($_POST['visitorlog_log_record_limit']) &&
			     $log_record_limit != $_POST['visitorlog_log_record_limit'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$log_record_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_log_record_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'log_record_limit', $log_record_limit );
				$msg .= __('A parameter has been successfully entered to limit the size of the error log table', 'visitorlog');
				$msg .= '. ';
			}
			if ( isset($_POST['visitorlog_log_display_limit']) &&
			     $log_display_limit != $_POST['visitorlog_log_display_limit'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$log_display_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_log_display_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'log_display_limit', $log_display_limit );
				$msg .= __('A parameter has been successfully entered to limit the display of error logging table rows on the screen', 'visitorlog');
				$msg .= '. ';
			}
			if ( isset($_POST['visitorlog_rows_file_limit']) &&
			     $row_report_file_limit != $_POST['visitorlog_rows_file_limit'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$row_report_file_limit = sanitize_text_field(wp_unslash($_POST['visitorlog_rows_file_limit']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'row_report_file_limit', $row_report_file_limit );
				$msg .= __('A parameter has been successfully entered that restricts the display of lines in the report file', 'visitorlog');
				$msg .= '. ';
			}
			if ( isset($_POST['visitorlog_smtphost']) && $smtphost != $_POST['visitorlog_smtphost'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$smtphost = sanitize_text_field(wp_unslash($_POST['visitorlog_smtphost']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'smtphost', $smtphost );
				$msg .= __('Smtp host parameter has been successfully entered', 'visitorlog');
				$msg .= '. ';
			}
			if ( isset($_POST['visitorlog_smtpport']) && $smtpport != $_POST['visitorlog_smtpport'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$smtp = trim(sanitize_text_field(wp_unslash($_POST['visitorlog_smtpport'])));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				if ( is_numeric($smtp) && strlen($smtp) < 9 ) {
					$smtpport = $smtp;
					VisitorLog_Utility::update_option_sl( 'smtpport', $smtp );
					$msg .= __('Smtp port parameter has been successfully entered', 'visitorlog');
					$msg .= '. ';
				} else {
                    $err .= __( 'Error entering the Smtp port parameter', 'visitorlog' );
                    $msg .= '. ';
				}
			}
			if ( isset($_POST['visitorlog_smtpusername']) && $smtpusername != $_POST['visitorlog_smtpusername'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$smtpusername = sanitize_text_field(wp_unslash($_POST['visitorlog_smtpusername']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				VisitorLog_Utility::update_option_sl( 'SMTPusername', $smtpusername );
				$msg .= __('The Email parameter has been successfully entered', 'visitorlog');
				$msg .= '. ';
			}
			if ( isset($_POST['visitorlog_smtppass']) && $smtppass != $_POST['visitorlog_smtppass'] ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$pass = sanitize_text_field(wp_unslash($_POST['visitorlog_smtppass']));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
				$res = self::update_option_pass( 'smtppass', $pass );
				if ( $res ) {
	                $msg .= __( 'The password was entered successfully', 'visitorlog' );
	                $msg .= '. ';
				} else {
                    $err .= __( 'Error writing the password to the db', 'visitorlog' );
                    $msg .= '. ';
				}
			}
			if ( isset($_POST['visitorlog_input_email_1']) && $send_email1 != sanitize_text_field(wp_unslash($_POST['visitorlog_input_email_1'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$send_email1_inp = sanitize_text_field(wp_unslash( $_POST['visitorlog_input_email_1'] ));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }

                if( !filter_var($send_email1_inp, FILTER_VALIDATE_EMAIL) ) {
                	if ( '' != $err ) $err .= '<br>';
                    $err .= __( 'Error entering the address for sending an email message', 'visitorlog' );
                    $msg .= '.&nbsp;';
                } else {
                	$send_email1 = $send_email1_inp;
					VisitorLog_Utility::update_option_sl( 'send_stat_email1', $send_email1 );
					if ( '' != $msg ) $msg .= '<br>';
	                $msg .= __( 'The email address has been successfully entered', 'visitorlog' );
	                $msg .= '.&nbsp;';
                }
			}
			if ( isset($_POST['visitorlog_input_email_2']) && $send_email2 != sanitize_text_field(wp_unslash($_POST['visitorlog_input_email_2'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$send_email2_inp = sanitize_text_field(wp_unslash( $_POST['visitorlog_input_email_2'] ));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }

                if( !filter_var($send_email2_inp, FILTER_VALIDATE_EMAIL ) ) {
                	if ( '' != $err ) $err .= '<br>';
                    $err .= __( 'Error entering the address for sending an email message', 'visitorlog' );
                    $msg .= '.&nbsp;';
                } else {
                	$send_email2 = $send_email2_inp;
					VisitorLog_Utility::update_option_sl( 'send_stat_email2', $send_email2 );
					if ( '' != $msg ) $msg .= '<br>';
	                $msg .= __( 'The email address has been successfully entered', 'visitorlog' );
	                $msg .= '.&nbsp;';
                }
			}
			if ( isset($_POST['visitorlog_input_email_3']) && $send_email3 != sanitize_text_field(wp_unslash($_POST['visitorlog_input_email_3'])) ) {

		        if ( isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce') ) {
					$send_email3_inp = sanitize_text_field(wp_unslash( $_POST['visitorlog_input_email_3'] ));
		        } else {    
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }

                if( !filter_var($send_email3_inp, FILTER_VALIDATE_EMAIL) ) {
                	if ( '' != $err ) $err .= '<br>';
                    $err .= __( 'Error entering the address for sending an email message', 'visitorlog' );
                    $msg .= '.&nbsp;';
                } else {
                	$send_email3 = $send_email3_inp;
					VisitorLog_Utility::update_option_sl( 'send_stat_email3', $send_email3 );
					if ( '' != $msg ) $msg .= '<br>';
	                $msg .= __( 'The email address has been successfully entered', 'visitorlog' );
	                $msg .= '.&nbsp;';
                }
			}
            //--------------------------------------Debug
            if ( isset($_POST['visitorlog_enable_debug']) ) {
		        if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
            	$post = 'true';
            } else {
                $post = 'false';
            }    
            if ( $post != $check_debug ) {
                $check_debug = $post;
                $check['debug'] = $post;
                $update_file_config = true;
            }
            if ( isset($_POST['visitorlog_enable_debug_display']) ) {
		        if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
            	$post = 'true';
            } else {
                $post = 'false';
            }
            if ( $post != $check_debug_display ) {
                $check_debug_display = $post;
                $check['debug_display'] = $post;
                $update_file_config = true;
            }
            if ( isset($_POST['visitorlog_enable_debug_log']) ) {
		        if ( ! (isset($_POST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_POST['_wpnonce']), 'visitorlog_nonce')) ) {
		            $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
		            VisitorLog_System_View::show_message( 'orange', $err, $err );
		            wp_die();
		        }
            	$post = 'true';
            } else {
                $post = 'false';
            }
            if ( $post != $check_debug_log ) {
                $check_debug_log = $post;
                $check['debug_log'] = $post;
                $update_file_config = true;
            }
            if ( $update_file_config ) {
				if ( false === VisitorLog_Security_Utility::update_file_config_debug( $check ) ) {
                    $err .= __( 'Error when making changes to the file config.php', 'visitorlog' );
				} else {
	                $msg .= __( 'The control instructions have been successfully entered into the file config.php', 'visitorlog' );
				}
            }
            //--------------------------------------Message
	        if ( '' != $err ) {
	        	$color = 'yellow';
	        	$msg = $err;
	        } else {
	        	$color = 'green';
	        }
	        if ( '' != $msg ) {
	            VisitorLog_System_View::show_message( $color, $msg );
	            VisitorLog_Utility::update_option_sl( 'temp_message', $msg );
	            VisitorLog_Utility::update_option_sl( 'temp_value', $color );
	            VisitorLog_Utility::vl_redirect( 'visitorlog_settings' );
	            exit;
	        }

        } // END if

        // Display--------

        $msg = VisitorLog_Utility::get_option_sl( 'temp_message' );
        if ( '' != $msg ) {
            $color = VisitorLog_Utility::get_option_sl( 'temp_value' );
            VisitorLog_Utility::update_option_sl( 'temp_message', '' );
            VisitorLog_System_View::show_message( $color, $msg );
        }

		$a100=$a500=$a1000=$a2000=$a5000=$a10_000=$a100_000=$a200_000 = '';
		$b20=$b40=$b60=$b80=$b100=$b200=$b300 = '';
		$r100=$r500=$r1000=$r2000=$r5000=$r10_000=$r100_000=$r200_000 = '';
		$d20=$d40=$d60=$d80=$d100=$d200=$d300 = '';
		$f100=$f300=$f500=$f1000=$f2000='';

		switch ($record_limit) {
			case 100:
				$a100 = 'selected';
				break;
			case 500:
				$a500 = 'selected';
				break;
			case 1000:
				$a1000 = 'selected';
				break;
			case 2000:
				$a2000 = 'selected';
				break;			
			case 5000:
				$a5000 = 'selected';
				break;
			case 10000:
				$a10_000 = 'selected';
				break;
			case 100000:
				$a100_000 = 'selected';
				break;
			case 200000:
				$a200_000 = 'selected';
				break;
			default:
				$a10_000 = 'selected';
				break;
		}
		switch ($display_limit) {
			case 20:
				$b20 = 'selected';
				break;
			case 40:
				$b40 = 'selected';
				break;
			case 60:
				$b60 = 'selected';
				break;
			case 80:
				$b80 = 'selected';
				break;			
			case 100:
				$b100 = 'selected';
				break;
			case 200:
				$b200 = 'selected';
				break;
			case 300:
				$b300 = 'selected';
				break;
			default:
				$b100 = 'selected';
				break;
		}
		switch ($log_record_limit) {
			case 100:
				$r100 = 'selected';
				break;
			case 500:
				$r500 = 'selected';
				break;
			case 1000:
				$r1000 = 'selected';
				break;
			case 2000:
				$r2000 = 'selected';
				break;			
			case 5000:
				$r5000 = 'selected';
				break;
			case 10000:
				$r10_000 = 'selected';
				break;
			case 100000:
				$r100_000 = 'selected';
				break;
			case 200000:
				$r200_000 = 'selected';
				break;
			default:
				$r10_000 = 'selected';
				break;
		}
		switch ($log_display_limit) {
			case 20:
				$d20 = 'selected';
				break;
			case 40:
				$d40 = 'selected';
				break;
			case 60:
				$d60 = 'selected';
				break;
			case 80:
				$d80 = 'selected';
				break;			
			case 100:
				$d100 = 'selected';
				break;
			case 200:
				$d200 = 'selected';
				break;
			case 300:
				$d300 = 'selected';
				break;
			default:
				$d100 = 'selected';
				break;
		}

		switch ($row_report_file_limit) {
			case 100:
				$f100 = 'selected';
				break;
			case 300:
				$f300 = 'selected';
				break;
			case 500:
				$f500 = 'selected';
				break;
			case 1000:
				$f1000 = 'selected';
				break;			
			case 2000:
				$f2000 = 'selected';
				break;
			default:
				$f300 = 'selected';
				break;
		}
		switch ($check_debug) {
			case 'true':
				$check_d = 'checked';
				break;
			case 'false':
				$check_d = '';
				break;
			default:
				$check_d = '';
				break;
		}		
		switch ($check_debug_log) {
			case 'true':
				$check_dl = 'checked';
				break;
			case 'false':
				$check_dl = '';
				break;
			default:
				$check_dl = '';
				break;
		}		
		switch ($check_debug_display) {
			case 'true':
				$check_dd = 'checked';
				break;
			case 'false':
				$check_dd = '';
				break;
			default:
				$check_dd = '';
				break;
		}		
        ?>  
		<form method="post">
		<?php wp_nonce_field( 'visitorlog_nonce' ); ?>
		<div style="margin-top:-10px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover dataTable">
                                <thead class="vl-color-thead">
                                    <tr>
                                    <tr>
					                    <td width="300"><?php esc_html_e('Options', 'visitorlog');?></td>
					                    <td width="200"><?php esc_html_e('Parameters', 'visitorlog');?></td>
					                    <td>            <?php esc_html_e('Description', 'visitorlog');?></td>
                                    </tr> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td colspan="3">
                                            <div>
                                                <em style="font-size:16px;">
<?php esc_html_e('Setting limits for writing rows to the database, to report files, and displaying rows on the screen for reports', 'visitorlog');?>
                                                </em>
                                            </div>
                                        </td>
                                    </tr>  
                                    <tr>
										<td><?php esc_html_e('DB limit of entries', 'visitorlog');?>:</td>
										<td>
									<select name="visitorlog_record_limit" style="width:10.4rem;">
									    <option value="100"    <?php echo esc_html($a100)?>>        100 </option>
									    <option value="500"    <?php echo esc_html($a500)?>>        500 </option>
									    <option value="1000"   <?php echo esc_html($a1000)?>>     1 000 </option>
									    <option value="2000"   <?php echo esc_html($a2000)?>>     2 000 </option>
									    <option value="5000"   <?php echo esc_html($a5000)?>>     5 000 </option>
									    <option value="10000"  <?php echo esc_html($a10_000)?>>  10 000 </option>
									    <option value="100000" <?php echo esc_html($a100_000)?>>100 000 </option>
									    <option value="200000" <?php echo esc_html($a200_000)?>>200 000 </option>
									</select>
										</td>
										<td>
										</td>                                  
                                    </tr>
                                    <tr>
										<td>
							<?php esc_html_e('The limit of displaying lines on the screen', 'visitorlog');?>:
										</td>
										<td>
											<select name="visitorlog_display_limit" style="width:10.4rem;">
											    <option value="20"  <?php echo esc_html($b20) ?>> 20 </option>
											    <option value="40"  <?php echo esc_html($b40) ?>> 40 </option>
											    <option value="60"  <?php echo esc_html($b60) ?>> 60 </option>
											    <option value="80"  <?php echo esc_html($b80) ?>> 80 </option>
											    <option value="100" <?php echo esc_html($b100)?>>100 </option>
											    <option value="200" <?php echo esc_html($b200)?>>200 </option>
											    <option value="300" <?php echo esc_html($b300)?>>300 </option>
											</select>
										</td>                                  
										<td>
										</td>
                                    </tr>
                                    <tr>
										<td>
							<?php esc_html_e('Limit of entries in the log table', 'visitorlog');?>:
										</td>
										<td>
									<select name="visitorlog_log_record_limit" style="width:10.4rem;">
									    <option value="100"    <?php echo esc_html($r100)?>>        100 </option>
									    <option value="500"    <?php echo esc_html($r500)?>>        500 </option>
									    <option value="1000"   <?php echo esc_html($r1000)?>>     1 000 </option>
									    <option value="2000"   <?php echo esc_html($r2000)?>>     2 000 </option>
									    <option value="5000"   <?php echo esc_html($r5000)?>>     5 000 </option>
									    <option value="10000"  <?php echo esc_html($r10_000)?>>  10 000 </option>
									    <option value="100000" <?php echo esc_html($r100_000)?>>100 000 </option>
									    <option value="200000" <?php echo esc_html($r200_000)?>>200 000 </option>
									</select>
										</td>                                  
										<td>
										</td>
                                    </tr>
                                    <tr>
										<td>
				<?php esc_html_e('The limit for displaying lines on the screen from the log', 'visitorlog');?>:
										</td>
										<td>
											<select name="visitorlog_log_display_limit" style="width:10.4rem;">
											    <option value="20"  <?php echo esc_html($d20) ?>> 20 </option>
											    <option value="40"  <?php echo esc_html($d40) ?>> 40 </option>
											    <option value="60"  <?php echo esc_html($d60) ?>> 60 </option>
											    <option value="80"  <?php echo esc_html($d80) ?>> 80 </option>
											    <option value="100" <?php echo esc_html($d100)?>>100 </option>
											    <option value="200" <?php echo esc_html($d200)?>>200 </option>
											    <option value="300" <?php echo esc_html($d300)?>>300 </option>
											</select>
										</td>                                  
										<td>
										</td>
                                    </tr>
                                    <tr>
										<td>
				<?php esc_html_e('Limit on displaying lines in the report file', 'visitorlog');?>:
										</td>
										<td>
											<select name="visitorlog_rows_file_limit" style="width:10.4rem;">
											<option value="100"  <?php echo esc_html($f100);?>>  100  </option>
											<option value="300"  <?php echo esc_html($f300);?>>  300  </option>
											<option value="500"  <?php echo esc_html($f500);?>>  500  </option>
											<option value="1000" <?php echo esc_html($f1000);?>> 1000 </option>
											<option value="2000" <?php echo esc_html($f2000);?>> 2000 </option>
											</select>
										</td>                                  
										<td>
										</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <div>
                                                <em style="font-size:16px;">
<?php esc_html_e('Configuring PHPMailer settings for sending emails via SMTP from a real mailbox', 'visitorlog');?>
                                                </em>
                                            </div>
                                        </td>
                                    </tr>  
									<tr>
										<td>SMTP host</td>
				                        <td>
				                            <p>
<input type="text" size="20" name="visitorlog_smtphost" value="<?php echo esc_html($smtphost);?>"/>
				                            </p>    
				                        </td>  
										<td>
<?php esc_html_e('Enter the SMTP host. For example, smtp.gmail.com','visitorlog');?>
										</td>
									</tr>
									<tr>
										<td>SMTP port</td>
				                        <td>
				                            <p>
<input type="text" size="20" name="visitorlog_smtpport" value="<?php echo esc_html($smtpport);?>"/>
				                            </p>    
				                        </td>  
										<td>
<?php esc_html_e('Enter the SMTP port. For example, 465','visitorlog');?>
										</td>
									</tr>
									<tr>
										<td>Email</td>
				                        <td>
				                            <p>
<input type="email" size="20" name="visitorlog_smtpusername" value="<?php echo esc_html($smtpusername);?>"/>
				                            </p>    
				                        </td>  
										<td>
<?php esc_html_e('Enter the email address from which the messages will be sent','visitorlog');?>
										</td>
									</tr>
									<tr>
										<td>password</td>
				                        <td>
				                            <p>
<input type="password" autocomplete="off" size="20" name="visitorlog_smtppass" value="<?php echo esc_html($smtppass);?>"/>
				                            </p>    
				                        </td>  
										<td>
<?php esc_html_e('Enter the e-mail password for remote programs','visitorlog');?>
										</td>
									</tr>
                                    <tr>
                                        <td>
<?php esc_html_e('Email address','visitorlog');?>:
                                        </td>
                                        <td>
<input type="email" size="25" name="visitorlog_input_email_1" value="<?php echo esc_html($send_email1);?>"/>
                                        </td>    
                                        <td>
<?php esc_html_e('Enter the email address to which the messages will be sent','visitorlog');?>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td>
<?php esc_html_e('Email address','visitorlog');?>:
                                        </td>
                                        <td>
<input type="email" size="25" name="visitorlog_input_email_2" value="<?php echo esc_html($send_email2);?>"/>
                                        </td>    
                                        <td>
<?php esc_html_e('Enter the additional email address to which the messages will be sent','visitorlog');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
<?php esc_html_e('Email address','visitorlog');?>:
                                        </td>
                                        <td>
<input type="email" size="25" name="visitorlog_input_email_3" value="<?php echo esc_html($send_email3);?>"/>
                                        </td>    
                                        <td>
<?php esc_html_e('Enter the additional email address to which the messages will be sent','visitorlog');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
							<div style="margin-top:0px;"></div>
                            <table class="table table-bordered table-hover dataTable">
                                <thead class="vl-color-thead">
                                    <tr>
                                    <tr>
                                        <td width="45"> <?php esc_html_e('Enable','visitorlog');?></td>
                                        <td width="160"><?php esc_html_e('Functions',  'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td> 
                                    </tr> 
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody">
                                    <tr>
                                        <td colspan="3">
                                            <div>
                                                <em style="font-size:16px;">
													<?php esc_html_e('Debug', 'visitorlog');?>
                                                </em>
                                            </div>
                                        </td>
                                    </tr>  
                                    <tr>
                                        <td colspan="3">
                                            <div>
<?php esc_html_e('Debug mode is enabled by the WP_DEBUG constant, it is disabled by default. Debugging includes processing absolutely all PHP errors and displaying them on the screen, activates additional logic in the kernel code, plugins and those where the value of this constant is checked and something is done if it is enabled', 'visitorlog');?>.&nbsp;
<?php esc_html_e('WP_DEBUG_DISPLAY and WP_DEBUG_LOG are activated only if the WP_DEBUG constant is enabled', 'visitorlog');?>.&nbsp;
<?php esc_html_e('When WP_DEBUG = false, error display and logging will work based on the settings of the php.ini file', 'visitorlog');?>.
<br>
<?php esc_html_e('All the debug mode constants are defined in the file wp-config.php', 'visitorlog');?>
                                            </div>
                                        </td>
                                    </tr>  
									<tr>
										<td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_enable_debug" name="visitorlog_enable_debug" type="checkbox" value="<?php echo esc_html($check_debug);?>" <?php echo esc_html($check_d);?>>
                                                    <label for="visitorlog_enable_debug"></label>
                                                </div>
                                            </div>
										</td>
				                        <td>
											WP_DEBUG
				                        </td>  
				                        <td>
<?php esc_html_e('When you enable define( \'WP_DEBUG\', true ), all error levels will be included in the error report', 'visitorlog');?>
				                        </td>
									</tr>
									<tr>
										<td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_enable_debug_display" name="visitorlog_enable_debug_display" type="checkbox" value="<?php echo esc_html($check_debug_display);?>" <?php echo esc_html($check_dd);?>>
                                                    <label for="visitorlog_enable_debug_display"></label>
                                                </div>
                                            </div>
										</td>
				                        <td>
  											WP_DEBUG_DISPLAY  
				                        </td>  
				                        <td>
<?php esc_html_e('When define( \'WP_DEBUG\', true ) is enabled, all errors will be displayed because WP_DEBUG_DISPLAY = true by default', 'visitorlog');?>
				                        </td>
									</tr>
									<tr>
										<td>
                                            <div class="switch" >
                                                <div class="switch__1">
<input id="visitorlog_enable_debug_log" name="visitorlog_enable_debug_log" type="checkbox" value="<?php echo esc_html($check_debug_log);?>" <?php echo esc_html($check_dl);?>>
                                                    <label for="visitorlog_enable_debug_log"></label>
                                                </div>
                                            </div>
										</td>
				                        <td>
  											WP_DEBUG_LOG
				                        </td>  
				                        <td>
<?php esc_html_e('With define enabled (\'WP_DEBUG\', true ), errors will be logged to the log file specified in php.ini, because WP_DEBUG_LOG = false by default', 'visitorlog');?>  
				                        </td>
									</tr>
                                </tbody>
                            </table>
							<div style="margin-top:10px;">
<?php
$title = __('Guide', 'visitorlog');
VisitorLog_Utility::redirect_html( 'help.png', $title, 'visitorlog_guide', '&addr=settings' );
?>
							</div>
							<div style="display:flex;flex-direction:row;justify-content:flex-end;margin-top:-20px;">
<input type="submit" name="visitorlog_submit_execute" class="btn btn__primary" value="<?php esc_html_e('Save', 'visitorlog');?>"/>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		</form>
		<?php
		exit;

    } // END func

	/**
	 * Reading the password in the db
	 *
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 * @uses VisitorLog_Utility::get_option_sl()
	 * @uses VisitorLog_Logger::instance()->warning()
	 * @return $decryption
	 */
	public static function get_option_pass( $pass_key )
	{
		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'pass';

        $array = VisitorLog_DB_Base::$wpdb_vl->get_results(VisitorLog_DB_Base::$wpdb_vl->prepare("SELECT * FROM $table_name WHERE `pass_key` = %s", $pass_key));
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'get_option_pass()-Error select into <pass>: ' . VisitorLog_DB_Base::$wpdb_vl->last_error; 
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        } 

		$encryption     = $array[0]->pass_value;
		$encryption_key = $array[0]->encryption_key;
		$encryption_iv  = $array[0]->encryption_iv;

		$ciphering = "AES-256-CTR";
		$options   = 0; 

		$decryption = openssl_decrypt( $encryption, $ciphering, $encryption_key, $options, $encryption_iv ); 

		return $decryption;

    } // END func

	/**
	 * Writing the password to the db
	 *
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 * @uses VisitorLog_Utility::generate_alpha_numeric_random_string()
	 * @uses VisitorLog_Logger::instance()->warning()
	 * @return true/false
	 */
	public static function update_option_pass( $pass_key, $value )
	{	
		$ciphering = "AES-256-CTR";
		$options   = 0; 
		$encryption_iv  = VisitorLog_Utility::generate_alpha_numeric_random_string( 16 );
		$encryption_key = VisitorLog_Utility::generate_alpha_numeric_random_string( 16 );

		$encryption = openssl_encrypt( $value, $ciphering, $encryption_key, $options, $encryption_iv );

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'pass';
		$data = array(
			'pass_value'     => $encryption,
			'encryption_key' => $encryption_key,
			'encryption_iv'  => $encryption_iv 
		);
		$result = VisitorLog_DB_Base::$wpdb_vl->update($table_name, $data, ['pass_key'=>$pass_key]);
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'update_option_pass()-Error update data into <pass>: ' . VisitorLog_DB_Base::$wpdb_vl->last_error; 
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        } 
		return true;

    } // END func

	/**
	 * Writing the key to the db
	 *
	 * @uses VisitorLog_DB_Base::$table_prefix_sl
	 * @uses VisitorLog_Logger::instance()->warning()
	 * @return true/false
	 */
	public static function update_option_key( $value )
	{	
		$ciphering = "AES-256-CTR";
		$options   = 0; 

		$encryption_key = mb_substr( $value, 0, 16 );
		$encryption_iv  = mb_substr( $value, 17, 16 );
		$encryption     = mb_substr( $value, 34 );

		$table_name = VisitorLog_DB_Base::$table_prefix_sl . 'pass';
		$data = array(
			'pass_value'     => $encryption,
			'encryption_key' => $encryption_key,
			'encryption_iv'  => $encryption_iv 
		);
		$result = VisitorLog_DB_Base::$wpdb_vl->update( $table_name, $data, ['pass_key'=>'license'] );
        if ( VisitorLog_DB_Base::$wpdb_vl->last_error ) {
            $err = 'update_option_key()-Error update data into <pass>: ' . VisitorLog_DB_Base::$wpdb_vl->last_error; 
            VisitorLog_Logger::instance()->warning( $err );
            return false;
        } 
		return true;

    } // END func


} // END class