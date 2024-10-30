<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://converted.in
 * @since      1.0.0
 *
 * @package    Convertedin_WooCommerce
 * @subpackage convertedin-ai-automation-for-woocommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Convertedin_WooCommerce
 * @subpackage convertedin-ai-automation-for-woocommerce/includes
 * @author     Your Name <email@example.com>
 */
class ConvertedIn_WooCommerce
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      ConvertedIn_WooCommerce_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    protected $syncListener;
    protected $syncService;
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        require_once plugin_dir_path(__FILE__) . 'class-ConvertedIn_WooCommerce_Store_Listener.php';
        require_once plugin_dir_path(__FILE__) . '../admin/partials/ConvertedIn-WooCommerce-admin-display.php';
        //require_once 'crons/manage-orders.php';
        if (defined('ConvertedIn_WooCommerce_VERSION')) {
            $this->version = ConvertedIn_WooCommerce_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'ConvertedIn-WooCommerce';
        $this->syncListener = new ConvertedIn_WooCommerce_Store_Listener();
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
//        if ( ! class_exists( 'WP_Async_Request' ) ) {
//            require_once 'crons/wp-async-request.php';
//        }
//        if ( ! class_exists( 'WP_Background_Process' ) ) {
//            require_once 'crons/wp-background-process.php';
//        }
//        if ( class_exists( 'CI_Manage_Orders_Crons' ) ) {
//            new CI_Manage_Orders_Crons();
//        }
        $this->wooCommerceListener();

//        if(!get_option('ConvertedIn_Customers_Forced_Sync')) {
//            $response = $this->syncService->syncUsers();
//        }

        //$this->showAlerts();
    }

    /**
     * adds all action hooks for WooCommerce important actions
     */
    public function wooCommerceListener()
    {
        // $this->loader->add_action('woocommerce_after_register_post_type', $this->syncListener, 'syncStore');

        // Product add and update
        $this->loader->add_action('woocommerce_update_product', $this->syncListener, 'added_product', 10, 3);
        // Order Add, Update, Status Change
        //$this->loader->add_action('woocommerce_thankyou', $this->syncListener, 'sendOrderUser', 9, 1);
        $this->loader->add_action('woocommerce_thankyou', $this->syncListener, 'added_order', 10, 1);
        //$this->loader->add_action('woocommerce_order_status_changed', $this->syncListener, 'updated_order', 10, 1);
        //$this->loader->add_action('woocommerce_update_order', $this->syncListener, 'updated_order', 10, 1);

         // Send Customer Data on Register / Login
         //$this->loader->add_action('wp_login', $this->syncListener, 'user_login', 10, 2);
         //$this->loader->add_action('user_register', $this->syncListener, 'user_reg', 10, 1);

        // Syncing data when clicking sync button in settings page.
        $this->loader->add_action('wp_ajax_get_customers', $this->syncListener, 'getCustomers');
        $this->loader->add_action('wp_ajax_get_orders', $this->syncListener, 'getOrders');
        $this->loader->add_action('wp_ajax_sync_products', $this->syncListener, 'syncProducts');
        $this->loader->add_action('wp_ajax_sync_categories', $this->syncListener, 'syncCategories');
        $this->loader->add_action('wp_ajax_sync_store', $this->syncListener, 'syncStore');

        // Entity APIs, these endpoints send entities to ConvertedIn Backend
        $this->loader->add_action('wp_ajax_nopriv_get_customers', $this->syncListener, 'getCustomers');
        $this->loader->add_action('wp_ajax_nopriv_get_orders', $this->syncListener, 'getOrders');
        $this->loader->add_action('wp_ajax_nopriv_get_products', $this->syncListener, 'getProducts');
        $this->loader->add_action('wp_ajax_nopriv_get_categories', $this->syncListener, 'getCategories');
        $this->loader->add_action('wp_ajax_nopriv_convertedin_platform_authenticated', $this, 'platformAuthenticated');
        $this->loader->add_action('activated_plugin', $this, 'redirect_to_settings', '10', '1');

        //$this->loader->add_action('plugins_loaded', $this, 'updatePluginVersion');
        $this->loader->add_action('plugins_loaded', $this, 'getStorePixel');

        $this->loader->add_action('wp_head', $this, 'injectToHead');
    }

    public function injectToHead()
    {
        //check if user is logged in
        $user = wp_get_current_user();
        $output = '';
        if($user->exists()) {
            $user_id = $user->ID;
            $user_email = $user->user_email;
            $user_name = $user->user_login;
            $user_display_name = $user->display_name;
            $user_roles = $user->roles;
            $user_role = $user_roles[0];
            $user_meta = get_user_meta($user_id);
            $user_meta = json_encode($user_meta);
            $user_meta = str_replace('"', "'", $user_meta);
            $store_locale = get_locale();
            //get user orders count
            $user_orders_count = wc_get_customer_order_count($user_id);
            //get user orders total
            $user_orders_total = wc_get_customer_total_spent($user_id);

            //insert hidden input field in head
            $output .= '<input type="hidden" id="convertedin_user_id" value="'.$user_id.'">';
            $output .= '<input type="hidden" id="convertedin_user_email" value="'.$user_email.'">';
            $output .= '<input type="hidden" id="convertedin_user_name" value="'.$user_name.'">';
            $output .= '<input type="hidden" id="convertedin_user_display_name" value="'.$user_display_name.'">';
            $output .= '<input type="hidden" id="convertedin_user_role" value="'.$user_role.'">';
            $output .= '<input type="hidden" id="convertedin_user_meta" value="'.$user_meta.'">';
            $output .= '<input type="hidden" id="convertedin_store_locale" value="'.$store_locale.'">';
            $output .= '<input type="hidden" id="convertedin_user_orders_count" value="'.$user_orders_count.'">';
            $output .= '<input type="hidden" id="convertedin_user_orders_total" value="'.$user_orders_total.'">';

        }

        $output .= '<input type="hidden" id="convertedin_user_logged_in" value="'.$user->exists().'">';

        echo $output;
    }

    public function updatePluginVersion()
    {
        if (get_option('ConvertedIn_WooCommerce_version', null) != ConvertedIn_WooCommerce_VERSION) {
            update_option('ConvertedIn_WooCommerce_version', ConvertedIn_WooCommerce_VERSION);
            $http = new HTTP_Client('https://app.converted.in/woocommerce');
            $http->post('/updatePluginVersion', [
                'plugin_version' => ConvertedIn_WooCommerce_VERSION,
                'token' => get_option('ConvertedIn_WooCommerce_api_token')
            ]);
        }
    }

    public function getStorePixel()
    {
        //trim https or http from url
        $url = str_replace(['https://', 'http://'], '', get_site_url());

        if(get_option('ConvertedIn_WooCommerce_Pixel_Code') == '') {

            $response = wp_remote_request(
                'https://app.converted.in/api/v1/stores/pixel?domain='. $url,
                array(
                    'method'    => 'GET',
                )
            );

            $results = json_decode(wp_remote_retrieve_body($response), true);


            if(isset($results['data'])) {
                update_option('ConvertedIn_WooCommerce_Pixel_Code', $results['data']['pixel_code']);
            }
        }
    }


    public function redirect_to_settings($plugin)
    {

        $should_redirect = get_option('ConvertedIn_WooCommerce_redirect_to_installation');

        if ($should_redirect) {

            if ($should_redirect !== false) {
                update_option('ConvertedIn_WooCommerce_redirect_to_installation', false);
            }

            if (current_user_can('manage_options')) {
                wp_redirect(admin_url('admin.php?page=converted-in'));
                exit;
            }
        }
    }

    public function platformAuthenticated()
    {
        update_option('ConvertedIn_WooCommerce_loggedIn_in_platform', true);
    }

    public function showAlerts()
    {
        $this->loader->add_action('admin_notices', $this, 'alertHTML');
    }

    public function alertHTML()
    {
        ?>
        <div class="success notice">
            <p>
                Your website is running Converted In
            </p>
        </div>
        <?php
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - ConvertedIn_WooCommerce_Loader. Orchestrates the hooks of the plugin.
     * - Plugin_Name_i18n. Defines internationalization functionality.
     * - Plugin_Name_Admin. Defines all hooks for the admin area.
     * - Plugin_Name_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ConvertedIn-WooCommerce-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/ConvertedIn-WooCommerce-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ConvertedIn-WooCommerce-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ConvertedIn-WooCommerce-public.php';

        $this->loader = new ConvertedIn_WooCommerce_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new ConvertedIn_WooCommerce_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new ConvertedIn_WooCommerce_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Plugin_Name_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }


    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    ConvertedIn_WooCommerce_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

}
