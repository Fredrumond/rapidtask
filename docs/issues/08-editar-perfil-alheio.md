# [ALTO] Usuário edita perfil de outro via `usuario_id` no request

## Severidade
Alto / Segurança

## Descrição
`UsuarioController@update` faz `User::find($request->usuario_id)` sem garantir `Auth::id()`.

## Critério de aceite
- Atualização sempre no usuário autenticado
- Teste cobrindo tentativa de alterar outro ID
