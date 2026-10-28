@extends('admin.layout')

@section('admin_content')
{{-- Filter & Pencarian Komentar --}}
<div style="background: #fff; padding: 15px 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;">
  <div class="row">
    <div class="col-md-6">
      <h4 style="margin: 5px 0 0; font-size: 16px; font-weight: bold; color: #212121;">
        <i class="fa fa-comments" style="color: #e91e63;"></i> Kelola Ulasan & Komentar Pengguna (Total: {{ $comments->total() }})
      </h4>
    </div>
    <div class="col-md-6 text-right">
      <form action="{{ route('admin.comments.index') }}" method="GET" class="form-inline">
        <div class="input-group">
          <input type="text" name="search" class="form-control" placeholder="Cari ulasan / film / nama..." value="{{ request('search') }}">
          <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i> Cari</button>
            @if(request('search'))
              <a href="{{ route('admin.comments.index') }}" class="btn btn-danger" title="Reset"><i class="fa fa-times"></i></a>
            @endif
          </span>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Tabel Komentar --}}
<div style="background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
  <div class="table-responsive">
    <table class="table table-hover table-bordered" style="margin-bottom: 0; vertical-align: middle;">
      <thead>
        <tr style="background: #fafafa;">
          <th style="width: 50px; text-align: center;">No</th>
          <th style="width: 160px;">Pengguna</th>
          <th style="width: 200px;">Judul Film</th>
          <th style="width: 80px; text-align: center;">Rating</th>
          <th>Isi Ulasan / Komentar</th>
          <th style="width: 140px; text-align: center;">Tanggal</th>
          <th style="width: 80px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($comments as $index => $c)
          <tr>
            <td style="text-align: center; vertical-align: middle;">
              {{ $comments->firstItem() + $index }}
            </td>
            <td style="vertical-align: middle;">
              @php
                $adminUserName = !empty($c->user_name) ? $c->user_name : ($c->user->name ?? 'Pengguna tidak dikenal');
              @endphp
              <strong>{{ $adminUserName }}</strong>
            </td>
            <td style="vertical-align: middle;">
              @if(!empty($c->film_title))
                <a href="{{ route('movies.show', $c->film_id) }}" target="_blank" style="color: #FF8D1B; font-weight: bold;">
                  {{ $c->film_title }}
                </a>
              @elseif($c->film)
                <a href="{{ route('movies.show', $c->film->id) }}" target="_blank" style="color: #FF8D1B; font-weight: bold;">
                  {{ $c->film->title }}
                </a>
              @else
                <span class="text-muted">Film telah dihapus</span>
              @endif
            </td>
            <td style="text-align: center; vertical-align: middle;">
              <span class="badge" style="background: #FF8D1B;"><i class="fa fa-star"></i> {{ $c->rating ?? 5 }}</span>
            </td>
            <td style="vertical-align: middle;">
              {{ $c->comment }}
            </td>
            <td style="text-align: center; vertical-align: middle;">
              <small class="text-muted">{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->locale('id')->translatedFormat('d M Y, H:i') : '-' }}</small>
            </td>
            <td style="text-align: center; vertical-align: middle;">
              <form action="{{ route('admin.comments.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus komentar ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Komentar">
                  <i class="fa fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center" style="padding: 25px; color: #888;">
              Belum ada komentar ulasan film.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="padding: 15px 20px; background: #fafafa; border-top: 1px solid #eee; text-align: center;">
    {{ $comments->links() }}
  </div>
</div>
@endsection
