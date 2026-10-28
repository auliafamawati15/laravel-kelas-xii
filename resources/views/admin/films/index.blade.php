@extends('admin.layout')

@section('admin_content')
{{-- Baris Tombol Tambah & Form Filter Pencarian --}}
<div style="background: #fff; padding: 15px 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;">
  <div class="row" style="display: flex; align-items: center; flex-wrap: wrap;">
    {{-- Tombol Tambah Film --}}
    <div class="col-md-3 col-sm-12" style="margin-bottom: 10px;">
      <a href="{{ route('admin.films.create') }}" class="btn btn-warning btn-block" style="background: #FF8D1B; border-color: #FF8D1B; font-weight: bold;">
        <i class="fa fa-plus-circle"></i> Tambah Film Baru
      </a>
    </div>

    {{-- Form Pencarian & Filter Genre --}}
    <div class="col-md-9 col-sm-12" style="margin-bottom: 10px;">
      <form action="{{ route('admin.films.index') }}" method="GET" class="form-inline text-right" style="width: 100%;">
        <div class="form-group" style="margin-right: 5px;">
          <select name="genre_id" class="form-control" onchange="this.form.submit()">
            <option value="">-- Semua Genre --</option>
            @foreach($genres as $genre)
              <option value="{{ $genre->id }}" {{ request('genre_id') == $genre->id ? 'selected' : '' }}>
                {{ $genre->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="input-group">
          <input type="text" name="search" class="form-control" placeholder="Cari judul film..." value="{{ request('search') }}">
          <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i> Cari</button>
            @if(request('search') || request('genre_id'))
              <a href="{{ route('admin.films.index') }}" class="btn btn-danger" title="Reset Filter"><i class="fa fa-times"></i></a>
            @endif
          </span>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Tabel Daftar Film --}}
<div style="background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
  <div style="padding: 12px 20px; background: #fafafa; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
    <h4 style="margin: 0; font-size: 15px; font-weight: bold; color: #333;">
      <i class="fa fa-film" style="color: #FF8D1B;"></i> Daftar Film (Total: {{ $films->total() }})
    </h4>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-bordered" style="margin-bottom: 0; vertical-align: middle;">
      <thead>
        <tr style="background: #fdfdfd;">
          <th style="width: 50px; text-align: center;">No</th>
          <th style="width: 80px; text-align: center;">Poster</th>
          <th>Judul Film</th>
          <th style="width: 140px;">Genre</th>
          <th style="width: 80px; text-align: center;">Tahun</th>
          <th style="width: 100px; text-align: center;">Durasi</th>
          <th>Sinopsis Ringkas</th>
          <th style="width: 150px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($films as $index => $film)
          <tr>
            <td style="text-align: center; vertical-align: middle;">
              {{ $films->firstItem() + $index }}
            </td>
            <td style="text-align: center; vertical-align: middle;">
              <img src="{{ asset($film->poster) }}" alt="{{ $film->title }}" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
            </td>
            <td style="vertical-align: middle;">
              <strong style="color: #212121; font-size: 14px;">{{ $film->title }}</strong>
            </td>
            <td style="vertical-align: middle;">
              <span class="label label-info" style="font-size: 12px;">{{ $film->genre ? $film->genre->name : '-' }}</span>
            </td>
            <td style="text-align: center; vertical-align: middle;">
              <span class="badge" style="background: #777;">{{ $film->year }}</span>
            </td>
            <td style="text-align: center; vertical-align: middle;">
              <small class="text-muted">{{ $film->durasi ? $film->durasi . ' mnt' : '-' }}</small>
            </td>
            <td style="vertical-align: middle; max-width: 250px;">
              <small class="text-muted">{{ Str::limit($film->sinopsis, 90) }}</small>
            </td>
            <td style="text-align: center; vertical-align: middle;">
              <div class="btn-group" role="group">
                {{-- Tombol Lihat di Web Publik --}}
                <a href="{{ route('movies.show', $film->id) }}" target="_blank" class="btn btn-default btn-sm" title="Lihat Halaman Publik">
                  <i class="fa fa-eye"></i>
                </a>
                
                {{-- Tombol Edit --}}
                <a href="{{ route('admin.films.edit', $film->id) }}" class="btn btn-primary btn-sm" title="Edit Data Film">
                  <i class="fa fa-pencil"></i>
                </a>

                {{-- Tombol Hapus dengan Konfirmasi Modal / JS --}}
                <button type="button" class="btn btn-danger btn-sm" title="Hapus Film" onclick="confirmDeleteFilm('{{ $film->id }}', '{{ addslashes($film->title) }}')">
                  <i class="fa fa-trash"></i>
                </button>
              </div>

              {{-- Form Delete Tersembunyi --}}
              <form id="delete-film-form-{{ $film->id }}" action="{{ route('admin.films.destroy', $film->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center" style="padding: 30px; color: #888;">
              <i class="fa fa-folder-open-o fa-2x" style="margin-bottom: 10px;"></i><br>
              Tidak ada data film yang sesuai dengan pencarian.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Link Pagination --}}
  <div style="padding: 15px 20px; background: #fafafa; border-top: 1px solid #eee; text-align: center;">
    {{ $films->links() }}
  </div>
</div>

{{-- Script Konfirmasi Hapus Film --}}
<script>
  function confirmDeleteFilm(filmId, filmTitle) {
    if (confirm('Yakin ingin menghapus film "' + filmTitle + '"?\nData pemeran dan ulasan terkait juga akan dihapus.')) {
      document.getElementById('delete-film-form-' + filmId).submit();
    }
  }
</script>
@endsection
