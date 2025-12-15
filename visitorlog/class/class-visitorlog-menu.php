<?php
/**
 * Build & Render Main Menu.
 */
namespace SecurityLine\VisitorLog;

if( !defined( 'ABSPATH' ) ) exit; 

/**
 * Methods of VisitorLog_Menu class
 * 
 * @method public static function get_class_name()
 * @method public        function __construct() 
 * @method public static function init_menus() 
 * @method public static function is_disable_menu_item() 
 * @method public static function add_left_menu()
 * @method public static function render_left_menu()
 * @method public static function render_sub_item()
 */
class VisitorLog_Menu
{
	public static function get_class_name()
	{
		return __CLASS__;
	}

	public function __construct()
	{
		global $visitorlog_global_parameters;

		// Init disable menu items, default is false.
		if ( !isset( $visitorlog_global_parameters['disable_menus_items'] ) ) {

			$visitorlog_global_parameters['disable_menus_items'] = array(
				'level_1' => array(
					'not_set_this_level' => true,
				),
				'level_2' => array(
					// 'visitorlog_tab' - Do not hide this menu.
					'Setup'         => false,
					'Update'        => false,
					'Overview'      => false,
					'Security'      => false,
					'LogsReports'   => false,
					'Logs'          => false,
					'Statistics'    => false,
					'SystemFiles'   => false,
					'Settings'      => false,
					'Cron'          => false,
					'OtherFeatures' => false,
					'Privacypolicy' => false,
					'Guide'         => false,
				),
				'level_3' => array(
				),
			);
			add_action( 'admin_menu', array( $this, 'init_menus' ) );
		}

	} // END __construct()

	/**
	 * Init menus.
	 *
	 * @uses VisitorLog_System_Utility::is_admin()
	 * @uses VisitorLog_Install_Wizard::is_admin()
	 * @uses VisitorLog_Overview::init_menu()
	 * @uses VisitorLog_Security::init_menu()
	 * @uses VisitorLog_Reports::init_menu()
	 * @uses VisitorLog_Tables::init_menu()
	 * @uses VisitorLog_Logs::init_menu()
	 * @uses VisitorLog_Statistics::init_menu()
	 * @uses VisitorLog_Settings::init_menu()
	 * @uses VisitorLog_Cron::init_menu()
	 * @uses VisitorLog_Other_Features::init_menu()
	 * @uses VisitorLog_Privacy_Policy::init_menu()
	 * @uses VisitorLog_Guide::init_menu()
	 */
	public function init_menus()
	{ 
		global $visitorlog_users_resident_parameters;

		if ( !VisitorLog_System_Utility::is_admin() ) return;

		if ( false !== get_option('visitorlog_run_quick_setup', false) ) {
			if ( !self::is_disable_menu_item( 2, 'Setup' ) ) {
				VisitorLog_Install_Wizard::init_menu();
			}
			return;
		}

		if ( !self::is_disable_menu_item( 2, 'Overview' ) ) {
			VisitorLog_Overview::init_menu();
		}			

		$url = $visitorlog_users_resident_parameters['url'];
		if ( false !== strpos( $url, '/index.php') ) return;

		if ( !self::is_disable_menu_item( 2, 'Security' ) ) {
			VisitorLog_Security::init_menu();
		}	

		if ( !self::is_disable_menu_item( 2, 'Reports' ) ) {
			VisitorLog_Reports::init_menu();
		}

		if ( !self::is_disable_menu_item( 2, 'Tables' ) ) {
			VisitorLog_Tables::init_menu();
		}

		if ( !self::is_disable_menu_item( 2, 'Logs' ) ) {
			VisitorLog_Logs::init_menu();
		}

		if ( !self::is_disable_menu_item( 2, 'Statistics' ) ) {
			VisitorLog_Statistics::init_menu();
		}

		if ( !self::is_disable_menu_item( 2, 'SystemFiles' ) ) {
			VisitorLog_System_Files::init_menu();
		}

 		if ( !self::is_disable_menu_item( 2, 'Cron' ) ) {
			VisitorLog_Cron::init_menu();
		}

		if ( !self::is_disable_menu_item( 2, 'OtherFeatures' ) ) {
			VisitorLog_Other_Features::init_menu();
		}

		if ( !self::is_disable_menu_item( 2, 'Settings' ) ) {
			VisitorLog_Settings::init_menu();
		}

		if ( !self::is_disable_menu_item( 2, 'Privacypolicy' ) ) {
			VisitorLog_Privacy_Policy::init_menu();
		}

		if ( !self::is_disable_menu_item( 2, 'Guide' ) ) {
			VisitorLog_Guide::init_menu();
		}

	} // END func

