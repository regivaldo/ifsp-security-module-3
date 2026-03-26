<?php

/**
 * Classe responsável pelo hashing e verificação de senhas.
 *
 * Utiliza o algoritmo Argon2id, considerado um dos mais seguros para
 * armazenamento de senhas, resistente a ataques de força bruta com GPU.
 */
class SecurityPassword {
    /**
     * Gera o hash de uma senha usando Argon2id.
     *
     * - password_hash(): função nativa do PHP (5.5+) que cria um hash seguro da senha.
     *   Gera automaticamente um salt aleatório e inclui os parâmetros no resultado.
     * - PASSWORD_ARGON2ID: constante nativa do PHP (7.3+) que seleciona o algoritmo
     *   Argon2id, uma variante que combina resistência a ataques side-channel (Argon2i)
     *   e resistência a ataques com GPU (Argon2d).
     *
     * @param string $senha Senha em texto plano
     * @return string Hash da senha (inclui salt e parâmetros do algoritmo)
     */
    public function hash(string $senha): string {
        return password_hash($senha, PASSWORD_ARGON2ID);
    }

    /**
     * Verifica se uma senha em texto plano corresponde a um hash armazenado.
     *
     * - password_verify(): função nativa do PHP (5.5+) que compara a senha com o hash
     *   de forma segura, usando tempo constante para prevenir ataques de timing.
     *
     * @param string $senha Senha em texto plano digitada pelo usuário
     * @param string $hash  Hash armazenado no banco de dados
     * @return bool true se a senha confere, false caso contrário
     */
    public function verificar(string $senha, string $hash): bool {
        return password_verify($senha, $hash);
    }
}
