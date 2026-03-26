<?php
/**
 * Página de acesso restrito para administradores
 * 
 * @var Security $security - Instância do sistema de segurança para controle de acesso e autenticação
 * 
 * Restrição: apenas administradores
 */
$security->exigirPapel('admin');

$erro    = '';
$sucesso = '';

// Processar formulário de cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$security->validarCsrf()) {
            $erro = 'Requisição inválida. Tente novamente.';
        } else {
            $nome  = trim($_POST['nome'] ?? '');
            $login = trim($_POST['login'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $tipo  = $_POST['tipo'] ?? '';

            if (empty($nome) || empty($login) || empty($senha) || empty($tipo)) {
                $erro = 'Preencha todos os campos.';
            } elseif (!$security->validarNome($nome)) {
                $erro = 'Nome inválido. Use de 2 a 100 caracteres.';
            } elseif (!$security->validarLogin($login)) {
                $erro = 'Login inválido. Use de 3 a 50 caracteres (letras, números, _ ou .).';
            } elseif (!$security->validarSenha($senha)) {
                $erro = 'A senha deve ter pelo menos 6 caracteres.';
            } elseif (!$security->validarTipoUsuario($tipo)) {
                $erro = 'Tipo de usuário inválido.';
            } elseif ($security->loginExiste($login)) {
                $erro = 'Este login já está em uso.';
            } elseif ($security->cadastrarUsuario($login, $senha, $tipo, $nome)) {
                $sucesso = 'Usuário cadastrado com sucesso!';
            } else {
                $erro = 'Erro ao cadastrar usuário. Tente novamente.';
            }
        }
    } catch (Throwable $e) {
        error_log('Erro no cadastro: ' . $e->getMessage());
        $erro = 'Ocorreu um erro. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuários</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="form-card">
        <div class="form-card__cabecalho">Cadastro de Usuário</div>

        <div class="form-card__corpo">
            <?php if ($erro): ?>
                <div class="erro"><?= $security->sanitizar($erro) ?></div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="sucesso"><?= $security->sanitizar($sucesso) ?></div>
            <?php endif; ?>

            <form method="POST" action="/cadastro">
                <?= $security->campoCsrf() ?>

                <div class="form-group">
                    <label for="nome">Nome completo</label>
                    <input type="text" id="nome" name="nome" required
                           maxlength="100" placeholder="Ex: João da Silva"
                           value="<?= $security->sanitizar($_POST['nome'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="login">Login do novo usuário</label>
                    <input type="text" id="login" name="login" required
                           maxlength="50" placeholder="Ex: joao.silva"
                           value="<?= $security->sanitizar($_POST['login'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required
                           placeholder="Mínimo 6 caracteres">
                </div>

                <div class="form-group">
                    <label for="tipo">Tipo de usuário</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Selecione...</option>
                        <option value="admin" <?= ($_POST['tipo'] ?? '') === 'admin' ? 'selected' : '' ?>>
                            Usuário Administrador
                        </option>
                        <option value="comum" <?= ($_POST['tipo'] ?? '') === 'comum' ? 'selected' : '' ?>>
                            Usuário Comum
                        </option>
                    </select>
                </div>

                <button type="submit">Cadastrar Usuário</button>
            </form>
        </div>

        <div class="form-card__rodape">
            <a href="/administradores">Voltar ao Painel Administrativo</a>
        </div>
    </div>
</body>
</html>
