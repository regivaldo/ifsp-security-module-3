<?php

/**
 * Fachada (Facade) centralizada de segurança da aplicação.
 *
 * Delega para classes especializadas em security/:
 *  - ErrorHandler: tratamento de erros
 *  - Database: conexão e inicialização do BD
 *  - Session: gerenciamento seguro de sessões
 *  - Csrf: proteção contra CSRF
 *  - Password: hashing de senhas
 *  - Sanitizer: sanitização e validação de entrada
 *  - Auth: autenticação e RBAC
 *  - UserRepository: cadastro e consulta de usuários
 */

require_once __DIR__ . '/security/ErrorHandler.php';
require_once __DIR__ . '/security/Database.php';
require_once __DIR__ . '/security/Session.php';
require_once __DIR__ . '/security/Csrf.php';
require_once __DIR__ . '/security/Password.php';
require_once __DIR__ . '/security/Sanitizer.php';
require_once __DIR__ . '/security/Auth.php';
require_once __DIR__ . '/security/UserRepository.php';

class Security {
    private SecurityErrorHandler $errorHandler;
    private SecurityDatabase $database;
    private SecuritySession $session;
    private SecurityCsrf $csrf;
    private SecurityPassword $password;
    private SecuritySanitizer $sanitizer;
    private SecurityAuth $auth;
    private SecurityUserRepository $userRepository;

    public function __construct() {
        $this->errorHandler = new SecurityErrorHandler($this);
        $this->errorHandler->setup();

        $this->password = new SecurityPassword();
        $this->database = new SecurityDatabase();

        // Database init precisa do cadastrarUsuario para criar admin padrão.
        // O callback usa $this->password diretamente pois UserRepository ainda não existe.
        $this->database->init(function (string $login, string $senha, string $tipo, string $nome) {
            $hash = $this->password->hash($senha);
            $this->database->getDb()->prepare('INSERT INTO usuarios (login, senha, nome, tipo) VALUES (:login, :senha, :nome, :tipo)')
                ->execute([':login' => $login, ':senha' => $hash, ':nome' => $nome, ':tipo' => $tipo]);
        });

        $this->userRepository = new SecurityUserRepository($this->password, $this->database->getDb());

        $this->session = new SecuritySession();
        $this->session->init();

        $this->csrf = new SecurityCsrf();
        $this->sanitizer = new SecuritySanitizer();
        $this->auth = new SecurityAuth($this->session, $this->password, $this->database->getDb());
    }

    // ── Banco de dados ──

    public function getDb(): PDO {
        return $this->database->getDb();
    }

    // ── Sessões ──

    public function regenerarSessao(): void {
        $this->session->regenerar();
    }

    public function destruirSessao(): void {
        $this->session->destruir();
    }

    // ── CSRF ──

    public function gerarTokenCsrf(): string {
        return $this->csrf->gerarToken();
    }

    public function campoCsrf(): string {
        return $this->csrf->campo();
    }

    public function validarCsrf(): bool {
        return $this->csrf->validar();
    }

    // ── Senhas ──

    public function hashSenha(string $senha): string {
        return $this->password->hash($senha);
    }

    public function verificarSenha(string $senha, string $hash): bool {
        return $this->password->verificar($senha, $hash);
    }

    // ── Sanitização ──

    public function sanitizar(string $input): string {
        return $this->sanitizer->sanitizar($input);
    }

    public function validarLogin(string $login): bool {
        return $this->sanitizer->validarLogin($login);
    }

    public function validarSenha(string $senha): bool {
        return $this->sanitizer->validarSenha($senha);
    }

    public function validarNome(string $nome): bool {
        return $this->sanitizer->validarNome($nome);
    }

    public function validarTipoUsuario(string $tipo): bool {
        return $this->sanitizer->validarTipoUsuario($tipo);
    }

    // ── Autenticação e RBAC ──

    public function autenticar(string $login, string $senha): bool {
        return $this->auth->autenticar($login, $senha);
    }

    public function estaAutenticado(): bool {
        return $this->auth->estaAutenticado();
    }

    public function getTipoUsuario(): ?string {
        return $this->auth->getTipo();
    }

    public function getIdUsuario(): ?int {
        return $this->auth->getId();
    }

    public function getLoginUsuario(): ?string {
        return $this->auth->getLogin();
    }

    public function getNomeUsuario(): ?string {
        return $this->auth->getNome();
    }

    public function ehAdmin(): bool {
        return $this->auth->ehAdmin();
    }

    public function exigirAutenticacao(): void {
        $this->auth->exigirAutenticacao();
    }

    public function exigirPapel(string $papel): void {
        $this->auth->exigirPapel($papel);
    }

    // ── Cadastro de usuários ──

    public function cadastrarUsuario(string $login, string $senha, string $tipo, string $nome): bool {
        return $this->userRepository->cadastrar($login, $senha, $tipo, $nome);
    }

    public function listarUsuarios(): array {
        return $this->userRepository->listarTodos();
    }

    public function loginExiste(string $login): bool {
        return $this->userRepository->loginExiste($login);
    }

    public function deletarUsuario(int $id): bool {
        return $this->userRepository->deletar($id);
    }
}
