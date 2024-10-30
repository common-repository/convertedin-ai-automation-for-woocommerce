<?php

class ConvertedIn_WooCommerce_Store_Listener
{
    /**
     * synchronization class
     *
     * @access  protected
     * @var     Synchronization_service
     */
    protected $syncService;

    public function __construct()
    {
        require_once plugin_dir_path(__FILE__) . 'class-Synchronization-service.php';
        $this->syncService = new Synchronization_service();

    }

    /**
     * @throws Requests_Exception
     */
    public function syncStore()
    {
        $this->syncService->syncStore();
    }

    /**
     * @param $order_id
     * @throws Requests_Exception
     */
    public function convertedin_add_orders_cron_function() {

    }

    public function added_order($order_id)
    {
        $order = wc_get_order($order_id);
        $orderArray = $order->get_data();
        $orderArray['line_items'] = array_map(function ($item) {
            /** @var WC_Order_Item_Product $item */
            return $item->get_data();
        }, $orderArray['line_items']);

        $orderArray['billing']['customer_id'] = $orderArray['customer_id'];
        $orderArray['customer'] = $orderArray['billing'];
        $orderArray['phone'] = $orderArray['billing']['phone'] ?? '';
        $orderArray['came_from'] = 'added_order';

        $this->syncService->sendOrder($orderArray);
        //send order user data send customer object with order --- FLAG
        //$this->syncService->sendUser($orderArray);
    }

    /**
     * @param $order_id
     * @throws Requests_Exception
     */
    public function updated_order($order_id)
    {
        $order = wc_get_order($order_id);
        $orderArray = $order->get_data();
        $orderArray['line_items'] = array_map(function ($item) {
            /** @var WC_Order_Item_Product $item */
            return $item->get_data();
        }, $orderArray['line_items']);

        $orderArray['billing']['customer_id'] = $orderArray['customer_id'];

        $orderArray['customer'] = $orderArray['billing'];
        $orderArray['phone'] = $orderArray['billing']['phone'] ?? '';
        $orderArray['came_from'] = 'updated_order';
        $this->syncService->sendOrder($orderArray);
        //send order user data
        //$this->syncService->sendUser($orderArray);
    }

    /**
     * @param $product_id
     * @param WC_Product $product
     * @throws Requests_Exception
     */
    public function added_product($product_id, WC_Product $product)
    {
        if ($product->get_status() == 'publish')
        {
            /** @var WC_Product $product */
            $productArray = $product->get_data();
            $productArray['tags'] = get_the_terms($productArray['id'], 'product_tag');

            $image = wp_get_attachment_image_src($productArray['image_id'], 'full');
            $productArray['image'] = [
                'path' => $image[0] ?? '',
                'width' => $image[1] ?? 0,
                'height' => $image[2] ?? 0,
            ];
            $productArray['gallery_images'] = array_map(function ($imageId) {
                $im = wp_get_attachment_image_src($imageId, 'full');
                return [
                    'path' => $im[0],
                    'width' => $im[1],
                    'height' => $im[2],
                ];
            }, $productArray['gallery_image_ids']);
            if (!$product instanceof WC_Product_Simple)
                $productArray['variations'] = $product->get_available_variations();
            $this->syncService->sendProduct($productArray);
        }
    }

    /**
     * @param $user_login
     * @param WP_User $user
     * @throws Requests_Exception
     */
    public function user_login($user_login, WP_User $user)
    {
        if (in_array('customer', $user->roles)) {
            $this->syncService->sendUser($user->data);
        }
    }

    public function user_reg($user_id)
    {
        $user = get_user_by('id', $user_id);
        if (in_array('customer', $user->roles)) {
            $this->syncService->sendUser($user->data);
        }
    }

    public function sendOrderUser($orderArray)
    {
        $this->syncService->sendUser($orderArray);
    }

    /**
     * @throws Requests_Exception
     */
    public function syncCustomers()
    {
        $response = $this->syncService->syncUsers();
        echo json_encode(['response' => $response]);
        die();
    }

    /**
     * @throws Requests_Exception
     */
    public function syncOrders()
    {
        $response = $this->syncService->syncOrders();
        echo json_encode($response);
        die();
    }

    /**
     * @throws Requests_Exception
     */
    public function syncCategories()
    {
        $response = $this->syncService->syncCategories();
        echo json_encode(['response' => $response]);
        die();
    }

    /**
     * @throws Requests_Exception
     */
    public function syncProducts()
    {
        $response = $this->syncService->syncProducts();
        echo json_encode(['response' => $response]);
        die();
    }

    public function getProducts()
    {
        $page = filter_var($_POST['page'], FILTER_SANITIZE_NUMBER_INT);
        $limit = filter_var($_POST['limit'], FILTER_SANITIZE_NUMBER_INT);
        $products = $this->syncService->getProductsPaginated($page, $limit);
        print_r(json_encode($products));
        die();
    }

    public function getCustomers()
    {
        if ($_POST['token'] !== get_option('ConvertedIn_WooCommerce_api_token')) {
            die();
        }
        $page = filter_var($_POST['page'], FILTER_SANITIZE_NUMBER_INT);
        $limit = filter_var($_POST['limit'], FILTER_SANITIZE_NUMBER_INT);
        $customers = $this->syncService->getCustomersPaginated($page, $limit);
        print_r(json_encode($customers));
        die();
    }

    public function getOrders()
    {
        if ($_POST['token'] !== get_option('ConvertedIn_WooCommerce_api_token')) {
            die();
        }
        $page = filter_var($_POST['page'], FILTER_SANITIZE_NUMBER_INT);
        $limit = filter_var($_POST['limit'], FILTER_SANITIZE_NUMBER_INT);
        $orders = $this->syncService->getOrdersPaginated($page, $limit);
        print_r(json_encode($orders));
        die();
    }

    public function getCategories()
    {
        $categories = $this->syncService->getCategories();
        print_r(json_encode(['categories' => $categories]));
        die();
    }
}
