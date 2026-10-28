<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Pastikan role default tersedia
        Role::firstOrCreate(['id' => 1], ['name' => 'user']);
        Role::firstOrCreate(['id' => 2], ['name' => 'admin']);
    }

    /**
     * Test registrasi berhasil dengan data valid.
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $uniqueName = 'testuser_' . time();
        $uniqueEmail = 'testuser_' . time() . '@example.com';

        $response = $this->post('/register', [
            'name'                  => $uniqueName,
            'email'                 => $uniqueEmail,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        // Cek data di database
        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals($uniqueName, $user->name);
        $this->assertEquals(1, $user->role_id); // Default role adalah "user"
        $this->assertNotNull($user->profile_id); // Profile otomatis dibuat
        $this->assertTrue(Hash::check('password123', $user->password)); // Password ter-hash
    }

    /**
     * Test registrasi gagal jika password kurang dari 8 karakter atau konfirmasi tidak cocok.
     */
    public function test_registration_fails_with_invalid_password(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'invaliduser',
            'email'                 => 'invalid@example.com',
            'password'              => '123',
            'password_confirmation' => '456',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * Test login gagal dengan password salah dan menampilkan pesan error umum.
     */
    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->post('/login', [
            'login'    => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    /**
     * Test user biasa login dan di-redirect ke halaman utama (home).
     */
    public function test_regular_user_login_redirects_to_home(): void
    {
        $profile = Profile::create(['umur' => 20, 'bio' => 'tester', 'alamat' => 'Jakarta']);
        $user = User::create([
            'name'       => 'regular_' . time(),
            'email'      => 'regular_' . time() . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->post('/login', [
            'login'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $response->assertSessionHas('user_role', 'user');
    }

    /**
     * Test admin login dan di-redirect ke dashboard admin.
     */
    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $profile = Profile::create(['umur' => 30, 'bio' => 'admin bio', 'alamat' => 'Bandung']);
        $admin = User::create([
            'name'       => 'admin_' . time(),
            'email'      => 'admin_' . time() . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 2, // admin
            'profile_id' => $profile->id,
        ]);

        $response = $this->post('/login', [
            'login'    => $admin->name, // test login pakai username
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $response->assertSessionHas('user_role', 'admin');
    }

    /**
     * Test user biasa tidak bisa mengakses dashboard admin (Forbidden 403).
     */
    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $profile = Profile::create(['umur' => 20, 'bio' => 'tester', 'alamat' => 'Jakarta']);
        $user = User::create([
            'name'       => 'blocked_' . time(),
            'email'      => 'blocked_' . time() . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test admin dapat mengakses dashboard admin.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $profile = Profile::create(['umur' => 30, 'bio' => 'admin bio', 'alamat' => 'Bandung']);
        $admin = User::create([
            'name'       => 'allowed_admin_' . time(),
            'email'      => 'allowed_admin_' . time() . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 2,
            'profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Panel Kontrol');
    }

    /**
     * Test logout berhasil menghapus session.
     */
    public function test_user_can_logout(): void
    {
        $profile = Profile::create(['umur' => 20, 'bio' => 'tester', 'alamat' => 'Jakarta']);
        $user = User::create([
            'name'       => 'logout_' . time(),
            'email'      => 'logout_' . time() . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 1,
            'profile_id' => $profile->id,
        ]);

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
