<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response
            ->assertOk()
            ->assertSeeVolt('profile.update-profile-information-form')
            ->assertSeeVolt('profile.update-password-form')
            ->assertSeeVolt('profile.manage-api-token-form')
            ->assertSeeVolt('profile.delete-user-form');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->call('updateProfileInformation');

        $component
            ->assertHasNoErrors()
            ->assertNoRedirect();

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', 'Test User')
            ->set('email', $user->email)
            ->call('updateProfileInformation');

        $component
            ->assertHasNoErrors()
            ->assertNoRedirect();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertSoftDeleted($user);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser');

        $component
            ->assertHasErrors('password')
            ->assertNoRedirect();

        $this->assertNotNull($user->fresh());
    }

    public function test_user_can_generate_api_token_from_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.manage-api-token-form')
            ->call('generateToken');

        $component
            ->assertHasNoErrors()
            ->assertSet('hasActiveToken', true)
            ->assertSet('plainTextToken', fn (?string $token): bool => is_string($token) && $token !== '');

        $this->assertSame(1, $user->tokens()->count());
    }

    public function test_user_can_revoke_api_token_from_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('profile.manage-api-token-form')
            ->call('generateToken');

        $component = Volt::test('profile.manage-api-token-form')
            ->call('revokeToken');

        $component
            ->assertHasNoErrors()
            ->assertSet('hasActiveToken', false)
            ->assertSet('plainTextToken', null);

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_plain_text_token_is_not_persisted_after_component_reload(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Volt::test('profile.manage-api-token-form')
            ->call('generateToken');

        $component = Volt::test('profile.manage-api-token-form');

        $component
            ->assertSet('hasActiveToken', true)
            ->assertSet('plainTextToken', null);
    }
}
