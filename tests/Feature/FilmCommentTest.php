<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use Illuminate\Support\Facades\Hash;

class FilmCommentTest extends TestCase
{
    protected User $userA;
    protected User $userB;
    protected User $admin;
    protected Film $film;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Pastikan roles tersedia
        Role::firstOrCreate(['id' => 1], ['name' => 'user']);
        Role::firstOrCreate(['id' => 2], ['name' => 'admin']);

        // 2. Buat Genre & Film untuk pengujian
        $genre = Genre::firstOrCreate(['name' => 'Sci-Fi']);
        $this->film = Film::firstOrCreate(
            ['title' => 'Interstellar Test'],
            [
                'sinopsis' => 'Sinopsis film pengujian komentar...',
                'year'     => 2024,
                'durasi'   => 150,
                'genre_id' => $genre->id,
                'poster'   => 'storage/images/poster_test.jpg',
            ]
        );

        // 3. Buat User A
        $profileA = Profile::create(['umur' => 22, 'bio' => 'User A Bio', 'alamat' => 'Jakarta']);
        $this->userA = User::create([
            'name'       => 'user_a_' . uniqid(),
            'email'      => 'user_a_' . uniqid() . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 1,
            'profile_id' => $profileA->id,
        ]);

        // 4. Buat User B
        $profileB = Profile::create(['umur' => 25, 'bio' => 'User B Bio', 'alamat' => 'Surabaya']);
        $this->userB = User::create([
            'name'       => 'user_b_' . uniqid(),
            'email'      => 'user_b_' . uniqid() . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 1,
            'profile_id' => $profileB->id,
        ]);

