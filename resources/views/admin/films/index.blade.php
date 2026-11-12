@extends('templates.index')

@section('content')
<div class="general-agileits-w3l">
  <div class="container" style="padding-top: 30px; padding-bottom: 50px;">
    
    <div style="border-bottom: 2px solid #FF8D1B; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
      <div>
        <h2 style="margin: 0; color: #212121; font-weight: 700; text-transform: uppercase;">
          <span style="color: #FF8D1B;"><i class="fa fa-film"></i> Kelola</span> Film & Cast
        </h2>
        <p style="margin: 5px 0 0; color: #777;">Daftar seluruh film dan pengelolaan cast/pemeran.</p>
      </div>
      <div style="margin-top: 10px;">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-default" style="border-radius: 0; margin-right: 5px;">
          <i class="fa fa-arrow-left"></i> Dashboard
        </a>
        <a href="{{ route('admin.films.create') }}" class="btn btn-warning" style="border-radius: 0; background: #FF8D1B; border-color: #FF8D1B; color: #fff; font-weight: bold;">
          <i class="fa fa-plus"></i> Tambah Film Baru
        </a>
      </div>
    </div>

    <!-- Search Form -->
    <div class="row" style="margin-bottom: 20px;">
      <div class="col-md-6">
        <form action="{{ route('admin.films.index') }}" method="GET" class="form-inline">
          <div class="input-group" style="width: 100%;">
            <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan judul atau tahun..." value="{{ $search }}" style="border-radius: 0;">
            <span class="input-group-btn">
              <button class="btn btn-default" type="submit" style="border-radius: 0; background: #212121; color: #fff;">
                <i class="fa fa-search"></i> Cari
              </button>
              @if($search)
                <a href="{{ route('admin.films.index') }}" class="btn btn-default" style="border-radius: 0;">Reset</a>
              @endif
            </span>
          </div>
        </form>
      </div>
    </div>

    <!-- Films Table -->
    <div class="panel panel-default" style="box-shadow: 0 2px 4px rgba(0,0,0,0.06); border-radius: 2px;">
      <div class="panel-heading" style="background: #212121; color: #fff; font-weight: bold;">
        <i class="fa fa-list"></i> Daftar Film (Total: {{ $films->total() }})
      </div>
      <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
          <table class="table table-striped table-hover" style="margin-bottom: 0;">
            <thead>
              <tr>
                <th style="width: 60px;">Poster</th>
                <th>Judul Film</th>
                <th>Genre</th>
                <th>Tahun</th>
                <th>Cast / Pemeran Terdaftar</th>
                <th style="text-align: right; width: 220px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($films as $f)
                <tr>
                  <td>
                    <img src="{{ $f->poster_url }}" alt="{{ $f->title }}" style="width: 45px; height: 60px; object-fit: cover; border-radius: 2px;">
                  </td>
                  <td>
                    <strong>{{ $f->title }}</strong>
                    <div style="font-size: 12px; color: #888;">ID: #{{ $f->id }}</div>
                  </td>
                  <td><span class="label label-info">{{ $f->genre->name ?? '-' }}</span></td>
                  <td><span class="badge" style="background: #FF8D1B;">{{ $f->year }}</span></td>
                  <td>
                    @if($f->perans->count() > 0)
                      <span class="label label-success" style="margin-right: 5px;">{{ $f->perans->count() }} Cast</span>
                      <small style="color: #666;">
                        {{ $f->perans->take(3)->map(fn($p) => ($p->cast->name ?? 'Cast') . ' (' . ($p->actor ?: '-') . ')')->implode(', ') }}
                        @if($f->perans->count() > 3)...@endif
                      </small>
                    @else
                      <span class="label label-default" style="color: #888;">Belum ada cast</span>
                    @endif
                  </td>
                  <td style="text-align: right; white-space: nowrap;">
                    <a href="{{ route('admin.films.edit', $f->id) }}" class="btn btn-xs btn-warning" style="border-radius: 0;" title="Kelola Cast & Edit">
                      <i class="fa fa-pencil"></i> Kelola Cast
                    </a>
                    <a href="{{ route('movies.show', $f->id) }}" class="btn btn-xs btn-info" target="_blank" style="border-radius: 0;" title="Lihat di Web">
                      <i class="fa fa-eye"></i> Detail
                    </a>
                    <form action="{{ route('admin.films.destroy', $f->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus film ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-xs btn-danger" style="border-radius: 0;" title="Hapus Film">
                        <i class="fa fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center" style="padding: 25px; color: #888;">
                    Tidak ada film yang ditemukan.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($films->hasPages())
        <div class="panel-footer text-center" style="background: #fff;">
          {{ $films->appends(['search' => $search])->links() }}
        </div>
      @endif
    </div>

  </div>
</div>
@endsection
