<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kritik;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Menampilkan daftar semua komentar / ulasan film.
     */
    public function index(Request $request)
    {
        // Query komentar menggunakan LEFT JOIN ke tabel users dan films
        // Mengambil nama user secara langsung, diurutkan dari yang terbaru,
        // dan komentar tidak hilang meskipun user telah dihapus
        $query = Kritik::leftJoin('users', 'kritiks.user_id', '=', 'users.id')
            ->leftJoin('films', 'kritiks.film_id', '=', 'films.id')
            ->with(['user', 'film'])
            ->select(
                'kritiks.*',
                'users.name as user_name',
                'films.title as film_title'
            )
            ->orderBy('kritiks.created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('kritiks.comment', 'like', "%{$search}%")
                  ->orWhere('users.name', 'like', "%{$search}%")
                  ->orWhere('films.title', 'like', "%{$search}%");
            });
        }

        $comments = $query->paginate(15)->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    /**
     * Menghapus ulasan / komentar.
     */
    public function destroy(Kritik $comment)
    {
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus!');
    }
}