	/**
	 * Check if disable menus items contains any menu items to hide.
	 *
	 * @param string $level  The level the menu item is on.
	 * @param array  $item   The menu items meta data.
	 *
	 * @return bool True|False, default is False.
	 */
	public static function is_disable_menu_item( $level, $item )
	{
		global $visitorlog_global_parameters;

		$disable_menus_items = $visitorlog_global_parameters['disable_menus_items'];		

		$_level = 'level_' . $level;
		if ( is_array( $disable_menus_items ) && 
			 isset( $disable_menus_items[ $_level ] ) && 
			 isset( $disable_menus_items[ $_level ][ $item ] ) ) {

			if ( $disable_menus_items[ $_level ][ $item ] ) {
				return true;
			} else {
				return false;
			}
		}

		$disable_menus_items[ $_level ][ $item ] = false;
		$visitorlog_global_parameters['disable_menus_items'] = $disable_menus_items;

		return false;

	} // END func

	/**
	 * Build Top Level Menu
	 *
	 * @param  array   $params Menu Item parameters.
	 * @param  integer $level Menu Item Level 1 or 2.
	 * @return array   leftmenu[] | sub_leftmenu[].
	 */
	public static function add_left_menu( $params = array(), $level = 1 )
	{
		if ( empty( $params ) ) return;

		if ( 1 != $level && 2 != $level ) $level = 1;

		$title = $params['title'];

		if ( 1 === $level ) {
			$parent_key = 'visitorlog_tab'; // forced value.
		} else {
			if ( isset( $params['parent_key'] ) ) {
				$parent_key = $params['parent_key'];
			} else {
				$parent_key = 'visitorlog_tab'; // forced value.
			}
		}

		$slug  = $params['slug'];
		$href  = $params['href'];
		$right = isset( $params['right'] ) ? $params['right'] : '';
		$id    = isset( $params['id'] ) ? $params['id'] : '';

		/**
		 * Left Menu, Sub Menu & Active menu slugs.
		 */
		global $visitorlog_global_parameters;

		$menu_active_slugs = array();
		$leftmenu          = array();
		$sub_leftmenu      = array();

		if ( isset($visitorlog_global_parameters['leftmenu']) ) {
			$leftmenu = $visitorlog_global_parameters['leftmenu'];
		}
		if ( isset($visitorlog_global_parameters['sub_leftmenu']) ) {
			$sub_leftmenu = $visitorlog_global_parameters['sub_leftmenu'];
		}
		if ( isset($visitorlog_global_parameters['menu_active_slugs']) ) {
			$menu_active_slugs = $visitorlog_global_parameters['menu_active_slugs'];
		}

		if ( 1 == $level ) {
			$leftmenu[ $parent_key ][] = array( $title, $slug, $href, $id );
			$visitorlog_global_parameters['leftmenu'] = $leftmenu;
			if ( ! empty( $slug ) ) {
				// to get active menu.
				$menu_active_slugs[ $slug ] = $slug;
				$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;
			}
		} elseif ( 2 == $level ) {
			$sub_leftmenu[ $parent_key ][] = array( $title, $href, $right, $id );
			$visitorlog_global_parameters['sub_leftmenu'] = $sub_leftmenu;
			if ( ! empty( $slug ) ) {
				// to get active menu.
				$menu_active_slugs[ $slug ] = $parent_key;
				$visitorlog_global_parameters['menu_active_slugs'] = $menu_active_slugs;
			}
		}

	} // END func

