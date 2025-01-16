<?php

namespace system\core;

class ExternalApiConection
{
    /**
     * Makes a cURL request to a specified URL.
     *
     * This method sends an HTTP request to the provided URL using cURL and returns the response content
     * if the request is successful. If the request fails, it returns false or an error message in case of
     * a cURL error.
     *
     * @param string $url The URL to which the request is to be sent.
     *
     * @return string|false The response content if the request is successful, or false if the request fails.
     *                      In case of a cURL error, it returns an error message.
     */
    public static function makeRequest(string $url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            return 'Request error. (Error: ' . curl_error($ch) . ')';
        }

        curl_close($ch);

        if ($httpCode == 200) {
            return $response;
        } else {
            return false;
        }
    }
}
