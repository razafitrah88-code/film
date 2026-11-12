<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_register_validation_requires_all_fields(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password'], null, 'register');
    }

    public function test_register_validation_rejects_invalid_email_and_short_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'John',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['email', 'password'], null, 'register');
    }

    public function test_register_validation_rejects_password_confirmation_mismatch(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john.mismatch@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors(['password'], null, 'register');
    }

    public function test_register_validation_rejects_duplicate_email(): void
    {
        $email = 'dup_' . uniqid() . '@example.com';
        User::create([
            'name' => 'Existing User',
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->post('/register', [
            'name' => 'Another User',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email'], null, 'register');
    }

    public function test_register_creates_user_with_hashed_password_and_default_user_role(): void
    {
        $uniqueEmail = 'newuser_' . time() . '@example.com';

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => $uniqueEmail,
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role);
        $this->assertNotEquals('secret12345', $user->password);
        $this->assertTrue(Hash::check('secret12345', $user->password));
    }

    public function test_login_validation_requires_credentials(): void
    {
        $response = $this->post('/login', [
            'login' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['login', 'password'], null, 'login');
    }

    public function test_login_with_invalid_credentials_returns_generic_error(): void
    {
        $response = $this->post('/login', [
            'login' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['login'], null, 'login');
        $this->assertGuest();
    }

    public function test_user_login_redirects_to_home(): void
    {
        $email = 'regular_' . time() . '@example.com';
        User::create([
            'name' => 'Regular Member',
            'email' => $email,
            'password' => Hash::make('memberpass123'),
            'role' => 'user',
            'role_id' => 1,
        ]);

        $response = $this->post('/login', [
            'login' => $email,
            'password' => 'memberpass123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $adminEmail = 'admin_' . time() . '@example.com';
        User::create([
            'name' => 'Super Admin',
            'email' => $adminEmail,
            'password' => Hash::make('adminpass123'),
            'role' => 'admin',
            'role_id' => 2,
        ]);

        $response = $this->post('/login', [
            'login' => $adminEmail,
            'password' => 'adminpass123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Normal User',
            'email' => 'normal_' . time() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'role_id' => 1,
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin Boss',
            'email' => 'boss_' . time() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'role_id' => 2,
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Admin');
        $response->assertSee('Dashboard');
    }

    public function test_logout_works_and_invalidates_session(): void
    {
        $user = User::create([
            'name' => 'Logout User',
            'email' => 'logout_' . time() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_home_page_shows_login_button_for_guest(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('data-target="#myModal"', false);
        $response->assertSee('Login');
        $response->assertSee('Create an account');
    }

    public function test_home_page_shows_user_profile_and_logout_when_authenticated(): void
    {
        $user = User::create([
            'name' => 'Siti Aisyah',
            'email' => 'siti_' . time() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Siti Aisyah');
        $response->assertSee('Logout');
        $response->assertDontSee('data-target="#myModal"', false);
    }

    public function test_admin_sees_admin_panel_button_in_header(): void
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin_utama_' . time() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'role_id' => 2,
        ]);

        $response = $this->actingAs($admin)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Admin Utama');
        $response->assertSee('Admin Panel');
        $response->assertSee(route('admin.dashboard'));
    }
}

