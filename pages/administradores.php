<?php
/**
 * Página de acesso restrito para administradores
 *
 * @var Security $security - Instância do sistema de segurança para controle de acesso e autenticação
 *
 * Restrição: apenas administradores
 */
$security->exigirPapel('admin');
$usuarios = $security->listarUsuarios();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Administrativa</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <div class="card-painel">
        <div class="card-painel__esquerda">
            <img class="card-painel__avatar" src="https://picsum.photos/id/<?= (int) $security->getIdUsuario() * 33 ?>/256/256" alt="Foto de perfil">
            <span class="card-painel__nome"><?= $security->sanitizar($security->getNomeUsuario() ?? '') ?></span>
            <span class="card-painel__login">@<?= $security->sanitizar($security->getLoginUsuario() ?? '') ?></span>
            <a href="/logout" class="card-painel__logout">Sair</a>
        </div>
        <div class="card-painel__direita">
            <h1 class="card-painel__titulo">Área Administrativa</h1>

            <div class="card-painel__acoes">
                <a href="/cadastro">Cadastrar Usuário</a>
            </div>

            <table class="tabela-usuarios">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Login</th>
                        <th>Último Acesso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $security->sanitizar($u['nome']) ?></td>
                        <td><?= $security->sanitizar($u['login']) ?></td>
                        <td><?= $u['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acesso'])) : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
