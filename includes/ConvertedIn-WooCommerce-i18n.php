<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://converted.in
 * @since      1.0.0
 *
 * @package    Convertedin_WooCommerce
 * @subpackage convertedin-ai-automation-for-woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Convertedin_WooCommerce
 * @subpackage convertedin-ai-automation-for-woocommerce/includes
 * @author     Your Name <email@example.com>
 */
class ConvertedIn_WooCommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ConvertedIn-WooCommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
