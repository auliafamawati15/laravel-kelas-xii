<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $table = 'films';
    
    /**
     * Kolom yang dapat diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'title',
        'sinopsis',
        'year',
        'durasi',
        'poster',
        'genre_id',
    ];

    /**
     * Relasi ke model Genre (Setiap film memiliki 1 genre).
     */
    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genre_id');
    }

    /**
     * Relasi ke model Kritik / Ulasan komentar film.
     */
    public function kritiks()
    {
        return $this->hasMany(Kritik::class, 'film_id');
    }

    /**
     * Relasi ke model Peran (Pemeran/Cast dalam film).
     */
    public function perans()
    {
        return $this->hasMany(Peran::class, 'film_id');
    }

    /**
     * Relasi many-to-many ke Cast melalui tabel perans.
     * Menggunakan withPivot untuk mendapatkan nama karakter (actor).
     * 
     * Catatan: Menggunakan nama method `castMembers()` bukan `casts()`
     * karena `casts()` adalah method bawaan Laravel 11+ untuk Eloquent attribute casting.
     */
    public function castMembers()
    {
        return $this->belongsToMany(Cast::class, 'perans', 'film_id', 'cast_id')
                    ->withPivot('actor');
    }
}
