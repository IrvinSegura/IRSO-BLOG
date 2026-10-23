<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanonicalHostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'http://localhost:8000']);
    }

    public function test_other_hosts_are_redirected_to_app_url(): void
    {
        $this->get('http://127.0.0.1:8000/login?next=blog')
            ->assertRedirect('http://localhost:8000/login?next=blog');
    }

    public function test_other_ports_are_redirected_to_app_url(): void
    {
        $this->get('http://localhost:8001/register')
            ->assertRedirect('http://localhost:8000/register');
    }

    public function test_the_canonical_host_is_not_redirected(): void
    {
        $this->get('http://localhost:8000/login')->assertOk();
    }

    public function test_form_posts_are_not_redirected(): void
    {
        $response = $this->post('http://127.0.0.1:8000/login', [
            'email' => 'nadie@example.com',
            'password' => 'password',
        ]);

        $this->assertNotSame('http://localhost:8000/login', $response->headers->get('Location'));
    }
}
