<?php

/**
 * Classe responsável pela conexão e inicialização do banco de dados SQLite.
 *
 * Cria a tabela de usuários caso não exista e insere o administrador padrão
 * quando o banco está vazio.
 *
 * - PDO: classe nativa do PHP (PHP Data Objects) que fornece uma interface
 *   uniforme para acesso a bancos de dados. Suporta SQLite, MySQL, PostgreSQL, etc.
 */
class SecurityDatabase {
    private PDO $db;

    /**
     * Inicializa a conexão com o banco SQLite e cria a estrutura necessária.
     *
     * - __DIR__: constante mágica nativa do PHP que retorna o diretório do arquivo atual.
     * - PDO::ATTR_ERRMODE => ERRMODE_EXCEPTION: faz o PDO lançar exceções em caso de erro SQL.
     * - PDO::ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC: retorna resultados como arrays associativos.
     * - PDO::ATTR_EMULATE_PREPARES => false: desabilita emulação de prepared statements,
     *   forçando o driver a preparar de verdade no banco, o que previne injeção de SQL.
     * - exec(): método do PDO que executa uma instrução SQL diretamente (sem retorno de dados).
     * - query(): método do PDO que executa uma consulta SQL e retorna um PDOStatement com os resultados.
     * - fetch(): método do PDOStatement que retorna a próxima linha do resultado.
     *
     * @param callable $cadastrarUsuario Callback para criar o usuário admin padrão
     */
    public function init(callable $cadastrarUsuario): void {
        $dbPath = __DIR__ . '/../database.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            # garante que as consultas preparadas sejam realmente preparadas pelo driver
            # assim, se alguém tentar passar trechos de SQL malicioso, ele será tratado como string e não executado
        ]);

        // Criar tabela de usuários se não existir
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS usuarios (
                id             INTEGER PRIMARY KEY AUTOINCREMENT,
                login          TEXT     NOT NULL UNIQUE,
                senha          TEXT     NOT NULL,
                nome           TEXT     NOT NULL,
                tipo           TEXT     NOT NULL CHECK(tipo IN ("admin","comum")),
                ultimo_acesso  DATETIME DEFAULT NULL
            )
        ');

        // Criar usuário admin padrão se a tabela estiver vazia
        $stmt = $this->db->query('SELECT COUNT(*) as total FROM usuarios');
        if ((int) $stmt->fetch()['total'] === 0) {
            $cadastrarUsuario('admin', 'admin123', 'admin', 'Administrador');
        }
    }

    /**
     * Retorna a instância PDO ativa para uso em outras classes.
     *
     * @return PDO Conexão com o banco de dados
     */
    public function getDb(): PDO {
        return $this->db;
    }
}
