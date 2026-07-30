<x-mail::message>
# Olá, {{ $convite->nome }}!

Você foi convidado(a) para participar de um time no RapidTask.

<x-mail::button :url="$aceitarUrl">
Aceitar convite
</x-mail::button>

Este link expira em 7 dias. Se você ainda não tem conta, cadastre-se com este e-mail antes de aceitar.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
