# [ALTO] Uploads sem validação robusta, salvos em `public/`

## Severidade
Alto / Segurança

## Descrição
- Avatar: sem mime/tamanho (`UsuarioController`)
- Arquivos de projeto: só extensão do nome (`UploadController`) — bypass renomeando `.php` → `.pdf`
- Destino: `public/avatar` e `public/projetos/arquivos` servidos pelo nginx

## Critério de aceite
- Validação mime + tamanho
- Disco privado + rota autorizada para download
