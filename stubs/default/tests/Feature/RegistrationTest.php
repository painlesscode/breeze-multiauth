<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {{Name}}RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('{{name}}/register');

        $response->assertStatus(200);
    }

    public function test_new_{{names}}_can_register()
    {
        $response = $this->post('{{name}}/register', [
            'name' => 'Test {{Name}}',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated('{{name}}');
        $response->assertRedirect(route('{{name}}.dashboard'));
    }
}
