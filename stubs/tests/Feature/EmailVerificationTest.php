<?php

namespace Tests\Feature;

use App\Models\{{Name}};
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class {{Name}}EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered()
    {
        ${{name}} = {{Name}}::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs(${{name}}, '{{name}}')->get('{{name}}/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified()
    {
        Event::fake();

        ${{name}} = {{Name}}::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            '{{name}}.verification.verify',
            now()->addMinutes(60),
            ['id' => ${{name}}->id, 'hash' => sha1(${{name}}->email)]
        );

        $response = $this->actingAs(${{name}}, '{{name}}')->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue(${{name}}->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('{{name}}.dashboard').'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash()
    {
        ${{name}} = {{Name}}::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            '{{name}}.verification.verify',
            now()->addMinutes(60),
            ['id' => ${{name}}->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs(${{name}}, '{{name}}')->get($verificationUrl);

        $this->assertFalse(${{name}}->fresh()->hasVerifiedEmail());
    }
}
