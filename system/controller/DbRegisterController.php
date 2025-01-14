<?php

namespace system\controller;

use system\model\LogModel;
use system\model\UserModel;

/**
 * DbRegisterController handles the process of saving log data into the database.
 * It processes incoming POST requests containing log data, validates it,
 * and stores it in the database.
 */
class DbRegisterController
{


    /**
     * Processes and saves log data into the database.
     *
     * This method expects a POST request with log data in JSON format.
     * It validates the data, and if valid, saves it into the database.
     * If successful, it returns a success message with the log ID.
     * In case of validation errors, it returns an error response with the validation messages.
     * If the log data fails to save, it returns a failure message.
     */
    public function saveLogRegister(array $posterData)
    {
        $saveLogRegister = new LogModel();
        $saveLogRegister->save($posterData);
    }

    public function getLogRegister()
    {
        $days = $_GET['days'] ?? null;
        $finished = $_GET['finished'] ?? null;
        $apiKey = $_GET['apikey']?? null;

        $endpoint = "/api/log-register/".basename($_SERVER['REQUEST_URI']);
        $method = $_SERVER['REQUEST_METHOD'];

        $endpointSecretKey = (strpos(
            $endpoint, 'apikey') !== false) ? substr_replace(
                $endpoint, 'apikey=SECRET-KEY', strpos(
                    $endpoint, 'apikey'
                )) : $endpoint;


        $checkApiKey = new UserModel();
        $apiMovieController = new ApiMoviesController();

        if (empty($apiKey) || !$checkApiKey->checkApiKey($apiKey)) {
            $responCode = 401;

            $apiMovieController->errorLogRegister([
                'endpoint' => $endpointSecretKey,
                'request_method' => $method,
                'response_code' => $responCode
            ]);

            http_response_code($responCode);
            return json_encode([
                "Method" => $method,
                "responseCode" => $responCode,
                "error" => "UNAUTHORIZED: NON-EXISTENT OR INVALID API KEY",
                "showErrorPage" => true
            ]);
        }

        $getLogRegister = new LogModel();
        $response = $getLogRegister->getLogRegister($days, $finished, $apiKey);

        $apiMovieController->errorLogRegister([
            'endpoint' => $endpointSecretKey,
            'request_method' => $method,
            'response_code' =>  $response['responseCode']
        ]);

        http_response_code(200);
        return json_encode([
            "Method" => $method,
            "responseCode" => $response['responseCode'],
            "data" => $response['data']
        ]);

    }


}
