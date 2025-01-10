<?php

namespace system\controller;

use system\model\LogModel;

class DbRegisterController
{
    public function saveLogRegister()
    {
        $saveLogRegister = new LogModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $data = json_decode(file_get_contents('php://input'), true);

            $saveLogRegister->validateLogData($data);

            if (!empty($validationErrors)) {
                header('Content-Type: application/json');
                echo json_encode(['errors' => $validationErrors]);
                http_response_code(400);
                exit;
            }

            // Salvar no banco
            $logId = $saveLogRegister->save($data);

            if ($logId) {
                header('Content-Type: application/json');
                echo json_encode([
                    'message' => 'Log registered successfully!',
                    'log_id' => $logId
                ]);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Failed to register log ',
                ]);
                http_response_code(500);
            }

            exit;
        }
    }


}