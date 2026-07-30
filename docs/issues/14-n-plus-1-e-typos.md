# [MÉDIO] N+1 em `detalheProjeto` e tipografia em models

- `ProjetosController@detalheProjeto`: 5 queries separadas sobre tarefas do mesmo projeto
- Typo `'satus'` em `TimeMembroController` ao criar usuário via convite
- Typo `identficacao` em `$fillable` de `Log` (coluna real: `identificacao`)
- `public/js/projeto-detalhe.js` órfão (typo de `projeto-detalhes.js`)

## Critério de aceite
Eager loading no detalhe; typos corrigidos; asset órfão removido.
