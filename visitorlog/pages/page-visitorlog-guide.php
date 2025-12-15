<?php
/**
 * Guide
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods VisitorLog_Guide.
 *
 * @method public    static function init_menu()
 * @method public    static function init_left_menu()
 * @method public    static function render_guide()
 * @method protected static function render_table_of_contents()
 * @method protected static function render_introduction()
 * @method protected static function render_security_control_panel()
 * @method protected static function render_security_log_blocking_visitor()
 * @method protected static function render_protection_admin_panel()
 * @method protected static function render_protection_db()
 * @method protected static function render_protection_system_files()
 * @method protected static function render_protection_spam()
 * @method protected static function render_firewall()
 * @method protected static function render_reports()
 * @method protected static function render_tables()
 * @method protected static function render_logs()
 * @method protected static function render_statistics()
 * @method protected static function render_settings()
 * @method protected static function render_cron()
 * @method protected static function render_other_features()
 * @method protected static function security_policy()
 * @method protected static function render_full_version()
 */
class VisitorLog_Guide
{
	/**
	 * Instantiate the Settings Menu.
	 *
	 */
	public static function init_menu()
    {
		add_submenu_page(
			'visitorlog_tab',
			__( 'Guide', 'visitorlog' ),
			__( 'Guide', 'visitorlog' ),
			'read',
			'visitorlog_guide',
			array( __CLASS__, 'render_guide' )
		);

		self::init_left_menu();

	} // END func

