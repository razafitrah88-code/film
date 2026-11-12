@extends('templates.index')

@section('content')
<div class="general-agileits-w3l">
  <div class="container" style="padding-top: 30px; padding-bottom: 50px;">
    
    <div style="border-bottom: 2px solid #FF8D1B; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
      <div>
        <h2 style="margin: 0; color: #212121; font-weight: 700; text-transform: uppercase;">
          <span style="color: #FF8D1B;"><i class="fa fa-plus-circle"></i> Tambah</span> Film Baru & Cast
        </h2>
        <p style="margin: 5px 0 0; color: #777;">Tambahkan data film baru serta pilih cast/pemerannya sekaligus.</p>
      </div>
      <div style="margin-top: 10px;">
        <a href="{{ route('admin.films.index') }}" class="btn btn-default" style="border-radius: 0; margin-right: 5px;">
          <i class="fa fa-arrow-left"></i> Semua Film
        </a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-default" style="border-radius: 0;">
          <i class="fa fa-dashboard"></i> Dashboard
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

    <form action="{{ route('admin.films.store') }}" method="POST">
      @csrf

      <div class="row">
        <!-- Informasi Utama Film -->
        <div class="col-md-6">
          <div class="panel panel-default" style="box-shadow: 0 2px 4px rgba(0,0,0,0.06); border-radius: 2px;">
            <div class="panel-heading" style="background: #212121; color: #fff; font-weight: bold;">
              <i class="fa fa-film"></i> 1. Informasi Film
            </div>
            <div class="panel-body">
              
              <div class="form-group">
                <label>Judul Film: <span style="color: red;">*</span></label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="Masukkan judul film" required style="border-radius: 0;">
              </div>

              <div class="form-group">
                <label>Genre: <span style="color: red;">*</span></label>
                <select name="genre_id" class="form-control" required style="border-radius: 0;">
                  <option value="">-- Pilih Genre --</option>
                  @foreach($genres as $g)
                    <option value="{{ $g->id }}" {{ old('genre_id') == $g->id ? 'selected' : '' }}>
                      {{ $g->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label>Tahun Rilis: <span style="color: red;">*</span></label>
                <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}" min="1900" max="{{ date('Y') + 5 }}" required style="border-radius: 0;">
              </div>

              <div class="form-group">
                <label>Poster (URL atau Path): <span style="color: red;">*</span></label>
                <input type="text" name="poster" class="form-control" value="{{ old('poster', 'images/m1.jpg') }}" placeholder="contoh: images/m1.jpg atau URL gambar" required style="border-radius: 0;">
              </div>

              <div class="form-group">
                <label>Sinopsis: <span style="color: red;">*</span></label>
                <textarea name="sinopsis" rows="5" class="form-control" placeholder="Tulis sinopsis film..." required style="border-radius: 0;">{{ old('sinopsis') }}</textarea>
              </div>

            </div>
          </div>
        </div>

        <!-- Pilih & Kelola Cast -->
        <div class="col-md-6">
          <div class="panel panel-default" style="box-shadow: 0 2px 4px rgba(0,0,0,0.06); border-radius: 2px; border-top: 3px solid #FF8D1B;">
            <div class="panel-heading" style="background: #212121; color: #fff; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
              <span><i class="fa fa-users"></i> 2. Pilih & Tambah Cast (Opsional)</span>
              <button type="button" id="btn-add-row" class="btn btn-xs btn-warning" style="background: #FF8D1B; border-color: #FF8D1B; color: #fff; border-radius: 0;">
                <i class="fa fa-plus"></i> Tambah Baris Cast
              </button>
            </div>
            <div class="panel-body">
              <p style="font-size: 13px; color: #777; margin-bottom: 15px;">
                Pilih cast dari database atau ketik nama cast baru jika belum ada, lalu tentukan peran/karakternya. Anda juga dapat menambahkan cast nanti di halaman edit film.
              </p>

              <div id="cast-rows-container">
                <!-- Baris Cast 1 -->
                <div class="cast-row" style="background: #fbfbfb; border: 1px solid #e5e5e5; padding: 12px; margin-bottom: 12px; border-radius: 2px; position: relative;">
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group" style="margin-bottom: 8px;">
                        <label style="font-size: 12px;">Pilih Cast yang Ada:</label>
                        <select name="casts[0][cast_id]" class="form-control input-sm" style="border-radius: 0;">
                          <option value="">-- Pilih Cast --</option>
                          @foreach($casts as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->age }} thn)</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group" style="margin-bottom: 8px;">
                        <label style="font-size: 12px;">Atau Nama Cast Baru:</label>
                        <input type="text" name="casts[0][new_cast_name]" class="form-control input-sm" placeholder="Nama aktor baru" style="border-radius: 0;">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-10">
                      <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 12px;">Nama Peran / Karakter:</label>
                        <input type="text" name="casts[0][actor]" class="form-control input-sm" placeholder="Contoh: Tokoh Utama / Detektif" style="border-radius: 0;">
                      </div>
                    </div>
                    <div class="col-sm-2 text-right" style="padding-top: 22px;">
                      <button type="button" class="btn btn-sm btn-danger btn-remove-row" style="border-radius: 0;" title="Hapus Baris Ini">
                        <i class="fa fa-times"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="text-center" style="margin-top: 10px;">
                <button type="button" id="btn-add-row-bottom" class="btn btn-sm btn-default" style="border-radius: 0;">
                  <i class="fa fa-plus"></i> Tambah Cast Lainnya
                </button>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="text-right" style="margin-top: 20px;">
        <button type="submit" class="btn btn-lg btn-warning" style="border-radius: 0; background: #FF8D1B; border-color: #FF8D1B; color: #fff; font-weight: bold; padding: 10px 40px;">
          <i class="fa fa-save"></i> Simpan Film & Cast
        </button>
      </div>

    </form>

  </div>
