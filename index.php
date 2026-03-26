<?php

/**
 * Front Controller / Roteador da aplicação.
 *
 * Todas as requisições passam por este arquivo.
 * O roteamento é feito com base no path da URL.
 */

require_once __DIR__ . '/Security.php';

try {
    $security = new Security();

    // Obter o path da URL requisitada
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = rtrim($path, '/');

    // Roteamento
    switch ($path) {
        case '':
        case '/login':
            require __DIR__ . '/pages/login.php';
            break;

        case '/cadastro':
            require __DIR__ . '/pages/cadastro.php';
            break;

        case '/administradores':
            require __DIR__ . '/pages/administradores.php';
            break;

        case '/usuarios':
            require __DIR__ . '/pages/usuarios.php';
            break;

        case '/deletar-usuario':
            $security->exigirPapel('admin');
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $security->validarCsrf()) {
                $id = (int) ($_POST['id'] ?? 0);
                if ($id > 0 && $id !== $security->getIdUsuario()) {
                    $security->deletarUsuario($id);
                }
            }
            header('Location: /administradores');
            exit;

        case '/logout':
            $security->destruirSessao();
            header('Location: /login');
            exit;

        default:
            http_response_code(404);
            echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>404</title>
                <link rel="stylesheet" href="/style.css"></head>
                <body class="bg-erro"><div class="box"><h2>Página não encontrada</h2>
                <a href="/login">Voltar ao login</a></div></body></html>';
            break;
    }
} catch (Throwable $e) {
    error_log('Erro fatal: ' . $e->getMessage());
    http_response_code(500);
    echo 'Ocorreu um erro interno. Tente novamente mais tarde.';
}
