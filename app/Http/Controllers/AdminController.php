<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Genre;
use App\Models\User;
use App\Models\Kritik;

class AdminController extends Controller
{
    /**
     * Halaman Dashboard Admin
     */
    public function dashboard()
    {
        // Hitung statistik data untuk ditampilkan di dashboard
        $totalFilms   = Film::count();
        $totalGenres  = Genre::count();
        $totalUsers   = User::count();
        $totalKritiks = Kritik::count();

        // Ambil 5 user terbaru
        $recentUsers = User::with('role')->latest()->limit(5)->get();

        // Ambil 5 film terbaru
        $recentFilms = Film::with('genre')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalFilms',
            'totalGenres',
            'totalUsers',
            'totalKritiks',
            'recentUsers',
            'recentFilms'
        ));
    }
}
