<?php

namespace Tests\Feature;

use App\Models\{{Name}};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {{Name}}PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered()
    {
        ${{name}} = {{Name}}::factory()->create();

        $response = $this->actingAs(${{name}}, '{{name}}')->get('{{name}}/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed()
    {
        ${{name}} = {{Name}}::factory()->create();

        $response = $this->actingAs(${{name}}, '{{name}}')->post('{{name}}/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password()
    {
        ${{name}} = {{Name}}::factory()->create();

        $response = $this->actingAs(${{name}}, '{{name}}')->post('{{name}}/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
