<?php
/** @noinspection PhpMissingFieldTypeInspection */

require_once plugin_dir_path(__FILE__) . 'HTTP_Client.php';

class Synchronization_service
{
    protected $gotEntities;
    protected $baseUrl;
    protected $http;
    protected $addedProductIds = [];
    protected $initialSyncDone;

    public function __construct()
    {
        $timeout = 45;
        set_time_limit($timeout + 10);

        $this->baseUrl = get_option('ConvertedIn_WooCommerce_baseUrl') . '/woocommerce';
        // todo abandoned checkouts not done

        $this->http = new HTTP_Client($this->baseUrl);
        $this->gotEntities = false;
        $this->initialSyncDone = (bool)get_option('ConvertedIn_WooCommerce_initialSyncDone') ?? false;

    }


    /**
     * @param $url
     * @param $params
     */
    protected function post($url, $params)
    {
        $args = array_merge([
            'api_token' => get_option('ConvertedIn_WooCommerce_api_token'),
        ], $params);
        $this->http->post($url, $args);
    }

    public function checkAccount()
    {
        $this->post('checkAccount', [
            'url' => 'url',
            'email' => 'email'
        ]);
    }

    public function getResponse()
    {
        return $this->http->getResponseData();
    }

    public function getCategories()
    {
        return get_categories(['taxonomy' => 'product_cat', 'hide_empty' => 0]);
    }

    public function getProductsPaginated($page, $limit)
    {
        $p = wc_get_products(array('limit' => $limit, 'page' => $page));
        $totalCount = count(wc_get_products(array('limit' => -1)));
        $products = array_map(function ($product) {
            /** @var WC_Product $product */
            $productArray = $product->get_data();
            $productArray['tags'] = get_the_terms($productArray['id'], 'product_tag');

            $image = wp_get_attachment_image_src($productArray['image_id'], 'full');
            $productArray['image'] = [
                'path' => $image[0],
                'width' => $image[1],
                'height' => $image[2],
            ];
            $productArray['gallery_images'] = array_map(function ($imageId) {
                $im = wp_get_attachment_image_src($imageId, 'full');
                return [
                    'path' => $im[0],
                    'width' => $im[1],
                    'height' => $im[2],
                ];
            }, $productArray['gallery_image_ids']);
            return $productArray;
        }, $p);
        return [
            'products' => $products,
            'products_count' => count($products),
            'total_count' => $totalCount,
        ];
    }

    public function getOrdersPaginated($page, $limit)
    {
        $orders = wc_get_orders([
            'paged' => $page,
            'limit' => $limit
        ]);
        $totalCount = count(wc_get_orders(['limit' => -1]));
        $orders = array_map(function ($order) {
            /** @var Automattic\WooCommerce\Admin\Overrides\Order $order */
            $orderArray = $order->get_data();
            $orderArray['line_items'] = array_map(function ($item) {
                /** @var WC_Order_Item_Product $item */
                return $item->get_data();
            }, $orderArray['line_items']);

            $orderArray['billing']['customer_id'] = $orderArray['customer_id'];

            $orderArray['customer'] = $orderArray['billing'];

            return $orderArray;
        }, $orders);

        return [
            'orders' => $orders,
            'orders_count' => count($orders),
            'total_count' => $totalCount,
        ];
    }

    public function getCustomersPaginated($page, $limit)
    {
        $orderUsers = $this->getOrdersUsersPaginated($page, $limit);
        $totalCount = count($this->getOrdersUsersPaginated(1, -1));
//        $users = get_users([
//            'role' => 'customer',
//            'number' => $limit,
//            'paged' => $page
//        ]);
//        $users = array_map(function ($user) {
//            return $user->data;
//        }, $users);
        //$users = array_merge($orderUsers, $users);
        return [
            'customers' => $orderUsers,
            'customers_count' => count($orderUsers),
            'total_count' => $totalCount,
        ];
    }

