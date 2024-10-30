<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://converted.in
 * @since      1.0.0
 *
 * @package    Convertedin_WooCommerce
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Convertedin_WooCommerce
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        //inject convertedin sdk for frontend integration
        wp_enqueue_script( 'convertedin-pixel-sdk', 'https://convertedin-pixel-sdk-v1.s3.amazonaws.com/sdk.js', array( 'jquery' ), $this->version, true );

        $script = '';

        if(get_option('ConvertedIn_WooCommerce_Pixel_Code') != '') {

            $user = wp_get_current_user();
            if($user->exists())
                $script .= 'localStorage.setItem("ci_cid", "' . $user->ID . '");';

            $script .= 'ciq("init", "'. get_option('ConvertedIn_WooCommerce_Pixel_Code') .'");';
            $script .= 'ciq("track", "PageView");';
            //check if user in product page and track view content
            if (is_product()) {
                $product = wc_get_product();
                $categories = wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ', ', '', '' ) );
                $script .= 'ciq("track", "ViewContent", {
                    content: [
                        {
                            id: ' . $product->get_id() . ', // required
                            quantity: 1, // required
                            name: "' . $product->get_name() . '", // optional
                            category: "'.$categories.'", // optional
                        }
                    ],
                    currency: "' . get_woocommerce_currency() . '", // required
                    value: ' . $product->get_price() . ' // required, sum of products price
                });';
            }

            //check if user clicked add to cart button and track add to cart
            if( is_product() && isset($_POST['add-to-cart']) ) {
                $product = wc_get_product();
                $categories = wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ', ', '', '' ) );

                // variable product
                if( $product->is_type('variable') && isset($_POST['variation_id']) ) {
                    $product_id = $_POST['variation_id'];
                    $price = number_format( wc_get_price_including_tax( wc_get_product($_POST['variation_id']) ), 2 );
                }
                // Simple product
                elseif ( $product->is_type('simple') ) {
                    $product_id = $product->get_id();
                    $price = number_format( wc_get_price_including_tax( $product ), 2 );
                }
                $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

                $script .= 'ciq("track", "AddToCart", {
                    content: [
                        {
                            id: ' . $product_id . ', // required
                            quantity: '.$quantity.', // required
                            name: "' . $product->get_name() . '", // optional
                            category: "'.$categories.'", // optional
                        }
                    ],
                    currency: "' . get_woocommerce_currency() . '", // required
                    value: ' . $price . ' // required
                });';

            }

            //check if user in checkout page and track initiate checkout
            if (is_checkout() && !is_order_received_page()) {
                global $woocommerce;
                $cart = $woocommerce->cart->get_cart();
                $script .= 'ciq("track", "InitiateCheckout", {
                    content: [';
                foreach ($cart as $item) {
                    $product = wc_get_product($item['product_id']);
                    $categories = wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ', ', '', '' ) );

                    $script .= '{
                        id: ' . $product->get_id() . ', // required
                        quantity: ' . $item['quantity'] . ', // required
                        name: "' . $product->get_name() . '", // optional
                        category: "'.$categories.'", // optional
                    },';
                }
                $script .= '],
                    currency: "' . get_woocommerce_currency() . '", // required
                    value: ' . $woocommerce->cart->total . ' // required, sum of products price
                });';
            }
            //check if user in thank you page and track purchase
            if (is_order_received_page()) {
                global $wp;
                // Get the order ID
                $order_id  = absint( $wp->query_vars['order-received'] );
                if ( empty($order_id) || $order_id == 0 )
                    return; // Exit;
                $order = wc_get_order($order_id);
                $script .= 'ciq("track", "Purchase", {
                    content: [';
                foreach ($order->get_items() as $item) {
                    $product = wc_get_product($item->get_product_id());
                   $categories = wp_strip_all_tags( wc_get_product_category_list( $product->get_id(), ', ', '', '' ) );

                    $script .= '{
                        id: ' . $product->get_id() . ', // required
                        quantity: ' . $item->get_quantity() . ', // required
                        name: "' . $product->get_name() . '", // optional
                        category: "'.$categories.'", // optional
                    },';
                }
                $script .= '],
                    order_id: "'.$order_id.'", // required
                    currency: "' . get_woocommerce_currency() . '", // required
                    value: ' . $order->get_total() . ' // required, sum of products price
                });';
            }
        }
        
        wp_add_inline_script('convertedin-pixel-sdk', $script, 'after');

	}
}
