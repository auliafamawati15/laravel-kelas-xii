<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    /**
     * Kolom yang boleh diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'umur',
        'bio',
        'alamat',
    ];

    /**
     * Relasi ke model User (One-to-One / One-to-Many).
     */
    public function user()
    {
        return $this->hasOne(User::class, 'profile_id');
    }
}
