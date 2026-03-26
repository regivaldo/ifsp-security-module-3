<?php

/**
 * Router para o servidor embutido do PHP.
 *
 * Uso: php -S localhost:8080 router.php
 *
 * Este arquivo faz o papel do .htaccess,
 * redirecionando todas as requisições para index.php.
 */

// Se o arquivo existe fisicamente (CSS, JS, imagens), servir diretamente
if (php_sapi_name() === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    if ($path !== '/' && is_file($file)) {
        return false;
    }
}

// Redirecionar tudo para o front controller
require __DIR__ . '/index.php';
