<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_invalid_credentials_return_a_validation_error_instead_of_an_internal_error(): void
    {
        $response = $this->from(route('login'))->post('/admin', [
            'email' => 'inexistente@sorteos.local',
            'password' => 'credencial-invalida',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }
}
