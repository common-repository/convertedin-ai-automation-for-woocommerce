
<h1><?php _e("ConvertedIn Plugin Installation.", 'convertedin-ai-automation-for-woocommerce');?></h1>
<div style="margin: auto; text-align: center">
    <img width="400px" src="<?= plugin_dir_url(__FILE__) . '../img/logo.svg' ?>" alt="">
</div>
<?php if (get_option('ConvertedIn_WooCommerce_api_token', 'no token') == 'no token') : ?>
    <p style="text-align: center;font-size: 20px">
        <?php _e("Choose the email you desire to have an account on ConvertedIn platform with, we're using the following email for
        the time being.", 'convertedin-ai-automation-for-woocommerce');?>

    </p>
    <form action="" method="post">

        <p style="text-align: center">
            <input class="form-field" style="min-width: 600px" type="email" name="ConvertedIn_WooCommerce_email"
                   value="<?= wp_get_current_user()->user_email ?>">
            <button type="submit" class="button button-primary"><?php _e("Confirm & Continue", 'convertedin-ai-automation-for-woocommerce');?></button>
        </p>
    </form>

    <br>
    <?php
    if (isset($_POST['ConvertedIn_WooCommerce_email'])) {
        update_option('ConvertedIn_WooCommerce_admin_email', sanitize_email($_POST['ConvertedIn_WooCommerce_email']));
        $http = new HTTP_Client('https://app.converted.in/woocommerce');
        $user = wp_get_current_user();
        $res = $http->post('/createStore', [
            'store_url' => get_site_url(),
            'store_name' => get_bloginfo('name'),
            'admin_email' => get_option('ConvertedIn_WooCommerce_admin_email', wp_get_current_user()->user_email),
            'system_admin_email' => wp_get_current_user()->user_email,
            'admin_first_name' => $user->first_name,
            'admin_last_name' => $user->last_name,
            'currency' => get_woocommerce_currency(),
            'time_zone' => wc_timezone_string(),
            'description' => get_bloginfo('description'),
            'country' => get_option('woocommerce_default_country'),
            'plugin_version' => ConvertedIn_WooCommerce_VERSION ?? null,
        ], true);
        print_r($res);
        if (!$res instanceof WP_Error) {
            $response = $http->getResponseData();
            if ($response['code'] == 200)
                update_option('ConvertedIn_WooCommerce_api_token', $response['data']['token']);
            else
                die($response);
            update_option('ConvertedIn_WooCommerce_initialSyncDone', false);
            update_option('ConvertedIn_WooCommerce_redirect_to_installation', true);
            header("Refresh:0");
        } else {
            /** @var WP_Error $res */
            die($res->get_error_message());
        }

    }
    ?>
<?php else: ?>
    <p style="text-align: center;font-size: 20px">
        <?php _e("Now you have an account on our platform with your email", 'convertedin-ai-automation-for-woocommerce');?>
        <strong><?= get_option('ConvertedIn_WooCommerce_admin_email', wp_get_current_user()->user_email) ?></strong>
        <?php _e("Click", 'convertedin-ai-automation-for-woocommerce');?>
        <a href="<?= get_option('ConvertedIn_WooCommerce_baseUrl') ?>/woo/authenticateUser?token=<?= get_option('ConvertedIn_WooCommerce_api_token') ?>&plugin_version=<?= ConvertedIn_WooCommerce_VERSION ?>"
           target="_blank"><?php _e("here", 'convertedin-ai-automation-for-woocommerce');?></a>
        <?php _e("to authenticate this website.", 'convertedin-ai-automation-for-woocommerce');?>
    </p>
<?php endif; ?>
