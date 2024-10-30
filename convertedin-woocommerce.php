<?php

/**
 * ConvertedIn AI-Powered E-Commerce Marketing Automation for WooCommerce
 *
 *
 * @wordpress-plugin
 * Plugin Name:       ConvertedIn AI-Powered E-Commerce Marketing Automation for WooCommerce
 * Plugin URI:        http://wordpress.org/ConvertedIn-AI-Automation-for-woocommerce/
 * Description:       Sync your WooCommerce store info with ConvertedIn platform
 * Version:           1.0.26
 * Author:            ConvertedIn
 * Author URI:        https://www.converted.in/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       convertedin-ai-automation-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.1 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ConvertedIn_WooCommerce_VERSION', '1.0.26');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_ConvertedIn_WooCommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ConvertedIn-WooCommerce-activator.php';
	ConvertedIn_WooCommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ConvertedIn-WooCommerce-deactivator.php
 */
function deactivate_ConvertedIn_WooCommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ConvertedIn-WooCommerce-deactivator.php';
	ConvertedIn_WooCommerce_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstallation.
 */
function uninstall_ConvertedIn_WooCommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ConvertedIn-WooCommerce-uninstaller.php';
	ConvertedIn_WooCommerce_Uninstaller::uninstall();
}


register_activation_hook(__FILE__, 'activate_ConvertedIn_WooCommerce');
register_deactivation_hook(__FILE__, 'deactivate_ConvertedIn_WooCommerce');
register_uninstall_hook(__FILE__, 'uninstall_ConvertedIn_WooCommerce');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ConvertedIn-WooCommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ConvertedIn_WooCommerce()
{

    $plugin = new ConvertedIn_WooCommerce();
    $plugin->run();

}

run_ConvertedIn_WooCommerce();
