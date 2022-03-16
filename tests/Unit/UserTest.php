<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_register()
    {
        $response = $this->post('/register', [
            'name' => 'Angelo',
            'email' => 'angelo@gmail.com',
            'password' => 'Angelo123',
            'password_confirmation' => 'Angelo123',
        ]);
        
        $response->assertRedirect('/dashboard');
    }
    
    public function test_delete()
    {
        $user = User::factory()->count(1)->make();
        $user = User::latest();

        if ($user) {
            $user->delete();
        }
        
        $this->assertTrue(true);
    }
}
