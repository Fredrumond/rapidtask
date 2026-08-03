<?php

use App\Services\TokenService;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $hasActiveToken = false;

    public ?string $plainTextToken = null;

    public function mount(TokenService $tokenService): void
    {
        $this->hasActiveToken = $tokenService->hasActiveToken(Auth::user());
    }

    public function generateToken(TokenService $tokenService): void
    {
        $token = $tokenService->issue(Auth::user());

        $this->plainTextToken = $token->plainTextToken;
        $this->hasActiveToken = true;

        $this->dispatch('api-token-generated');
    }

    public function revokeToken(TokenService $tokenService): void
    {
        $tokenService->revoke(Auth::user());

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
            {{ __('Generate a personal access token to authenticate API requests. Generating a new token revokes the previous one.') }}
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
                {{ __('You have an active API token.') }}
            </p>
        @else
            <p class="text-sm text-gray-600">
                {{ __('You do not have an active API token.') }}
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
