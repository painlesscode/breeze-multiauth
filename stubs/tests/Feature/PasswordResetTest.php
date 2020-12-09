<?php

namespace Tests\Feature;

use App\Models\{{Name}};
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class {{Name}}PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered()
    {
        $response = $this->get('{{name}}/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested()
    {
        Notification::fake();

        ${{name}} = {{Name}}::factory()->create();

        $response = $this->post('{{name}}/forgot-password', [
            'email' => ${{name}}->email,
        ]);

        Notification::assertSentTo(${{name}}, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered()
    {
        Notification::fake();

        ${{name}} = {{Name}}::factory()->create();

        $response = $this->post('{{name}}/forgot-password', [
            'email' => ${{name}}->email,
        ]);

        Notification::assertSentTo(${{name}}, ResetPassword::class, function ($notification) {
            $response = $this->get('{{name}}/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token()
    {
        Notification::fake();

        ${{name}} = {{Name}}::factory()->create();

        $response = $this->post('{{name}}/forgot-password', [
            'email' => ${{name}}->email,
        ]);

        Notification::assertSentTo(${{name}}, ResetPassword::class, function ($notification) use (${{name}}) {
            $response = $this->post('{{name}}/reset-password', [
                'token' => $notification->token,
                'email' => ${{name}}->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}
