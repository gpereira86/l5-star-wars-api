<?php

spl_autoload_register(function ($class) {
    // Converte o namespace em um caminho de arquivo
    $baseDir = __DIR__ . '/../../'; // Caminho base para o projeto
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    // Verifica se o arquivo existe e o inclui
    if (file_exists($file)) {
        require_once $file;
    } else {
        throw new Exception("Arquivo da classe '{$class}' não encontrado: {$file}");
    }
});