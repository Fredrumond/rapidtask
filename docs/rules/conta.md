# Conta

Estado do [PR #53](https://github.com/Fredrumond/rapidtask/pull/53) (commits `47cb7eb` a `f0a4154`). A conta passa a ser a fronteira entre empresas. O token de API por conta veio depois e não faz parte destas regras.

- [Um time pertence a uma conta](#um-time-pertence-a-uma-conta)
- [O cadastro abre uma conta](#o-cadastro-abre-uma-conta)
- [O usuário opera uma conta](#o-usuário-opera-uma-conta)
- [Só o dono edita o nome da conta](#só-o-dono-edita-o-nome-da-conta)
- [Ninguém vê dados de outra conta](#ninguém-vê-dados-de-outra-conta)
- [Sem conta ativa o acesso é negado](#sem-conta-ativa-o-acesso-é-negado)

## Um time pertence a uma conta
- **Mudança:** criada
- **Antes:** o time era a única fronteira; não havia conta
- **Agora:** cada time pertence a exatamente uma conta
- **Condição:** em qualquer time da plataforma
- **Impacto:** clientes, projetos e tarefas continuam no time, e o time fica dentro da conta

## O cadastro abre uma conta
- **Mudança:** criada
- **Antes:** o cadastro criava o usuário e o time, sem conta
- **Agora:** ao se cadastrar, a pessoa ganha uma conta e entra como administradora do primeiro time
- **Condição:** no registro de um usuário novo
- **Impacto:** toda empresa nova já nasce isolada das demais

## O usuário opera uma conta
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** a pessoa trabalha na conta do time ativo; a tela não oferece troca de conta
- **Condição:** durante a navegação autenticada
- **Impacto:** quem precisa de outra empresa não troca de conta por esta tela

## Só o dono edita o nome da conta
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** apenas o dono da conta altera o nome; as demais pessoas recebem acesso negado
- **Condição:** na edição do nome da conta
- **Impacto:** o nome da empresa fica sob o controle de quem criou a conta

## Ninguém vê dados de outra conta
- **Mudança:** criada
- **Antes:** o isolamento era só por time; um vínculo indevido podia expor dados de outra empresa
- **Agora:** listagens, telas e autorizações mostram só o que é da conta ativa e dos times dos quais a pessoa faz parte
- **Condição:** em qualquer consulta ou ação no painel
- **Impacto:** duas empresas no mesmo ambiente não veem clientes, projetos, tarefas nem times uma da outra

## Sem conta ativa o acesso é negado
- **Mudança:** criada
- **Antes:** não existia
- **Agora:** sem uma conta ativa na sessão, a autorização não libera o recurso
- **Condição:** quando a sessão não tem conta definida
- **Impacto:** um acesso sem conta não consulta dados de nenhuma empresa
