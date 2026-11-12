@extends('templates.index')

@section('content')
<div class="general-agileits-w3l">
  <div class="container" style="padding-top: 30px; padding-bottom: 50px;">
    
    <div style="border-bottom: 2px solid #FF8D1B; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
      <div>
        <h2 style="margin: 0; color: #212121; font-weight: 700; text-transform: uppercase;">
          <span style="color: #FF8D1B;"><i class="fa fa-users"></i> Kelola Cast & Film:</span> {{ $film->title }}
        </h2>
        <p style="margin: 5px 0 0; color: #777;">Atur daftar pemeran/cast dan data film.</p>
      </div>
      <div style="margin-top: 10px;">
        <a href="{{ route('admin.films.index') }}" class="btn btn-default" style="border-radius: 0; margin-right: 5px;">
          <i class="fa fa-arrow-left"></i> Semua Film
        </a>
        <a href="{{ route('movies.show', $film->id) }}" target="_blank" class="btn btn-info" style="border-radius: 0;">
          <i class="fa fa-eye"></i> Lihat di Web
        </a>
      </div>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
        <ul style="margin: 0; padding-left: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="row">
      <!-- Kolom Kiri: Kelola Cast & Pemeran (FOKUS UTAMA TUGAS) -->
      <div class="col-md-7">
        <div class="panel panel-default" style="box-shadow: 0 2px 4px rgba(0,0,0,0.06); border-radius: 2px; border-top: 3px solid #FF8D1B;">
          <div class="panel-heading" style="background: #212121; color: #fff; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa fa-users"></i> Daftar Cast / Pemeran Film</span>
            <span class="badge" style="background: #FF8D1B;">{{ $film->perans->count() }} Cast</span>
          </div>
          <div class="panel-body">
            
            <!-- Tabel Cast Saat Ini -->
            <form action="{{ route('admin.films.update', $film->id) }}" method="POST">
              @csrf
              @method('PUT')
              <!-- Hidden fields to keep film data intact when saving roles -->
              <input type="hidden" name="title" value="{{ $film->title }}">
              <input type="hidden" name="genre_id" value="{{ $film->genre_id }}">
              <input type="hidden" name="year" value="{{ $film->year }}">
              <input type="hidden" name="poster" value="{{ $film->poster }}">
              <input type="hidden" name="sinopsis" value="{{ $film->sinopsis }}">

              <div class="table-responsive">
                <table class="table table-bordered table-striped" style="margin-bottom: 15px;">
                  <thead style="background: #f7f7f7;">
                    <tr>
                      <th style="width: 35%;">Nama Cast</th>
                      <th style="width: 45%;">Peran / Karakter</th>
                      <th style="width: 20%; text-align: center;">Hapus</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($film->perans as $peran)
                      <tr>
                        <td>
                          <strong>{{ $peran->cast->name ?? ('Cast #' . $peran->cast_id) }}</strong>
                          @if($peran->cast)
                            <div style="font-size: 11px; color: #888;">Usia: {{ $peran->cast->age ?? '-' }} thn</div>
                          @endif
                        </td>
                        <td>
                          <div class="input-group input-group-sm">
                            <input type="text" name="existing_roles[{{ $peran->id }}]" value="{{ $peran->actor }}" class="form-control" style="border-radius: 0;" placeholder="Peran/karakter">
                          </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                          <button type="button" class="btn btn-xs btn-danger" style="border-radius: 0;" onclick="if(confirm('Hapus cast ini dari film?')) { document.getElementById('delete-cast-{{ $peran->id }}').submit(); }">
                            <i class="fa fa-trash"></i> Hapus
                          </button>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="text-center" style="padding: 20px; color: #888;">
                          <i class="fa fa-info-circle"></i> <em>Data cast belum tersedia untuk film ini.</em>
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              @if($film->perans->count() > 0)
                <div style="margin-bottom: 25px; text-align: right;">
                  <button type="submit" class="btn btn-sm btn-default" style="border-radius: 0;">
                    <i class="fa fa-save"></i> Simpan Perubahan Peran
                  </button>
                </div>
              @endif
            </form>

            <!-- Form Hidden Hapus Cast per item -->
            @foreach($film->perans as $peran)
              <form id="delete-cast-{{ $peran->id }}" action="{{ route('admin.films.cast.remove', [$film->id, $peran->id]) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
              </form>
            @endforeach

            <hr style="margin: 20px 0;">

            <!-- Form Tambah Cast ke Film -->
            <div style="background: #fbfbfb; border: 1px solid #eee; padding: 15px; border-radius: 2px;">
              <h4 style="margin: 0 0 15px; font-size: 15px; font-weight: bold; color: #212121;">
                <i class="fa fa-plus-circle" style="color: #FF8D1B;"></i> Tambah Cast ke Film Ini
              </h4>

              <form action="{{ route('admin.films.cast.add', $film->id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                  <label style="font-size: 13px; font-weight: 600;">1. Pilih Cast yang Sudah Ada:</label>
                  <select name="cast_id" class="form-control" style="border-radius: 0;">
                    <option value="">-- Pilih Cast dari Database (Total: {{ $casts->count() }}) --</option>
                    @foreach($casts as $c)
                      <option value="{{ $c->id }}">{{ $c->name }} (Usia: {{ $c->age }} thn)</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group">
                  <label style="font-size: 13px; font-weight: 600;">ATAU Masukkan Nama Cast Baru (Jika Belum Terdaftar):</label>
                  <input type="text" name="new_cast_name" class="form-control" placeholder="Contoh: Reza Rahadian" style="border-radius: 0;">
                  <p class="help-block" style="font-size: 11px; margin-bottom: 0;">Kosongkan jika Anda sudah memilih cast dari dropdown di atas.</p>
                </div>

                <div class="form-group">
                  <label style="font-size: 13px; font-weight: 600;">2. Peran / Karakter dalam Film ini: <span style="color: red;">*</span></label>
                  <input type="text" name="actor" class="form-control" placeholder="Contoh: Tokoh Utama / Detektif James" required style="border-radius: 0;">
                </div>

                <button type="submit" class="btn btn-warning" style="border-radius: 0; background: #FF8D1B; border-color: #FF8D1B; color: #fff; font-weight: bold; width: 100%;">
                  <i class="fa fa-plus"></i> Tambahkan Cast ke Film
                </button>
              </form>
            </div>

          </div>
        </div>
      </div>

      <!-- Kolom Kanan: Edit Informasi Film -->
      <div class="col-md-5">
        <div class="panel panel-default" style="box-shadow: 0 2px 4px rgba(0,0,0,0.06); border-radius: 2px;">
          <div class="panel-heading" style="background: #212121; color: #fff; font-weight: bold;">
            <i class="fa fa-pencil-square-o"></i> Edit Informasi Film
          </div>
          <div class="panel-body">
            
            <form action="{{ route('admin.films.update', $film->id) }}" method="POST">
              @csrf
              @method('PUT')

              <div class="form-group">
                <label>Judul Film: <span style="color: red;">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $film->title) }}" required style="border-radius: 0;">
              </div>

              <div class="form-group">
                <label>Genre: <span style="color: red;">*</span></label>
                <select name="genre_id" class="form-control" required style="border-radius: 0;">
                  @foreach($genres as $g)
                    <option value="{{ $g->id }}" {{ (old('genre_id', $film->genre_id) == $g->id) ? 'selected' : '' }}>
                      {{ $g->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>Tahun Rilis: <span style="color: red;">*</span></label>
                <input type="number" name="year" class="form-control" value="{{ old('year', $film->year) }}" min="1900" max="{{ date('Y') + 5 }}" required style="border-radius: 0;">
              </div>

              <div class="form-group">
                <label>Poster (URL atau Path): <span style="color: red;">*</span></label>
                <input type="text" name="poster" class="form-control" value="{{ old('poster', $film->poster) }}" required style="border-radius: 0;">
                <div style="margin-top: 10px;">
                  <img src="{{ $film->poster_url }}" alt="{{ $film->title }}" style="max-height: 120px; border-radius: 2px; border: 1px solid #ddd;">
                </div>
              </div>

              <div class="form-group">
                <label>Sinopsis: <span style="color: red;">*</span></label>
                <textarea name="sinopsis" rows="5" class="form-control" required style="border-radius: 0;">{{ old('sinopsis', $film->sinopsis) }}</textarea>
              </div>

              <button type="submit" class="btn btn-primary" style="border-radius: 0; background: #212121; border-color: #212121; width: 100%; font-weight: bold;">
                <i class="fa fa-save"></i> Simpan Informasi Film
              </button>
            </form>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
