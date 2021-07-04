<?php

namespace Tests\Feature;

use App\Models\{{Name}};
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {{Name}}AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('{{name}}/login');

        $response->assertStatus(200);
    }

    public function test_{{names}}_can_authenticate_using_the_login_screen()
    {
        ${{name}} = {{Name}}::factory()->create();

        $response = $this->post('{{name}}/login', [
            'email' => ${{name}}->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('{{name}}');
        $response->assertRedirect(route('{{name}}.dashboard'));
    }

    public function test_{{names}}_can_not_authenticate_with_invalid_password()
    {
        ${{name}} = {{Name}}::factory()->create();

        $this->post('{{name}}/login', [
            'email' => ${{name}}->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('{{name}}');
    }
}
