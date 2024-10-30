<div style="background: #fff;padding: 15px;border-radius: 5px;">
    <h1>
        <img src="<?= plugin_dir_url(__FILE__) . '../img/logo.svg' ?>" alt="">
    </h1>
    <a href="<?= get_option('ConvertedIn_WooCommerce_baseUrl') ?>/woo/authenticateUser?plugin_version=<?= ConvertedIn_WooCommerce_VERSION ?>&token=<?= get_option('ConvertedIn_WooCommerce_api_token')?>" target="_blank">
        <button type="button" class="button button-primary">
            <?php _e("Login to ConvertedIn", 'convertedin-ai-automation-for-woocommerce');?>
        </button>
    </a>

    <form method="post">
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row"><label for="blogname"> <?php _e("ConvertedIn APi Token", 'convertedin-ai-automation-for-woocommerce');?></label></th>
                <td><input placeholder="Token" name="ConvertedIn_WooCommerce_api_token" type="text" id="blogname"
                           value="<?php echo  get_option('ConvertedIn_WooCommerce_api_token') ?? esc_attr($_POST['ConvertedIn_WooCommerce_api_token']) ?>"
                           class="regular-text"></td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary"
                   value="Save Token and sync store">
            <input type="submit" name="submit" id="submit" class="button button-secondary" value="Save Token">
        </p>
    </form>

    <form method="post">
        <h3> <?php _e("Manual Actions", 'convertedin-ai-automation-for-woocommerce');?></h3>

        <!-- todo disallow sync in false order -->
        <div style="margin-bottom: 20px;">
            <button type="button" class="button button-primary sync-users">
                <?php _e("Sync Customers", 'convertedin-ai-automation-for-woocommerce');?>
            </button>
            last sync:
            <?php if (get_option('ConvertedIn_WooCommerce_users_last_sync', 'not set') != 'not set') : ?>
                <span style="color: #64b450; font-weight: bold"><?= (new \DateTime(get_option('ConvertedIn_WooCommerce_users_last_sync')))->format('h:i A, d M Y') ?></span>
            <?php else: ?>
                <span style="color: #d54e21; font-weight: bold"><?php _e("Never", 'convertedin-ai-automation-for-woocommerce');?></span>
            <?php endif; ?>
            <br>
        </div>
        <div style="margin-bottom: 20px;">
            <button type="button" class="button button-primary sync-orders">
                <?php _e("Sync Orders", 'convertedin-ai-automation-for-woocommerce');?>
            </button>
            <?php _e("Last Sync:", 'convertedin-ai-automation-for-woocommerce');?>

            <?php if (get_option('ConvertedIn_WooCommerce_orders_last_sync', 'not set') != 'not set') : ?>
                <span style="color: #64b450; font-weight: bold"><?= (new \DateTime(get_option('ConvertedIn_WooCommerce_orders_last_sync')))->format('h:i A, d M Y') ?></span>
            <?php else: ?>
                <span style="color: #d54e21; font-weight: bold"><?php _e("Never", 'convertedin-ai-automation-for-woocommerce');?></span>
            <?php endif; ?>
            <br>
        </div>
        <div style="margin-bottom: 20px;">
            <button type="button" class="button button-primary sync-products">
                <?php _e("Sync Products", 'convertedin-ai-automation-for-woocommerce');?>
            </button>
            last sync:
            <?php if (get_option('ConvertedIn_WooCommerce_products_last_sync', 'not set') != 'not set') : ?>
                <span style="color: #64b450; font-weight: bold"><?= (new \DateTime(get_option('ConvertedIn_WooCommerce_products_last_sync')))->format('h:i A, d M Y') ?></span>
            <?php else: ?>
                <span style="color: #d54e21; font-weight: bold"><?php _e("Never", 'convertedin-ai-automation-for-woocommerce');?></span>
            <?php endif; ?>
            <br>
        </div>
        <div style="margin-bottom: 20px;">
            <button type="button" class="button button-primary sync-categories">
                <?php _e("Sync Categories", 'convertedin-ai-automation-for-woocommerce');?>
            </button>
            last sync:
            <?php if (get_option('ConvertedIn_WooCommerce_categories_last_sync', 'not set') != 'not set') : ?>
                <span style="color: #64b450; font-weight: bold"><?= (new \DateTime(get_option('ConvertedIn_WooCommerce_categories_last_sync')))->format('h:i A, d M Y') ?></span>
            <?php else: ?>
                <span style="color: #d54e21; font-weight: bold"><?php _e("Never", 'convertedin-ai-automation-for-woocommerce');?></span>
            <?php endif; ?>
            <br>
        </div>
        <p style="text-align: center">
            <button class="center button button-primary button-large sync-all" style="width: 500px; height: 50px; font-weight: bold"><?php _e("Sync All", 'convertedin-ai-automation-for-woocommerce');?></button>
        </p>

    </form>
</div>

<script>
    jQuery('.sync-users').click(function () {
        var data = {
            'action': 'get_customers',
        };
        jQuery.post(ajaxurl, data, function (response) {
            alert('Sync Request has been sent, we\'re syncing your data in background.');
            location.reload()
        });
    });

    jQuery('.sync-orders').click(function () {
        var data = {
            'action': 'get_orders',
        };
        jQuery.post(ajaxurl, data, function (response) {
            alert('Sync Request has been sent, we\'re syncing your data in background.');
            location.reload()
        });
    });

    jQuery('.sync-products').click(function () {
        var data = {
            'action': 'sync_products',
        };
        jQuery.post(ajaxurl, data, function (response) {
            alert('Sync Request has been sent, we\'re syncing your data in background.');
            location.reload()
        });
    });

    jQuery('.sync-categories').click(function () {
        var data = {
            'action': 'sync_categories',
        };
        jQuery.post(ajaxurl, data, function (response) {
            alert('Sync Request has been sent, we\'re syncing your data in background.');
            location.reload()
        });
    });

    jQuery('.sync-all').click(function () {
        var data = {
            'action': 'sync_store',
        };
        jQuery.post(ajaxurl, data, function (response) {
            alert('Sync Request has been sent, we\'re syncing your data in background.');
            location.reload()
        });
    });


</script>
