<?php

class HTTP_Client
{
    protected $response;
    protected $baseUrl;
    protected $requestUrl;
    protected $errorMessage;
    protected $timeout = 45;

    public function __construct($baseURL)
    {
        $this->baseUrl = $baseURL;
        set_time_limit($this->timeout + 10);
    }

    /**
     * @param $url
     * @param $params
     * @param $blocking
     * @return array|WP_Error
     */
    public function post($url, $params, $blocking = false)
    {
        $this->requestUrl = $url;
        $res = null;
        try {
            $res = wp_remote_post($this->baseUrl . $url, array(
                'method'      => 'POST',
                'blocking'    => $blocking,
                'timeout'     => $this->timeout,
                'headers'     =>  array(
                    'Access-Control-Allow-Origin' => site_url()
                ),
                'body'        =>  $params
            ));
        } catch (Requests_Exception $e) {
            $res = $e->getMessage();
            $this->errorMessage = $e->getMessage();
        }

        $this->response = $res;
        return $this->response;
    }


    /**
     * @param $url
     * @param $params
     * @param $blocking
     * @return array|WP_Error
     */
    public function get($url, $params, $blocking = false)
    {
        $this->requestUrl = $url;
        $res = null;
        try {
            $res = wp_remote_get($this->baseUrl . $url . '?' . http_build_query($params), array(
                'blocking' => $blocking,
                'method' => 'GET',
            ));
        } catch (Requests_Exception $e) {
            $res = $e->getMessage();
            $this->errorMessage = $e->getMessage();
        }

        $this->response = $res;
        return $this->response;
    }


    /**
     * @return mixed|string
     */
    public function getResponseData()
    {
        $response = $this->response;
        if ($response instanceof WP_Error) {
            var_dump($response);
            $this->errorMessage = $response->get_error_message();
            add_action('admin_notices', [$this, 'error']);
            return $response->get_error_message();
        }

        if (!is_string($response) && !is_null($response['http_response'])) {
            $response = json_decode($response['http_response']->get_response_object()->body, true);
            if ($response['code'] != 200) {
                $this->errorMessage = 'authentication error in ' . $this->requestUrl;
                add_action('admin_notices', [$this, 'error']);
            }
            return $response;
        }
        return $response;
    }

    public function error()
    {
        ?>
        <div class="notice error">
            ConvertedIn WooCommerce error: <?= $this->errorMessage ?>
        </div>
        <?php
    }
}
