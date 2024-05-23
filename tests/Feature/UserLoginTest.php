<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    
    use RefreshDatabase;

    public function test_login_with_valid_phone()
    {
        $response = $this->postJson('/app/v1/api/user/login', ['phone' => '0912345678']);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);
    }

    public function test_login_with_invalid_phone()
    {
        $response = $this->postJson('/app/v1/api/user/login', ['phone' => '0812345678']);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    public function test_login_with_short_phone()
    {
        $response = $this->postJson('/app/v1/api/user/login', ['phone' => '091234567']);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                 ]);
    }

    public function test_login_with_long_phone()
    {
        $response = $this->postJson('/app/v1/api/user/login', ['phone' => '091234567890']);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                 ]);
    }
}
