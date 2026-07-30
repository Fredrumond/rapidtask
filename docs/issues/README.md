# Issues priorizadas — baseline pré-refatoração

Geradas na T01 a partir da auditoria do código Laravel 5.7.
Se o `gh` CLI estiver disponível, use os arquivos `.md` desta pasta para abrir issues no GitHub.

```bash
# Exemplo (com gh autenticado):
gh issue create --title "$(head -1 docs/issues/01-idor-multi-tenant.md | sed 's/^# //')" \
  --body-file docs/issues/01-idor-multi-tenant.md --label "security,critical"
```
