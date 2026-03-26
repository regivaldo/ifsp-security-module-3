<?php

/**
 * Classe responsável pela autenticação de usuários e controle de acesso (RBAC).
 *
 * Gerencia o login, armazena dados do usuário na sessão, registra o último acesso
 * e controla o acesso às páginas por papel (admin/comum).
 *
 * - $_SESSION: superglobal nativa do PHP usada para manter dados entre requisições.
 * - PDO: classe nativa do PHP para acesso ao banco de dados (injetada via construtor).
 */
class SecurityAuth {
    private SecuritySession $session;
    private SecurityPassword $password;
    private PDO $db;

    public function __construct(SecuritySession $session, SecurityPassword $password, PDO $db) {
        $this->session = $session;
        $this->password = $password;
        $this->db = $db;
    }

    /**
     * Autentica o usuário verificando login e senha no banco de dados.
     *
     * Em caso de sucesso: regenera a sessão (prevenindo session fixation),
     * armazena os dados do usuário na sessão e registra o último acesso.
     *
     * - prepare(): método do PDO que cria uma consulta preparada (prepared statement),
     *   prevenindo injeção de SQL ao separar os dados da instrução SQL.
     * - execute(): método do PDOStatement que executa a consulta com os parâmetros fornecidos.
     * - fetch(): método do PDOStatement que retorna a próxima linha do resultado.
     * - date(): função nativa do PHP que formata uma data/hora. 'Y-m-d H:i:s' gera o
     *   formato padrão de datetime (ex: 2026-03-26 14:30:00).
     * - error_log(): função nativa do PHP que grava mensagens no log de erros do servidor.
     *
     * @param string $login Login informado pelo usuário
     * @param string $senha Senha em texto plano informada pelo usuário
     * @return bool true se autenticado com sucesso, false caso contrário
     */
    public function autenticar(string $login, string $senha): bool {
        try {
            $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE login = :login');
            $stmt->execute([':login' => $login]);
            $usuario = $stmt->fetch();

            if (!$usuario || !$this->password->verificar($senha, $usuario['senha'])) {
                return false;
            }

            // Regenerar ID de sessão após login bem-sucedido
            $this->session->regenerar();

            $_SESSION['usuario_id']    = $usuario['id'];
            $_SESSION['usuario_login'] = $usuario['login'];
            $_SESSION['usuario_nome']  = $usuario['nome'];
            $_SESSION['usuario_tipo']  = $usuario['tipo'];

            // Registrar último acesso
            $update = $this->db->prepare('UPDATE usuarios SET ultimo_acesso = :agora WHERE id = :id');
            $update->execute([':agora' => date('Y-m-d H:i:s'), ':id' => $usuario['id']]);

            return true;
        } catch (\Throwable $e) {
            error_log('Erro na autenticação: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica se há um usuário autenticado na sessão atual.
     *
     * - empty(): construto nativo do PHP que verifica se uma variável é vazia ou não definida.
     *
     * @return bool true se existe um usuário logado
     */
    public function estaAutenticado(): bool {
        return !empty($_SESSION['usuario_id']);
    }

    /**
     * Retorna o tipo/papel do usuário logado ("admin" ou "comum").
     *
     * @return string|null Tipo do usuário ou null se não autenticado
     */
    public function getTipo(): ?string {
        return $_SESSION['usuario_tipo'] ?? null;
    }

    /**
     * Retorna o ID numérico do usuário logado.
     *
     * - isset(): construto nativo do PHP que verifica se uma variável existe e não é null.
     *
     * @return int|null ID do usuário ou null se não autenticado
     */
    public function getId(): ?int {
        return isset($_SESSION['usuario_id']) ? (int) $_SESSION['usuario_id'] : null;
    }

    /**
     * Retorna o login do usuário logado.
     *
     * @return string|null Login do usuário ou null se não autenticado
     */
    public function getLogin(): ?string {
        return $_SESSION['usuario_login'] ?? null;
    }

    /**
     * Retorna o nome completo do usuário logado.
     *
     * @return string|null Nome do usuário ou null se não autenticado
     */
    public function getNome(): ?string {
        return $_SESSION['usuario_nome'] ?? null;
    }

    /**
     * Verifica se o usuário logado é administrador.
     *
     * @return bool true se o tipo do usuário é "admin"
     */
    public function ehAdmin(): bool {
        return $this->getTipo() === 'admin';
    }

    /**
     * Redireciona para a página de login se o usuário não estiver autenticado.
     *
     * - header(): função nativa do PHP que envia um cabeçalho HTTP ao navegador.
     *   'Location: /login' instrui o navegador a redirecionar para a URL indicada.
     * - exit: construto nativo do PHP que encerra a execução do script imediatamente,
     *   garantindo que nenhum código posterior seja executado após o redirecionamento.
     */
    public function exigirAutenticacao(): void {
        if (!$this->estaAutenticado()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Exige que o usuário tenha um papel específico para acessar a página.
     *
     * Se o papel não corresponder, redireciona para a área correta do usuário
     * (admin vai para /administradores, comum vai para /usuarios).
     *
     * @param string $papel Papel exigido ("admin" ou "comum")
     */
    public function exigirPapel(string $papel): void {
        $this->exigirAutenticacao();

        if ($this->getTipo() !== $papel) {
            if ($this->ehAdmin()) {
                header('Location: /administradores');
            } else {
                header('Location: /usuarios');
            }
            exit;
        }
    }
}