        // 5. Buat Admin
        $profileAdmin = Profile::create(['umur' => 30, 'bio' => 'Admin Bio', 'alamat' => 'Bandung']);
        $this->admin = User::create([
            'name'       => 'admin_' . uniqid(),
            'email'      => 'admin_' . uniqid() . '@example.com',
            'password'   => Hash::make('password123'),
            'role_id'    => 2,
            'profile_id' => $profileAdmin->id,
        ]);
    }

    /**
     * Skenario (a): Tanpa login tidak bisa komentar di tampilan frontend.
     * Form disembunyikan, pesan instruksi login dan tombol login ditampilkan.
     * Komentar yang sudah ada tetap bisa dilihat oleh semua pengunjung (guest).
     */
    public function test_guest_cannot_see_comment_form_but_can_view_existing_comments(): void
    {
        // Buat komentar yang sudah ada
        Kritik::create([
            'comment' => 'Komentar publik yang sudah ada sebelumnya',
            'rating'  => 5,
            'film_id' => $this->film->id,
            'user_id' => $this->userA->id,
        ]);

        $response = $this->get("/movies/{$this->film->id}");

        $response->assertStatus(200);
        // Form input textarea komentar tidak ada untuk guest
        $response->assertDontSee('name="comment"', false);
        // Pesan login tampil
        $response->assertSee('Silakan', false);
        $response->assertSee('login', false);
        // Komentar yang sudah ada tetap bisa dilihat
        $response->assertSee('Komentar publik yang sudah ada sebelumnya');
    }

    /**
     * Skenario (b): Coba kirim komentar langsung tanpa login harus ditolak di backend (redirect ke login / 401).
     */
    public function test_guest_posting_comment_is_rejected_and_redirected_to_login(): void
    {
        $commentData = [
            'comment' => 'Komentar ilegal tanpa login',
            'rating'  => 4,
        ];

        // 1. Test via request HTTP biasa -> Ditolak dan diarahkan ke login
        $response = $this->post("/movies/{$this->film->id}/comments", $commentData);
        $response->assertRedirect(route('login', ['redirect_to' => route('movies.show', $this->film->id)]));
        $response->assertSessionHas('error');

        // Pastikan komentar TIDAK tersimpan di database
        $this->assertDatabaseMissing('kritiks', [
            'comment' => 'Komentar ilegal tanpa login',
        ]);

        // 2. Test via request JSON/API -> Ditolak dengan status 401 Unauthorized
        $responseJson = $this->postJson("/movies/{$this->film->id}/comments", $commentData);
        $responseJson->assertStatus(401);
    }

    /**
     * Skenario (c): Setelah login bisa mengirim komentar.
     * user_id komentar HARUS diambil dari sesi Auth (bukan dari input form).
     */
    public function test_authenticated_user_can_submit_comment_and_user_id_is_taken_from_session(): void
    {
        $uniqueCommentText = 'Film yang sangat menarik dan berbobot! ' . uniqid();

        // User mencoba memalsukan user_id di form (mengirimkan user_id = 999999)
        $response = $this->actingAs($this->userA)->post("/movies/{$this->film->id}/comments", [
            'comment' => $uniqueCommentText,
            'rating'  => 5,
            'user_id' => 999999, // Percobaan manipulasi ID
        ]);

        $response->assertRedirect(route('movies.show', $this->film->id));
        $response->assertSessionHas('success');

        // Pastikan komentar masuk ke database dengan user_id milik userA yang login (bukan 999999)
        $comment = Kritik::where('comment', $uniqueCommentText)->first();
        $this->assertNotNull($comment);
        $this->assertEquals($this->userA->id, $comment->user_id);
        $this->assertEquals(5, $comment->rating);
        $this->assertEquals($this->film->id, $comment->film_id);
    }

    /**
     * Validasi: Komentar kosong atau melebihi 500 karakter harus ditolak.
     */
    public function test_comment_validation_fails_for_empty_or_excessive_length(): void
    {
        // 1. Komentar kosong
        $responseEmpty = $this->actingAs($this->userA)->post("/movies/{$this->film->id}/comments", [
            'comment' => '',
            'rating'  => 5,
        ]);
        $responseEmpty->assertSessionHasErrors(['comment']);

        // 2. Komentar lebih dari 500 karakter
        $responseTooLong = $this->actingAs($this->userA)->post("/movies/{$this->film->id}/comments", [
            'comment' => str_repeat('a', 501),
            'rating'  => 5,
        ]);
        $responseTooLong->assertSessionHasErrors(['comment']);
    }

    /**
     * Sanitasi: Tag HTML berbahaya (<script>, <iframe>) harus dibersihkan.
     */
    public function test_comment_sanitizes_dangerous_html_tags(): void
    {
        $uniqueTag = uniqid();
        $xssComment = "<script>alert('hack');</script>Film {$uniqueTag} ini sangat seru!";

        $response = $this->actingAs($this->userA)->post("/movies/{$this->film->id}/comments", [
            'comment' => $xssComment,
            'rating'  => 4,
        ]);

        $response->assertRedirect(route('movies.show', $this->film->id));

        // Script tag harus sudah terhapus di database
        $this->assertDatabaseMissing('kritiks', ['comment' => $xssComment]);
        $this->assertDatabaseHas('kritiks', [
            'comment' => "alert('hack');Film {$uniqueTag} ini sangat seru!",
            'film_id' => $this->film->id,
            'user_id' => $this->userA->id,
        ]);
    }

    /**
     * Otorisasi Hapus Komentar:
     * - User hanya boleh menghapus komentarnya sendiri.
     * - User lain TIDAK boleh menghapus komentar orang lain (403 Forbidden).
     * - Admin boleh menghapus komentar siapapun.
     */
    public function test_comment_deletion_authorization(): void
    {
        // User A membuat komentar
        $commentA = Kritik::create([
            'comment' => 'Komentar eksklusif milik User A',
            'rating'  => 5,
            'film_id' => $this->film->id,
            'user_id' => $this->userA->id,
        ]);

        // 1. User B mencoba menghapus komentar User A -> Ditolak (403 Forbidden)
        $responseForbidden = $this->actingAs($this->userB)->delete("/comments/{$commentA->id}");
        $responseForbidden->assertStatus(403);
        $this->assertDatabaseHas('kritiks', ['id' => $commentA->id]);

        // 2. Admin menghapus komentar User A -> Berhasil
        $responseAdmin = $this->actingAs($this->admin)->delete("/comments/{$commentA->id}");
        $responseAdmin->assertRedirect(route('movies.show', $this->film->id));
        $this->assertDatabaseMissing('kritiks', ['id' => $commentA->id]);

        // 3. User B membuat komentar dan menghapus komentarnya sendiri -> Berhasil
        $commentB = Kritik::create([
            'comment' => 'Komentar milik User B',
            'rating'  => 4,
            'film_id' => $this->film->id,
            'user_id' => $this->userB->id,
        ]);

        $responseOwner = $this->actingAs($this->userB)->delete("/comments/{$commentB->id}");
        $responseOwner->assertRedirect(route('movies.show', $this->film->id));
        $this->assertDatabaseMissing('kritiks', ['id' => $commentB->id]);
    }

    /**
     * Fitur Tambahan: Setelah login dari tombol/halaman film, kembalikan user ke halaman film tadi (redirect back).
     */
    public function test_user_is_redirected_back_to_movie_page_after_login(): void
    {
        $movieUrl = route('movies.show', $this->film->id);

        $response = $this->post('/login', [
            'login'       => $this->userA->email,
            'password'    => 'password123',
            'redirect_to' => $movieUrl,
        ]);

        // Harus diarahkan kembali ke halaman detail film
        $response->assertRedirect($movieUrl);
        $this->assertAuthenticatedAs($this->userA);
    }

    /**
     * Test komentar menampilkan nama user dan tanggal terformat, serta diurutkan dari yang terbaru.
     */
    public function test_comments_display_user_name_and_formatted_date_ordered_by_newest(): void
    {
        // Komentar lama dari User A
        $oldComment = Kritik::forceCreate([
            'comment'    => 'Komentar paling lama',
            'rating'     => 4,
            'film_id'    => $this->film->id,
            'user_id'    => $this->userA->id,
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        // Komentar baru dari User B
        $newComment = Kritik::forceCreate([
            'comment'    => 'Komentar paling baru',
            'rating'     => 5,
            'film_id'    => $this->film->id,
            'user_id'    => $this->userB->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get("/movies/{$this->film->id}");
        $response->assertStatus(200);

        // Pastikan nama user tampil di halaman (bukan ID angka murni / "Pengguna #")
        $response->assertSee($this->userA->name);
        $response->assertSee($this->userB->name);
        $response->assertDontSee('Pengguna #' . $this->userA->id);

        // Pastikan urutan terbaru muncul lebih awal di konten HTML
        $content = $response->getContent();
        $posNew = strpos($content, 'Komentar paling baru');
        $posOld = strpos($content, 'Komentar paling lama');
        $this->assertTrue($posNew !== false && $posOld !== false && $posNew < $posOld);
    }

    /**
     * Test komentar dengan user yang tidak ditemukan/dihapus menampilkan 'Pengguna tidak dikenal'.
     */
    public function test_comment_with_deleted_or_unknown_user_shows_pengguna_tidak_dikenal(): void
    {
        // Masukkan komentar dengan user_id yang tidak ada di tabel users
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = OFF;');
        $comment = Kritik::forceCreate([
            'comment' => 'Komentar dari user yang tidak ada di database',
            'rating'  => 3,
            'film_id' => $this->film->id,
            'user_id' => 9999999, // user ID yang tidak terdaftar
        ]);
        \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = ON;');

        // Cek di halaman film publik
        $response = $this->get("/movies/{$this->film->id}");
        $response->assertStatus(200);
        $response->assertSee('Pengguna tidak dikenal');
        $response->assertSee('Komentar dari user yang tidak ada di database');

        // Cek di dashboard admin
        $adminResponse = $this->actingAs($this->admin)->get('/admin/comments');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Pengguna tidak dikenal');
    }
}
