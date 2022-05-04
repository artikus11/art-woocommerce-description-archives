<?php
/**
 * Plugin Name: Art WooCommerce Description Archives
 * Plugin URI: #
 * Text Domain: art-woocommerce-description-archives
 * Domain Path: /languages
 * Description: Плагин WooCommerce для вывода описания на страницах архивов
 * Version: 1.0.0
 * Author: Artem Abramovich
 * Author URI: https://wpruse.ru/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.5.2
 *
 * Copyright Artem Abramovich
 *
 *     This file is part of Art WooCommerce Order One Click,
 *     a plugin for WordPress.
 *
 *     Art WooCommerce Order One Click is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 3 of the License, or (at your option)
 *     any later version.
 *
 *     Art WooCommerce Order One Click is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'AWOOD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWOOD_PLUGIN_URI', plugin_dir_url( __FILE__ ) );

$awooc_data = get_file_data(
	__FILE__,
	array(
		'ver'         => 'Version',
		'name'        => 'Plugin Name',
		'text_domain' => 'Text Domain',
	)
);

define( 'AWOOD_PLUGIN_VER', $awooc_data['ver'] );
define( 'AWOOD_PLUGIN_NAME', $awooc_data['name'] );
define( 'AWOOD_TEXTDOMAIN', $awooc_data['text_domain'] );

register_uninstall_hook( __FILE__, array( 'Art_Woo_Desc_Loop', 'uninstall' ) );

/**
 * Class Art_Woo_Desc_Loop
 *
 * Main Art_Woo_Desc_Loop class, initialized the plugin
 *
 * @class       Art_Woo_Desc_Loop
 * @version     1.0.0
 * @author      Artem Abramovich
 */
class Art_Woo_Desc_Loop {

	/**
	 * Instance of Art_Woo_Desc_Loop.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var object $instance The instance of AWOOS_Custom_Sale.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->init();

	}


	/**
	 * Init.
	 *
	 * Initialize plugin parts.
	 *
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( version_compare( PHP_VERSION, '5.6', 'lt' ) ) {

			return add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
		}

		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

				return add_action( 'admin_notices', array( $this, 'wc_active_notice' ) );
			}
		}

		/**
		 * Terms fields
		 */
		require_once AWOOD_PLUGIN_DIR . 'includes/admin/class-awood-termdata.php';

		/**
		 * Front end
		 */
		require_once AWOOD_PLUGIN_DIR . 'includes/class-awood-frontend.php';

		// Load hooks
/*		$this->hooks();

		global $pagenow;
		if ( 'plugins.php' === $pagenow ) {
			// Plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ), 10, 2 );
		}*/

	}


	/**
	 * Hooks.
	 *
	 * Initialize all class hooks.
	 *
	 * @since 1.0.0
	 */
	public function hooks() {

		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_admin_settings' ), 15 );

	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


	/**
	 * Settings.
	 *
	 * Include the WooCommerce settings class.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_settings( $settings ) {

		$settings[] = require_once AWOOC_PLUGIN_DIR . 'includes/admin/class-awooc-admin-settings.php';

		return $settings;
	}

	/**
	 * Plugin action links.
	 *
	 * Add links to the plugins.php page below the plugin name
	 * and besides the 'activate', 'edit', 'delete' action links.
	 *
	 * @since 1.8.0
	 *
	 * @param    array  $links List of existing links.
	 * @param    string $file  Name of the current plugin being looped.
	 *
	 * @return    array            List of modified links.
	 */
	public function add_plugin_action_links( $links, $file ) {

		if ( plugin_basename( __FILE__ ) === $file ) {
			$links = array_merge(
				array(
					'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=awooc_settings' ) ) . '">Настройки</a>',
				),
				$links
			);
		}

		return $links;

	}


	/**
	 * Display PHP 5.6 required notice.
	 *
	 * Display a notice when the required PHP version is not met.
	 *
	 * @since 1.8.0
	 */
	public function php_version_notice() {

		?>
		<div class="notice notice-error">
			<p>
				<?php

				printf(
					esc_html(
						'%1$s требует версию PHP 5.6 или выше. Ваша текущая версия PHP %2$s. Пожалуйста, обновите версию PHP.'
					),
					esc_html( AWOOD_PLUGIN_NAME ),
					PHP_VERSION
				);
				?>
			</p>
		</div>
		<?php

	}


	/**
	 * Display WooCommerce required notice.
	 *
	 * Display a notice required plugin is not active.
	 *
	 * @since 1.0.0
	 */
	public function wc_active_notice() {

		?>
		<div class="notice notice-error">
			<p>
				<?php

				printf(
					'Для работы плагина %s требуется плагин <strong><a href="//wordpress.org/plugins/woocommerce/" target="_blank">%s %s</a></strong> или выше.',
					esc_attr( AWOOD_PLUGIN_NAME ),
					'WooCommerce',
					'3.0'
				);
				?>
			</p>
		</div>
		<?php

	}


	/**
	 * Deleting settings when uninstalling the plugin
	 *
	 * @since 1.8.0
	 */
/*	public function uninstall() {

		delete_option( 'woocommerce_awooc_padding' );
		delete_option( 'woocommerce_awooc_margin' );
		delete_option( 'woocommerce_awooc_mode_catalog' );
		delete_option( 'woocommerce_awooc_select_form' );
		delete_option( 'woocommerce_awooc_title_button' );
		delete_option( 'woocommerce_awooc_select_item' );
		delete_option( 'woocommerce_awooc_created_order' );
	}*/
}

/**
 * The main function responsible for returning the Art_Woo_Desc_Loop object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php awood_desc_loop()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object Art_Woo_Desc_Loop class object.
 */
if ( ! function_exists( 'awood_desc_loop' ) ) {

	function awood_desc_loop() {

		return Art_Woo_Desc_Loop::instance();
	}
}

$GLOBALS['awood'] = awood_desc_loop();