	/**
	 * Build Top Level Main Menu HTML & Render.
	 */
	public static function render_left_menu()
	{
		global $plugin_page;
		global $visitorlog_global_parameters;

		$leftmenu = $visitorlog_global_parameters['leftmenu'];
		$leftmenu = apply_filters( 'visitorlog_main_menu', $leftmenu );
		$leftmenu = isset( $leftmenu['visitorlog_tab'] ) ? $leftmenu['visitorlog_tab'] : array();
		$visitorlog_global_parameters['leftmenu'] = $leftmenu;

		$sub_leftmenu = $visitorlog_global_parameters['sub_leftmenu'];
		$sub_leftmenu = apply_filters( 'visitorlog_main_menu_submenu', $sub_leftmenu );

		$menu_active_slugs = $visitorlog_global_parameters['menu_active_slugs'];

		$go_back_wpadmin_url = admin_url( 'index.php' );
		$link = array(
			'url'  => $go_back_wpadmin_url,
			'text' => __( 'Exit', 'visitorlog' ),
			'tip'  => '',
		);
		$go_back_link = apply_filters( 'visitorlog_go_back_wpadmin_link', $link );

		if ( false !== $go_back_link ) {
			if ( is_array( $go_back_link ) ) {
				if ( isset( $go_back_link['url'] ) ) {
					$link['url'] = $go_back_link['url'];
				}
				if ( isset( $go_back_link['text'] ) ) {
					$link['text'] = $go_back_link['text'];
				}
				if ( isset( $go_back_link['tip'] ) ) {
					$link['tip'] = $go_back_link['tip'];
				}
			}
		}
		?>
		<div class="vl-nav-wrap">
			<div id="vl-logo">
<a href="<?php echo esc_url(admin_url('admin.php?page=visitorlog_tab')) . '&_wpnonce=' . esc_html(wp_create_nonce('visitorlog_nonce'));?>">
	<?php
	VisitorLog_Utility::render_picture('logo.png', 'medium');
	?>
</a>
			</div>
			<div class="ui hidden divider"></div>
			<div class="vl-nav-menu">
				<div id="vl-main-menu" class="ui inverted vertical accordion menu stackable">
					<?php
					if ( is_array( $leftmenu ) && !empty( $leftmenu ) ) {
						foreach ( $leftmenu as $item ) {
							$title    = wptexturize( $item[0] );
							$item_key = $item[1];
							$href     = $item[2];
							$item_id  = isset( $item[3] ) ? $item[3] : '';

							$has_sub = true;
							if ( !isset($sub_leftmenu[$item_key]) || empty($sub_leftmenu[$item_key]) ) {
								$has_sub = false;
							}
							$active_item = '';
							// to fix active menu.
							if ( isset( $menu_active_slugs[$plugin_page] ) ) {
								if ( $item_key == $menu_active_slugs[$plugin_page] ) {
									$active_item = 'active';
								}
							}
							$id_attr = !empty($item_id) ? 'id="' . esc_html($item_id) . '"' : '';

							if ( $has_sub ) {
								echo '<div ' . esc_html($id_attr) . ' class="item ' . esc_html($active_item);
								echo '"><a class="title with-sub ' . esc_html($active_item) . '" href="';
								echo esc_html($href) . '" style="font-weight:400;font-size:14px;">';
								echo esc_html($title) . ' <i class="dropdown icon"></i></a>';
								echo '<div class="content menu ' . esc_html($active_item) . '">';
								self::render_sub_item($item_key);
								echo '</div></div>';
							} else {
								echo '<div ' . esc_html($id_attr) . ' class="item">';
								echo '<a class="title '.esc_html($active_item).'" href="'.esc_html($href).'" ';
								echo 'style="font-weight:400;font-size:14px;">'.esc_html($title).'</a></div>';
							}
						}
					} else {
						echo "\n\t<div class='item'>";
						echo '</div>';
					}
					?>
				</div>
				<div class="vl-footer-menu">
					<div>
<a href="<?php echo esc_html($link['url']);?>">
	<?php
	VisitorLog_Utility::render_picture('right-from-bracket-inv.png', array(16,16));
	?>
	&nbsp;<?php echo esc_html($link['text']);?>
</a>
					</div> 
				</div>
			</div>
		</div>
		<?php

	} // END func

	/**
	 * Grab all submenu items and attatch to Main Menu.
	 *
	 * @param mixed $parent_key   The parent key.
	 */
	public static function render_sub_item( $parent_key )
	{
		if ( empty( $parent_key ) ) return;

		global $visitorlog_global_parameters;

		$sub_leftmenu  = $visitorlog_global_parameters['sub_leftmenu'];
		$submenu_items = $sub_leftmenu[ $parent_key ];

		if ( ! is_array( $submenu_items ) || count( $submenu_items ) == 0 ) {
			return;
		}
		foreach ( $submenu_items as $sub_key => $sub_item ) {
			$title  = $sub_item[0];
			$href   = $sub_item[1];
			$right  = $sub_item[2];
			$_blank = isset( $sub_item[3] ) ? $sub_item[3] : '';

			$right_group = 'dashboard';
			if ( ! empty( $right ) ) {
				if ( strpos( $right, 'extension_' ) === 0 ) {
					$right_group = 'extension';
					$right       = str_replace( 'extension_', '', $right );
				}
			}
			?>
			<a class="item" href="<?php echo esc_url($href);?>" <?php echo '_blank' == $_blank ? 'target="_blank"' : '';?>><?php echo esc_html( $title );?></a>
			<?php
		}

	} // END func


} // END class