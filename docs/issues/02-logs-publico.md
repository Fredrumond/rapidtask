# [CRÍTICO] Visualizador de logs público sem autenticação

## Severidade
Crítico / Segurança

## Descrição
`routes/web.php` registra `Route::get('logs', LogViewerController@index)` fora do grupo `auth`. Qualquer visitante acessa os logs da aplicação em `/logs`.

## Critério de aceite
- Rota `/logs` removida em produção ou protegida por `auth` + autorização de admin
- Pacote `rap2hpoutre/laravel-log-viewer` avaliado/substituído
