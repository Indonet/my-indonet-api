<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'midtrans_snap_sanitizer.php';

/**
 * MidtransSnap API
 *
 * @link https://indonet.co.id Jason Wanardi
 */
class MidtransSnapApi {
    ##
    # EDIT REQUIRED Update the below API url or replace it with an appropriate gateway field
    ##

    /**
     * @var string Placeholder description
     */
    private $mid;

    /**
     * @var string Placeholder description
     */
    private $skey;

    /**
     * @var string Placeholder description
     */
    private $ckey;

    /**
     * @var bool Placeholder description
     */
    private $devmode;

    /**
     * Set it true to enable 3D Secure by default
     * 
     * @static
     */
    public $is3ds = true;

    /**
     * Enable request params sanitizer (validate and modify charge request params).
     * See Midtrans_Sanitizer for more details
     * 
     * @static
     */
    public $isSanitized = false;

    /**
     *  Set Append URL notification
     * 
     * @static
     */
    public $appendNotifUrl = null;

    /**
     *  Set Override URL notification
     * 
     * @static
     */
    public $overrideNotifUrl = null;

    const SANDBOX_BASE_URL = 'https://api.sandbox.midtrans.com/v2';
    const PRODUCTION_BASE_URL = 'https://api.midtrans.com/v2';
    const SNAP_SANDBOX_BASE_URL = 'https://app.sandbox.midtrans.com/snap/v1';
    const SNAP_PRODUCTION_BASE_URL = 'https://app.midtrans.com/snap/v1';

    ##
    # EDIT REQUIRED Update the above variable descriptions
    ##

    // The data sent with the last request served by this API
    private $lastRequest = [];

    /**
     * Initializes the request parameter
     *
     * @param string $mid Placeholder description
     * @param string $skey Placeholder description
     * @param string $ckey Placeholder description
     * @param string $devmode Placeholder description
     * @param string $snapmode Placeholder description
     * @param string $paymode Placeholder description
     * @param string $notification_mode Placeholder description
     */
    ##
    # EDIT REQUIRED Update the above variable descriptions and parameter list below
    ##
    public function __construct($mid, $skey, $ckey, bool $devmode = true) {
        $this->mid = $mid;
        $this->skey = $skey;
        $this->ckey = $ckey;
        $this->devmode = $devmode;
    }

    /**
     * Get baseUrl
     * 
     * @return string Midtrans API URL, depends on $isProduction
     */
    public function getSnapBaseUrl() {
        return $this->devmode ? self::SNAP_SANDBOX_BASE_URL : self::SNAP_PRODUCTION_BASE_URL;
    }

    public function getBaseUrl() {
        return $this->devmode ? self::SANDBOX_BASE_URL : self::PRODUCTION_BASE_URL;
    }

    /**
     * Send an API request to MidtransSnap
     *
     * @param string $route The path to the API method
     * @param array $body The data to be sent
     * @param string $method Data transfer method (POST, GET, PUT, DELETE)
     * @return MidtransSnapResponse
     */
    public function apiRequest($route, array $body, $method, $snap = true) {
        $base = $snap ? $this->getSnapBaseUrl() : $this->getBaseUrl();
        $url = $base . '/' . $route;
        $curl = curl_init();

        switch (strtoupper($method)) {
            case 'DELETE':
            // Set data using get parameters
            case 'GET':
                $url .= empty($body) ? '' : '?' . http_build_query($body);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                $body = empty($body) ? '' : json_encode($body);
            // Use the default behavior to set data fields
            default:
                curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                break;
        }

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
//        curl_setopt($curl, CURLOPT_SSLVERSION, 1);

        $headers = [];
        ##
        #  Set any neccessary headers here
        ##
        if ($this->appendNotifUrl)
            $headers[] = 'X-Append-Notification: ' . $this->appendNotifUrl;
        if ($this->overrideNotifUrl)
            $headers[] = 'X-Override-Notification: ' . $this->overrideNotifUrl;
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Basic ' . base64_encode($this->skey . ':');

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $this->lastRequest = ['content' => $body, 'headers' => $headers];

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        if ($result === false) {
            throw new \Exception('CURL Error: ' . curl_error($curl), curl_errno($curl));
        } else {
            try {
                $result_array = json_decode($result);
            } catch (\Exception $e) {
                $message = "API Request Error unable to json_decode API response: " . $result . ' | Request url: ' . $url;
                throw new \Exception($message);
            }
            if ($snap) {
                if ($info['http_code'] != 201) {
                    $message = 'Midtrans Error (' . $info['http_code'] . '): '
                            . $result . ' | Request url: ' . $url;
                    throw new \Exception($message, $info['http_code']);
                } else {
                    return $result_array;
                }
            } else {
                if (!in_array($result_array->status_code, array(200, 201, 202, 407))) {
                    $message = 'Midtrans Error (' . $result_array->status_code . '): '
                            . $result_array->status_message;
                    if (isset($result_array->validation_messages)) {
                        $message .= '. Validation Messages (' . implode(", ", $result_array->validation_messages) . ')';
                    }
                    if (isset($result_array->error_messages)) {
                        $message .= '. Error Messages (' . implode(", ", $result_array->error_messages) . ')';
                    }
                    throw new \Exception($message, $result_array->status_code);
                } else {
                    return $result_array;
                }
            }
        }
    }

