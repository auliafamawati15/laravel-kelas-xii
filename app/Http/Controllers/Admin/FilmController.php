<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Cast;
use App\Models\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FilmController extends Controller
{
    /**
     * Menampilkan daftar film dengan fitur pencarian dan pagination.
     */
    public function index(Request $request)
    {
        $query = Film::with('genre')->latest();

        // Fitur Pencarian berdasarkan Judul Film
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter berdasarkan Genre jika dipilih
        if ($request->filled('genre_id')) {
            $query->where('genre_id', $request->input('genre_id'));
        }

        // Tampilkan 10 film per halaman
        $films = $query->paginate(10)->withQueryString();
        $genres = Genre::orderBy('name')->get();

        return view('admin.films.index', compact('films', 'genres'));
    }

    /**
     * Menampilkan form untuk menambah film baru.
     */
    public function create()
    {
        $genres = Genre::orderBy('name')->get();
        $casts = Cast::orderBy('name')->get();
        return view('admin.films.create', compact('genres', 'casts'));
    }

    /**
     * Menyimpan film baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi data input di sisi server
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'year'     => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'durasi'   => 'nullable|integer|min:1|max:600',
            'genre_id' => 'required|exists:genres,id',
            'poster'   => 'required|image|mimes:jpeg,jpg,png,webp|max:2048', // Maksimal 2MB
        ], [
            'title.required'    => 'Judul film wajib diisi.',
            'sinopsis.required' => 'Sinopsis film wajib diisi.',
            'year.required'     => 'Tahun rilis wajib diisi.',
            'genre_id.required' => 'Silakan pilih genre film.',
            'poster.required'   => 'Poster film wajib diunggah.',
            'poster.image'      => 'File yang diunggah harus berupa gambar.',
            'poster.mimes'      => 'Format poster harus berupa JPG, JPEG, PNG, atau WEBP.',
            'poster.max'        => 'Ukuran poster maksimal 2MB (2048 KB).',
        ]);

        // 2. Upload file poster dengan nama unik
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = 'poster_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan gambar ke storage/app/public/images
            $file->storeAs('images', $filename, 'public');
            $validated['poster'] = 'storage/images/' . $filename;
        }

        // 3. Simpan data film ke database
        $film = Film::create($validated);

        // 4. Simpan data pemeran (cast) jika ada
        if ($request->has('cast_entries')) {
            foreach ($request->input('cast_entries') as $entry) {
                if (!empty($entry['cast_id'])) {
                    Peran::create([
                        'cast_id' => $entry['cast_id'],
                        'film_id' => $film->id,
                        'actor'   => $entry['character'] ?? '',
                    ]);
                }
            }
        }

        return redirect()->route('admin.films.index')
            ->with('success', 'Film baru "' . $validated['title'] . '" berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit film dengan data lama yang sudah terisi.
     */
    public function edit(Film $film)
    {
        $genres = Genre::orderBy('name')->get();
        $casts = Cast::orderBy('name')->get();
        $film->load('perans.cast');
        return view('admin.films.edit', compact('film', 'genres', 'casts'));
    }

    /**
     * Memperbarui data film di database.
     */
    public function update(Request $request, Film $film)
    {
        // 1. Validasi data input (poster bersifat opsional saat edit)
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'sinopsis' => 'required|string',
            'year'     => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'durasi'   => 'nullable|integer|min:1|max:600',
            'genre_id' => 'required|exists:genres,id',
            'poster'   => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048', // Opsional, maks 2MB
        ], [
            'title.required'    => 'Judul film wajib diisi.',
            'sinopsis.required' => 'Sinopsis film wajib diisi.',
            'year.required'     => 'Tahun rilis wajib diisi.',
            'genre_id.required' => 'Silakan pilih genre film.',
            'poster.image'      => 'File yang diunggah harus berupa gambar.',
            'poster.mimes'      => 'Format poster harus berupa JPG, JPEG, PNG, atau WEBP.',
            'poster.max'        => 'Ukuran poster maksimal 2MB (2048 KB).',
        ]);

        // 2. Cek apakah ada file poster baru yang diunggah
        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $filename = 'poster_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan gambar baru
            $file->storeAs('images', $filename, 'public');

            // Hapus file lama jika sebelumnya merupakan custom upload di storage
            if ($film->poster && str_contains($film->poster, 'storage/images/poster_')) {
                $oldPath = str_replace('storage/', '', $film->poster);
                Storage::disk('public')->delete($oldPath);
            }

            $validated['poster'] = 'storage/images/' . $filename;
        } else {
            // Gunakan poster lama jika tidak ada upload baru
            $validated['poster'] = $film->poster;
        }

        // 3. Update data film
        $film->update($validated);

        // 4. Sync data pemeran (cast): hapus lama, simpan yang baru
        DB::transaction(function () use ($film, $request) {
            // Hapus semua peran lama untuk film ini
            $film->perans()->delete();

            // Simpan peran baru jika ada
            if ($request->has('cast_entries')) {
                foreach ($request->input('cast_entries') as $entry) {
                    if (!empty($entry['cast_id'])) {
                        Peran::create([
                            'cast_id' => $entry['cast_id'],
                            'film_id' => $film->id,
                            'actor'   => $entry['character'] ?? '',
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.films.index')
            ->with('success', 'Data film "' . $film->title . '" berhasil diperbarui!');
    }

    /**
     * Menghapus film dari database beserta relasi perans dan kritiks.
     */
    public function destroy(Film $film)
    {
        $title = $film->title;

        // Gunakan Database Transaction untuk keamanan konsistensi data
        DB::transaction(function () use ($film) {
            // Hapus foreign data terkait film terlebih dahulu
            $film->perans()->delete();
            $film->kritiks()->delete();

            // Hapus file poster jika tersimpan di disk public
            if ($film->poster && str_contains($film->poster, 'storage/images/poster_')) {
                $oldPath = str_replace('storage/', '', $film->poster);
                Storage::disk('public')->delete($oldPath);
            }

            // Hapus data film
            $film->delete();
        });

        return redirect()->route('admin.films.index')
            ->with('success', 'Film "' . $title . '" beserta data terkait berhasil dihapus!');
    }
}
