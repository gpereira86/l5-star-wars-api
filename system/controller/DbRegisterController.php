<?php

namespace system\controller;

use system\model\LogModel;

class DbRegisterController
{
    public function saveLogRegister()
    {
        $saveLogRegister = new LogModel();
        $saveLogRegister->save($dataSet);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nome = $_POST['nome'] ?? null;  // Usa null caso o campo não tenha sido enviado
            $email = $_POST['email'] ?? null;
        }

    }

}