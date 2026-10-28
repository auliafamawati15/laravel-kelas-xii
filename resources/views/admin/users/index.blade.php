@extends('admin.layout')

@section('admin_content')
{{-- Filter & Pencarian Pengguna --}}
<div style="background: #fff; padding: 15px 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px;">
  <div class="row">
    <div class="col-md-6">
      <h4 style="margin: 5px 0 0; font-size: 16px; font-weight: bold; color: #212121;">
        <i class="fa fa-users" style="color: #4caf50;"></i> Kelola Data Pengguna (Total: {{ $users->total() }})
      </h4>
    </div>
    <div class="col-md-6 text-right">
      <form action="{{ route('admin.users.index') }}" method="GET" class="form-inline">
        <div class="input-group">
          <input type="text" name="search" class="form-control" placeholder="Cari nama / email..." value="{{ request('search') }}">
          <span class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="fa fa-search"></i> Cari</button>
            @if(request('search'))
              <a href="{{ route('admin.users.index') }}" class="btn btn-danger" title="Reset"><i class="fa fa-times"></i></a>
            @endif
          </span>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Tabel Pengguna --}}
<div style="background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden;">
  <div class="table-responsive">
    <table class="table table-hover table-bordered" style="margin-bottom: 0; vertical-align: middle;">
      <thead>
        <tr style="background: #fafafa;">
          <th style="width: 50px; text-align: center;">No</th>
          <th>Nama Pengguna</th>
          <th>Email</th>
          <th style="width: 200px;">Peran (Role)</th>
          <th style="width: 150px; text-align: center;">Terdaftar</th>
          <th style="width: 100px; text-align: center;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $index => $user)
          <tr>
            <td style="text-align: center; vertical-align: middle;">
              {{ $users->firstItem() + $index }}
            </td>
            <td style="vertical-align: middle;">
              <strong>{{ $user->name }}</strong>
              @if($user->id === Auth::id())
                <span class="label label-success" style="margin-left: 5px;">Akun Anda</span>
              @endif
            </td>
            <td style="vertical-align: middle;">{{ $user->email }}</td>
            <td style="vertical-align: middle;">
              @if($user->id === Auth::id())
                <span class="label label-warning" style="font-size: 13px;">{{ ucfirst($user->role ? $user->role->name : 'Admin') }}</span>
              @else
                <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" style="display: flex; gap: 5px;">
                  @csrf
                  @method('PUT')
                  <select name="role_id" class="form-control input-sm" style="width: auto;">
                    @foreach($roles as $role)
                      <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ ucfirst($role->name) }}
                      </option>
                    @endforeach
                  </select>
                  <button type="submit" class="btn btn-default btn-sm" title="Simpan Perubahan Role">
                    <i class="fa fa-check"></i>
                  </button>
                </form>
              @endif
            </td>
            <td style="text-align: center; vertical-align: middle;">
              <small class="text-muted">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</small>
            </td>
            <td style="text-align: center; vertical-align: middle;">
              @if($user->id !== Auth::id())
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna {{ addslashes($user->name) }}?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm" title="Hapus User">
                    <i class="fa fa-trash"></i>
                  </button>
                </form>
              @else
                <span class="text-muted"><small>Aktif</small></span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center" style="padding: 25px; color: #888;">
              Tidak ada data pengguna ditemukan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div style="padding: 15px 20px; background: #fafafa; border-top: 1px solid #eee; text-align: center;">
    {{ $users->links() }}
  </div>
</div>
@endsection
