<?php

/**
 * Classe responsável pelo tratamento global de erros da aplicação.
 *
 * Captura exceções não tratadas e exibe uma página de erro genérica,
 * evitando que detalhes internos sejam expostos ao usuário.
 */
class SecurityErrorHandler {
    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    /**
     * Configura o tratamento de erros da aplicação.
     *
     * - ini_set(): função nativa do PHP que altera configurações em tempo de execução.
     *   Aqui desabilita a exibição de erros ao usuário e habilita o log em arquivo.
     * - error_reporting(): função nativa do PHP que define quais níveis de erro serão reportados.
     *   E_ALL reporta todos os tipos (notices, warnings, fatals, etc.).
     * - set_exception_handler(): função nativa do PHP que registra um callback global
     *   para capturar exceções não tratadas por blocos try/catch.
     * - error_log(): função nativa do PHP que grava mensagens no log de erros do servidor.
     * - http_response_code(): função nativa do PHP que define o código HTTP da resposta (500 = erro interno).
     */
    public function setup(): void {
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        set_exception_handler(function (\Throwable $e) {
            error_log('Erro da aplicação: ' . $e->getMessage());
            http_response_code(500);
            echo $this->renderError('Ocorreu um erro interno. Tente novamente mais tarde.');
            exit;
        });
    }

    /**
     * Renderiza uma página HTML de erro genérica.
     *
     * - htmlspecialchars(): função nativa do PHP que converte caracteres especiais em entidades HTML,
     *   prevenindo ataques XSS (Cross-Site Scripting). ENT_QUOTES escapa aspas simples e duplas.
     *
     * @param string $message Mensagem de erro a ser exibida (será sanitizada)
     * @return string HTML completo da página de erro
     */
    public function renderError(string $message): string {
        return '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">
            <title>Erro</title>
            <style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;background:#f5f5f5;}
            .box{background:#fff;padding:40px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,.1);text-align:center;}
            a{color:#3b82f6;}</style></head>
            <body><div class="box"><h2>Erro</h2><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>
            <a href="/login">Voltar ao login</a></div></body></html>';
    }
}
