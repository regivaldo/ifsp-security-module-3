<?php

/**
 * Classe responsável pela proteção contra ataques CSRF (Cross-Site Request Forgery).
 *
 * Gera e valida tokens únicos por sessão que devem ser enviados junto
 * a cada formulário POST, garantindo que a requisição veio do próprio site.
 *
 * - $_SESSION: superglobal nativa do PHP usada para armazenar o token entre requisições.
 * - $_POST: superglobal nativa do PHP que contém os dados enviados via método POST.
 */
class SecurityCsrf {
    /**
     * Gera um token CSRF e armazena na sessão (ou retorna o existente).
     *
     * - random_bytes(): função nativa do PHP (7.0+) que gera bytes aleatórios
     *   criptograficamente seguros. Aqui gera 32 bytes (256 bits) de entropia.
     * - bin2hex(): função nativa do PHP que converte dados binários em representação
     *   hexadecimal, resultando em uma string de 64 caracteres.
     *
     * @return string Token CSRF em hexadecimal (64 caracteres)
     */
    public function gerarToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Gera um campo HTML hidden contendo o token CSRF para uso em formulários.
     *
     * - htmlspecialchars(): função nativa do PHP que escapa caracteres especiais em HTML,
     *   prevenindo XSS. ENT_QUOTES escapa aspas simples e duplas.
     *
     * @return string Tag <input type="hidden"> com o token CSRF
     */
    public function campo(): string {
        $token = $this->gerarToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valida o token CSRF enviado no POST contra o armazenado na sessão.
     *
     * - hash_equals(): função nativa do PHP que compara duas strings em tempo constante,
     *   prevenindo ataques de timing (side-channel). Retorna true se forem idênticas.
     *
     * @return bool true se o token é válido, false caso contrário
     */
    public function validar(): bool {
        $tokenEnviado = $_POST['csrf_token'] ?? '';
        $tokenSessao  = $_SESSION['csrf_token'] ?? '';

        if (empty($tokenEnviado) || empty($tokenSessao)) {
            return false;
        }

        return hash_equals($tokenSessao, $tokenEnviado);
    }
}
