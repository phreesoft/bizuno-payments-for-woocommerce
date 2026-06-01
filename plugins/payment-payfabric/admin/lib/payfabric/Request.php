<?php

class payFabric_Request extends payFabric_Builder
{

    protected function sendXml()
    {
        if (is_object(payFabric_RequestBase::$logger)) {
            self::$logger->logInfo('_data has been generated');
            self::$logger->logDebug(' ', json_encode($this->_data));
        }
        // Use the WordPress HTTP API instead of raw cURL (WP.org coding standards).
        $args = array(
            'method'    => empty($this->_data) ? 'GET' : 'POST',
            'headers'   => array(
                'Content-Type'  => 'application/json',
                'Authorization' => $this->merchantId . '|' . $this->merchantKey,
            ),
            'timeout'   => $this->timeout,
            'sslverify' => (bool) self::$sslVerifyPeer,
        );
        if (!empty($this->_data)) {
            $args['body'] = json_encode($this->_data);
        }
        $response = wp_remote_request($this->endpoint, $args);
        if (is_object(payFabric_RequestBase::$logger)) {
            self::$logger->logInfo('Sending data to ' . $this->endpoint);
        }
        if (is_wp_error($response)) {
            $this->xmlResponse = false;
            $curlInfo = array();
        } else {
            $this->xmlResponse = wp_remote_retrieve_body($response);
            $curlInfo = array('total_time' => 0);
        }
        if (payFabric_RequestBase::$debug == true) {
            $this->printDebug($curlInfo);
        }
        if ($this->xmlResponse) {
            if (is_object(payFabric_RequestBase::$logger)) {
                self::$logger->logInfo('Response received');
                self::$logger->logDebug(' ', $this->xmlResponse);
            }
            return $this->xmlResponse;
        } else {
            throw new UnexpectedValueException('[PayFabric Class] Connection error with PayFabric server!', 503);
        }
    }

    private function printDebug($param, $_mpInfo = '')
    {
        $this->debugger("Target URL: " . $this->endpoint);
        $this->debugger("Request: " . htmlentities(mb_convert_encoding(json_encode($this->_data), "UTF-8")));
        if ($param) {
            $this->debugger("Response: " . htmlentities(mb_convert_encoding($this->xmlResponse, "UTF-8")));
            $this->debugger("Response time: " . round($param["total_time"], 3) . " secs.");
        } else {
            $this->debugger("Response: Connection problems with PayFabric!");
            foreach ($param as $k => $v) {
                if ($k != "certinfo") {
                    $_mpInfo .= $k . ": " . $v . ", ";
                }
            }
            $this->debugger("cURL_getinfo data: " . $_mpInfo);
        }
    }

    private function debugger($string)
    {
        $_d = gmdate('Y-m-d H:m:s', (int) substr(microtime(), 11, 10)) . ":" . substr(microtime(), 2, 5);
        echo '<br>' . esc_html(str_repeat("-", 20)) . '<br>[' . esc_html($_d) . '] ' . esc_html($string) . '<br>' . esc_html(str_repeat("-", 20)) . '<br>';
    }

}