<?php

/**
 * Classe responsável pela sanitização de entrada e validação de dados.
 *
 * Previne ataques XSS ao sanitizar saídas e garante que os dados
 * recebidos dos formulários respeitam os formatos esperados.
 */
class SecuritySanitizer {
    /**
     * Sanitiza uma string para exibição segura em HTML.
     *
     * - trim(): função nativa do PHP que remove espaços em branco do início e fim da string.
     * - htmlspecialchars(): função nativa do PHP que converte caracteres especiais
     *   (<, >, &, ", ') em entidades HTML, prevenindo injeção de código (XSS).
     *   ENT_QUOTES escapa tanto aspas duplas quanto simples.
     *
     * @param string $input Texto do usuário a ser sanitizado
     * @return string Texto seguro para inserção em HTML
     */
    public function sanitizar(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida o formato do login (3-50 caracteres alfanuméricos, underline ou ponto).
     *
     * - preg_match(): função nativa do PHP que executa uma busca por expressão regular.
     *   Retorna 1 se encontrou correspondência, 0 se não, ou false em caso de erro.
     *   O padrão ^[a-zA-Z0-9_.]{3,50}$ aceita apenas letras, números, _ e .
     *
     * @param string $login Login a ser validado
     * @return bool true se o login é válido
     */
    public function validarLogin(string $login): bool {
        return (bool) preg_match('/^[a-zA-Z0-9_.]{3,50}$/', $login);
    }

    /**
     * Valida se a senha tem o comprimento mínimo de 6 caracteres.
     *
     * - strlen(): função nativa do PHP que retorna o comprimento da string em bytes.
     *   Para senhas ASCII isso equivale ao número de caracteres.
     *
     * @param string $senha Senha a ser validada
     * @return bool true se a senha atende ao requisito mínimo
     */
    public function validarSenha(string $senha): bool {
        return strlen($senha) >= 6;
    }

    /**
     * Valida se o nome tem entre 2 e 100 caracteres (suporte a UTF-8).
     *
     * - mb_strlen(): função nativa do PHP (extensão mbstring) que retorna o comprimento
     *   da string em caracteres, respeitando a codificação multibyte (ex: acentos, emojis).
     *   Diferente de strlen() que conta bytes, mb_strlen() conta caracteres reais.
     *
     * @param string $nome Nome a ser validado
     * @return bool true se o nome tem comprimento válido
     */
    public function validarNome(string $nome): bool {
        $len = mb_strlen($nome, 'UTF-8');
        return $len >= 2 && $len <= 100;
    }

    /**
     * Valida se o tipo de usuário é um dos valores permitidos.
     *
     * - in_array(): função nativa do PHP que verifica se um valor existe em um array.
     *   O terceiro parâmetro true ativa comparação estrita (===), verificando tipo e valor.
     *
     * @param string $tipo Tipo a ser validado ("admin" ou "comum")
     * @return bool true se o tipo é válido
     */
    public function validarTipoUsuario(string $tipo): bool {
        return in_array($tipo, ['admin', 'comum'], true);
    }
}
