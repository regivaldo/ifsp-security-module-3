<?php

/**
 * Classe responsável pelo gerenciamento seguro de sessões.
 *
 * Configura os cookies de sessão com flags de segurança e controla
 * a expiração por inatividade.
 *
 * - $_SESSION: superglobal nativa do PHP que armazena dados da sessão do usuário.
 * - session_*: família de funções nativas do PHP para manipulação de sessões.
 */
class SecuritySession
{
    private const SESSION_LIFETIME = 1800; // 30 minutos de inatividade

    /**
     * Inicializa a sessão com configurações de segurança.
     *
     * - session_status(): função nativa do PHP que retorna o estado atual da sessão.
     *   PHP_SESSION_ACTIVE indica que a sessão já está iniciada.
     * - ini_set(): função nativa do PHP que altera configurações em tempo de execução.
     *   Aqui configura flags de segurança dos cookies de sessão:
     *     - use_strict_mode: rejeita IDs de sessão não gerados pelo servidor
     *     - use_only_cookies: impede que o ID de sessão trafegue pela URL
     *     - cookie_httponly: impede acesso ao cookie via JavaScript (mitiga XSS)
     *     - cookie_samesite: restringe envio do cookie a requisições do mesmo site (mitiga CSRF)
     * - session_start(): função nativa do PHP que inicia ou retoma uma sessão.
     * - time(): função nativa do PHP que retorna o timestamp Unix atual (em segundos).
     */
    public function init(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Configurações seguras de sessão

        # impede que o PHP aceite IDs de sessão fornecidos pelo usuário, forçando a criação de um novo ID para cada sessão
        ini_set('session.use_strict_mode', '1');

        # garante que as sessões sejam gerenciadas apenas por cookies, evitando a exposição do ID da sessão na URL
        ini_set('session.use_only_cookies', '1');

        # marca os cookies de sessão como HttpOnly para impedir acesso via JavaScript, mitigando ataques XSS
        ini_set('session.cookie_httponly', '1');

        # marca os cookies de sessão como Secure para que só sejam enviados em conexões HTTPS
        # ini_set('session.cookie_secure', '1'); # deve ser ligado quando usar HTTPs

        # define SameSite=Strict para que o cookie só seja enviado em requisições do mesmo site
        ini_set('session.cookie_samesite', 'Strict');

        session_start();

        // Verificar expiração por inatividade
        if (isset($_SESSION['ultimo_acesso'])) {
            $inativo = time() - $_SESSION['ultimo_acesso'];
            if ($inativo > self::SESSION_LIFETIME) {
                $this->destruir();
                return;
            }
        }

        $_SESSION['ultimo_acesso'] = time();
    }

    /**
     * Regenera o ID da sessão, mantendo os dados.
     *
     * Deve ser chamada após login bem-sucedido para prevenir ataques
     * de fixação de sessão (session fixation).
     *
     * - session_regenerate_id(): função nativa do PHP que gera um novo ID de sessão.
     *   O parâmetro true deleta o arquivo da sessão anterior.
     */
    public function regenerar(): void
    {
        session_regenerate_id(true);
        $_SESSION['ultimo_acesso'] = time();
    }

    /**
     * Destrói completamente a sessão: dados, cookie e arquivo.
     *
     * - ini_get(): função nativa do PHP que lê o valor de uma configuração.
     * - session_get_cookie_params(): função nativa do PHP que retorna os parâmetros
     *   do cookie de sessão (path, domain, secure, httponly).
     * - setcookie(): função nativa do PHP que envia um cookie ao navegador.
     *   Aqui define valor vazio e expiração no passado para removê-lo.
     * - session_name(): função nativa do PHP que retorna o nome do cookie de sessão (ex: PHPSESSID).
     * - session_destroy(): função nativa do PHP que destrói todos os dados da sessão no servidor.
     */
    public function destruir(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }

        session_destroy();
    }
}