	/**
	 * Instantiate left menu
	 *
	 * @param array $subPages SubPages Array.
	 *
	 * @uses VisitorLog_Menu::add_left_menu()
	 */
	public static function init_left_menu()
    {
		VisitorLog_Menu::add_left_menu(
			array(
				'title'      => __( 'Guide', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_guide',
				'href'       => 'admin.php?page=visitorlog_guide&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => ''
			),
			1
		);

	} // END func

	/**
	 * Render Guide page.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_View::render_preloader()
	 */
	public static function render_guide()
    {
		$params = array( 'title' => __('Guide', 'visitorlog') );
		VisitorLog_UI::render_top_header( $params );
        ?>
        	<div class="ui segment">
        		<div class="vl-primary-content-wrap"> 
        			<body class="theme-cyan">
                        <?php VisitorLog_View::render_preloader();?>
        				<section class="content home" >
        				    <div class="block-header">
        				        <div class="row" >
        				            <div class="col-lg-7 col-md-6 col-sm-12" >
        				                <h2>
                                            <span class="guide"><?php esc_html_e('Guide', 'visitorlog');?></span>
        				            	</h2>
        				            </div>
        				        </div>
        				    </div>
        				    <div class="container-fluid" >
            			    	<?php 
            			    	self::render_table_of_contents();
                                self::render_introduction();
                                self::render_security_control_panel();
                                self::render_security_log_blocking_visitor();
                                self::render_protection_admin_panel();
                                self::render_protection_db();
                                self::render_protection_system_files();
                                self::render_protection_spam();
                                self::render_firewall();
                                self::render_reports();
                                self::render_tables();
                                self::render_logs();
                                self::render_statistics();
                                self::render_settings();
                                self::render_cron();
                                self::render_other_features();
                                self::security_policy();
                                self::render_full_version();
            			    	?>
        				    </div>
        				</section>
        			</body>
        		</div>	
            </div>
        </div>
		<?php

	} // END func

	/**
	 * Render table of contents.
	 */
	protected static function render_table_of_contents()
    {
        if ( isset($_GET['addr']) ) {
            if ( isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_key($_GET['_wpnonce']), 'visitorlog_nonce') ) {
                $addr = sanitize_text_field(wp_unslash($_GET['addr']));
            } else {
                $err = __( 'Nonce check failed for protection operation. Try refreshing the page', 'visitorlog' );
                VisitorLog_System_View::show_message( 'orange', $err, $err );
                wp_die();
            }
            VisitorLog_Utility::vl_redirect2( $addr );
        }
		?>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="">
                    <div class="header">
                        <h3><em><?php esc_html_e('Contents list', 'visitorlog');?></em></h3>
                    </div>
                    <div></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>    
                                            <b>
<a href="#introduction" title="<?php esc_html_e('Introduction', 'visitorlog');?>">&ensp;<?php esc_html_e('Introduction','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                            <b>
<a href="#security" title="<?php esc_html_e('Security','visitorlog');?>">&ensp;<?php esc_html_e('Security','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#control_panel" title="<?php esc_html_e( 'Control panel', 'visitorlog' );?>">&ensp;<?php esc_html_e( 'Control panel', 'visitorlog' );?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#block_ip" title="<?php esc_html_e( 'Log & Blocking of Visitors', 'visitorlog' );?>">&ensp;<?php esc_html_e( 'Log & Blocking of Visitors', 'visitorlog' );?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#admin_panel" title="<?php esc_html_e( 'Admin panel', 'visitorlog' );?>">&ensp;<?php esc_html_e( 'Admin panel', 'visitorlog' );?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#db_protect" title="<?php esc_html_e( 'DataBase protection', 'visitorlog' );?>">&ensp;<?php esc_html_e( 'DataBase protection', 'visitorlog' );?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#file_protect" title="<?php esc_html_e( 'File system protection', 'visitorlog' );?>">&ensp;<?php esc_html_e( 'File system protection', 'visitorlog' );?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#spam" title="<?php esc_html_e( 'Spam Protection', 'visitorlog' );?>">&ensp;<?php esc_html_e( 'Spam Protection', 'visitorlog' );?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#firewall" title="<?php esc_html_e( 'Firewall Protection', 'visitorlog' );?>">&ensp;<?php esc_html_e( 'Firewall Protection', 'visitorlog' );?>
</a>
<div style="margin-top:3px;"></div>
                                            <b>
<a href="#reports" title="<?php esc_html_e('Reports', 'visitorlog');?>">&ensp;<?php esc_html_e('Reports','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#backups_report" title="<?php esc_html_e('Backups Report','visitorlog');?>">&ensp;<?php esc_html_e('Backups Report','visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#server_info" title="<?php esc_html_e('Server Info', 'visitorlog');?>">&ensp;<?php esc_html_e('Server Info', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#file_permissions" title="<?php esc_html_e('File Permissions Scan Report', 'visitorlog');?>">&ensp;<?php esc_html_e('File Permissions Scan Report', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#updates" title="<?php esc_html_e('Updates Report', 'visitorlog');?>">&ensp;<?php esc_html_e('Updates Report', 'visitorlog');?>
</a>

<div style="margin-top:3px;"></div>
                                            <b>
<a href="#tables" title="<?php esc_html_e('Tables', 'visitorlog');?>">&ensp;<?php esc_html_e('Tables','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#blacklist" title="<?php esc_html_e('Table Blocked IPs','visitorlog');?>">&ensp;<?php esc_html_e('Table Blocked IPs','visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#failed_logins_table" title="<?php esc_html_e('Failed Logins', 'visitorlog');?>">&ensp;<?php esc_html_e('Failed Logins', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#spam_table" title="<?php esc_html_e('Spam report', 'visitorlog');?>">&ensp;<?php esc_html_e('Spam report', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#event404" title="<?php esc_html_e('Event 404', 'visitorlog');?>">&ensp;<?php esc_html_e('Event 404', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#bots_table" title="<?php esc_html_e('Table of Bots', 'visitorlog');?>">&ensp;<?php esc_html_e('Table of Bots', 'visitorlog');?>
</a>

<div style="margin-top:3px;"></div>
                                            <b>
<a href="#logs" title="<?php esc_html_e('Logs', 'visitorlog');?>">&ensp;<?php esc_html_e('Logs','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#visitor_log" title="<?php esc_html_e('VisitorLog','visitorlog');?>">&ensp;<?php esc_html_e('VisitorLog','visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#admins" title="<?php esc_html_e('Administrators', 'visitorlog');?>">&ensp;<?php esc_html_e('Administrators', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#account_activity_log" title="<?php esc_html_e('Account activity log', 'visitorlog');?>">&ensp;<?php esc_html_e('Account activity log', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#action_log" title="<?php esc_html_e('Action Log', 'visitorlog');?>">&ensp;<?php esc_html_e('Action Log', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                        &emsp;-
<a href="#error_log" title="<?php esc_html_e('Error Log', 'visitorlog');?>">&ensp;<?php esc_html_e('Error Log', 'visitorlog');?>
</a>
<div style="margin-top:3px;"></div>
                                            <b>
<a href="#statistics" title="<?php esc_html_e('Statistics', 'visitorlog');?>">&ensp;<?php esc_html_e('Statistics','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                            <b>
<a href="#settings" title="<?php esc_html_e('Settings', 'visitorlog');?>">&ensp;<?php esc_html_e('Settings','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                            <b>
<a href="#cron" title="<?php esc_html_e('Cron', 'visitorlog');?>">&ensp;<?php esc_html_e('Cron','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                            <b>
<a href="#otherfeatures" title="<?php esc_html_e('Other Features', 'visitorlog');?>">&ensp;<?php esc_html_e('Other Features','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                            <b>
<a href="#security_policy" title="<?php esc_html_e('Security policy', 'visitorlog');?>">&ensp;<?php esc_html_e('Security policy','visitorlog');?>
</a>
                                            </b>
<div style="margin-top:3px;"></div>
                                            <b>
<a href="#fullversion" title="<?php esc_html_e('Full version', 'visitorlog');?>">&ensp;<?php esc_html_e('Full version','visitorlog');?>
</a>
                                            </b>
                                        </td>
                                    </tr>    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php

	} // END func	

	/**
	 * Render guide contents.
	 */
	protected static function render_introduction()
    {
		?>
        <div class="row clearfix" id="introduction">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Introduction', 'visitorlog');?></span>
                        </h3>
                    </div>
                    <div></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('The VisitorLog plugin is the embodiment of an integrated approach to creating tools for managing the operation of your site. This is a useful features in the field of security, statistics and some other functions. It is user-friendly and has a nice design', 'visitorlog');?>.
                                            <br><br>&nbsp;
<?php esc_html_e('There are four main directions in the functionality', 'visitorlog');?>:
                                            <br>&emsp;&emsp;1.
<?php esc_html_e('Site security', 'visitorlog');?>.
                                            <br>&emsp;&emsp;2.
<?php esc_html_e('Traffic statistics', 'visitorlog');?>.
                                            <br>&emsp;&emsp;3.
<?php esc_html_e('Tabular and graphical representation of the necessary information for the site administrator', 'visitorlog');?>.
                                            <br>&emsp;&emsp;4.
<?php esc_html_e('Necessary and useful functions in the work of an administrator', 'visitorlog');?>.
                                            <br><br>&nbsp;1.&nbsp;
<?php esc_html_e('Site security', 'visitorlog');?>
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Protection against DDoS attacks', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Protection of the administrative panel', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('DataBase protection', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('File system protection', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Protection from 404 events', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Spam protection in comments', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Spam protection in feedback forms', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Bot recognition', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Blocking bots with control instructions in the file .htaccess', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Blocking bots by redirection', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Creating a database of unwanted IP addresses', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Creating a white list of IP addresses to log in to the administrative panel', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Firewall', 'visitorlog');?>;
                                            <br><br>&emsp;&emsp;
<?php esc_html_e('It is possible to block the IP address automatically, in accordance with the built-in algorithm, and this happens in two stages', 'visitorlog');?>.
                                            &nbsp;
<?php esc_html_e('First, the IP address is temporarily blocked for a specified period and the counter starts counting the number of temporary locks. Upon reaching the temporary blocking limit, the IP address is permanently blocked by the control instructions in the htaccess file', 'visitorlog');?>.
                                            <br>&emsp;&emsp;
<?php esc_html_e('The administrator can manually permanently block the IP address in the htaccess file in the table in which the address is registered', 'visitorlog');?>.
                                            <br>&emsp;&emsp;
<?php esc_html_e('The administrator has the ability to enable or disable temporary or permanent address locks, as well as change some algorithm parameters', 'visitorlog');?>.
                                            <br>&emsp;&emsp;
<?php esc_html_e('The table shows the relationships between the protection modules and the blocking modes', 'visitorlog');?>.
                                            <br>
        <div style="margin-top:5px;"></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                    <td></td>
                                    <td><?php esc_html_e('Temporary blocking', 'visitorlog');?></td>
                                    <td><?php esc_html_e('Permanent lock is automatic', 'visitorlog');?></td>
                                    <td><?php esc_html_e('Permanent lock is manual', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
<?php esc_html_e('Protection against DDoS attacks', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
(<?php esc_html_e('in full version', 'visitorlog');?>)
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
(<?php esc_html_e('in full version', 'visitorlog');?>)
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>
<?php esc_html_e('Protection of the administrative panel', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>
<?php esc_html_e('Protection from 404 events', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>
<?php esc_html_e('Spam protection in comments', 'visitorlog');?>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>
<?php esc_html_e('Spam protection in feedback forms', 'visitorlog');?>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>                                        
                                    </tr>                                       
                                    <tr>
                                        <td>
<?php esc_html_e('Bot recognition', 'visitorlog');?>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                                            <br>2.&nbsp;
<?php esc_html_e('Website traffic statistics', 'visitorlog');?>.
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('generating statistics of site visits by categories of visitors', 'visitorlog');?>(
<?php esc_html_e('visitors, administrators, bots', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('generating statistics on site visits by bots', 'visitorlog');?>(
<?php esc_html_e('DDoS, admin panel bots, 404 bots, spam bots, other bots', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('maintaining statistics of site visits by the hour', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('maintaining statistics of site visits by day', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('maintaining statistics of site visits by month', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('maintaining statistics of site visits by year', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('displaying the name of the country, city, geographical coordinates, national flag of the country', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('displaying statistics on the screen in the form of tables and graphs', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('a summary report in the form of a table can be sent by e-mail', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>).
                                            <br><br>3.&nbsp;
<?php esc_html_e('Reports, tables, logs', 'visitorlog');?>
                                            <br>3.1&nbsp;
<?php esc_html_e('Reports', 'visitorlog');?>
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('database backup report', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('file system verification report', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('system information (server, WordPress, PHP, MySQL, plugins, ...)', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('file and folder access report', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('plugin update report', 'visitorlog');?>.
                                            <br>3.2&nbsp;
<?php esc_html_e('Tables', 'visitorlog');?>
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('blacklist of unwanted IP addresses', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Login whitelist', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('address table when login to the admin panel fails', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('table of spam addresses', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('address table at event 404', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('a table of unwanted IP addresses that the administrator creates on his own', 'visitorlog');?>;
                                            <br>3.3&nbsp;
<?php esc_html_e('Logs', 'visitorlog');?>
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('visitor log (all site visitors, admins, bots are registered)', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('log with administrators\' IP addresses (all dynamic and static addresses are stored)', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('account activity log, duration of sessions in the system', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Action Log', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('Error Log', 'visitorlog');?>.
                                            <br><br>4.&nbsp;
<?php esc_html_e('Other useful functions', 'visitorlog');?>
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('site maintenance mode, blocking access to the site except for administrators', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('deleting all comments from the database', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('speeding up the work of the administrative panel', 'visitorlog');?>
&nbsp;(<?php esc_html_e('in full version', 'visitorlog');?>);
                                            <br>&emsp;&emsp;&#8226;
                                            Cron,
<?php esc_html_e('scheduled task log', 'visitorlog');?>.
                                            <br><br>&nbsp;
<?php esc_html_e('Localization', 'visitorlog');?>
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('the plugin code is executed taking into account the possibility of translating messages into different languages', 'visitorlog');?>.
                                            <br><br>&nbsp;
<?php esc_html_e('Documentation', 'visitorlog');?>
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('as an integral part, the plugin contains documentation describing each function in each section', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('Installation', 'visitorlog');?>
                                            <br>&nbsp;1.&nbsp;
<?php esc_html_e('You can install the VisitorLog plugin by following the usual installation procedure, or by uploading the plugin to the /wp-content/plugins directory/', 'visitorlog');?>.
                                            <br>&nbsp;2.&nbsp;
<?php esc_html_e('After installation, activate the plugin via the plugins menu in WordPress', 'visitorlog');?>.
                                            <br>&nbsp;3.&nbsp;
<?php esc_html_e('Set the plugin settings in the installation wizard by following the suggested steps sequentially. The wizard will install the database, tables and fill them with initial data', 'visitorlog');?>.
                                            <br><br>&emsp;
<?php esc_html_e('Devs', 'visitorlog');?>&emsp;<?php echo esc_html(VisitorLog_Utility::$name_firm);?>,&nbsp;
<a href="<?php echo esc_url(VisitorLog_Utility::$home_site);?>">
<?php echo esc_url(VisitorLog_Utility::$home_site);?>
</a>
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php

	} // END func

    //                                   ---------- Security ----------
    /**
     * Render the contents of the Security Control panel
     */
    protected static function render_security_control_panel()
    {
        ?>
        <div class="row clearfix" id="security">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Security', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix" style="margin-top:7px;" id="control_panel">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <em><?php esc_html_e('Control panel', 'visitorlog');?></em>
                        </h3>
                    </div>
                    <div></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('The control panel is the top level of control of all the functions of the plugin', 'visitorlog');?>
(<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_controlpanel')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>">
    <?php esc_html_e('Link','visitorlog');?>
</a>).
                                        <br>
<?php esc_html_e('You can use a single switch to block the operation of the functions of the entire module, for example, the protection of the administrative panel or spam', 'visitorlog');?>.
                                        <br><br>
<?php esc_html_e('The list of the main functional modules of the plug-in on the control panel', 'visitorlog');?>:
                                        <br>&emsp;&emsp;&#8226;
<?php esc_html_e('protection of site pages from unwanted visitors of people and bots, their blocking (including DDoS attacks)', 'visitorlog');?>;
                                        <br>&emsp;&emsp;&#8226;
<?php esc_html_e('protection of the admin panel (including DDoS attacks, change of access to the admin panel login page)', 'visitorlog');?>;
                                        <br>&emsp;&emsp;&#8226;
<?php esc_html_e('database protection (backup, change of table prefix)', 'visitorlog');?>;
                                        <br>&emsp;&emsp;&#8226;
<?php esc_html_e('file system protection by scanning files and checking access to files and folders', 'visitorlog');?>;
                                        <br>&emsp;&emsp;&#8226;
<?php esc_html_e('spam protection in comments and feedback forms', 'visitorlog');?>;
                                        <br>&emsp;&emsp;&#8226;
<?php esc_html_e('firewall modes (G7, G6, config WP file protection, etc.)', 'visitorlog');?>;
                                        <br>&emsp;&emsp;&check;
<?php esc_html_e('statistics of site visits (permanently enabled)', 'visitorlog');?>;
                                        <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func    

    /**
     * Render the contents of the Log & Blocking of Visitors
     */
    protected static function render_security_log_blocking_visitor()
    {
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="block_ip">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <em><?php esc_html_e('Log & Blocking of Visitors', 'visitorlog');?></em>
                        </h3>
                    </div>
                    <div></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('This module allows you to activate DDoS attack detection modes, block attacks during 404 events, and control the parameters of attack detection and address blocking algorithms', 'visitorlog');?>
(<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_blockvisitor')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>">
    <?php esc_html_e('Link','visitorlog');?>
</a>).
                                            <br>
<?php esc_html_e('Together with the mode and parameter management module, the blocked IP address registration table interacts', 'visitorlog');?>
(<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_lockedaddr')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>">
    <?php esc_html_e('Link','visitorlog');?>
</a>).
                                            <br>
<?php esc_html_e('Unwanted visits are automatically detected according to the parameters of the embedded algorithm and the visitor\'s data is entered into the table for blocking', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The parameter values are set by default and can be changed by the site administrator', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The site entry points and HTTP request methods are constantly monitored', 'visitorlog');?>:
                                            <br>&emsp;&emsp;&#8226; pages
                                            <br>&emsp;&emsp;&#8226; authorization
                                            <br>&emsp;&emsp;&#8226; xmlrpc;
                                            <br>&emsp;&emsp;&#8226; post
                                            <br>&emsp;&emsp;&#8226; get
                                            <br><br>
<?php esc_html_e('Types of blocking during an attack from a single IP address', 'visitorlog');?>:
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('temporary blocking of an address for a set period of time, organized programmatically', 'visitorlog');?>;
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('permanent blocking with entering the address in the system file .htaccess, triggered after several temporary locks', 'visitorlog');?>;
                                            <br>
<?php esc_html_e('Protecting the site from attacks from multiple IP addresses', 'visitorlog');?>.
                                            <br>&emsp;&emsp;&#8226;
<?php esc_html_e('blocking the operation of the entire site for a specified period of time', 'visitorlog');?>
(<?php esc_html_e('in full version', 'visitorlog');?>).
                                            <br><br>
<?php esc_html_e('In this version of the plugin, only DDoS attacks are detected, address blocking is implemented in the full version', 'visitorlog');?>
&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php        

    } // END func

    /**
     * Render table of contents.
     */
    protected static function render_protection_admin_panel()
    {
        if ( get_option('permalink_structure') ) {
            $home_url = trailingslashit(home_url());
        } else {
            $home_url = trailingslashit(home_url()) . '?';
        }
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="admin_panel">
            <div class="col-lg-12">
                <div class="">
                    <div class="header">
                        <h3>
                            <em><?php esc_html_e('Protection of the administrative panel', 'visitorlog');?></em>
                        </h3>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                        &emsp;&emsp;
<?php esc_html_e('To crack the password to log into the site\'s administrative control panel, hackers use a brute force attack, trying many combinations of usernames and passwords until they can guess the correct combination', 'visitorlog');?>.
                                            <br>&emsp;&emsp;
<?php esc_html_e('Attackers need access to your site for their own bad purposes. There are a lot of people who want to and this is the most common type of attack. Please don\'t make passwords easy. At least 8 characters in different registers and there must be special characters', 'visitorlog');?>.
                                            <br>&emsp;&emsp;
<?php esc_html_e('The plugin implements comprehensive protection against such threats', 'visitorlog');?>.
                                            <br><br>&emsp;&emsp;
<?php esc_html_e('The module allows you to activate the protection modes of the admin panel from malicious attacks, manage the IP address blocking parameters, install two types of captcha', 'visitorlog');?>
(<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_adminpanel')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>">
    <?php esc_html_e('Link','visitorlog');?>
</a>).
                                            <br>
<?php esc_html_e('Together with the mode and parameter management module, the blocked IP address registration table interacts', 'visitorlog');?>
(<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_failedlogins')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>">
    <?php esc_html_e('Link','visitorlog');?>
</a>),&nbsp;
<?php esc_html_e('Administrators Table', 'visitorlog');?>
(<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_administrators')).'&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce'));?>">
    <?php esc_html_e('Link','visitorlog');?>
</a>).
                                            <br><br>
<?php esc_html_e('Notes','visitorlog');?>:&ensp;
<?php esc_html_e('Additional functions for protecting the admin panel by applying a white list of IP addresses and renaming the login page are presented in the full version of the plugin','visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>&emsp;&emsp;
<?php esc_html_e('A list and a brief description of the security functions of the site\'s administrative panel', 'visitorlog');?>
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                            <td width="35">N</td>
                            <td width="190"><?php esc_html_e('Functions',  'visitorlog');?></td>
                            <td>            <?php esc_html_e('Description',  'visitorlog');?></td>
                            <td width="40"> <?php esc_html_e('File', 'visitorlog');?>&nbsp;.htaccess</td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>1</td>
                                        <td>
<?php esc_html_e('Blocking bots when logging in', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('Enable it if you want erroneous login attempts using usernames or their IP addresses to be blocked', 'visitorlog');?>.             <br>
<?php esc_html_e('You can manually adjust some parameters of the bot search and blocking algorithm', 'visitorlog');?>:
                                            <br>&ensp;&nbsp;&#8226;
<?php esc_html_e('the limit of login attempts for a certain period of time', 'visitorlog');?>
                                            <br>&ensp;&nbsp;&#8226;
<?php esc_html_e('enabling the ability to permanently block the address in the htaccess file', 'visitorlog');?>
                                            <br>&ensp;&nbsp;&#8226;
<?php esc_html_e('you can set a redirect URL for temporary blocking', 'visitorlog');?>
                                            <br><br>  
<?php esc_html_e('The IP address registration table interacts with the mode', 'visitorlog');?>
                                            <br>
<?php
$title = __('Failed Login Attempt table', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_failedlogins' );
?>
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('plus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                                <?php esc_html_e('Enable Captcha On Login Page', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('Enable this feature if you want to insert a captcha form on the login page', 'visitorlog');?>.
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('minus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
                                <?php esc_html_e('Enable Google reCAPTCHA On Login Page', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('Enable it if you want to use Google reCAPTCHA v2. Enter the Site Key, enter the Secret Key', 'visitorlog');?>.
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('minus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>
                                <?php esc_html_e('Enable Force User Logout', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('The administrator can set his own time period, after which you will need to log in again', 'visitorlog');?>.
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('minus.png');
?>
                                            <br><br><br>
                                        </td>                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
    } // END func

    /**
     * Render the contents of protection db
     */
    protected static function render_protection_db()
    {
        ?>
        <div class="row clearfix" id="db_protect">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <em><?php esc_html_e('DataBase protection', 'visitorlog');?></em>
                        </h3>
                    </div>
                    <div></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            <em>
                                                <p style="color:blue; font-size:16px;">
                                                </p>
                                            </em>
                                        &emsp;&emsp;
<?php esc_html_e('The database is a target for hackers because it is an important asset of the site because it contains a lot of valuable information', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Attackers use methods such as SQL injection and malicious automated code aimed at accessing a data table', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The plugin allows the site administrator to create an archive file of all database tables, save it to an installed directory on the server and send the archive to an email address. The plugin can create an archive automatically with a set time interval. Another method to protect the database is to change the table prefix. The administrator chooses the prefix value independently', 'visitorlog');?>.

                                            <br>
<?php esc_html_e('Database security functions are managed in the section', 'visitorlog');?>
                                            <br>&nbsp;
<?php
$title = __('DataBase protection', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_dbprotection' );
?>
                                            <br>
<?php esc_html_e('The results are shown in the table', 'visitorlog');?>
                                            <br>&nbsp;
<?php
$title = __('Backups Report', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_backupreport' );
?>
                                            <br>
<?php esc_html_e('This plugin is a comprehensive data protection system and includes the following list of functions', 'visitorlog');?>:

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="35">N</td>
                                        <td width="190"><?php esc_html_e('Functions',  'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description',  'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>1</td>
                                        <td>
<?php esc_html_e('Change Database Prefix', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('If, during the installation of WordPress, the administrator did not change the default prefix of the database tables "wp_", then he can do this using the plugin at any time and thereby increase the level of database protection', 'visitorlog');?>.
<?php esc_html_e('When the task is activated, the plugin itself will perform the necessary operations', 'visitorlog');?>:
                                        <br>-&nbsp;
<?php esc_html_e('creates a backup copy of the file wp-config.php and will place it in the service directory', 'visitorlog');?>;
                                        <br>-&nbsp;
<?php esc_html_e('will replace the prefix of all database tables', 'visitorlog');?>;
                                        <br>-&nbsp;
<?php esc_html_e('replaces the names of some fields in the "options" and "usermeta" tables', 'visitorlog');?>;
                                        <br>-&nbsp;
<?php esc_html_e('replaces the old prefix value with the new one in the file wp-config.php', 'visitorlog');?>.
                                        <br>
<?php esc_html_e('To successfully complete the task, the plugin must be able to write to a file wp-config.php , to the wp-content system folder, and also have access to the database', 'visitorlog');?>.
                                        <br>
<?php esc_html_e('The plugin\'s lack of write permissions to files and folders can be detected during the task and lead to a breakdown of the database and website. In this case, the administrator will need to manually write to the files and database. Accordingly, you need to get the rights in advance and prepare tools such as PHPMYADMIN and a file manager', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('Create a backup copy of the database and file before using this feature wp-config.php', 'visitorlog');?>!
                                            <br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
<?php esc_html_e('Manual Database Backups', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('This allows you to create a backup copy of the database manually at any time necessary for you', 'visitorlog');?>.
                                        <br>
<?php esc_html_e('Just click on the "Start Backup" button and the process of creating a backup copy of the database will be started', 'visitorlog');?>.
                                        <br>   
<?php esc_html_e('The backup file with the zip extension will be placed in the folder', 'visitorlog');?>
&nbsp;<code>wp-content/visitorlog</code> 
                                        <br>   
<?php esc_html_e('The plugin must have write permissions set to this folder.', 'visitorlog');?>
                                        <br>   
<?php esc_html_e('If you want to receive the backup file by e-mail, check the box in the enabled position', 'visitorlog');?>.
                                        <br>
<?php esc_html_e('The email settings are set in the Settings section', 'visitorlog');?>.
                                        <br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
<?php esc_html_e('Auto Database Backups', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('Enable automatic database backup if you want to do it regularly at a set time interval', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The copy will be started by the CRON task scheduler WP', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Set the period', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('Monthly', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('Weekly', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('Daily', 'visitorlog');?>
                                            <br><br>
<?php esc_html_e('The backup file with the zip extension will be placed in the folder', 'visitorlog');?>
&nbsp;<code>wp-content/visitorlog</code> 
                                            <br>   
<?php esc_html_e('The plugin must have write permissions set to this folder.', 'visitorlog');?>
                                            <br>   
<?php esc_html_e('If you want to receive the backup file by e-mail, check the box in the enabled position', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The email settings are set in the Settings section', 'visitorlog');?>.
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    /**
     * Render the contents of protection system files
     */
    protected static function render_protection_system_files()
    {
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="file_protect">
            <div class="col-lg-12">
                <div class="">
                    <div class="header">
                        <h3>
                            <em><?php esc_html_e('File system protection', 'visitorlog');?></em>
                        </h3>
                    </div>
                    <div></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            <em>
                                                <p style="color:blue; font-size:16px;">
                                                </p>
                                            </em>
<?php esc_html_e('Another point of security control is monitoring permissions for WordPress files and folders, which determine the availability and read/write permissions of files and folders', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Sometimes users change access rights to files and folders for some reason and this makes the site less secure', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('To control the security of access to files and folders, permission values are scanned and information is displayed on the screen in the form of a table', 'visitorlog');?>.
                                            <br>&emsp;&emsp;
<?php
$title = __('File system protection', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_fileprotection' );
?>
                                            <br><br>  
<?php esc_html_e('Notes','visitorlog');?>:&ensp;
<?php esc_html_e('Additional functions to protect system files and folders by scanning and monitoring against unauthorized changes are provided in the full version of the plugin','visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="35">N</td>
                                        <td width="190"><?php esc_html_e('Functions',  'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description',  'visitorlog');?></td>
                                        <td width="40"> <?php esc_html_e('File', 'visitorlog');?>&nbsp;.htaccess</td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>1</td>
                                        <td>
                            <?php esc_html_e('Disable Ability To Edit PHP Files', 'visitorlog');?>
                                        </td>
                                        <td>
                                            &emsp;
<?php esc_html_e('Check this if you want to remove the ability for people to edit PHP files via the WP dashboard', 'visitorlog');?>.
                                        <br>
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('minus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
                            <?php esc_html_e('Enable prevent wp file access', 'visitorlog');?>
                                        </td>
                                        <td>
                                            &emsp;
<?php esc_html_e('Check this if you want to prevent access to readme.html, license.txt and wp-config-sample.php', 'visitorlog');?>.
                                        <br>
<?php esc_html_e('To implement this function , the control instructions in the file are used .htaccess', 'visitorlog');?>.
                                        <br>
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('plus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
                            <?php esc_html_e('File Permissions Scan', 'visitorlog');?>
                                        </td>
                                        <td>
                                                &emsp;
<?php esc_html_e('This feature will scan the critical WP core folders and files and will highlight any permission settings which are insecure', 'visitorlog');?>.
                                            <br>
<?php
$title = __('Table of WP core folder and file access settings', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_filepermissions' );
?>
                                            <br>
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('minus.png');
?>
                                        </td>                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    /**
     * Render the contents protection of spam
     */
    protected static function render_protection_spam()
    {
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="spam">
            <div class="col-lg-12">
                <div class="">
                    <div class="header">
                        <h3>
                            <em><?php esc_html_e('Spam Protection', 'visitorlog');?></em>
                        </h3>
                    </div>
                    <div></div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('The plugin has two anti-spam algorithms in its arsenal in comments on WordPress blogs', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('For Algorithm 1.1 the passage of spam messages is blocked and they are recorded in a temporary storage - spam table', 'visitorlog');?>.&nbsp;
<?php esc_html_e('In the table, the administrator can manually permanently block the IP address in the htaccess file', 'visitorlog');?>.
                                            <br>&nbsp;
<?php
$title = __('Spam protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_spamprotection' );
?>
                                            <br>&nbsp;
<?php
$title = __('Spam report', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_spam' );
?>
                                            <br><br>
<?php esc_html_e('Notes','visitorlog');?>:&ensp;
<?php esc_html_e('The full version of the plugin provides additional features to protect against spam in the comments and feedback form using short codes','visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="35">N</td>
                                        <td width="190"><?php esc_html_e('Functions',  'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description',  'visitorlog');?></td>
                                        <td width="40"> <?php esc_html_e('File', 'visitorlog');?>&nbsp;.htaccess</td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td colspan="3">
                                            1.&emsp;
<?php esc_html_e('Spam protection in comments', 'visitorlog');?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>1.1</td>
                                        <td>
<?php esc_html_e('Algorithm', 'visitorlog');?>&nbsp;1<br>
<?php esc_html_e('Blocking a spammer when it is detected by WordPress', 'visitorlog');?>
                                        </td>
                                        <td>
                                            &emsp;
<?php esc_html_e('In this case, the plugin receives a signal about spam comments from WordPress and enters the spammer\'s data into the table', 'visitorlog');?>.&nbsp;
<?php esc_html_e('In the future, the administrator decides what to do with it - delete it from the quarantine table or permanently block the IP address in the htaccess file', 'visitorlog');?>.
                                            <br>
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('plus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>1.2</td>
                                        <td>
<?php esc_html_e('Algorithm', 'visitorlog');?>&nbsp;2<br>
<?php esc_html_e('Forbidding access to the file wp-comments-post.php', 'visitorlog');?>
                                        </td>
                                        <td>
                                            &emsp;
<?php esc_html_e('Use the Htaccess file instructions to block bots from accessing the file wp-comments-post.php and redirect them to the address', 'visitorlog');?>&nbsp;http://127.0.0.1
                                            <br>
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('plus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>1.3</td>
                                        <td>
<?php esc_html_e('Forbid Proxy Comment Posting', 'visitorlog');?>
                                        </td>
                                        <td>
                                            &emsp;
<?php esc_html_e('Use the Htaccess file instructions to blacklist the various HTTP protocols used by proxy servers', 'visitorlog');?>.
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('plus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td>1.4</td>
                                        <td>
                                            <?php esc_html_e('Captcha', 'visitorlog');?>
                                        </td>
                                        <td>
                                            &emsp;
<?php esc_html_e('Select one of the two captcha options to install in the comment form field', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('Check the box if you want to insert a captcha field into the comments form', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('Check the box if you want to use Google reCAPTCHA v2', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('For Google captcha, enter the two service codes that you received when registering the captcha on the Google service', 'visitorlog');?>.
                                            <br>
                                            <br>
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('minus.png');
?>
                                        </td>                                        
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            2.&emsp;
<?php esc_html_e('Blocking IP addresses. For sections 1.1', 'visitorlog');?>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
<?php esc_html_e('Permanent blocking of IP addresses in Htaccess file', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('When enabled, the administrator will be able to manually permanently block the IP address using the control instructions in the htaccess file. This is done in the Spam table', 'visitorlog');?>.
                                        </td>
                                        <td>
<?php
VisitorLog_Utility::render_picture('plus.png');
?>
                                        </td>                                        
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    /**
     * Render the contents of Firewall
     */
    protected static function render_firewall()
    {
        ?>
        <div class="row clearfix" id="firewall">
            <div class="col-lg-12">
                <div class="">
                    <div class="header">
                        <h3>
                            <em><?php esc_html_e('Firewall', 'visitorlog');?></em>
                        </h3>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('Firewall functions are applied by inserting special code (instructions) into your active file htaccess', 'visitorlog');?>.
<?php esc_html_e('Therefore , it is recommended to create a backup copy of the file htaccess before activating the firewall', 'visitorlog');?>.
                                            <br><br>&emsp;&emsp;
<?php esc_html_e('The first in the list are the firewall protection functions of generation 7 (7G), then generation 6 (6G), developed and released by Portable Press', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Select one of the suggested generations by turning on the switch', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Next come the basic protection functions', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('file protection wp-config.php by denying access to it', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('disabling the server signature, which deprives hackers of additional information about the system', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('limiting the maximum size of the uploaded file. The default value is 10 MB', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('disabling index browsing, which does not allow viewing a list of directories and files', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('ban image linking if you want to prevent hot links to images on your site', 'visitorlog');?>.
                                            <br><br>&emsp;&emsp;
<?php esc_html_e('Cross–site scripting (XSS) is a code injection attack that allows an attacker to run malicious JavaScript in another user\'s browser', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Protection against cross-site scripting attacks (XSS)', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('Disable Trace and Track', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('Deny Bad Query Strings', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('Enable Advanced Character String Filter', 'visitorlog');?>
                                            <br><br>&emsp;&emsp;
<?php
$title = __('Firewall protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_firewall' );
?>
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
    } // END func

    //                                   ---------- Reports ----------
    /**
     * Render the contents of reports
     */
    protected static function render_reports()
    {
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="reports">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Reports', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('Reports are useful information for administrators presented in a tabular format', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The content is generated during the execution of the plug-in functions (database backup, plug-in updates) or is generated specifically on request (system information, file access rights report)', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The administrator can only clear the report data, but there is no way to change it', 'visitorlog');?>.
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix" id="backups_report">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="35">N</td>
                                        <td width="190"><?php esc_html_e('Functions',  'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description',  'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                        1                                            
                                        </td>
                                        <td>
<?php esc_html_e('Backups Report', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Backups Report', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_backupreport' );
?>
                                            <br>
                                            &emsp;
<?php esc_html_e('The report is linked to the control panel for the backup parameters of the tables of the WordPress database plugin', 'visitorlog');?>.
                                            <br>
<?php
$title = __('DataBase protection', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_dbprotection' );
?>
                                            <br><br>
<?php esc_html_e('The report contains information about', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time of copying', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the initiator of the operation, it can be CRON in automatic mode or administrator in manual mode', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('as a result of database copying', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('as a result of sending the copied database file to an email', 'visitorlog');?>;
                                            <br>
<?php esc_html_e('Data from the report can be deleted', 'visitorlog');?>.
                                        </td>
                                    </tr>
                                    <tr id="server_info">
                                        <td>2</td>
                                        <td>
<?php esc_html_e('Server info', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Server info', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_serverinfo' );
?>
                                            <br><br>
<?php esc_html_e('The report on system data, server, plugins contains', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('Information about plugins', 'visitorlog');?>:
                                            <br>&emsp;                                                
<?php esc_html_e('currently active', 'visitorlog');?>;
                                            <br>&emsp;                                                
<?php esc_html_e('Inactive', 'visitorlog');?>;
                                            <br>&emsp;                                                
<?php esc_html_e('VisitorLog plugin', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('information about WordPress', 'visitorlog');?>:
                                            <br>&emsp;                                                
<?php esc_html_e('about the version', 'visitorlog');?>;
                                            <br>&emsp;                                                
<?php esc_html_e('memory limit', 'visitorlog');?>;
                                            <br>&emsp;                                                
<?php esc_html_e('Multisite on/off', 'visitorlog');?>;
                                            <br>&emsp;                                                
<?php esc_html_e('File System Method', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('information about PHP', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('information about MySQL', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('Server and OS information', 'visitorlog');?>
                                            <br>
                                            <br>
<?php esc_html_e('You can generate a report in an HTML file and send it to an email address in the full version of the plugin', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                        </td>
                                    </tr>
                                    <tr id="file_permissions">
                                        <td>3</td>
                                        <td>
<?php esc_html_e('File Permissions Scan Report', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('File Permissions Scan Report', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_filepermissions' );
?>
                                            <br><br>
<?php esc_html_e('The File Access Rights report contains', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('current permissions of system files and folders', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('Recommended Permissions', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('Recommended Actions', 'visitorlog');?>.

                                        </td>
                                    </tr>
                                    <tr id="updates">
                                        <td>4</td>
                                        <td>
<?php esc_html_e('Updates', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Updates', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_updates' );
?>
                                            <br><br>
<?php esc_html_e('Messages about the availability of updates are displayed in the administrative panel of the site and on the current pages of the plugin', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The plugin updates report contains', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time of the update of the new version of the plugin or the plugin database tables', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('update mode', 'visitorlog');?>
                                            <br>&emsp;
<?php esc_html_e('Installation', 'visitorlog');?>
                                            <br>&emsp;
<?php esc_html_e('activation', 'visitorlog');?>
                                            <br>&emsp;
<?php esc_html_e('upgrade to a new version', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('the new version of the plugin', 'visitorlog');?>
                                            <br>&#8226;
<?php esc_html_e('a new version of the plugin\'s DB tables', 'visitorlog');?>
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    //                                   ---------- Tables ----------
    /**
     * Render tables
     */
    protected static function render_tables()
    {
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="tables">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Tables', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('Tables have more opportunities for administrators to work with data compared to reports and logs', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Data is entered into tables in accordance with data locking algorithms. The administrator can also manually edit add and delete data, send it by e-mail', 'visitorlog');?>.
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix" id="blacklist">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="35">N</td>
                                        <td width="190"><?php esc_html_e('Functions', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                        1                                            
                                        </td>
                                        <td>
<?php esc_html_e('Table Blocked IPs', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Table Blocked IPs', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_lockedaddr' );
?>
                                            <br>
<?php
$title = __('Logging & blocking functions', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_blockvisitor' );
?>
                                            <br><br>
<?php esc_html_e('The data is entered into the table automatically from the DDoS protection module, as well as manually by the site administrator','visitorlog');?>.&nbsp;
<?php esc_html_e('IP addresses are not automatically blocked, this is implemented in the full version of the plugin','visitorlog');?>.&nbsp;
<?php esc_html_e('The administrator can add a new IP address, delete, permanently block one or a group of addresses','visitorlog');?>.
                                            <br>
<?php esc_html_e('The table with the addresses of invalid logins is linked to the security management module of the admin panel','visitorlog');?>.
                                            <br><br>
<?php esc_html_e('Managing table data', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('You can choose one of three options for sorting table data to display on the screen - all rows, blocked, unblocked', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('code.png');
?>
<?php esc_html_e('View file .htaccess', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('You can generate a report in an HTML file and send it to an email address in the full version of the plugin', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>&#8226;
<input type="checkbox" name="vl_checkbox_guide" id="vl_checkbox_guide" title="<?php esc_html_e('Select row', 'visitorlog');?>" value="">
<?php esc_html_e('check the box and select the lines you need', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('b_drop.png');
?>
<?php esc_html_e('Delete selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('ban.png');
?>
<?php esc_html_e('Block selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('unban.png');
?>
<?php esc_html_e('Unblock selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('deltbl.png');
?>
<?php esc_html_e('Delete all rows from the table', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('b_insrow.png');
?>
<?php esc_html_e('Row insert', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('The table contains columns with information', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time of the visitor\'s registration', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('date and time when the IP address was unblocked', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('IP address in ipv4 or ipv6 format', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('count - counter of temporary IP address locks', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the channels of entry to the site and methods are determined by the plugin algorithm and can be as follows', 'visitorlog');?>:&ensp;PAGES, AUTH, XHR, GET, POST;
                                            <br>&#8226;
<?php esc_html_e('category - the type of visitor is determined by the plugin algorithm and can be', 'visitorlog');?>:&ensp;BotDdos, BotDdosIP, BlockDdos;
                                            <br>&#8226;
<?php esc_html_e('user agent - when visiting the site, the client application sends information about itself to the web server - a text string that is part of an HTTP request. Includes the name, application version, computer operating system, and language', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('URL - is a unique address of a resource on the Internet', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('The Block = temp parameter, temporary blocking of the IP address for the period of time set in the parameter', 'visitorlog');?>&ensp;
<?php esc_html_e('Date Write/Release', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The Block = perm parameter, permanently blocking the name or IP address using the instructions in the htaccess file', 'visitorlog');?>.
                                            <br><br>
                                        </td>
                                    </tr>
                                    <tr id="failed_logins_table">
                                        <td>2</td>
                                        <td>
<?php esc_html_e('Failed logins report', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Failed Logins', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_failedlogins' );
?>
                                            <br>
<?php
$title = __('Admin panel protection', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_adminpanel' );
?>
                                            <br><br>
<?php esc_html_e('The data in the table is generated automatically when the bot tries to log into the admin panel','visitorlog');?>.
                                            <br>
<?php esc_html_e('Automatic address blocking takes place in two stages. First, the IP address is blocked for a specified period of time. The temporary lock counter is turned on. If the counter reaches the set limit, permanent blocking is enabled by instructions in the htaccess file','visitorlog');?>.
                                            <br>
<?php esc_html_e('Manual address blocking occurs at the request of the administrator, he can delete an entry from the table, unlock or permanently block one address or a group of IP addresses in the htaccess file','visitorlog');?>.
                                            <br>
<?php esc_html_e('The table with the addresses of invalid logins is linked to the security management module of the admin panel','visitorlog');?>.
                                            <br><br>
<?php esc_html_e('Managing table data', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('You can choose one of three options for sorting table data to display on the screen - all rows, blocked, unblocked', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('code.png'); 
?>
<?php esc_html_e('View file .htaccess', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('You can generate a report in an HTML file and send it to an email address in the full version of the plugin', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>&#8226;
<input type="checkbox" name="vl_checkbox_guide" id="vl_checkbox_guide" title="<?php esc_html_e('Select row', 'visitorlog');?>" value="">
<?php esc_html_e('check the box and select the lines you need', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('b_drop.png');
?>
<?php esc_html_e('Delete selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('ban.png');
?>
<?php esc_html_e('Block selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('unban.png');
?>
<?php esc_html_e('Unblock selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('deltbl.png');
?>
<?php esc_html_e('Delete all rows from the table', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('The table contains columns with information', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time of the visitor\'s registration', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('date and time when the IP address was unblocked', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('IP address in ipv4 or ipv6 format', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('count - counter of temporary IP address locks', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the channels of entry to the site and methods are determined by the plugin algorithm and can be as follows', 'visitorlog');?>:&ensp;PAGES, AUTH, XHR, GET, POST;

                                            <br>&#8226;
<?php esc_html_e('author - type of visitor, it is determined by the plugin algorithm', 'visitorlog');?>:&ensp;AdminLogin, BlockLogin, BotLogin
                                            <br>&#8226;
<?php esc_html_e('login is a set of characters that the user entered as a login in the admin panel login form', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('password is a set of characters that the user entered as a password in the admin panel login form', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('URL - is a unique address of a resource on the Internet', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('refinfo - contains the HTTP Referer header, which indicates the address from which the current page was accessed', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('The Block = temp parameter, temporary blocking of the IP address for the period of time set in the parameter', 'visitorlog');?>&ensp;
<?php esc_html_e('Date Write/Release', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The Block = perm parameter, permanently blocking the name or IP address using the instructions in the htaccess file', 'visitorlog');?>.
                                            <br><br>
                                        </td>
                                    </tr>
                                    <tr id="spam_table">
                                        <td>3</td>
                                        <td>
<?php esc_html_e('Spam report', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Spam report', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_spam' );
?>
                                            <br>
<?php
$title = __('Spam protection features', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_spamprotection' );
?>
                                            <br><br>
<?php esc_html_e( 'The data in the table is received automatically when Wordpress detects spam in comments', 'visitorlog' );?>.
                                            <br> 
<?php esc_html_e('Manual address blocking occurs at the request of the administrator, he can delete an entry from the table, unlock or permanently block one address or a group of IP addresses in the htaccess file','visitorlog');?>.
                                            <br>
<?php esc_html_e('The table with spammers\' addresses is linked to the spam parameters management module','visitorlog');?>.
                                            <br><br>
<?php esc_html_e('Managing table data', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('You can choose one of three options for sorting table data to display on the screen - all rows, blocked, unblocked', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('code.png');
?>
<?php esc_html_e('View file .htaccess', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('You can generate a report in an HTML file and send it to an email address in the full version of the plugin', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>&#8226;
<input type="checkbox" name="vl_checkbox_guide" id="vl_checkbox_guide" title="<?php esc_html_e('Select row', 'visitorlog');?>" value="">
<?php esc_html_e('check the box and select the lines you need', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('b_drop.png');
?>
<?php esc_html_e('Delete selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('ban.png');
?>
<?php esc_html_e('Block selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('unban.png');
?>
<?php esc_html_e('Unblock selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('deltbl.png');
?>
<?php esc_html_e('Delete all rows from the table', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('The table contains columns with information', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time of the visitor\'s registration', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('IP address in ipv4 or ipv6 format', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('The "Reason" column shows where the information came from - from the comments or from the feedback form', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('URL - is a unique address of a resource on the Internet', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('user agent - when visiting the site, the client application sends information about itself to the web server - a text string that is part of an HTTP request. Includes the name, application version, computer operating system, and language', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('The Block = perm parameter, permanently blocking the name or IP address using the instructions in the htaccess file', 'visitorlog');?>.
                                            <br><br>
                                        </td>
                                    </tr>
                                    <tr id="event404">
                                        <td>4</td>
                                        <td>
<?php esc_html_e('Event 404', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Event 404', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_event404' );
?>
                                            <br><br>
<?php esc_html_e('The data in the table is generated automatically if a visitor or bot tries to go to a non-existent page of the site','visitorlog');?>.
                                            <br>
<?php esc_html_e('Automatic address blocking takes place in two stages. First, the IP address is blocked for a specified period of time. The temporary lock counter is turned on. If the counter reaches the set limit, permanent blocking is enabled by instructions in the htaccess file','visitorlog');?>.
                                            <br>
<?php esc_html_e('Manual address blocking occurs at the request of the administrator, he can delete an entry from the table, unlock or permanently block one address or a group of IP addresses in the htaccess file','visitorlog');?>.
                                            <br>
<?php esc_html_e('The 404 event address table is linked to the User registration and blocking parameters management module','visitorlog');?>.
                                            <br><br>
<?php esc_html_e('Managing table data', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('You can choose one of three options for sorting table data to display on the screen - all rows, blocked, unblocked', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('code.png');
?>
<?php esc_html_e('View file .htaccess', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('You can generate a report in an HTML file and send it to an email address in the full version of the plugin', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>&#8226;
<input type="checkbox" name="vl_checkbox_guide" id="vl_checkbox_guide" title="<?php esc_html_e('Select row', 'visitorlog');?>" value="">
<?php esc_html_e('check the box and select the lines you need', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('b_drop.png');
?>
<?php esc_html_e('Delete selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('ban.png');
?>
<?php esc_html_e('Block selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('unban.png');
?>
<?php esc_html_e('Unblock selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('deltbl.png');
?>
<?php esc_html_e('Delete all rows from the table', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('The table contains columns with information', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time of the visitor\'s registration', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('date and time when the IP address was unblocked', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('IP address in ipv4 or ipv6 format', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('count - counter of temporary IP address locks', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the channels of entry to the site and methods are determined by the plugin algorithm and can be as follows', 'visitorlog');?>:&ensp;PAGES, AUTH, XHR, GET, POST;
                                            <br>&#8226;
<?php esc_html_e('user agent - when visiting the site, the client application sends information about itself to the web server - a text string that is part of an HTTP request. Includes the name, application version, computer operating system, and language', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('URL - is a unique address of a resource on the Internet', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('refinfo - contains the HTTP Referer header, which indicates the address from which the current page was accessed', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('The Block = temp parameter, temporary blocking of the IP address for the period of time set in the parameter', 'visitorlog');?>&ensp;
<?php esc_html_e('Date Write/Release', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The Block = perm parameter, permanently blocking the name or IP address using the instructions in the htaccess file', 'visitorlog');?>.
                                            <br><br>
                                        </td>
                                    </tr>
                                    <tr id="bots_table">
                                        <td>5</td>
                                        <td>
<?php esc_html_e('Table of Bots', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Table of Bots', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_botstable' );
?>
                                            <br><br>
<?php esc_html_e('The table allows the administrator to create his own archive of bots with their names and IP addresses','visitorlog');?>.
                                            <br>
<?php esc_html_e('The administrator can manually perform the following operations with the data in the table','visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('add a new bot with its name and IP address, as well as specify its type and the possibility of permanent blocking', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('delete a row from a table', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('copy a row from the table', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('edit a row from a table', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('permanently block one or a group of addresses in the htaccess file', 'visitorlog');?>;
                                            <br>
<?php esc_html_e('To permanently block the IP address in the htaccess file, the appropriate mode must be enabled in the registration and blocking management module','visitorlog');?>
<?php
$title = __('Logging & blocking functions', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_blockvisitor' );
?>
                                            <br>
<?php esc_html_e('Keep in mind that if there are bots in your table that index the pages of your site to display its pages in the browser, for example GoogleBot, then it is not necessary to block such a bot','visitorlog');?>.
                                            <br><br> 
<?php esc_html_e('Managing table data', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('You can choose one of five options for sorting tabular data to display on the screen - all rows, blocked, not blocked, which are forbidden to block, by type of bots (search bots for browsers)', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('code.png');
?>
<?php esc_html_e('View file .htaccess', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('You can generate a report in an HTML file and send it to an email address in the full version of the plugin', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>&#8226;
<input type="checkbox" name="vl_checkbox_guide" id="vl_checkbox_guid" title="<?php esc_html_e('Select row', 'visitorlog');?>" value="">
<?php esc_html_e('check the box and select the lines you need', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('b_drop.png');
?>
<?php esc_html_e('Delete selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('ban.png');
?>
<?php esc_html_e('Block selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('unban.png');
?>
<?php esc_html_e('Unblock selected rows', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('deltbl.png');
?>
<?php esc_html_e('Delete all rows from the table', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('b_insrow.png');
?>
<?php esc_html_e('Row insert', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('The table contains columns with information', 'visitorlog');?>:
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('line_edit.png');
?>
<?php esc_html_e('Edit row', 'visitorlog');?>;
                                            <br>&#8226;
<?php
VisitorLog_Utility::render_picture('b_insrow.png');
?>
<?php esc_html_e('Copy row', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the name of the bot from the user agent line', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('Bot Type', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('IP address in ipv4 or ipv6 format', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('short name to display in the table', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('Comments', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('The Block = not block parameter prohibits the permanent blocking of the bot in the htaccess file', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The Block = perm parameter, permanently blocking the name or IP address using the instructions in the htaccess file', 'visitorlog');?>.
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    //                                   ---------- Logs ----------
    /**
     * Render the contents of Logs
     */
    protected static function render_logs()
    {
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="logs">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Logs', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('Logs register event parameters and display them on the screen in the form of tables', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The administrator does not have the ability to change the data, only delete it', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The number of rows of output to the screen and the number of rows of storage in the database is determined by the parameters in the Settings mode', 'visitorlog');?>.
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix" id="visitor_log">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="35">N</td>
                                        <td width="190"><?php esc_html_e('Functions', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                        1                                            
                                        </td>
                                        <td>
<?php esc_html_e('Visitor Log', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Visitor Log', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_visitorlog' );
?>
                                            <br><br>
<?php esc_html_e('All site visitors and their parameters are recorded in the log', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The table contains a list of the user\'s sessions and the number of pages he visited on the site','visitorlog');?>.
                                            <br>
<?php esc_html_e('The administrator has the option to delete all entries in the table', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('You can generate a report in an HTML file and send it to an email address in the full version of the plugin', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>
<?php esc_html_e('The table contains columns with information', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time of the visitor\'s registration', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('date and time when the IP address was unblocked', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('IP address in ipv4 or ipv6 format', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('visitor - algorithm of plugin can determine how', 'visitorlog');?>:&ensp;Admin, Admin404, AdminLogin, Bot, BotLogin, Bot404, BotDdos, BotddosIP, BlockDdos, Block404, BlockLogin, BlockSpamCO, BlockSpamCF;
                                            <br>&#8226;
<?php esc_html_e('the channels of entry to the site and methods are determined by the plugin algorithm and can be as follows', 'visitorlog');?>:&ensp;PAGES, AUTH, XHR, GET, POST;
                                            <br>&#8226;
<?php esc_html_e('the "Pages" column contains the number of pages viewed in one session','visitorlog');?>.
                                            <br>&#8226;
<?php esc_html_e('the "Session" column indicates the number of sessions with the site of this visitor','visitorlog');?>.
                                            <br>&#8226;
<?php esc_html_e('the "All pages" column indicates the number of all pages viewed in all sessions of working with the site','visitorlog');?>.
                                            <br>&#8226;
<?php esc_html_e('user agent - when visiting the site, the client application sends information about itself to the web server - a text string that is part of an HTTP request. Includes the name, application version, computer operating system, and language', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('URL - is a unique address of a resource on the Internet', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('refinfo - contains the HTTP Referer header, which indicates the address from which the current page was accessed', 'visitorlog');?>;
                                            <br><br>
                                        </td>
                                    </tr>
                                    <tr id="admins">
                                        <td>2</td>
                                        <td>
                                            <?php esc_html_e('Administrators', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Administrators', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_administrators' );
?>
                                            <br><br>
<?php esc_html_e('The log contains the IP addresses of the site administrators, the date and time of the first login. It should be borne in mind that if the ip address is dynamic, the log will register a new address in the log every time', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The administrator has the option to delete selected entries in the table', 'visitorlog');?>.
                                            <br>
                                        </td>
                                    </tr>
                                    <tr id="account_activity_log">
                                        <td>3</td>
                                        <td>
<?php esc_html_e('Account activity log', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Account activity log', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_accountactivity' );
?>
                                            <br><br>
<?php esc_html_e('The log logs the activity of administrators in the account of the administrative panel of the site', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('The log contains information', 'visitorlog');?>:
                                            <br>&#8226;                                                
<?php esc_html_e('date and time of authorization of site administrators', 'visitorlog');?>;
                                            <br>&#8226;                                               
<?php esc_html_e('date and time of logging out of the authorization session', 'visitorlog');?>;
                                            <br>&#8226;                                                
<?php esc_html_e('time spent in the session', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('administrator\'s name, ID, IP address', 'visitorlog');?>.
                                            <br>
                                        </td>
                                    </tr>
                                    <tr id="action_log">
                                        <td>4</td>
                                        <td>
<?php esc_html_e('Action Log', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Action Log', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_actionlog' );
?>
                                            <br><br>
<?php esc_html_e('The plugin logs messages that occur during various actions on the site', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The administrator has the option to delete selected entries in the table', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('the log file contains lines with information', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time of logging the event on the site', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the type of visitor, it can be User, Bot, Admin', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the content of the event message on the website', 'visitorlog');?>.
                                            <br>
                                        </td>
                                    </tr>
                                    <tr id="error_log">
                                        <td>5</td>
                                        <td>
<?php esc_html_e('Error Log', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Error Log', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_errorlog' );
?>
                                            <br><br>
<?php esc_html_e('The log uses system error information that is stored in PHP logs', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('To do this, use the ini_get(error_log) function', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The system automatically records and deletes error messages in the log', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('The body of the log contains lines with information', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('date and time when the system error was logged', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('text description of the error content', 'visitorlog');?>.
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    //                                   ---------- Statistics ----------
    /**
     * Render the contents of Statistics
     */
    protected static function render_statistics()
    {
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="statistics">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Statistics', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('This version of the plugin implements an algorithm for registering site visitors and accumulating statistics of visits by the hour within one day', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The type of visitor is determined - administrator, user or bot', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Bots are categorized depending on how they affect the site - login bot, 404 bot, DDoS bot, spam bot', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Statistical data can be seen on the screen in the form of graphs and tables', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('The collection of statistical data on the site cannot be disabled, it works all the time', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The display of information is limited to the specified output parameters', 'visitorlog');?>.
                                            <br><br>
<?php
$title = __('Report on visits by the hour', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_visitshour' );
?>
                                            <br><br>
<?php esc_html_e('In the full version of the plugin, you can get statistical data in the form of graphs and tables additionally for the current month, the current year, by regions, cities and countries, summary information, as well as generate a report in the form of an HTML file and send it to an email address', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    //                                   ---------- Settings ----------
    /**
     * Render the contents of Settings
     */
    protected static function render_settings()
    {
        ?>
        <div class="row clearfix" id="settings">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Settings', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('The option settings mode allows you to set the values of some parameters', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('Parameter values are selected from the set values', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('List of parameters for installation', 'visitorlog');?>:
                                            <br>&#8226;
<?php esc_html_e('the limit on the number of entries in the database applies to all tables, reports, with the exception of the log table', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('limiting the display of rows on the screen for all tables, reports, and modules, except for the log table', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the number of records in the database is limited only for the active log table', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('limiting the display of rows on the screen only for the log table', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('entering the license code with the key will allow you to use the full version of the plugin with all functions', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('To send messages from the site, the plugin uses the PHPMailer library, which is included with Wordpress. You can configure SMTP settings yourself to send messages from a real mailbox. This will prevent your message from getting into spam. Get the necessary parameters from the service provider and enter them in the required fields', 'visitorlog');?>;
                                            <br><br>
<?php esc_html_e('Note, for security reasons, passwords and keys are stored separately in the database and do not participate in database backup', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('Debug mode is enabled by the WP_DEBUG constant, it is disabled by default. Debugging includes processing absolutely all PHP errors and displaying them on the screen, activates additional logic in the kernel code, plugins and those where the value of this constant is checked and something is done if it is enabled', 'visitorlog');?>.&nbsp;
<?php esc_html_e('WP_DEBUG_DISPLAY and WP_DEBUG_LOG are activated only if the WP_DEBUG constant is enabled', 'visitorlog');?>.&nbsp;
<?php esc_html_e('When WP_DEBUG = false, error display and logging will work based on the settings of the php.ini file', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('All the debug mode constants are defined in the file wp-config.php', 'visitorlog');?>
                                            <br><br>
<?php
$title = __('Settings', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_settings' );
?>
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    //                                   ---------- Cron ----------
    /**
     * Render the contents of Cron
     */
    protected static function render_cron()
    {
        ?>
        <div class="row clearfix" id="cron">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Cron', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('Cron is a task scheduler whose task is to periodically perform the specified actions at a certain time', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('By default, the WP-Cron scheduler is enabled and performs system tasks for WordPress', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The VisitorLog plugin uses the capabilities of WP-Cron and forces the scheduler to turn on at the right time for its tasks', 'visitorlog');?>.
                                            <br><br>
<?php
$title = __('Scheduled Tasks', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_schedultasks' );
?>
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="35">N</td>
                                        <td width="190"><?php esc_html_e('Functions', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                        1                                            
                                        </td>
                                        <td>
<?php esc_html_e('List of tasks of the VisitorLog plugin', 'visitorlog');?>
                                        </td>
                                        <td>
                                            &#8226;
visitorlog_cron_backups_action;
                                            <br>&#8226;
vl_login_captcha_action;
                                            <br>&#8226;
vl_perform_files_scan;
                                            <br>&#8226;
visitorlog_cron_statistics; 
                                            <br>&#8226;
visitorlog_cron_check_system; 
                                            <br>&#8226;
visitorlog_cron_activity_test. 
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
<?php esc_html_e('A table with a list of scheduled tasks includes information', 'visitorlog');?>
                                        </td>
                                        <td>
                                            &#8226;
<?php esc_html_e('task name', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('task attribute', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('task schedule, the interval between inclusions is specified', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the time of the last task execution', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('time remaining until the task is completed', 'visitorlog');?>.
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
<?php esc_html_e('WP-Cron Scheduler Test', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('Independent mode starts from its own page', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('On the test page there is a step-by-step instruction', 'visitorlog');?>:
                                            <br><br>
1.&ensp;<?php esc_html_e( 'Press the "Cron test start" button', 'visitorlog' );?>
                                            <br>
2.&ensp;<?php esc_html_e( 'Press the "Refresh the page" button', 'visitorlog' );?>
                                            <br><br>
<?php esc_html_e( 'After the second action, the test results are displayed on the screen and simultaneously recorded in the "Action Logs" log', 'visitorlog' );?>.
                                            <br>
<?php
$title = __('Action Log', 'visitorlog');
VisitorLog_Utility::redirect_html( 'b_browse.png', $title, 'visitorlog_actionlog' );
?>
                                            <br>
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    //                                   ---------- Other Features----------
    /**
     * Render the contents of Other Features
     */
    protected static function render_other_features()
    {
        $link_site = VisitorLog_Utility::$home_site;
        ?>
        <div class="row clearfix" id="otherfeatures">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Other Features', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="35">N</td>
                                        <td width="190"><?php esc_html_e('Functions', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Description', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                        1                                            
                                        </td>
                                        <td>
<?php esc_html_e('Maintenance', 'visitorlog');?>
                                        </td>
                                        <td>
<?php
$title = __('Maintenance', 'visitorlog');
VisitorLog_Utility::redirect_html( 'list-repo-com.png', $title, 'visitorlog_maintenance' );
?>
                                            <br><br>
<?php esc_html_e('This feature allows you to put your site into "maintenance mode" by locking down the front-end to all visitors except logged in users with super admin privileges', 'visitorlog');?>.
                                            <br>&#8226;
<?php esc_html_e('the mode is turned on by the On/Off switch', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('the text that you want to display to site visitors during its blocking is entered into the information entry window', 'visitorlog');?>;
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
<?php esc_html_e('Hiding notifications of new WordPress version in admin panel', 'visitorlog');?>
                                        </td>
                                        <td>
<?php esc_html_e('When a new version of WP appears, notifications appear throughout the admin panel that an update is needed. Sometimes, push notifications need to be removed, while leaving the update check itself and the ability to update', 'visitorlog');?>.
                                            <br>&#8226;
<?php esc_html_e('Total update counter in the admin bar (engine + themes + plugins + translations)', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('"WordPress X.X Available" in the Console', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('"Download Version X.X" in the footer', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('Plugin update counter in the admin menu', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('Total update counter in the admin menu (engine + themes + plugins + translations)', 'visitorlog');?>;
                                            <br>&#8226;
<?php esc_html_e('"Update to X.X" in the "At a Glance" widget in the Console', 'visitorlog');?>.


                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
<?php esc_html_e('The full version of the plugin contains additional functions for deleting all comments without subsequent restoration, speeding up the work of the administrative panel by disabling the "aggressive" update check', 'visitorlog');?>&ensp;
<a target="_blank" href="<?php echo esc_url($link_site);?>">
<?php
VisitorLog_Utility::render_picture('globe.png');
?>
</a>
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    //                        ---------- Security policy for site files and folders----------
    /**
     * Render the contents of Security_policy
     */
    protected static function security_policy()
    {
        ?>
        <div class="row clearfix" id="security_policy">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Security policy', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            &emsp;&emsp;
<?php esc_html_e('The functionality of the plugin is closely related to the security policy applied by the server administrator', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('To implement its functions, the plugin works with files and folders of the system and records working information in them', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('So the access rights may need to be loose', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('The plugin uses certain files and folders to record data according to the functions that the administrator wants to use on his site', 'visitorlog');?>:
                                            <br>
                                            1.&nbsp;
<?php esc_html_e('The system file htaccess', 'visitorlog');?>.
                                            <br>
                                            1.1&nbsp;
<?php esc_html_e('Automatic writing to the htaccess file on an ongoing basis', 'visitorlog');?>.
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the Anti-DDoS mode is enabled and the IP address blocking criterion is triggered, control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the 404 event detection mode is enabled and the IP address blocking criterion is triggered, control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the error logging event detection mode is enabled in the admin panel and the IP address blocking criterion is triggered, control instructions are automatically written to a file', 'visitorlog');?>;
                                            <br>
                                            1.2&nbsp;
<?php esc_html_e('One-time entry in the htaccess file', 'visitorlog');?>.
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the access denial mode to the XML RPC channel is enabled, the control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the Login Whitelist is enabled, the control instructions are automatically written to a file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('the administrator can manually select IP addresses from his own database of IP addresses, which he created himself, and in this case, the control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the ban on editing PHP files is enabled, the control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when disabling access to wp files, control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the site administrator manually blocks the IP addresses selected from the table that were detected during spam in the comments or feedback form, the control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the G6 or G7 firewall is enabled, the control instructions are automatically written to a file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when protection against cross-site scripting attacks (XSS) is enabled, control instructions are automatically written to a file', 'visitorlog');?>;
                                            <br><br>
                                            2.&nbsp;
<?php esc_html_e('The system file wp-config.php', 'visitorlog');?>.
                                            <br>
                                            2.1&nbsp;
<?php esc_html_e('One-time entry in the wp-config.php file', 'visitorlog');?>.
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when debugging mode is enabled, the WP_DEBUG, WP_DEBUG_DISPLAY, and WP_DEBUG_LOG constants are set and control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when the mode is enabled, the constant DISALLOW_FILE_EDIT is set, which disables the plugin and theme file editor, and control instructions are automatically written to the file', 'visitorlog');?>;
                                            <br><br>
                                            3.&nbsp;
<?php esc_html_e('Folder \wp-content', 'visitorlog');?>.
                                            <br>
                                            3.1&nbsp;
<?php esc_html_e('Automatic writing to the \wp-content folder on an ongoing basis', 'visitorlog');?>.
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when writing control instructions to an htaccess file, the plugin automatically creates a backup copy of the file named .htaccess.backup and places it in the "wp-content/visitorlog" folder', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when generating report files, the plugin automatically creates files with the html extension and places them in the "wp-content/visitorlog" folder', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when generating files containing a copy of the database, the plugin automatically creates files with the zip extension and places them in the "wp-content/visitorlog" folder', 'visitorlog');?>;
                                            <br>&nbsp;-&nbsp;
<?php esc_html_e('when debugging mode is enabled, the system creates a debug.log file and writes error information to it. The file is located in the wp-content folder', 'visitorlog');?>.
                                            <br><br>&emsp;
<?php esc_html_e('This information will help you make the best decision in choosing the necessary plug-in functions in combination with the site\'s security level', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('You already understand that for short-term changes to the contents of the htaccess system files and wp-config.php it is enough to establish write access for the data and then restore the original level', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('To use the plug-in functions with constant monitoring of malicious bot actions through all input channels of the site, access to the entry in the htaccess file is required. You also need permanent write access for the wp-content folder if you are using database backup and generating various reports and sending them by mail', 'visitorlog');?>.
                                            <br><br>&emsp;
<?php esc_html_e('If you are the site administrator and have the necessary permissions to access the site\'s files and folders, then you know what to do and which commands to use in the console prompt or in the file manager', 'visitorlog');?>.
                                            <br><br>&emsp;
<?php esc_html_e('If you do not have the necessary rights to access the files and folders of the site, do not worry, the plugin has something to provide you with in this case, a wide range of visit statistics, admin panel protection, databases, spam protection, protection from malicious attacks using software locks and much more', 'visitorlog');?>.
                                            <br><br>
<?php esc_html_e('Some useful information can be found at the links', 'visitorlog');?>:
                                            <br><br>
<a href="https://developer.wordpress.org/advanced-administration/server/file-permissions/">
    <?php VisitorLog_Utility::render_picture('link.png');?>
    &nbsp;
    <?php echo('https://developer.wordpress.org/advanced-administration/server/file-permissions/');?>
</a>
<br>
<a href="https://developer.wordpress.org/advanced-administration/security/hardening/">
    <?php VisitorLog_Utility::render_picture('link.png');?>
    &nbsp;
    <?php echo('https://developer.wordpress.org/advanced-administration/security/hardening/');?>
</a>
                                            <br><br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func

    //                                   ---------- Full version ----------
    /**
     * Render of the full version of the plugin
     */
    protected static function render_full_version()
    {
        ?>
        <div class="row clearfix" id="fullversion">
            <div class="col-lg-12">
                <div>
                    <div class="header">
                        <h3>
                            <span class="guide"><?php esc_html_e('Full version', 'visitorlog');?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
<?php esc_html_e('Comparison of the full and shortened version of the plugin', 'visitorlog');?>.
                                            <br>
<?php esc_html_e('The full version of the plugin is available on the manufacturer\'s website', 'visitorlog');?>
&nbsp;
<a href="<?php echo esc_url(VisitorLog_Utility::$home_site);?>">
<?php echo esc_url(VisitorLog_Utility::$home_site);?>
</a>
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="margin-top:-5px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <thead style="color:#4169E1; background-color:#ffffff; font-size:16px;">
                                    <tr>
                                        <td width="100">#</td>
                                        <td width="550"><?php esc_html_e('Functions', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Shortened version', 'visitorlog');?></td>
                                        <td>            <?php esc_html_e('Full version', 'visitorlog');?></td>
                                    </tr>
                                </thead>
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>1</td>
                                        <td>
<b><?php esc_html_e('Security','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;1.1</td>
                                        <td>&emsp;
<b><?php esc_html_e('Log & blocking of visitors','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.1.1</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Anti DDoS','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.1.2</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('404 Event Logging & Locking','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.1.3</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Permanent or temporary blocking of IP addresses','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.1.4</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Storing data in a table with the ability to block an IP address','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.1.5</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Generate the report as an HTML file and send it by email', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.1.6</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Disallow Unauthorized REST Requests','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.1.7</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Block Access To XMLRPC','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.1.8</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Disable Pingback Functionality From XMLRPC','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;1.2</td>
                                        <td>&emsp;
<b><?php esc_html_e('Admin panel protection','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.2.1</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Blocking an invalid username when logging in','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.2.2</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Login whitelist','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.2.3</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Renaming Login Page','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.2.4</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Captcha On Login Page','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.2.5</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Google reCAPTCHA On Login Page','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.2.6</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Force User Logout','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;1.3</td>
                                        <td>&emsp;
<b><?php esc_html_e('DataBase protection','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.3.1</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Change Database Prefix','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.3.2</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Manual database backup','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.3.3</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Auto db backups','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.3.4</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Generate a report in an HTML file and send it to the mail manually or automatically according to a schedule', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;1.4</td>
                                        <td>&emsp;
<b><?php esc_html_e('File system protection','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.4.1</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Manual File Scan','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.4.2</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Automated File Scan','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.4.3</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Generate a report in an HTML file and send it to the mail manually or automatically according to a schedule', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.4.4</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Disable Ability To Edit PHP Files','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.4.5</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Prevent wp files access','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.4.6</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Check the WP core folder and file access settings', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;1.5</td>
                                        <td>&emsp;
<b><?php esc_html_e('Spam Protection','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.5.1</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Blocking spam comments using a shortcode', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.5.2</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Blocking the addresses of spam comment bots in htaccess', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.5.3</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Forbid Proxy Comment Posting', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.5.4</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Captcha On Comment Forms','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.5.5</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Google reCAPTCHA On Comment Forms','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.5.6</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Blocking spam in contact feedback forms','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.5.7</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Storing data in a table with the ability to block an IP address','visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;1.5.8</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'Generate the report as an HTML file and send it by email', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;1.6</td>
                                        <td>&emsp;
<b><?php esc_html_e('Firewall','visitorlog');?></b>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>
<b><?php esc_html_e('Reports, Tables, Logs','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;2.1</td>
                                        <td>&emsp;
<b><?php esc_html_e('Reports','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.1.1</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Backups Report', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.1.2</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Files scan report', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.1.3</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Server info', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.1.4</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('File Permissions Scan Report', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.1.5</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Updates', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.1.6</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Generate a report in an HTML file and send it to the mail manually or automatically according to a schedule', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;2.2</td>
                                        <td>&emsp;
<b><?php esc_html_e('Tables','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.2.1</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Blacklist Locked IPs', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.2.2</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Login whitelist', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.2.3</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'Failed Logins', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.2.4</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Spam', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.2.5</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'Event 404', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.2.6</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Creating your own database of unwanted addresses', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.2.7</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'Generate the report as an HTML file and send it by email', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;2.3</td>
                                        <td>&emsp;
<b><?php esc_html_e('Logs', 'visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.3.1</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'VisitorLog', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.3.2</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'Administrators', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.3.3</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e('Account activity log', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.3.4</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'Action Log', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.3.5</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'Error Log', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;&emsp;2.3.6</td>
                                        <td>&emsp;&emsp;
<?php esc_html_e( 'Generate the report as an HTML file and send it by email', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>
<b><?php esc_html_e('Statistics','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;3.1</td>
                                        <td>&emsp;
<?php esc_html_e('Report on the current day\'s visits', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;3.2</td>
                                        <td>&emsp;
<?php esc_html_e('Report for current month', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;3.3</td>
                                        <td>&emsp;
<?php esc_html_e('Report for current year', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;3.4</td>
                                        <td>&emsp;
<?php esc_html_e('Summary report', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;3.5</td>
                                        <td>&emsp;
<?php esc_html_e('Statistics of visits by regions', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;3.6</td>
                                        <td>&emsp;
<?php esc_html_e('Generate a report in an HTML file and send it to the mail manually or automatically according to a schedule', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>
                                            <b>CRON</b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;4.1</td>
                                        <td>&emsp;
<?php esc_html_e( 'Report on scheduled tasks', 'visitorlog' );?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>
<b><?php esc_html_e('Other Features','visitorlog');?></b>
                                        </td>
                                        <td>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;5.1</td>
                                        <td>&emsp;
<?php esc_html_e('Maintenance', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;5.2</td>
                                        <td>&emsp;
<?php esc_html_e('Hide notifications', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;5.3</td>
                                        <td>&emsp;
<?php esc_html_e('Delete all comments', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&emsp;5.4</td>
                                        <td>&emsp;
<?php esc_html_e('Speeding up the work of the admin panel', 'visitorlog');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('remove.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>
<b><?php esc_html_e('Guide', 'visitorlog');?></b>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                        <td>
<?php VisitorLog_Utility::render_picture('accept.png');?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div>
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered dataTable">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
<?php esc_html_e('Success in your work', 'visitorlog');?>!
                                            <br>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

    } // END func


} // END class