    /**
     * Create Snap payment page, with this version returning full API response
     *
     * @param  array $params Payment options
     * @return object Snap response (token and redirect_url).
     * @throws Exception curl error or midtrans error
     */
    public function createTransaction($params) {
        $payloads = array(
            'credit_card' => array(
                'secure' => $this->is3ds
            )
        );

        if (isset($params['item_details'])) {
            $gross_amount = 0;
            foreach ($params['item_details'] as $item) {
                $gross_amount += $item['quantity'] * $item['price'];
            }
            $params['transaction_details']['gross_amount'] = $gross_amount;
        }

        if ($this->isSanitized) {
            MidtransSnapSanitizer::jsonRequest($params);
        }

        $params = array_replace_recursive($payloads, $params);
        $result = $this->apiRequest('/transactions', $params, 'POST');

        return $result;
    }

    public function getSnapToken($params) {
        return ($this->createTransaction($params)->token);
    }

    /**
     * Retrieve transaction status
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public function getTransactionStatus($id) {
        $uri = "{$id}/status";
        return $this->apiRequest($uri, [], 'GET', false);
    }

    /**
     * Approve challenge transaction
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return string
     */
    public static function approveTransaction($id) {
        $uri = "{$id}/approve";
        return $this->apiRequest($uri, [], 'POST', false)->status_code;
    }

    /**
     * Cancel transaction before it's settled
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return string
     */
    public function cancelTransaction($id) {
        $uri = "{$id}/cancel";
        return $this->apiRequest($uri, [], 'POST', false)->status_code;
    }

    /**
     * Expire transaction before it's settled
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public function expireTransaction($id) {
        $uri = "{$id}/expire";
        return $this->apiRequest($uri, [], 'POST', false);
    }

    /**
     * Transaction status can be updated into refund
     * if the customer decides to cancel completed/settlement payment.
     * The same refund id cannot be reused again.
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public function refundTransaction($id, $params) {
        $uri = "{$id}/refund";
        return $this->apiRequest($uri, $params, 'POST', false);
    }

    /**
     * Transaction status can be updated into refund
     * if the customer decides to cancel completed/settlement payment.
     * The same refund id cannot be reused again.
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public function refundDirectTransaction($id, $params) {
        $uri = "{$id}/refund/online/direct";
        return $this->apiRequest($uri, $params, 'POST', false);
    }

    /**
     * Deny method can be triggered to immediately deny card payment transaction
     * in which fraud_status is challenge.
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public function denyTransaction($id) {
        $uri = "{$id}/deny";
        return $this->apiRequest($uri, [], 'POST', false);
    }

    /**
     * Transaction Status from notification
     * 
     * @return mixed[]
     */
    public function getTransactionFromNotification($input_source = "php://input") {
        $raw_notification = json_decode(file_get_contents($input_source), true);
        $status_response = $this->getTransactionStatus($raw_notification['transaction_id']);
        return $status_response;
    }

}
