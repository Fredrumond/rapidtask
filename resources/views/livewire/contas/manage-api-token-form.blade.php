<?php

use App\Models\Conta;
use App\Services\TokenService;
use Livewire\Volt\Component;

new class extends Component
{
    public Conta $conta;

    public bool $hasActiveToken = false;

    public ?string $plainTextToken = null;

    public function mount(Conta $conta, TokenService $tokenService): void
    {
        abort_unless(auth()->id() === $conta->usuario_id, 403);

        $this->conta = $conta;
        $this->hasActiveToken = $tokenService->hasActiveToken($conta);
    }

    public function generateToken(TokenService $tokenService): void
    {
        abort_unless(auth()->id() === $this->conta->usuario_id, 403);

        $token = $tokenService->issue($this->conta);

        $this->plainTextToken = $token->plainTextToken;
        $this->hasActiveToken = true;

        $this->dispatch('api-token-generated');
    }

    public function revokeToken(TokenService $tokenService): void
    {
        abort_unless(auth()->id() === $this->conta->usuario_id, 403);

        $tokenService->revoke($this->conta);

        $this->plainTextToken = null;
        $this->hasActiveToken = false;

        $this->dispatch('api-token-revoked');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('API Token') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Generate an API token for this account. Generating a new token revokes the previous one.') }}
        </p>
    </header>

    <div class="mt-6 space-y-6">
        @if ($plainTextToken)
            <div class="rounded-md bg-yellow-50 p-4">
                <p class="text-sm text-yellow-800">
                    {{ __('Copy your token now. It will not be shown again.') }}
                </p>
                <code class="mt-2 block break-all rounded bg-yellow-100 px-3 py-2 text-sm text-yellow-900">
                    {{ $plainTextToken }}
                </code>
            </div>
        @elseif ($hasActiveToken)
            <p class="text-sm text-gray-600">
                {{ __('This account has an active API token.') }}
            </p>
        @else
            <p class="text-sm text-gray-600">
                {{ __('This account does not have an active API token.') }}
            </p>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button type="button" wire:click="generateToken">
                {{ $hasActiveToken ? __('Regenerate Token') : __('Generate Token') }}
            </x-primary-button>

            @if ($hasActiveToken)
                <x-danger-button type="button" wire:click="revokeToken">
                    {{ __('Revoke Token') }}
                </x-danger-button>
            @endif

            <x-action-message class="me-3" on="api-token-generated">
                {{ __('Token generated.') }}
            </x-action-message>

            <x-action-message class="me-3" on="api-token-revoked">
                {{ __('Token revoked.') }}
            </x-action-message>
        </div>
    </div>
</section>
