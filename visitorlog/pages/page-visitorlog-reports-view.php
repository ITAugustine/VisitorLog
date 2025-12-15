<?php
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Methods of VisitorLog_Reports_View class.
 *
 * @method protected static function render_header_file()
 */
class VisitorLog_Reports_View
{
	public static function render_header_file()
	{ 

        $dir = VISITORLOG_PLUGIN_DIR . 'assets/';
		$HTML_body =		
		'<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
		<title>Log of site visits</title>';

        $HTML_body .= VisitorLog_Utility_File::wp_get_contents( $dir.'css/visitorlog/text1.txt' );
        $HTML_body .= VisitorLog_Utility_File::wp_get_contents( $dir.'css/visitorlog/text3.txt' );
        $HTML_body .= VisitorLog_Utility_File::wp_get_contents( $dir.'css/visitorlog/text2.txt' );
		$HTML_body .= '</head><body>';

		return $HTML_body;

	} // END func	


} // END class