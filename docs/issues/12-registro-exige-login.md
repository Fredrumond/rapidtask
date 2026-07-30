# [ALTO] Registro público exige estar logado

## Severidade
Alto / Funcional

## Descrição
`RegisterController` aplica middleware `auth`, impedindo cadastro de novos usuários sem já ter conta.

## Critério de aceite
- Registro público (guest) funcional
- Fluxo de convite separado do registro aberto
