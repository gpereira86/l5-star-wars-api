<?php

namespace system\controller;

use system\model\LogModel;
use system\model\UserModel;

/**
 * DbRegisterController handles the process of saving log data into the database.
 * It processes incoming requests containing log data, validates it, and stores it in the database.
 * It also handles retrieving log data, validating API keys, and logging each request made to the system.
 */
class DbRegisterController
{

    /**
     * Processes and saves log data into the database.
     *
     * This method expects an associative array containing log data, typically from a POST request.
     * It calls the `save` method of the `LogModel` to save the data in the database.
     *
     * If successful, it returns a success response. If an error occurs while saving the log,
     * it will throw an exception, which should be handled in a higher layer of the application.
     *
     * @param array $posterData The log data to be saved into the database. This should include necessary information like
     *                           log type, user, timestamp, etc.
     * @return void
     */
    public function saveLogRegister(array $posterData)
    {
        // Creates an instance of LogModel and saves the log data
        $saveLogRegister = new LogModel();
        $saveLogRegister->save($posterData);
    }

    /**
     * Retrieves log data based on specific parameters.
     *
     * This method processes incoming GET requests to retrieve log data from the database.
     * It checks if an API key is provided and validates it. If the API key is valid, it fetches the log data.
     * Otherwise, it returns a 401 Unauthorized error with a message.
     *
     * The log data can be filtered by the number of days (`days`) and whether the log is finished (`finished`).
     *
     * @return string JSON response containing either the log data or an error message.
     */
    public function getLogRegister()
    {
        // Retrieve the optional query parameters
        $days = $_GET['days'] ?? null;
        $finished = $_GET['finished'] ?? null;
        $apiKey = $_GET['apikey'] ?? null;

        // Define the endpoint and method for logging purposes
        $endpoint = "/api/log-register/" . basename($_SERVER['REQUEST_URI']);
        $method = $_SERVER['REQUEST_METHOD'];

        // Modify the endpoint to hide the API key for security purposes in logs
        $endpointSecretKey = (strpos($endpoint, 'apikey') !== false) ? substr_replace(
            $endpoint, 'apikey=SECRET-KEY', strpos($endpoint, 'apikey')
        ) : $endpoint;

        // Instantiate the UserModel to validate the API key and ApiMoviesController for logging
        $checkApiKey = new UserModel();
        $apiMovieController = new ApiMoviesController();

        // Check if the API key is valid
        if (empty($apiKey) || !$checkApiKey->checkApiKey($apiKey)) {
            $responCode = 401;

            // Log the failed attempt
            $apiMovieController->logRegister([
                'endpoint' => $endpointSecretKey,
                'request_method' => $method,
                'response_code' => $responCode
            ]);

            // Return the unauthorized error response
            http_response_code($responCode);
            return json_encode([
                "Method" => $method,
                "responseCode" => $responCode,
                "error" => "UNAUTHORIZED: NON-EXISTENT OR INVALID API KEY",
                "showErrorPage" => true
            ]);
        }

        // Retrieve the log data using LogModel
        $getLogRegister = new LogModel();
        $response = $getLogRegister->getLogRegister($days, $finished, $apiKey);

        // Log the successful request
        $apiMovieController->logRegister([
            'endpoint' => $endpointSecretKey,
            'request_method' => $method,
            'response_code' => $response['responseCode']
        ]);

        // Return the log data or any error response from LogModel
        http_response_code(200);
        return json_encode([
            "Method" => $method,
            "responseCode" => $response['responseCode'],
            "data" => $response['data']
        ]);
    }

}
