<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;
class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    public function test_the_application_returns_a_successful_response()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_if_this_works()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_if_this_admin_get_a_successful_response()
    {
        $response = $this->get('/admin/auth/login');

        $response->assertStatus(200);
    }
}
