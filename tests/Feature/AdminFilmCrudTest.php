<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class AdminFilmCrudTest extends TestCase
{
    protected User $admin;
    protected User $regularUser;
    protected Genre $genre;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Buat role jika belum ada
        Role::firstOrCreate(['id' => 1], ['name' => 'user']);
        Role::firstOrCreate(['id' => 2], ['name' => 'admin']);

        // Buat Genre
        $this->genre = Genre::firstOrCreate(['name' => 'Action']);

        // Buat Akun Admin
        $uidAdmin = uniqid('adm_');
        $adminProfile = Profile::create(['umur' => 30, 'bio' => 'Admin Bio', 'alamat' => 'Jakarta']);
        $this->admin = User::create([
            'name'       => $uidAdmin,
            'email'      => $uidAdmin . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 2,
            'profile_id' => $adminProfile->id,
        ]);

        // Buat Akun User Biasa
        $uidUser = uniqid('usr_');
        $userProfile = Profile::create(['umur' => 20, 'bio' => 'User Bio', 'alamat' => 'Bandung']);
        $this->regularUser = User::create([
            'name'       => $uidUser,
            'email'      => $uidUser . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 1,
            'profile_id' => $userProfile->id,
        ]);
    }

    /**
     * Test user non-admin tidak boleh mengakses daftar film admin (403).
     */
    public function test_non_admin_cannot_access_admin_films(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin/films');
        $response->assertStatus(403);
    }

    /**
     * Test admin dapat melihat daftar film.
     */
    public function test_admin_can_view_films_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/films');
        $response->assertStatus(200);
        $response->assertSee('Daftar Film');
    }

    /**
     * Test admin dapat menambah film baru dengan upload poster.
     */
    public function test_admin_can_create_film_with_poster_upload(): void
    {
        Storage::fake('public');

        $poster = UploadedFile::fake()->image('poster.jpg', 600, 800)->size(1500); // 1.5MB
        $uniqueTitle = 'Film Baru ' . time();

        $response = $this->actingAs($this->admin)->post('/admin/films', [
            'title'    => $uniqueTitle,
            'sinopsis' => 'Sinopsis film pengujian otomatis...',
            'year'     => 2025,
            'durasi'   => 120,
            'genre_id' => $this->genre->id,
            'poster'   => $poster,
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $response->assertSessionHas('success');

        // Pastikan film masuk ke database
        $film = Film::where('title', $uniqueTitle)->first();
        $this->assertNotNull($film);
        $this->assertEquals(2025, $film->year);
        $this->assertEquals(120, $film->durasi);
        $this->assertEquals($this->genre->id, $film->genre_id);
        $this->assertStringStartsWith('storage/images/poster_', $film->poster);
    }

    /**
     * Test validasi upload poster gagal jika ukuran melebihi 2MB.
     */
    public function test_film_creation_fails_if_poster_exceeds_2mb(): void
    {
        Storage::fake('public');

        // Gambar 3MB (> 2048 KB)
        $posterOver2MB = UploadedFile::fake()->image('large.jpg')->size(3072);

        $response = $this->actingAs($this->admin)->post('/admin/films', [
            'title'    => 'Film Gagal',
            'sinopsis' => 'Sinopsis...',
            'year'     => 2024,
            'durasi'   => 90,
            'genre_id' => $this->genre->id,
            'poster'   => $posterOver2MB,
        ]);

        $response->assertSessionHasErrors(['poster']);
    }

    /**
     * Test admin dapat mengupdate data film.
     */
    public function test_admin_can_update_film(): void
    {
        $film = Film::create([
            'title'    => 'Film Lama ' . time(),
            'sinopsis' => 'Sinopsis lama',
            'year'     => 2020,
            'durasi'   => 100,
            'genre_id' => $this->genre->id,
            'poster'   => 'storage/images/c10.jpg',
        ]);

        $newTitle = 'Judul Diperbarui ' . time();

        $response = $this->actingAs($this->admin)->put("/admin/films/{$film->id}", [
            'title'    => $newTitle,
            'sinopsis' => 'Sinopsis baru yang diedit',
            'year'     => 2023,
            'durasi'   => 135,
            'genre_id' => $this->genre->id,
            // poster tidak diisi (menggunakan poster lama)
        ]);

        $response->assertRedirect(route('admin.films.index'));
        $film->refresh();
        $this->assertEquals($newTitle, $film->title);
        $this->assertEquals(135, $film->durasi);
        $this->assertEquals(2023, $film->year);
        $this->assertEquals('storage/images/c10.jpg', $film->poster);
    }

    /**
     * Test admin dapat menghapus film.
     */
    public function test_admin_can_delete_film(): void
    {
        $film = Film::create([
            'title'    => 'Film Dihapus ' . time(),
            'sinopsis' => 'Akan segera dihapus',
            'year'     => 2021,
            'durasi'   => 95,
            'genre_id' => $this->genre->id,
            'poster'   => 'storage/images/c10.jpg',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/films/{$film->id}");
        $response->assertRedirect(route('admin.films.index'));
        $this->assertDatabaseMissing('films', ['id' => $film->id]);
    }

    /**
     * Test admin dapat mengubah role user lain.
     */
    public function test_admin_can_update_user_role(): void
    {
        $response = $this->actingAs($this->admin)->put("/admin/users/{$this->regularUser->id}/role", [
            'role_id' => 2, // ubah jadi admin
        ]);

        $response->assertSessionHas('success');
        $this->regularUser->refresh();
        $this->assertEquals(2, $this->regularUser->role_id);
    }

    /**
     * Test admin dapat melihat daftar komentar.
     */
    public function test_admin_can_view_comments_list(): void
    {
        $film = Film::first();
        if (!$film) {
            $film = Film::create([
                'title'    => 'Film Test',
                'sinopsis' => 'Sinopsis Test',
                'year'     => 2024,
                'durasi'   => 120,
                'genre_id' => $this->genre->id,
                'poster'   => 'storage/images/test.jpg',
            ]);
        }

        Kritik::create([
            'comment'  => 'Komentar pengujian untuk admin',
            'rating'   => 4,
            'film_id'  => $film->id,
            'user_id'  => $this->regularUser->id,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/comments');
        $response->assertStatus(200);
        $response->assertSee('Komentar pengujian untuk admin');
    }

    /**
     * Test admin dapat menghapus komentar.
     */
    public function test_admin_can_delete_comment(): void
    {
        $film = Film::first();
        $kritik = Kritik::create([
            'comment'  => 'Komentar spam uji coba',
            'rating'   => 3,
            'film_id'  => $film->id,
            'user_id'  => $this->regularUser->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/comments/{$kritik->id}");
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('kritiks', ['id' => $kritik->id]);
    }
}