    protected function getOrdersUsersPaginated($page, $limit)
    {
        $orders = wc_get_orders([
            'paged' => $page,
            'limit' => $limit
        ]);
        $orders = array_map(function ($order) {
            /** @var WC_Order $order */
            return $order->get_data();
        }, $orders);
        //$emailsArray = [];
        $orderUsers = array_map(function ($order) {
            $order['billing']['customer_id'] = $order['customer_id'];

            $order['customer'] = $order['billing'];

            return $order['customer'];
        }, $orders);
//        // unique users
//        $orderUsers = array_filter($orderUsers, function ($client) use (&$emailsArray) {
//            if ($client['email'] != null && array_search($client['email'], $emailsArray) === false) {
//                $emailsArray[] = $client['email'];
//                return true;
//            }
//            return false;
//        });
        return array_values(array_filter($orderUsers));
    }

//    protected function getOrders()
//    {
//        $orders = wc_get_orders([]);
//        $orders = array_map(function ($order) {
//            /** @var Automattic\WooCommerce\Admin\Overrides\Order $order */
//            $orderArray = $order->get_data();
//            $orderArray['line_items'] = array_map(function ($item) {
//                /** @var WC_Order_Item_Product $item */
//                return $item->get_data();
//            }, $orderArray['line_items']);
//            return $orderArray;
//        }, $orders);
//
//        $this->orders = $orders;
//    }

//    protected function getAbandonedCheckouts()
//    {
//        $abandonedCheckouts = [];
//        // todo get abandoned checkouts
//        $this->abandonedCheckouts = $abandonedCheckouts;
//    }

    /**
     * @throws Requests_Exception
     */
    public function syncStore()
    {
        if (!$this->initialSyncDone) {
            $this->syncCategories();
            $this->syncProducts();
            $this->syncUsers();
            $this->syncOrders();
        }
    }

    /**
     * @return mixed|string
     * @throws Requests_Exception
     */
    public function syncCategories()
    {
        $this->post('/getCategories', []);
        update_option('ConvertedIn_WooCommerce_categories_last_sync', date('Y-m-d H:i:s'));
        return $this->getResponse();
    }

    /**
     * @return array
     * @throws Requests_Exception
     */
    public function syncProducts()
    {
        $this->post('/getProducts', []);
        update_option('ConvertedIn_WooCommerce_products_last_sync', date('Y-m-d H:i:s'));
        return $this->getResponse();
    }

    /**
     * @return mixed|string
     * @throws Requests_Exception
     */
    public function syncUsers()
    {
        $this->post('/getCustomers', []);
        update_option('ConvertedIn_WooCommerce_users_last_sync', date('Y-m-d H:i:s'));
        return $this->getResponse();
    }

    /**
     * @return mixed|string
     * @throws Requests_Exception
     */
    public function syncOrders()
    {
        $this->post('/getOrders', []);
        update_option('ConvertedIn_WooCommerce_orders_last_sync', date('Y-m-d H:i:s'));
        return $this->getResponse();
    }

    /**
     * @param $product
     * @return mixed|string
     * @throws Requests_Exception
     */
    public function sendProduct($product)
    {
        if (!in_array($product['id'], $this->addedProductIds)) {
            $this->post('/getSingleProduct', [
                'product' => $product
            ]);
            $this->addedProductIds[] = $product['id'];
        }
        update_option('ConvertedIn_WooCommerce_products_last_sync', date('Y-m-d H:i:s'));
        return $this->getResponse();
    }

    /**
     * @param $customer
     * @return mixed|string
     * @throws Requests_Exception
     */
    public function sendUser($customer)
    {
        $this->post('/getSingleCustomer', [
            'customer' => $customer
        ]);
        update_option('ConvertedIn_WooCommerce_users_last_sync', date('Y-m-d H:i:s'));

        return $this->getResponse();
    }

    /**
     * @param $category
     * @return mixed|string
     * @throws Requests_Exception
     */
    public function sendCategory($category)
    {
        $this->post('/getSingleCategory', [
            'category' => $category
        ]);
        update_option('ConvertedIn_WooCommerce_categories_last_sync', date('Y-m-d H:i:s'));

        return $this->getResponse();
    }

    /**
     * @param array $order
     * @throws Requests_Exception
     */
    public function sendOrder(array $order)
    {
        $this->post('/getSingleOrder', [
            'order' => $order
        ]);
        update_option('ConvertedIn_WooCommerce_orders_last_sync', date('Y-m-d H:i:s'));

        return $this->getResponse();
    }

}
