@extends('admin.layout')

@section('admin_content')
<div style="background: #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 25px;">
  {{-- Header Form --}}
  <div style="border-bottom: 2px solid #FF8D1B; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    <div>
      <h3 style="margin: 0; font-size: 20px; font-weight: bold; color: #212121;">
        <i class="fa fa-pencil-square-o" style="color: #FF8D1B;"></i> Edit Data Film: "{{ $film->title }}"
      </h3>
      <p style="margin: 4px 0 0; color: #777; font-size: 13px;">
        Perbarui informasi film di bawah ini
      </p>
    </div>
    <a href="{{ route('admin.films.index') }}" class="btn btn-default btn-sm">
      <i class="fa fa-arrow-left"></i> Kembali ke Daftar Film
    </a>
  </div>

  {{-- Alert Error Jika Validasi Gagal --}}
  @if($errors->any())
    <div class="alert alert-danger" style="border-radius: 4px;">
      <i class="fa fa-exclamation-triangle"></i> <strong>Terdapat kesalahan pengisian data:</strong>
      <ul style="margin: 5px 0 0; padding-left: 20px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Formulir Edit Film --}}
  <form action="{{ route('admin.films.update', $film->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
      {{-- Kolom Kiri: Informasi Teks Film --}}
      <div class="col-md-8">
        {{-- Judul Film --}}
        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
          <label for="title" style="font-weight: bold;">Judul Film <span class="text-danger">*</span></label>
          <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Interstellar" value="{{ old('title', $film->title) }}" required>
          @if($errors->has('title'))
            <span class="help-block">{{ $errors->first('title') }}</span>
          @endif
        </div>

        <div class="row">
          {{-- Genre Film --}}
          <div class="col-md-4">
            <div class="form-group {{ $errors->has('genre_id') ? 'has-error' : '' }}">
              <label for="genre_id" style="font-weight: bold;">Genre <span class="text-danger">*</span></label>
              <select name="genre_id" id="genre_id" class="form-control" required>
                <option value="">-- Pilih Genre --</option>
                @foreach($genres as $genre)
                  <option value="{{ $genre->id }}" {{ old('genre_id', $film->genre_id) == $genre->id ? 'selected' : '' }}>
                    {{ $genre->name }}
                  </option>
                @endforeach
              </select>
              @if($errors->has('genre_id'))
                <span class="help-block">{{ $errors->first('genre_id') }}</span>
              @endif
            </div>
          </div>

          {{-- Tahun Rilis --}}
          <div class="col-md-4">
            <div class="form-group {{ $errors->has('year') ? 'has-error' : '' }}">
              <label for="year" style="font-weight: bold;">Tahun Rilis <span class="text-danger">*</span></label>
              <input type="number" name="year" id="year" class="form-control" placeholder="Contoh: 2024" value="{{ old('year', $film->year) }}" min="1900" max="{{ date('Y') + 5 }}" required>
              @if($errors->has('year'))
                <span class="help-block">{{ $errors->first('year') }}</span>
              @endif
            </div>
          </div>

          {{-- Durasi Film (Menit) --}}
          <div class="col-md-4">
            <div class="form-group {{ $errors->has('durasi') ? 'has-error' : '' }}">
              <label for="durasi" style="font-weight: bold;">Durasi (Menit)</label>
              <input type="number" name="durasi" id="durasi" class="form-control" placeholder="Contoh: 120" value="{{ old('durasi', $film->durasi) }}" min="1" max="600">
              @if($errors->has('durasi'))
                <span class="help-block">{{ $errors->first('durasi') }}</span>
              @endif
            </div>
          </div>
        </div>

        {{-- Sinopsis Film --}}
        <div class="form-group {{ $errors->has('sinopsis') ? 'has-error' : '' }}">
          <label for="sinopsis" style="font-weight: bold;">Sinopsis / Alur Cerita <span class="text-danger">*</span></label>
          <textarea name="sinopsis" id="sinopsis" rows="6" class="form-control" placeholder="Tuliskan deskripsi ringkas mengenai alur cerita film ini..." required>{{ old('sinopsis', $film->sinopsis) }}</textarea>
          @if($errors->has('sinopsis'))
            <span class="help-block">{{ $errors->first('sinopsis') }}</span>
          @endif
        </div>
      </div>

      {{-- Kolom Kanan: Upload Poster Film & Pratinjau --}}
      <div class="col-md-4">
        <div class="form-group {{ $errors->has('poster') ? 'has-error' : '' }}" style="background: #fafafa; border: 1px dashed #ccc; padding: 20px; border-radius: 6px; text-align: center;">
          <label for="poster" style="font-weight: bold; display: block; margin-bottom: 10px;">
            Poster Film (Opsional saat Edit)
          </label>

          {{-- Pratinjau Poster Saat Ini / Baru --}}
          <div style="margin-bottom: 15px;">
            <img id="posterPreview" src="{{ asset($film->poster) }}" alt="{{ $film->title }}" style="max-width: 160px; height: 220px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); border: 2px solid #fff;">
          </div>

          <input type="file" name="poster" id="posterInput" class="form-control" accept="image/jpeg,image/png,image/webp" onchange="previewPoster(this)">
          <small class="text-muted" style="display: block; margin-top: 8px;">
            Kosongkan jika tidak ingin mengubah poster.<br>
            Format: <strong>JPG, PNG, WEBP</strong> (Maks: <strong>2 MB</strong>)
          </small>

          @if($errors->has('poster'))
            <span class="help-block text-danger" style="margin-top: 8px;">{{ $errors->first('poster') }}</span>
          @endif
        </div>
      </div>
    </div>

    {{-- Bagian Pemeran / Cast --}}
    <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
      <h4 style="font-weight: bold; color: #212121; margin-bottom: 15px;">
        <i class="fa fa-users" style="color: #FF8D1B;"></i> Pemeran / Cast
      </h4>
      <p style="color: #777; font-size: 13px; margin-bottom: 15px;">
        Kelola pemeran film beserta nama karakter/peran yang dimainkan.
      </p>

      <div id="cast-entries-container">
        {{-- Entri cast akan diisi secara dinamis via JS --}}
      </div>

      <button type="button" class="btn btn-default btn-sm" onclick="addCastEntry()" style="margin-top: 10px;">
        <i class="fa fa-plus"></i> Tambah Pemeran
      </button>
    </div>

    {{-- Tombol Submit & Batal --}}
    <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
      <button type="submit" class="btn btn-warning btn-lg" style="background: #FF8D1B; border-color: #FF8D1B; font-weight: bold; min-width: 150px;">
        <i class="fa fa-save"></i> Perbarui Film
      </button>
      <a href="{{ route('admin.films.index') }}" class="btn btn-default btn-lg" style="margin-left: 10px;">
        Batal
      </a>
    </div>
  </form>
