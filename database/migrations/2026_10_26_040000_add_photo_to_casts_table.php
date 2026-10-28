<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk menambahkan kolom 'photo' pada tabel casts.
 * 
 * Alasan: Agar foto aktor/aktris bisa ditampilkan di halaman detail film
 * pada bagian "Pemeran / Cast". Kolom bersifat nullable karena tidak
 * semua data cast yang sudah ada memiliki foto.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casts', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
