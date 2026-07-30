# [CRÍTICO] Rota `/email` de debug pública com e-mail pessoal hardcoded

## Severidade
Crítico / Segurança

## Descrição
`routes/web.php` linhas 96–99: rota GET pública envia `BemVindo` para `fredrumond@gmail.com`.

## Critério de aceite
- Rota removida
- E-mails de teste apenas em ambiente local / via Artisan
