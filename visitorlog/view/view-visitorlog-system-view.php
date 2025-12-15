<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_System_View class.
 *
 * @method	public static function get_class_name() 
 * @method	public static function show_message()
 * @method  public static function render_notice_version()
 * @method  public static function render_notice_config_warning()
 * @method  public static function render_notice_multi_sites()
 * @method  public static function admin_body_class()
 * @method  public static function show_select_badge()
 * @method  public static function get_html_text()
 */
class VisitorLog_System_View
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Method show message
	 *
	 * @param string $type    Type of message: green, orange
	 * @param string $message Notice ID.
	 *
	 * @return print screen of message
	 */
	public static function show_message( $type, $message, $message_log = null )
	{
		if ( null != $message_log ) {
			VisitorLog_Logger::instance()->warning( $message_log );
		}

		echo "<div class='ui " . esc_html($type) . " message' style='margin-left:5px;margin-top:0px;margin-bottom:4px;text-align:left;'>";
		echo wp_kses( $message, 'default' );
		echo '</div>';

	} // END func

	/**
	 * VisitorLog Admin body CSS class attributes.
	 *
	 * @param  mixed   $class_string  VisitorLog CSS Class attributes.
	 * @return string  $class_string  The CSS attributes to add to the page.
	 *
	 * @uses  VisitorLog_System::is_current_pages()
	 */
	public static function admin_body_class( $class_string )
	{
		if ( VisitorLog_System::is_current_pages() ) {
			$class_string .= ' vl-ui vl-ui-page ';
			$class_string .= ' vl-ui-leftmenu ';
		}
		return $class_string;

	} // END func

	/**
	 * Show the selected badge.
	 *
	 * @param  string  $BadgeType  Badge Type.
	 * @return string  $class      The CSS attributes to add to the page.
	 */
	public static function show_badge( $BadgeType )
	{
		if ( '' == $BadgeType ) return;

		switch ( $BadgeType ) {
			case 'protected': 
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FFB236;color:#FFB236;"'; // warning
				break;
			case 'system': 
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FFB236;color:#FFB236;"'; // warning			
				break;
			case 'this plugin':  
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#2CA8FF;color:#2CA8FF;"';  // info
				break;
            case 'User':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#2CA8FF;color:#2CA8FF;"';  // info
                break;
            case 'Admin':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FFB236;color:#FFB236;"'; // warning
                break;
            case 'AdminLogin':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FFB236;color:#FFB236;"'; // warning
                break;
            case 'Admin404':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FFB236;color:#FFB236;"'; // warning
                break;
            case 'Bot':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break;                                          
            case 'BotDdos':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break;  
            case 'BotDdosIP':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break; 
            case 'blockddos':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FF3636;color:#FF3636;"';  // danger
                break; 
            case 'BotLogin':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break; 
            case 'blocklogin':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FF3636;color:#FF3636;"';  // danger
                break; 
            case 'Bot404':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break; 
            case 'block404':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FF3636;color:#FF3636;"';  // danger
                break; 
            case 'botspamcf':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break; 
            case 'botspamco':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break; 
            case 'blockspamcf':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FF3636;color:#FF3636;"';  // danger
                break; 
            case 'blockspamco':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FF3636;color:#FF3636;"';  // danger
                break; 
            case 'BotOther':       
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break; 
            case 'WP_CLI':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#18ce0f;color:#18ce0f;"';  // success
                break;
            case 'WP':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#18ce0f;color:#18ce0f;"';  // success
                break;
            case 'CRON':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#f96332;color:#f96332;"';  // primary
                break;
            case 'manual':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#f96332;color:#f96332;"';  // primary
                break;
            case 'auto':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break;                                          
            case 'success':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#18ce0f;color:#18ce0f;"';  // success
                break;
            case 'fail':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#FF3636;color:#FF3636;"';  // danger
                break;
            case 'update':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#2CA8FF;color:#2CA8FF;"';  // info
                break;
            case 'activated':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#f96332;color:#f96332;"';  // primary
                break;
            case 'install':
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
                break;
			default:
				echo 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;';
				echo 'border-color:#888;color:#888;"';  // default
				break;
            }

	} // END func

	/**
	 * Show the selected badge.
	 *
	 * @param  string  $BadgeType  Badge Type.
	 * @return string  $class      The CSS attributes to add to the page.
	 */
	public static function show_select_badge( $BadgeType )
	{
        $badge = 'style="border-radius:8px;padding:4px 8px;text-transform:uppercase;font-size:.7142em; line-height:12px;background-color:rgba(0,0,0,0);border:1px solid;margin-bottom:5px;border-radius:.875rem;'; 

        $badge_icon    = 'padding:0.4em 0.55em;"';
		$badge_default = 'border-color:#888;color:#888;"';
		$badge_primary = 'border-color:#f96332;color:#f96332;"';
        $badge_info    = 'border-color:#2CA8FF;color:#2CA8FF;"';
		$badge_success = 'border-color:#18ce0f;color:#18ce0f;"';
        $badge_warning = 'border-color:#FFB236;color:#FFB236;"';
		$badge_danger  = 'border-color:#FF3636;color:#FF3636;"';
		$badge_neutral = 'border-color:#fff;color:#fff;"';

		$class = '';
		if ( '' == $BadgeType ) return $class;

		switch ( $BadgeType ) {
			case 'protected': 
				$class = $badge . $badge_warning;
				break;
			case 'system': 
				$class = $badge . $badge_warning;
				break;
			case 'this plugin':  
				$class  = $badge . $badge_info;
				break;
            case 'User':       
            	$class  = $badge . $badge_info;
                break;
            case 'Admin':       
                $class = $badge . $badge_warning;
                break;
            case 'AdminLogin':       
                $class = $badge . $badge_warning;
                break;
            case 'Admin404':       
                $class = $badge . $badge_warning;
                break;
            case 'Bot':       
                $class = $badge . $badge_default;
                break;                                          
            case 'BotDdos':       
                $class = $badge . $badge_default;
                break;  
            case 'BotDdosIP':       
                $class = $badge . $badge_default;
                break; 
            case 'blockddos':       
                $class = $badge . $badge_danger;
                break; 
            case 'BotLogin':       
                $class = $badge . $badge_default;
                break; 
            case 'blocklogin':       
                $class = $badge . $badge_danger;
                break; 
            case 'Bot404':       
                $class = $badge . $badge_default;
                break; 
            case 'block404':       
                $class = $badge . $badge_danger;
                break; 
            case 'botspamcf':       
                $class = $badge . $badge_default;
                break; 
            case 'botspamco':       
                $class = $badge . $badge_default;
                break; 
            case 'blockspamcf':       
                $class = $badge . $badge_danger;
                break; 
            case 'blockspamco':       
                $class = $badge . $badge_danger;
                break; 
            case 'BotOther':       
                $class = $badge . $badge_default;
                break; 
            case 'WP_CLI':
                $class = $badge . $badge_success;
                break;
            case 'WP':
                $class = $badge . $badge_success;
                break;
            case 'CRON':
                $class = $badge . $badge_primary;
                break;
            case 'manual':
                $class = $badge . $badge_primary;
                break;
            case 'auto':
               $class = $badge . $badge_default;
                break;                                          
            case 'success':
                $class = $badge . $badge_success;
                break;
            case 'fail':
                $class = $badge . $badge_danger;
                break;
            case 'update':
                $class = $badge . $badge_info;
                break;
            case 'activated':
                $class = $badge . $badge_primary;
                break;
            case 'install':
                $class = $badge . $badge_default;
                break;
			default:
				$class = $badge . $badge_default;
				break;
            }

       	return $class;

	} // END func

	/**
	 *  Renders PHP Text HTML.
	 *
	 *  @param  string    
	 *  @return string PHP Text html.
	 */
	public static function get_html_text( $flag )
	{
		$html = '';
		switch ( $flag ) {

		    case 'on':
		        $html = '<div class="ui green basic label"><i class="fa fa-check-circle" ia-hidden="true"></i> ' . __( 'On', 'visitorlog' ) . '</div>';
		        break;
		    case 'off':
		        $html = '<div class="ui yellow basic label"><i class="fa fa-times-circle" ia-hidden="true"></i> ' . __( 'Off', 'visitorlog' ) . '</div>';
		        break;
		    case 'Pass':
		        $html = '<div class="ui green basic label"><i class="fa fa-check-circle" ia-hidden="true"></i> ' . __( 'Pass', 'visitorlog' ) . '</div>';
		        break;
		    case '0':
		        $html = '<div class="ui green basic label"><i class="fa fa-check-circle" ia-hidden="true"></i> ' . __( 'Pass', 'visitorlog' ) . '</div>';
		        break;
		    case 'Warning':
		        $html = '<div class="ui yellow basic label"><i class="fa fa-exclamation-circle"></i> ' . __( 'Warning', 'visitorlog' ) . '</div>';
		        break;
		    case '1':
		        $html = '<div class="ui yellow basic label"><i class="fa fa-exclamation-circle"></i> ' . __( 'Warning', 'visitorlog' ) . '</div>';
		        break;
		    case 'Fail':
		        $html = '<span class="ui red basic label"><i class="fa fa-times-circle" ia-hidden="true"></i> ' . __( 'Fail', 'visitorlog' ) . '</div>';
		        break;
		    case '2':
		        $html = '<span class="ui red basic label"><i class="fa fa-times-circle" ia-hidden="true"></i> ' . __( 'Fail', 'visitorlog' ) . '</div>';
		        break;
		    case 'Free':
		        $html = '<div class="ui green basic label"><i class="fa fa-check-circle" ia-hidden="true"></i> ' . __( 'Free', 'visitorlog' ) . '</div>';
		        break;
		    case 'Pro':
		        $html = '<span class="ui red basic label"><i class="fa fa-times-circle" ia-hidden="true"></i> ' . __( 'Pro', 'visitorlog' ) . '</div>';
		        break;
            case 'active':
                $html = '<div class="ui green basic label">' . __( 'Active', 'visitorlog' ) . '</div>';
                break;          
            case 'inactive':
                $html = '<div class="ui yellow basic label">' . __( 'Inactive', 'visitorlog' ) . '</div>';
                break;
		}

		return $html;

	} // END func

	/**
	 *  Renders PHP Text HTML.
	 *
	 *  @param  string    
	 *  @return string PHP Text html.
	 */
	public static function get_badge( $flag, $img='' )
	{
		$html = '';
		switch ( $flag ) {

		    case 'on':
		        echo '<div class="ui green basic label">';
		        echo '<i>';
		        VisitorLog_Utility::render_picture('accept.png', array(12,12));
		        echo '</i> ';
		        echo esc_html__( 'On', 'visitorlog' );
		        echo '</div>';
		        return;
		    case 'off':
		        echo '<div class="ui yellow basic label">';
		        echo '<i>';
		        VisitorLog_Utility::render_picture('remove.png', array(12,12));
		        echo '</i> ';
		        echo esc_html__( 'Off', 'visitorlog' );
		        echo '</div>';
		        return;
            case 'active':
                echo '<div class="ui green basic label">';
		        echo '<i>';
		        VisitorLog_Utility::render_picture('accept.png', array(12,12));
		        echo '</i> ';
                echo esc_html__( 'Active', 'visitorlog' );
                echo '</div>';
                return;         
            case 'inactive':
                echo '<div class="ui yellow basic label">';
		        echo '<i>';
		        VisitorLog_Utility::render_picture('remove.png', array(12,12));
		        echo '</i> ';
                echo esc_html__( 'Inactive', 'visitorlog' );
                echo '</div>';
                return;
		    case 'Pass':
		        echo '<div class="ui green basic label"><i class="fa fa-check-circle" ia-hidden="true"></i> ';
		        echo '<i>';
		        if ( '' == $img ) VisitorLog_Utility::render_picture('accept.png', array(12,12));
		        echo '</i> ';
		        echo esc_html__( 'Pass', 'visitorlog' );
		        echo '</div>';
		        return;
		    case '0':
		        echo '<div class="ui green basic label"><i class="fa fa-check-circle" ia-hidden="true"></i> ';
		        echo esc_html__( 'Pass', 'visitorlog' );
		        echo '</div>';
		        return;
		    case 'Warning':
		        echo '<div class="ui yellow basic label"><i class="fa fa-exclamation-circle"></i> ';
		        echo '<i>';
		        if ( '' == $img ) VisitorLog_Utility::render_picture('remove.png', array(12,12));
		        echo '</i> ';
		        echo esc_html__( 'Warning', 'visitorlog' );
		        echo '</div>';
		        return;
		    case '1':
		        echo '<div class="ui yellow basic label"><i class="fa fa-exclamation-circle"></i> ';
		        echo esc_html__( 'Warning', 'visitorlog' );
		        echo '</div>';
		        return;
		    case 'Fail':
		        echo '<span class="ui red basic label"><i class="fa fa-times-circle" ia-hidden="true"></i> ';
		        echo esc_html__( 'Fail', 'visitorlog' );
		        echo '</div>';
		        return;
		    case '2':
		        echo '<span class="ui red basic label"><i class="fa fa-times-circle" ia-hidden="true"></i> ';
		        echo esc_html__( 'Fail', 'visitorlog' );
		        echo '</div>';
		        return;
		    case 'Free':
		        echo '<div class="ui green basic label"><i class="fa fa-check-circle" ia-hidden="true"></i> ';
		        echo esc_html__( 'Free', 'visitorlog' );
		        echo '</div>';
		        return;
		    case 'Pro':
		        echo '<span class="ui red basic label"><i class="fa fa-times-circle" ia-hidden="true"></i> ';
		        echo esc_html__( 'Pro', 'visitorlog' );
		        echo '</div>';
		        return;
		}

	} // END func


} // END class