<?php
/**
 * Página de login
 * 
 */

$erro = '';

// Se já está autenticado, redireciona para a página correta
if ($security->estaAutenticado()) {
    if ($security->ehAdmin()) {
        header('Location: /administradores');
    } else {
        header('Location: /usuarios');
    }
    exit;
}

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!$security->validarCsrf()) {
            $erro = 'Requisição inválida. Tente novamente.';
        } else {
            $login = trim($_POST['login'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if (empty($login) || empty($senha)) {
                $erro = 'Preencha todos os campos.';
            } elseif ($security->autenticar($login, $senha)) {
                if ($security->ehAdmin()) {
                    header('Location: /administradores');
                } else {
                    header('Location: /usuarios');
                }
                exit;
            } else {
                $erro = 'Usuário ou senha inválidos.';
            }
        }
    } catch (Throwable $e) {
        error_log('Erro no login: ' . $e->getMessage());
        $erro = 'Ocorreu um erro. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="form-card">
        <div class="form-card__cabecalho">Login</div>

        <div class="form-card__corpo">
            <?php if ($erro): ?>
                <div class="erro"><?= $security->sanitizar($erro) ?></div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <?= $security->campoCsrf() ?>

                <div class="form-group">
                    <label for="login">Usuário</label>
                    <input type="text" id="login" name="login" required
                           autocomplete="username" maxlength="50"
                           value="<?= $security->sanitizar($_POST['login'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required
                           autocomplete="current-password">
                </div>

                <button type="submit">Entrar</button>
            </form>
        </div>

        <div class="form-card__rodape">
            Desenvolvido para o trabalho de Segurança da Informação, módulo 3
        </div>
    </div>
</body>
</html>
