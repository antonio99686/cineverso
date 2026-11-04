# CINEVERSO — Cinema físico + streaming (MVP)

Projeto demonstrativo desenvolvido em PHP 8.1+, MySQL 8/MariaDB, JavaScript, HTML e CSS. Funciona no WampServer com Apache.

## Instalação no Windows (WampServer)

1. Copie a pasta `cineverse_project` para `C:\wamp64\www\cineverse_project`.
2. No phpMyAdmin, importe `database.sql`. Ele cria o banco `cineverso`, tabelas, 3 filmes fictícios e 2 salas.
3. Copie `config.example.php` para `config.php`, na RAIZ do projeto (fora de `public`). Ajuste MySQL e `base_url` conforme necessário.
4. Inicie Apache e MySQL. Acesse `http://localhost/cineverse_project/public/`.
5. Cadastre sua conta no site. Para tornar **somente a sua conta** administradora, execute no phpMyAdmin, trocando o e-mail:

```sql
UPDATE users SET role='admin' WHERE email='seu-email@exemplo.com';
```

6. Saia e faça login de novo. Aparecerá o botão **Admin** para cadastrar filmes, salas e sessões, confirmar reservas e liberar acesso ao streaming.

## Recursos implementados

- Site responsivo, home com busca, filtro por streaming/cinema, catálogo e detalhes.
- Cadastro/login com `password_hash`, sessão regenerada, validação CSRF, consultas preparadas.
- Upload validado de capas (JPG/PNG/WEBP/GIF), administrador gerencia filmes e salas.
- Sessões com preço e data, seleção de poltronas, assentos únicos em banco e transação para evitar venda duplicada.
- Reservas pendentes, lista de ingressos e atualização manual de status pelo admin.
- Player HTML5 para conteúdo previamente autorizado, com controle de acesso por filme e usuário.
- Favoritos por usuário.

## Limitações importantes antes de colocar em produção

- **Não existe cobrança online real nesta versão.** O admin marca pagamentos manualmente. Não habilite ingressos válidos com base em um clique de usuário.
- A demonstração mantém poltronas reservadas enquanto pedidos estão pendentes. Em produção, implemente vencimento de reservas + job para liberar assentos e conciliação de pagamentos com webhooks verificados (ex.: Mercado Pago/InfinitePay), idempotência e auditoria.
- **Streaming não está protegido contra compartilhamento de arquivo/URL.** Embora a página `/watch` exija permissão, a mídia remota pode ser aberta por URL fora do site. Para produção, use armazenamento privado, links temporários assinados, HLS/CDN e DRM se necessário. Não coloque vídeos pagos em `public/uploads` sem proteção adicional.
- Mídia audiovisual exige direitos de distribuição/exibição. Filmes de demonstração são fictícios, não incluem mídia protegida.
- Não há venda de assinaturas automática, QR code de ingressos, integração de e-mail, listas de séries/episódios, avaliações ou analytics avançado. Esses módulos ficam para versões futuras.
- Antes de publicar: HTTPS, configurações de cookies secure, proteção contra força bruta, rate limits, backups, logs, políticas LGPD, verificação de segurança e revisão de permissões de hospedagem. Bloqueie execução de scripts em uploads no Apache e configure um diretório de mídia privado para streaming.
- Use `config.php` fora do webroot. Em hospedagem, a pasta apontada pelo domínio deve ser `public/` e não a raiz.

## Observações

O seed SQL é para banco novo; não execute repetidamente porque duplicará salas/sessões. Horários usam o fuso configurado no servidor MySQL. Para implantar num cinema real, alinhe fuso de PHP e banco, preferencialmente `America/Sao_Paulo`.
