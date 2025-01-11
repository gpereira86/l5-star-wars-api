<?php

namespace system\controller;

use system\model\LogModel;

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
    public function saveLogRegister()
    {
        $saveLogRegister = new LogModel();  // Create an instance of LogModel

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Check if the request is a POST

            // Decode the incoming JSON data
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate the log data
            $saveLogRegister->validateLogData($data);

            // If there are validation errors, return them as a JSON response
            if (!empty($validationErrors)) {
                header('Content-Type: application/json');
                echo json_encode(['errors' => $validationErrors]);  // Return validation errors
                http_response_code(400);  // Set HTTP response code to 400 (Bad Request)
                exit;
            }

            // Save the log data into the database
            $logId = $saveLogRegister->save($data);

            // If the log is saved successfully, return a success message with the log ID
            if ($logId) {
                header('Content-Type: application/json');
                echo json_encode([
                    'message' => 'Log registered successfully!',
                    'log_id' => $logId
                ]);
            } else {
                // If the log registration fails, return an error message
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Failed to register log',
                ]);
                http_response_code(500);  // Set HTTP response code to 500 (Internal Server Error)
            }

            exit;
        }
    }
}
