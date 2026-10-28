<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kritik;
use App\Models\Film;

class KritikController extends Controller
{
    /**
     * Menyimpan komentar / ulasan film baru ke database.
     * Hanya dapat diakses oleh pengguna yang sudah terautentikasi (login).
     */
    public function store(Request $request, Film $film)
    {
        // 1. Cek sesi autentikasi pengguna (Wajib Login)
        if (!Auth::check()) {
            // Jika request berupa API/AJAX, kembalikan response 401 Unauthorized
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Silakan login terlebih dahulu untuk berkomentar.'], 401);
            }

            // Simpan intended URL ke session agar setelah login dapat kembali ke halaman film
            session(['url.intended' => route('movies.show', $film->id)]);

            // Tolak request dan redirect ke halaman login dengan pesan kesalahan
            return redirect()->route('login', ['redirect_to' => route('movies.show', $film->id)])
                ->with('error', 'Silakan login terlebih dahulu untuk memberikan komentar.')
                ->with('auth_modal', true)
                ->with('auth_form', 'login');
        }

        // 2. Validasi isi komentar dan rating
        $validated = $request->validate([
            'comment' => 'required|string|min:3|max:500',
            'rating'  => 'nullable|integer|min:1|max:5',
        ], [
            'comment.required' => 'Isi komentar tidak boleh kosong.',
            'comment.min'      => 'Komentar minimal berisi 3 karakter.',
            'comment.max'      => 'Komentar maksimal 500 karakter.',
            'rating.integer'   => 'Rating harus berupa angka bulat.',
            'rating.min'       => 'Rating minimal bernilai 1 bintang.',
            'rating.max'       => 'Rating maksimal bernilai 5 bintang.',
        ]);

        // 3. Bersihkan isi komentar dari tag HTML berbahaya (mencegah serangan XSS)
        $cleanComment = strip_tags(trim($validated['comment']));

        // Jika setelah strip_tags komentar menjadi kosong (misal hanya tag HTML kosong)
        if (empty($cleanComment)) {
            return back()->withErrors(['comment' => 'Komentar tidak boleh hanya berisi tag HTML kosong.'])->withInput();
        }

        // 4. Ambil user_id dari sesi login (Auth::id()), BUKAN dari input form (mencegah manipulasi ID)
        $userId = Auth::id();

        // 5. Tentukan rating (default 5 jika tidak dipilih pengguna)
        $rating = !empty($validated['rating']) ? (int) $validated['rating'] : 5;

        // 6. Simpan komentar ke database menggunakan Eloquent (Prepared Statements otomatis)
        Kritik::create([
            'comment' => $cleanComment,
            'rating'  => $rating,
            'film_id' => $film->id,
            'user_id' => $userId,
        ]);

        return redirect()->route('movies.show', $film->id)
            ->with('success', 'Komentar dan ulasan Anda berhasil dipublikasikan!');
    }

    /**
     * Menghapus komentar film.
     * Aturan otorisasi:
     * - User biasa HANYA boleh menghapus komentarnya sendiri.
     * - Admin berhak menghapus komentar siapapun.
     */
    public function destroy(Request $request, Kritik $comment)
    {
        // 1. Cek sesi autentikasi pengguna
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Silakan login terlebih dahulu.'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek otorisasi: pemilik komentar atau admin
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $isOwner = (int) $comment->user_id === (int) $currentUser->id;
        $isAdmin = method_exists($currentUser, 'isAdmin') ? $currentUser->isAdmin() : ((int)$currentUser->role_id === 2);

        // Jika bukan pemilik dan bukan admin, tolak dengan HTTP 403 Forbidden
        if (!$isOwner && !$isAdmin) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus komentar ini.');
        }

        // Simpan ID film untuk redirect kembali
        $filmId = $comment->film_id;

        // 3. Hapus komentar dari database
        $comment->delete();

        return redirect()->route('movies.show', $filmId)
            ->with('success', 'Komentar berhasil dihapus.');
    }
}
