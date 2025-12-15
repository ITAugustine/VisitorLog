<?php
/**
 * Privacy policy
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Privacy_Policy class.
 *
 * @method public    static function init_menu()
 * @method public    static function init_left_menu()
 * @method protected static function render_privacy_policy()
 * @method protected static function privacy_policy()
 */
class VisitorLog_Privacy_Policy
{
	/**
	 * Instantiate the Settings Menu.
	 *
	 */
	public static function init_menu()
	{
		add_submenu_page(
			'visitorlog_tab',
			__( 'Privacy policy', 'visitorlog' ),
			__( 'Privacy policy', 'visitorlog' ),
			'read',
			'visitorlog_privacy_policy',
			array( __CLASS__, 'render_privacy_policy' )
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
				'title'      => __( 'Privacy policy', 'visitorlog' ),
				'parent_key' => 'visitorlog_tab',
				'slug'       => 'visitorlog_privacy_policy',
				'href'       => 'admin.php?page=visitorlog_privacy_policy&_wpnonce='.esc_html(wp_create_nonce('visitorlog_nonce')),
				'icon'       => ''
			),
			1
		);

	} // END func

	/**
	 * Render Privacy policy page.
	 *
     * @uses VisitorLog_UI::render_top_header()
     * @uses VisitorLog_View::render_preloader()
	 */
	public static function render_privacy_policy()
	{
		$params = array( 'title' => __('Privacy policy', 'visitorlog') );
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
                                            <span class="guide"><?php esc_html_e('Privacy policy', 'visitorlog');?></span>
        				            	</h2>
        				            </div>
        				        </div>
        				    </div>
        				    <div class="container-fluid" >
            			    	<?php self::privacy_policy();?>
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
    protected static function privacy_policy()
    {
        ?>
        <div style="margin-top:0px;"></div>
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="">
                    <div class="body">
                        <div class="table-responsive">
                            <table class="table table-bordered ">
                                <tbody class="vl-color-tbody-guide">
                                    <tr>
                                        <td>
                                            <br>&emsp;
<?php esc_html_e('This Personal Data Privacy Policy applies to all information that the Visitorlog plugin can receive about the User (Site Administrator) while using the plugin on his WordPress site, as well as information about his domains and subdomains, visitors to his site, his programs and his software products', 'visitorlog');?>.             <br><br>1.&nbsp;
<?php esc_html_e('General provisions', 'visitorlog');?>.
<br><br>1.1.&nbsp;
<?php esc_html_e('Using the Visitorlog plugin on the WordPress site of the User (Site Administrator) means agreeing to this Privacy Policy and the terms of processing the User\'s personal data and the data of visitors to his site', 'visitorlog');?>.
<br><br>1.2.&nbsp;
<?php esc_html_e('In case of disagreement with the terms of the Privacy Policy, the User (Site Administrator) must stop using the Visitorlog plugin on his WordPress site', 'visitorlog');?>.
<br><br>1.3.&nbsp;
<?php esc_html_e('This Privacy Policy applies to the Visitorlog plugin and does not control and is not responsible for third-party sites to which the User (Site Administrator) can click on links available on his WordPress site', 'visitorlog');?>.
<br><br>1.4.&nbsp;
<?php esc_html_e('The Visitorlog plugin does not verify the accuracy of personal data provided by the User (the Site Administrator)', 'visitorlog');?>.
<br><br>2.&nbsp;
<?php esc_html_e('Subject of the Privacy Policy', 'visitorlog');?>.
<br><br>2.1.&nbsp;
<?php esc_html_e('This Privacy Policy sets out the obligations of the User (Site Administrator) for non-disclosure and ensuring confidentiality of personal data about site visitors that the User (Site Administrator) receives as a result of the Visitorlog plugin, including its informational e-mail messages', 'visitorlog');?>.
<br><br>2.2.&nbsp;
<?php esc_html_e('The Visitorlog plugin protects the Data that is automatically logged when the site pages are visited', 'visitorlog');?>:
                                            <br>&ensp;&nbsp;&#8226;
<?php esc_html_e('IP address', 'visitorlog');?>;
                                            <br>&ensp;&nbsp;&#8226;
<?php esc_html_e('browser information', 'visitorlog');?>;
                                            <br>&ensp;&nbsp;&#8226;
<?php esc_html_e('access time', 'visitorlog');?>;
                                            <br>&ensp;&nbsp;&#8226;
<?php esc_html_e('referrer (address of the previous page)', 'visitorlog');?>.
<br><br>2.3.&nbsp;
<?php esc_html_e('The Visitorlog plugin collects statistics about the IP addresses of site visitors and the time of their visit. This information is used to accumulate the history of visits, determine the user\'s location, analyze the effectiveness of the site, protect against intruders during DDoS attacks on site pages, protect against Brute-force attacks on the administrative panel of the site, from spam in comments and spam in feedback forms, from unwanted bots, identify and solve technical problems.', 'visitorlog');?>.
<br><br>2.4.&nbsp;
<?php esc_html_e('Any personal information (browsing history, browsers used, operating systems, etc.) is subject to secure storage and non-disclosure in accordance with this Privacy Policy', 'visitorlog');?>.
<br><br>3.&nbsp;
<?php esc_html_e('Personal information', 'visitorlog');?>.
<br><br>3.1.&nbsp;
<?php esc_html_e('The user (the Site Administrator) takes the necessary organizational and technical measures to protect the personal information of visitors from unauthorized or accidental access, destruction, modification, blocking, copying, distribution, as well as from other illegal actions of third parties', 'visitorlog');?>.
<br><br>3.2.&nbsp;
<?php esc_html_e('The user (the site Administrator) takes all necessary measures to prevent losses or other negative consequences caused by the loss or disclosure of personal data of visitors', 'visitorlog');?>.
<br><br>4.&nbsp;
<?php esc_html_e('User Responsibilities', 'visitorlog');?>.
<br><br>4.1.&nbsp;
<?php esc_html_e('To ensure that confidential information is kept confidential, not disclosed without the prior written permission of the site visitor, as well as not to sell, exchange, publish, or otherwise disclose the transmitted personal data of the site visitor', 'visitorlog');?>.
<br><br>4.2.&nbsp;
<?php esc_html_e('Take precautions to protect the confidentiality of the personal data of the site visitor in accordance with the procedure usually used to protect this kind of information in the existing business environment', 'visitorlog');?>.
<br><br>5.&nbsp;
<?php esc_html_e('User\'s Responsibility', 'visitorlog');?>.
<br><br>5.1.&nbsp;
<?php esc_html_e('The user (the Site Administrator) agrees that the information provided to him may be an intellectual property object, the rights to which are protected and belong to other people. The User (Site Administrator) is not entitled to make changes, lease, loan, sell, distribute the Content (in whole or in part), except in cases where such actions have been expressly authorized in writing by the owners of such Content in accordance with the terms of a separate agreement', 'visitorlog');?>.
<br><br><br><br>
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