# IFSP \* Segurança da Informação

## Descrição do projeto

### Implemente e mostre (registre em vídeo) o funcionamento de uma "mini aplicação Web" com quatro páginas:

- Uma página de "login", acessível por qualquer usuário, autenticado ou não autenticado;
- Uma página de "cadastro de usuários", acessível apenas por usuários "administradores" autenticados;
- Uma página de "acesso restrito aos usuários administradores", acessível apenas por usuários "administradores" autenticados;
- Uma página de "acesso restrito aos usuários comuns", acessível apenas por "usuários comuns (usuários não administradores)" autenticados.

### Cada página deve ser acessível a partir de uma URL específica, via HTTPS:

- A página de "login" deve ser a página padrão da aplicação, acessível via path "/login" (ex.: https://127.0.0.1/login, https://localhost/login, etc);
- A página de "cadastro de usuários" deve ser acessível via path "/cadastro" (ex.: https://127.0.0.1/cadastro, https://localhost/cadastro, etc);
- A página de "acesso restrito aos usuários administradores" deve ser exibida assim que usuários administradores efetuarem login na aplicação, acessível via path "/administradores" (ex.: https://127.0.0.1/administradores, https://localhost/administradores, etc);
- A página de "acesso restrito aos usuários comuns" deve ser exibida assim que usuários comuns efetuarem login na aplicação, acessível via path "/usuarios" (ex.: https://127.0.0.1/usuarios, https://localhost/usuarios, etc).

### Enquanto conteúdo de cada página:

- A página de "login" deve ter uma entrada para o nome (login) do usuário, uma entrada para a senha do usuário e um botão para acesso à aplicação;
- A página de "cadastro de usuários" deve ter uma entrada para o nome (login) do usuário, uma entrada para a senha do usuário, uma entrada para seleção do tipo de usuário ("usuário administrador" ou "usuário comum") e um botão para cadastro do usuário na aplicação;
- A página de "acesso restrito aos usuários administradores" deve exibir a mensagem "Página de acesso restrito aos usuários administradores" e ter um botão/link para encerrar a sessão do usuário autenticado;
- A página de "acesso restrito aos usuários comuns" deve exibir a mensagem "Página de acesso restrito aos usuários comuns" e ter um botão/link para encerrar a sessão do usuário autenticado.

### Para a implementação da aplicação:

- Utilize o ambiente de sua preferência (ex.: linguagem de programação de sua preferência, sistema gerenciador de banco de dados de sua preferência, etc);
- Utilize mecanismos de segurança para prevenir ataques de injeção por meio das entradas fornecidas pelo usuário nas páginas de "login" e de "cadastro de usuários" da aplicação (ex.: uso de consultas parametrizadas, validações e sanitizações de entrada, etc);
- Utilize mecanismos de segurança para prevenir ataques de CSRF em todas as páginas da aplicação (ex.: uso tokens CSRF, cookies com atributo SameSite, etc);
- Utilize mecanismos de controle de acesso para prevenir que usuários não autenticados acessem as páginas de acesso restrito (páginas em "/cadastro", "/administradores" e "/usuarios"), para prevenir que usuários administradores acessem a página em "/usuarios" e para prevenir que usuários comuns acessem as páginas em "/administradores" e "/cadastro" (ex.: controle de acesso baseado em papéis, negar o acesso por padrão, etc);
- Utilize mecanismos para prevenir ataques nas sessões dos usuários (ex.: regenerar ID de sessão após login, expirar sessão após um período de inatividade, etc);
- Utilize funções de hash seguras para as senhas dos usuários (ex.: Argon2, yescrypt, etc), armazenando hashes de senhas no banco de dados;
- Utilize mecanismos para tratar todos os erros da aplicação e não exibir mensagens de erro detalhadas para o usuário (ex.: uso de blocos try/catch e similares, mensagens de erro genéricas, etc);
- Centralize a implementação de todos os mecanismos de segurança da aplicação em um "único ponto" (ex.: arquivo único, módulo ou classe única, etc).

### Para testar e registrar (gravar) o funcionamento da aplicação, nesta ordem:

1. Como um usuário não autenticado, acesse as páginas de acesso restrito da aplicação (páginas em "/cadastro", "/administradores" e "/usuarios"). O acesso deve ser negado para ambas as páginas (usuários não autenticados não devem acessar páginas restritas a usuários autenticados);
2. Efetue login na aplicação como um usuário administrador e acesse as páginas em "/cadastro", "/administradores" e "/usuarios". O acesso deve ser permitido para as páginas em "/cadastro" e "/administradores". O acesso deve ser negado para a página em "/usuarios" (usuários administradores não devem acessar a página de acesso restrito aos usuários comuns);
3. Ainda como usuário administrador, acesse a página em "/cadastro" e cadastre um usuário do tipo "usuário administrador" e um usuário do tipo "usuário comum";
4. Encerre a sessão do usuário administrador;
5. Efetue login na aplicação utilizando a conta do usuário comum criada no item 3 e acesse as páginas em "/cadastro", "/administradores" e "/usuarios". O acesso deve ser permitido para a página em "/usuarios" e negado para as páginas em "/cadastro" e "/administradores" (usuários comuns não devem acessar as páginas de acesso restrito aos usuários administradores);
6. Encerre a sessão do usuário comum;
7. Mostre o "ponto único" utilizado para implementar os mecanismos de segurança da aplicação;
8. Mostre o mecanismo de segurança para prevenir ataques de injeção;
9. Mostre o mecanismo de segurança para prevenir ataques de CSRF;
10. Mostre o mecanismo de segurança para o controle de acesso da aplicação (para prevenir que usuários não autenticados acessem as páginas em "/cadastro", "/administradores" e "/usuarios"; para prevenir que usuários administradores acessem a página em "/usuarios"; e para prevenir que usuários comuns acessem as páginas em "/cadastro" e "/administradores");
11. Mostre o mecanismo de segurança para prevenir ataques nas sessões dos usuários;
12. Mostre o mecanismo de segurança para o tratamento de erros da aplicação, incluindo a abordagem para a não exibição de mensagens de erro detalhadas para o usuário;
13. Mostre o mecanismo de segurança para gerar os hashes das senhas dos usuários, incluindo os hashes gerados para os usuários cadastrados na aplicação (mostre os hashes armazenados no banco de dados).

### Para o registro e disponibilização do vídeo:

- Utilize aplicações como OBS Studio e similares ou celulares e similares para a gravação;
- Considere 10 minutos para a duração máxima do vídeo;
- Disponibilize o vídeo via Internet (ex.: via YouTube e similares) e, se desejável, sem acesso público.

### Para a entrega da atividade avaliativa:

- Entregue o código-fonte da aplicação e o código necessário para a criação do banco de dados (ex.: script SQL);
- Entregue o link de acesso do vídeo de apresentação da aplicação. Obs.: o vídeo deve estar disponível online, como via YouTube e similares; links para o arquivo do vídeo, como via Google Drive e similares não serão aceitos.

## Inciando o projeto

```bash
php -S localhost:8000 router.php
```

## Banco de dados

Foi usado do SQLite
