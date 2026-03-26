<?php

/**
 * Classe responsável pelas operações de banco de dados relacionadas a usuários.
 *
 * Centraliza cadastro, listagem e verificação de existência de usuários,
 * utilizando prepared statements para prevenir injeção de SQL.
 *
 * - PDO: classe nativa do PHP para acesso ao banco de dados (injetada via construtor).
 */
class SecurityUserRepository {
    private SecurityPassword $password;
    private PDO $db;

    public function __construct(SecurityPassword $password, PDO $db) {
        $this->password = $password;
        $this->db = $db;
    }

    /**
     * Cadastra um novo usuário no banco de dados.
     *
     * A senha é transformada em hash antes de ser armazenada, nunca salva em texto plano.
     *
     * - prepare(): método do PDO que cria uma consulta preparada (prepared statement).
     *   Os placeholders (:login, :senha, etc.) são substituídos de forma segura pelo execute(),
     *   prevenindo injeção de SQL.
     * - execute(): método do PDOStatement que executa a consulta com os valores fornecidos.
     * - error_log(): função nativa do PHP que grava mensagens no log de erros do servidor.
     *
     * @param string $login Login único do novo usuário
     * @param string $senha Senha em texto plano (será convertida em hash)
     * @param string $tipo  Tipo do usuário ("admin" ou "comum")
     * @param string $nome  Nome completo do usuário
     * @return bool true se cadastrado com sucesso, false em caso de erro
     */
    public function cadastrar(string $login, string $senha, string $tipo, string $nome): bool {
        try {
            $hash = $this->password->hash($senha);
            $stmt = $this->db->prepare('INSERT INTO usuarios (login, senha, nome, tipo) VALUES (:login, :senha, :nome, :tipo)');
            $stmt->execute([
                ':login' => $login,
                ':senha' => $hash,
                ':nome'  => $nome,
                ':tipo'  => $tipo,
            ]);
            return true;
        } catch (\Throwable $e) {
            error_log('Erro no cadastro: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lista todos os usuários cadastrados, ordenados por ID.
     *
     * Retorna apenas os campos necessários para exibição (sem a senha).
     *
     * - query(): método do PDO que executa uma consulta SQL diretamente.
     *   Seguro aqui pois não há parâmetros vindos do usuário.
     * - fetchAll(): método do PDOStatement que retorna todas as linhas do resultado
     *   como um array de arrays associativos (configurado via FETCH_ASSOC no PDO).
     *
     * @return array Lista de usuários com id, login, nome, tipo e ultimo_acesso
     */
    public function listarTodos(): array {
        try {
            $stmt = $this->db->query('SELECT id, login, nome, tipo, ultimo_acesso FROM usuarios ORDER BY id');
            return $stmt->fetchAll();
        } catch (\Throwable $e) {
            error_log('Erro ao listar usuários: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica se um login já existe no banco de dados.
     *
     * Em caso de erro na consulta, retorna true por segurança para evitar
     * a criação de logins duplicados.
     *
     * - prepare(): método do PDO que cria uma consulta preparada com placeholder :login.
     * - fetch(): método do PDOStatement que retorna a próxima linha do resultado.
     *
     * @param string $login Login a ser verificado
     * @return bool true se o login já existe (ou em caso de erro), false se disponível
     */
    public function loginExiste(string $login): bool {
        try {
            $stmt = $this->db->prepare('SELECT COUNT(*) as total FROM usuarios WHERE login = :login');
            $stmt->execute([':login' => $login]);
            return (int) $stmt->fetch()['total'] > 0;
        } catch (\Throwable $e) {
            error_log('Erro ao verificar login: ' . $e->getMessage());
            return true; // Em caso de erro, assume que existe para evitar duplicatas
        }
    }
}
