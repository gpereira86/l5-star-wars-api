<?php

spl_autoload_register(function ($class) {
    $baseDir = dirname(__DIR__) . '/';

    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        error_log("Erro ao carregar classe {$class}: Arquivo {$file} não encontrado.");
        // throw new Exception("Arquivo da classe '{$class}' não encontrado: {$file}");
    }
});
