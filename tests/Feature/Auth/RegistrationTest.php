<?php



namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $response = $this->postJson('/api/register', [
            'fullname' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'organisateur', 
            'nom_organis' => 'evenement',
        ]);

        $response->assertStatus(201); // ou 200 selon ta réponse
        $response->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }
}


// namespace Tests\Feature\Auth;

// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Tests\TestCase;

// class RegistrationTest extends TestCase
// {
//     use RefreshDatabase;

//     public function test_new_users_can_register(): void
//     {
//         $response = $this->post('/register', [
//             'name' => 'Test User',
//             'email' => 'test@example.com',
//             'password' => 'password',
//             'password_confirmation' => 'password',
//         ]);

//         $this->assertAuthenticated();
//         $response->assertNoContent();
//     }
// }