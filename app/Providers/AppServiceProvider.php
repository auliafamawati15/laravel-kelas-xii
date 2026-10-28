<?php

namespace App\Providers;

use App\Models\Genre;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        try {
            if (Schema::hasTable('genres')) {
                view()->share('genres', Genre::all());
            }
        } catch (\Throwable $e) {
            // Abaikan error koneksi database saat CLI/migration belum jalan
        }
    }
}