</div>

<script>
  $(document).ready(function() {
    var rowIndex = 1;

    function addRow() {
      var rowHtml = `
        <div class="cast-row" style="background: #fbfbfb; border: 1px solid #e5e5e5; padding: 12px; margin-bottom: 12px; border-radius: 2px; position: relative;">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group" style="margin-bottom: 8px;">
                <label style="font-size: 12px;">Pilih Cast yang Ada:</label>
                <select name="casts[` + rowIndex + `][cast_id]" class="form-control input-sm" style="border-radius: 0;">
                  <option value="">-- Pilih Cast --</option>
                  @foreach($casts as $c)
                    <option value="{{ $c->id }}">{{ addslashes($c->name) }} ({{ $c->age }} thn)</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group" style="margin-bottom: 8px;">
                <label style="font-size: 12px;">Atau Nama Cast Baru:</label>
                <input type="text" name="casts[` + rowIndex + `][new_cast_name]" class="form-control input-sm" placeholder="Nama aktor baru" style="border-radius: 0;">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-10">
              <div class="form-group" style="margin-bottom: 0;">
                <label style="font-size: 12px;">Nama Peran / Karakter:</label>
                <input type="text" name="casts[` + rowIndex + `][actor]" class="form-control input-sm" placeholder="Contoh: Tokoh Utama / Detektif" style="border-radius: 0;">
              </div>
            </div>
            <div class="col-sm-2 text-right" style="padding-top: 22px;">
              <button type="button" class="btn btn-sm btn-danger btn-remove-row" style="border-radius: 0;" title="Hapus Baris Ini">
                <i class="fa fa-times"></i>
              </button>
            </div>
          </div>
        </div>
      `;
      $('#cast-rows-container').append(rowHtml);
      rowIndex++;
    }

    $('#btn-add-row, #btn-add-row-bottom').click(function() {
      addRow();
    });

    $(document).on('click', '.btn-remove-row', function() {
      if ($('.cast-row').length > 1) {
        $(this).closest('.cast-row').remove();
      } else {
        $(this).closest('.cast-row').find('input').val('');
        $(this).closest('.cast-row').find('select').val('');
      }
    });
  });
</script>
@endsection
