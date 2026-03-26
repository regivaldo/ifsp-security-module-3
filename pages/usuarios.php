<?php
/**
 * Página de acesso restrito para usuários comuns
 *
 * @var Security $security - Instância do sistema de segurança para controle de acesso e autenticação
 *
 * Restrição: apenas usuários comuns
 */
$security->exigirPapel('comum');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Usuário</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body class="bg-usuarios">
    <div class="card-painel">
        <div class="card-painel__esquerda card-painel__esquerda--verde">
            <img class="card-painel__avatar" src="https://picsum.photos/id/<?= (int) $security->getIdUsuario() * 33 ?>/256/256" alt="Foto de perfil">
            <span class="card-painel__nome"><?= $security->sanitizar($security->getNomeUsuario() ?? '') ?></span>
            <span class="card-painel__login">@<?= $security->sanitizar($security->getLoginUsuario() ?? '') ?></span>
            <a href="/logout" class="card-painel__logout">Sair</a>
        </div>
        <div class="card-painel__direita">
            <h1 class="card-painel__titulo">Área do Usuário</h1>
            <div class="alerta-info">
                <svg class="alerta-info__icone" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L1 21h22L12 2z" fill="#b8860b"/>
                    <text x="12" y="18" text-anchor="middle" fill="#fff" font-size="12" font-weight="700">!</text>
                </svg>
                <p>Bem-vindo à sua área restrita. Esta página é exclusiva para usuários comuns do sistema.</p>
            </div>
        </div>
    </div>
</body>
</html>
