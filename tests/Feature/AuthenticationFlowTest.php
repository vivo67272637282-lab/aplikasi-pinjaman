<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    public function test_guests_are_redirected_to_login_for_application_pages(): void
    {
        foreach (['/', '/karyawan', '/whatsapp'] as $uri) {
            $this->get($uri)->assertRedirect(route('login.show'));
        }
    }

    public function test_login_page_remains_accessible_to_guests(): void
    {
        $this->get(route('login.show'))->assertOk();
    }
}
