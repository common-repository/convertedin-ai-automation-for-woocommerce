<?php
/**
 * Fired during plugin activation
 *
 * @link       https://converted.in
 * @since      1.0.0
 *
 * @package    Convertedin_WooCommerce
 * @subpackage convertedin-ai-automation-for-woocommerce/includes
 */

require_once plugin_dir_path(__FILE__) . 'HTTP_Client.php';

class ConvertedIn_WooCommerce_Activator
{
    // Define the base URL as a constant for easy maintenance
    const BASE_URL = 'https://app.converted.in';

    protected $products;

    /**
     * Store activation
     *
     * The following method is used to create/update the plugin wp options on first and subsequent activations.
     * These options contain store account details that will be used to communicate with the API.
     *
     * @throws Exception
     * @since    1.0.0
     */
    public static function activate()
    {
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
        $woo_not_found = false;

        if (!is_multisite()) {
            if (!in_array('woocommerce/woocommerce.php', $active_plugins)) {
                $woo_not_found = true;
            }
        } else {
            $plugins = get_site_option('active_sitewide_plugins');
            if (!array_key_exists('woocommerce/woocommerce.php', $plugins)) {
                $woo_not_found = true;
            }
        }

        if ($woo_not_found) {
            wp_die(__('ConvertedIn WooCommerce can only work with WooCommerce. Please install WooCommerce first.', 'convertedin'));
        }

        if (!is_multisite()) {
            self::activate_normal();
        } else {
            self::activate_multisite();
        }
    }

    /**
     * Activates plugin for a normal site.
     */
    static function activate_normal()
    {
        update_option('ConvertedIn_WooCommerce_baseUrl', self::BASE_URL);
        require_once plugin_dir_path(__FILE__) . 'class-Synchronization-service.php';

        try {
            $account = self::check_account(get_site_url());
            if ($account && $account['exists']) {
                update_option('ConvertedIn_WooCommerce_redirect_to_installation', true);
                update_option('ConvertedIn_WooCommerce_api_token', $account['access_token']);
                update_option('ConvertedIn_WooCommerce_admin_email', $account['email']);
                self::set_plugin_activated(get_option('ConvertedIn_WooCommerce_api_token' ));
            } else {
                update_option('ConvertedIn_WooCommerce_redirect_to_installation', true);
            }
        } catch (Exception $e) {
            error_log('Error during plugin activation: ' . $e->getMessage());
            wp_die(__('There was an error during the plugin activation. Please try again later.', 'convertedin'));
        }
    }

    /**
     * Activates plugin for a multisite environment.
     */
    static function activate_multisite()
    {
            $blog_id = get_current_blog_id();
            update_blog_option($blog_id,'ConvertedIn_WooCommerce_baseUrl', self::BASE_URL);
            require_once plugin_dir_path(__FILE__) . 'class-Synchronization-service.php';

            try {
                $account = self::check_account(get_site_url($site->blog_id));
                if ($account && $account['exists']) {
                    update_blog_option($blog_id, 'ConvertedIn_WooCommerce_redirect_to_installation', true);
                    update_blog_option($blog_id, 'ConvertedIn_WooCommerce_api_token', $account['access_token']);
                    update_blog_option($blog_id, 'ConvertedIn_WooCommerce_admin_email', $account['email']);
                    self::set_plugin_activated(get_blog_option($blog_id,'ConvertedIn_WooCommerce_api_token' ));
                } else {
                    update_blog_option($blog_id, 'ConvertedIn_WooCommerce_redirect_to_installation', true);
                }
            } catch (Exception $e) {
                error_log('Error during multisite activation for site ID ' . $blog_id . ': ' . $e->getMessage());
            }

    }

    /**
     * Checks if the store account exists on ConvertedIn.
     *
     * @param string $site_url The site URL.
     * @return array|null The account data or null if there is an error.
     */
    static function check_account($site_url)
    {
        $http = new HTTP_Client(self::BASE_URL . '/woocommerce');

        try {
            $res = $http->post('/checkAccount', [
                'store_url' => $site_url
            ], true);

            if (!$res instanceof WP_Error) {
                return $http->getResponseData();
            } else {
                throw new Exception($res->get_error_message());
            }
        } catch (Exception $e) {
            error_log('Error checking account: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sets the plugin as activated in the ConvertedIn platform.
     *
     * @param string $site_url The site URL.
     */
    static function set_plugin_activated($api_token)
    {
        $http = new HTTP_Client(self::BASE_URL . '/woocommerce');

        try {
            $res = $http->post('/plugin-activated', [
                'api_token' => $api_token,
            ], true);

            if ($res instanceof WP_Error) {
                throw new Exception($res->get_error_message());
            }
        } catch (Exception $e) {
            error_log('Error during plugin activation request: ' . $e->getMessage());
        }
    }
}