</div>

{{-- JavaScript untuk Pratinjau Gambar Langsung & Kelola Pemeran --}}
<script>
  function previewPoster(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('posterPreview').src = e.target.result;
      }
      reader.readAsDataURL(input.files[0]);
    }
  }

  // Data cast dari server untuk dropdown
  var availableCasts = @json($casts);
  var castEntryIndex = 0;

  function addCastEntry(castId, character) {
    castId = castId || '';
    character = character || '';
    var container = document.getElementById('cast-entries-container');
    var entryDiv = document.createElement('div');
    entryDiv.className = 'cast-entry-row';
    entryDiv.style.cssText = 'display: flex; align-items: center; gap: 10px; margin-bottom: 8px; padding: 10px; background: #f9f9f9; border-radius: 4px; border: 1px solid #eee;';

    var selectOptions = '<option value="">-- Pilih Pemeran --</option>';
    for (var i = 0; i < availableCasts.length; i++) {
      var selected = (availableCasts[i].id == castId) ? ' selected' : '';
      selectOptions += '<option value="' + availableCasts[i].id + '"' + selected + '>' + availableCasts[i].name + '</option>';
    }

    entryDiv.innerHTML =
      '<div style="flex: 1;">' +
        '<label style="font-size: 12px; color: #555; display: block; margin-bottom: 3px;">Nama Pemeran</label>' +
        '<select name="cast_entries[' + castEntryIndex + '][cast_id]" class="form-control" style="font-size: 13px;">' +
          selectOptions +
        '</select>' +
      '</div>' +
      '<div style="flex: 1;">' +
        '<label style="font-size: 12px; color: #555; display: block; margin-bottom: 3px;">Karakter / Peran</label>' +
        '<input type="text" name="cast_entries[' + castEntryIndex + '][character]" class="form-control" placeholder="Contoh: Dokter, Polisi" style="font-size: 13px;" value="' + character.replace(/"/g, '&quot;') + '">' +
      '</div>' +
      '<div style="padding-top: 18px;">' +
        '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'.cast-entry-row\').remove();" title="Hapus Pemeran">' +
          '<i class="fa fa-trash"></i>' +
        '</button>' +
      '</div>';

    container.appendChild(entryDiv);
    castEntryIndex++;
  }

  // Muat pemeran yang sudah ada pada saat halaman dimuat
  var existingPerans = @json($film->perans);
  for (var i = 0; i < existingPerans.length; i++) {
    addCastEntry(existingPerans[i].cast_id, existingPerans[i].actor);
  }
</script>
@endsection
