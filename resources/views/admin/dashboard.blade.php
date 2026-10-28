@extends('admin.layout')

@section('admin_content')
{{-- Kartu Statistik Ringkasan --}}
<div class="row" style="margin-bottom: 25px;">
  {{-- Card 1: Total Film --}}
  <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
    <div style="background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 5px solid #FF8D1B;">
      <div style="float: right; font-size: 34px; color: #FF8D1B; opacity: 0.8;"><i class="fa fa-film"></i></div>
      <div style="font-size: 13px; color: #888; text-transform: uppercase; font-weight: bold;">Total Film</div>
      <div style="font-size: 28px; font-weight: bold; color: #212121; margin-top: 5px;">{{ $totalFilms }}</div>
      <div style="margin-top: 10px;">
        <a href="{{ route('admin.films.index') }}" style="font-size: 12px; color: #FF8D1B; font-weight: bold;">
          Kelola Film &rarr;
        </a>
      </div>
    </div>
  </div>

  {{-- Card 2: Total Genre --}}
  <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
    <div style="background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 5px solid #00bcd4;">
      <div style="float: right; font-size: 34px; color: #00bcd4; opacity: 0.8;"><i class="fa fa-tags"></i></div>
      <div style="font-size: 13px; color: #888; text-transform: uppercase; font-weight: bold;">Total Genre</div>
      <div style="font-size: 28px; font-weight: bold; color: #212121; margin-top: 5px;">{{ $totalGenres }}</div>
      <div style="margin-top: 10px;">
        <span style="font-size: 12px; color: #888;">Kategori Film</span>
      </div>
    </div>
  </div>

  {{-- Card 3: Total Pengguna --}}
  <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
    <div style="background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 5px solid #4caf50;">
      <div style="float: right; font-size: 34px; color: #4caf50; opacity: 0.8;"><i class="fa fa-users"></i></div>
      <div style="font-size: 13px; color: #888; text-transform: uppercase; font-weight: bold;">Total Pengguna</div>
      <div style="font-size: 28px; font-weight: bold; color: #212121; margin-top: 5px;">{{ $totalUsers }}</div>
      <div style="margin-top: 10px;">
        <a href="{{ route('admin.users.index') }}" style="font-size: 12px; color: #4caf50; font-weight: bold;">
          Kelola User &rarr;
        </a>
      </div>
    </div>
  </div>

  {{-- Card 4: Total Ulasan --}}
  <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
    <div style="background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-left: 5px solid #e91e63;">
      <div style="float: right; font-size: 34px; color: #e91e63; opacity: 0.8;"><i class="fa fa-comments"></i></div>
      <div style="font-size: 13px; color: #888; text-transform: uppercase; font-weight: bold;">Total Ulasan</div>
      <div style="font-size: 28px; font-weight: bold; color: #212121; margin-top: 5px;">{{ $totalKritiks }}</div>
      <div style="margin-top: 10px;">
        <a href="{{ route('admin.comments.index') }}" style="font-size: 12px; color: #e91e63; font-weight: bold;">
          Kelola Komentar &rarr;
        </a>
      </div>
    </div>
  </div>
</div>

{{-- Tombol Aksi Cepat --}}
<div style="margin-bottom: 25px;">
  <a href="{{ route('admin.films.create') }}" class="btn btn-warning" style="background: #FF8D1B; border-color: #FF8D1B; font-weight: bold;">
    <i class="fa fa-plus-circle"></i> Tambah Film Baru
  </a>
</div>

{{-- Tabel Data Pengguna & Film Terbaru --}}
<div class="row">
  {{-- Kolom Kiri: Pengguna Terbaru --}}
  <div class="col-md-6" style="margin-bottom: 25px;">
    <div style="background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
      <div style="padding: 15px 20px; background: #fafafa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
        <h4 style="margin: 0; font-size: 15px; font-weight: bold; color: #333;">
          <i class="fa fa-user-plus" style="color: #4caf50;"></i> Pengguna Baru Terdaftar
        </h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-default btn-xs">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" style="margin-bottom: 0;">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Terdaftar</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentUsers as $index => $u)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $u->name }}</strong></td>
                <td>{{ $u->email }}</td>
                <td>
                  @if($u->isAdmin())
                    <span class="label label-warning">Admin</span>
                  @else
                    <span class="label label-info">User</span>
                  @endif
                </td>
                <td><small class="text-muted">{{ $u->created_at ? $u->created_at->diffForHumans() : '-' }}</small></td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada user terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Kolom Kanan: Film Terbaru --}}
  <div class="col-md-6" style="margin-bottom: 25px;">
    <div style="background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
      <div style="padding: 15px 20px; background: #fafafa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
        <h4 style="margin: 0; font-size: 15px; font-weight: bold; color: #333;">
          <i class="fa fa-film" style="color: #FF8D1B;"></i> Film Terbaru di Katalog
        </h4>
        <a href="{{ route('admin.films.index') }}" class="btn btn-default btn-xs">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" style="margin-bottom: 0;">
          <thead>
            <tr>
              <th>No</th>
              <th>Judul</th>
              <th>Genre</th>
              <th>Tahun</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentFilms as $index => $f)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $f->title }}</strong></td>
                <td><span class="label label-default">{{ $f->genre ? $f->genre->name : '-' }}</span></td>
                <td>{{ $f->year }}</td>
                <td>
                  <a href="{{ route('admin.films.edit', $f->id) }}" class="btn btn-xs btn-primary" title="Edit Film">
                    <i class="fa fa-pencil"></i>
                  </a>
                  <a href="{{ route('movies.show', $f->id) }}" class="btn btn-xs btn-default" target="_blank" title="Lihat di Web">
                    <i class="fa fa-eye"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted">Belum ada film di katalog.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
