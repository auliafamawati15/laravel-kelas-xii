<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cast extends Model
{
    use HasFactory;
    protected $table = 'casts';
    protected $fillable = ['name', 'age', 'bio', 'photo'];

    /**
     * Relasi ke model Peran (semua peran yang dimainkan cast ini).
     */
    public function perans()
    {
        return $this->hasMany(Peran::class, 'cast_id');
    }

    /**
     * Relasi many-to-many ke Film melalui tabel perans.
     */
    public function films()
    {
        return $this->belongsToMany(Film::class, 'perans', 'cast_id', 'film_id')
                    ->withPivot('actor');
    }
}
