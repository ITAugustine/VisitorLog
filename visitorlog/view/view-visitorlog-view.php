<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_View class.
 *
 * @method public static function get_class_name() 
 * @method public static function render_preloader() 
 */

class VisitorLog_View
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	/**
	 * Render preloader.
	 */
	public static function render_preloader()
	{
		?>
		<div class="page-loader-wrapper">
		    <div class="loader">
		        <div class="m-t-30">
<?php
$size = array(48, 48);
$attr = array('class' => 'zmdi-hc-spin');
VisitorLog_Utility::render_picture('logoload.png', $size, $attr);
?>
		        </div>
		        <p><?php esc_html_e('loading...', 'visitorlog');?></p>
		    </div>
		</div>
		<?php

	} // END func	


} // END class