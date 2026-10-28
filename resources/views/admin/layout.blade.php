@extends('templates.index')

@section('content')
<div class="general_social_icons"></div>

<div class="admin-wrapper" style="padding: 25px 0 50px; background: #fdfdfd; min-height: 700px;">
  <div class="container">
    {{-- Header Admin & Navigasi Tab --}}
    <div style="background: #ffffff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 15px 20px; margin-bottom: 25px; border-top: 4px solid #FF8D1B;">
      <div class="row" style="display: flex; align-items: center; flex-wrap: wrap;">
        <div class="col-md-6 col-sm-12">
          <h3 style="margin: 0; font-size: 22px; font-weight: bold; color: #212121;">
            <i class="fa fa-tachometer" style="color: #FF8D1B; margin-right: 5px;"></i> Panel Kontrol Administrator
          </h3>
          <p style="margin: 4px 0 0; font-size: 13px; color: #777;">
            Kelola data film, kategori, pengguna, dan ulasan OneMovies
          </p>
        </div>
        <div class="col-md-6 col-sm-12 text-right" style="margin-top: 10px;">
          <a href="{{ route('home') }}" class="btn btn-default btn-sm" target="_blank">
            <i class="fa fa-external-link"></i> Kunjungi Website Utama
          </a>
        </div>
      </div>

      {{-- Navigasi Menu Admin --}}
      <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px;">
        <ul class="nav nav-pills" style="margin-bottom: 0;">
          <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" style="{{ request()->routeIs('admin.dashboard') ? 'background: #FF8D1B;' : '' }}">
              <i class="fa fa-dashboard"></i> Dashboard
            </a>
          </li>
          <li class="{{ request()->routeIs('admin.films.*') ? 'active' : '' }}">
            <a href="{{ route('admin.films.index') }}" style="{{ request()->routeIs('admin.films.*') ? 'background: #FF8D1B;' : '' }}">
              <i class="fa fa-film"></i> Kelola Film
            </a>
          </li>
          <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}" style="{{ request()->routeIs('admin.users.*') ? 'background: #FF8D1B;' : '' }}">
              <i class="fa fa-users"></i> Kelola User
            </a>
          </li>
          <li class="{{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
            <a href="{{ route('admin.comments.index') }}" style="{{ request()->routeIs('admin.comments.*') ? 'background: #FF8D1B;' : '' }}">
              <i class="fa fa-comments"></i> Kelola Komentar
            </a>
          </li>
        </ul>
      </div>
    </div>

    {{-- Konten Dinamis Halaman Admin --}}
    @yield('admin_content')

  </div>
</div>
@endsection
