<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://converted.in
 * @since      1.0.0
 *
 * @package    Convertedin_WooCommerce
 * @subpackage convertedin-ai-automation-for-woocommerce/includes
 */


class ConvertedIn_WooCommerce_Deactivator
{
    const BASE_URL = 'https://app.converted.in';
    /**
     * Handle plugin deactivation webhook
     *
     * @since    1.0.0
     */
    public static function deactivate()
    {

        if (is_multisite()) {
            // Multisite: Uninstall only for the current site
            $blog_id = get_current_blog_id(); // Get current site blog ID

            $api_token = get_blog_option($blog_id, 'ConvertedIn_WooCommerce_api_token');
            if ($api_token) {
                self::set_plugin_deactivated($api_token);
            }


        } else {
            // Single site: Send API token to server
            $api_token = get_option('ConvertedIn_WooCommerce_api_token');
            if ($api_token) {
                self::set_plugin_deactivated($api_token);
            }

        }
    }


    /**
     * Helper method to handle the deactivation request.
     *
     * @param string $api_token The API token to send in the request.
     */
    static function set_plugin_deactivated($api_token)
    {
        $http = new HTTP_Client(self::BASE_URL . '/woocommerce');

        try {
            $res = $http->post('/plugin-deactivated', [
                'api_token' => $api_token,
            ], true);

            if ($res instanceof WP_Error) {
                throw new Exception($res->get_error_message());
            }
        } catch (Exception $e) {
            error_log('Error during plugin deactivate request: ' . $e->getMessage());
        }
    }

}
