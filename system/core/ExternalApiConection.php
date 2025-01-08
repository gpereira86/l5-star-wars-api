<?php

namespace system\core;

class ExternalApiConection
{


    /**
     * Faz a requisição cURL
     *
     * @param string $url The URL to which the request is to be sent.
     * @return string|false The response content if the request is successful, or false if it fails.
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

        if(curl_errno($ch)) {
            return 'Erro na solicitação. (Erro: ' . curl_error($ch). ')';
        }

        curl_close($ch);

        if ($httpCode == 200) {
            return $response;
        } else {
            return false;
        }
    }

}