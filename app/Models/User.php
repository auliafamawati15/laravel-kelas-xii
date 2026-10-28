<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi secara massal (Mass Assignment).
     * role_id dan profile_id ditambahkan agar registrasi dapat menyimpan role dan profil user.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'profile_id',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi (misal toArray atau toJson).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Otomatis hash jika diset lewat model
        ];
    }

    /**
     * Relasi ke model Role.
     * Setiap user memiliki satu role (misal: "admin" atau "user").
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Relasi ke model Profile.
     * Setiap user memiliki satu profil data diri.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id');
    }

    /**
     * Method bantuan untuk mengecek apakah user yang sedang login adalah admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role && strtolower($this->role->name) === 'admin';
    }
}
