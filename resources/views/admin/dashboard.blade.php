@extends('templates.index')

@section('content')
<div class="general-agileits-w3l">
  <div class="container" style="padding-top: 30px; padding-bottom: 50px;">
    
    <div style="border-bottom: 2px solid #FF8D1B; padding-bottom: 15px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
      <div>
        <h2 style="margin: 0; color: #212121; font-weight: 700; text-transform: uppercase;">
          <span style="color: #FF8D1B;"><i class="fa fa-dashboard"></i> Admin</span> Dashboard
        </h2>
        <p style="margin: 5px 0 0; color: #777;">Selamat datang, <strong>{{ Auth::user()->name }}</strong> (Role: <span class="label label-danger">Admin</span>)</p>
      </div>
      <div>
        <a href="{{ route('home') }}" class="btn btn-default" style="border-radius: 0; border: 1px solid #ddd;">
          <i class="fa fa-home"></i> Kembali ke Beranda
        </a>
      </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row" style="margin-bottom: 30px;">
      <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
        <div class="panel panel-default" style="border-left: 4px solid #FF8D1B; border-radius: 2px; box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
          <div class="panel-body" style="padding: 20px;">
            <div class="row">
              <div class="col-xs-8">
                <div style="font-size: 28px; font-weight: bold; color: #212121;">{{ $totalFilms }}</div>
                <div style="color: #888; text-transform: uppercase; font-size: 12px; font-weight: 600;">Total Film</div>
              </div>
              <div class="col-xs-4 text-right">
                <i class="fa fa-film" style="font-size: 36px; color: #FF8D1B;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
        <div class="panel panel-default" style="border-left: 4px solid #2196F3; border-radius: 2px; box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
          <div class="panel-body" style="padding: 20px;">
            <div class="row">
              <div class="col-xs-8">
                <div style="font-size: 28px; font-weight: bold; color: #212121;">{{ $totalGenres }}</div>
                <div style="color: #888; text-transform: uppercase; font-size: 12px; font-weight: 600;">Total Genre</div>
              </div>
              <div class="col-xs-4 text-right">
                <i class="fa fa-tags" style="font-size: 36px; color: #2196F3;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
        <div class="panel panel-default" style="border-left: 4px solid #4CAF50; border-radius: 2px; box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
          <div class="panel-body" style="padding: 20px;">
            <div class="row">
              <div class="col-xs-8">
                <div style="font-size: 28px; font-weight: bold; color: #212121;">{{ $totalUsers }}</div>
                <div style="color: #888; text-transform: uppercase; font-size: 12px; font-weight: 600;">Total Pengguna</div>
              </div>
              <div class="col-xs-4 text-right">
                <i class="fa fa-users" style="font-size: 36px; color: #4CAF50;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-3 col-sm-6" style="margin-bottom: 15px;">
        <div class="panel panel-default" style="border-left: 4px solid #E91E63; border-radius: 2px; box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
          <div class="panel-body" style="padding: 20px;">
            <div class="row">
              <div class="col-xs-8">
                <div style="font-size: 28px; font-weight: bold; color: #212121;">{{ $totalKritik }}</div>
                <div style="color: #888; text-transform: uppercase; font-size: 12px; font-weight: 600;">Total Ulasan</div>
              </div>
              <div class="col-xs-4 text-right">
                <i class="fa fa-comments" style="font-size: 36px; color: #E91E63;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
      <div class="col-md-7">
        <div class="panel panel-default" style="box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
          <div class="panel-heading" style="background: #212121; color: #fff; font-weight: bold;">
            <i class="fa fa-users"></i> Daftar Pengguna Terbaru
          </div>
          <div class="panel-body" style="padding: 0;">
            <div class="table-responsive">
              <table class="table table-striped table-hover" style="margin-bottom: 0;">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($recentUsers as $u)
                    <tr>
                      <td>{{ $u->id }}</td>
                      <td><strong>{{ $u->name }}</strong></td>
                      <td>{{ $u->email }}</td>
                      <td>
                        @if($u->isAdmin())
                          <span class="label label-danger">admin</span>
                        @else
                          <span class="label label-info">user</span>
                        @endif
                      </td>
                      <td>{{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center">Belum ada pengguna.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="panel panel-default" style="box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
          <div class="panel-heading" style="background: #212121; color: #fff; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="fa fa-film"></i> Film Terbaru</span>
            <div>
              <a href="{{ route('admin.films.create') }}" class="btn btn-xs btn-warning" style="background: #FF8D1B; border-color: #FF8D1B; color: #fff; border-radius: 0;">
                <i class="fa fa-plus"></i> Tambah Film
              </a>
              <a href="{{ route('admin.films.index') }}" class="btn btn-xs btn-default" style="border-radius: 0;">
                Semua Film
              </a>
            </div>
          </div>
          <div class="panel-body" style="padding: 0;">
            <div class="table-responsive">
              <table class="table table-striped table-hover" style="margin-bottom: 0;">
                <thead>
                  <tr>
                    <th>Poster</th>
                    <th>Judul</th>
                    <th>Cast</th>
                    <th style="text-align: right;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($recentFilms as $f)
                    <tr>
                      <td style="width: 45px;">
                        <img src="{{ $f->poster_url }}" alt="{{ $f->title }}" style="width: 36px; height: 50px; object-fit: cover; border-radius: 2px;">
                      </td>
                      <td>
                        <a href="{{ route('movies.show', $f->id) }}" style="color: #212121; font-weight: 600;">
                          {{ $f->title }}
                        </a>
                        <div style="font-size: 11px; color: #888;">{{ $f->year }}</div>
                      </td>
                      <td>
                        <span class="badge" style="background: #5cb85c; font-size: 11px;">{{ $f->perans->count() }} Cast</span>
                      </td>
                      <td style="text-align: right; white-space: nowrap;">
                        <a href="{{ route('admin.films.edit', $f->id) }}" class="btn btn-xs btn-warning" style="border-radius: 0;" title="Kelola Cast & Edit">
                          <i class="fa fa-users"></i> Kelola Cast
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" class="text-center">Belum ada film.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
