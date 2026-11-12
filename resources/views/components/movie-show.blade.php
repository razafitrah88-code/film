@extends('templates.index')

@push('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('template/list-css/table-style.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('template/list-css/basictable.css') }}" />
@endpush

@push('scripts')

<script type="text/javascript" src="{{ asset('template/list-js/jquery.basictable.min.js') }}"></script>
 <script type="text/javascript">
    $(document).ready(function() {
      $('#table').basictable();
      $('#table-breakpoint').basictable({
        breakpoint: 768
      });
    });
  </script>
@endpush

@section('content')

<div class="container">
  <br />
  <!-- /w3l-medile-movies-grids -->
    <div class="agileits-single-top">
      <ol class="breadcrumb">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('movies') }}">Movies</a></li>
        <li class="active">{{ $film->title }}</li>
      </ol>
    </div>
    <div class="single-page-agile-info">
                 <!-- /movie-browse-agile -->
                         <div class="show-top-grids-w3lagile">
      <div class="col-sm-8 single-left">
        <div class="song">
          <div class="single-right-grids">

            <div class="col-md-4 single-right-grid-left">
              <a href="{{ route('movies.show', $film->id) }}"><img src="{{ $film->poster_url }}" alt="" /></a>
            </div>
            <div class="col-md-8 single-right-grid-right">
              <div class="song-info">
                <h2>{{ $film->title }}</h2>	
                <p class="author"><a href="#" class="author">{{ $film->year }}</a></p>
                <p class="views">{{ $film->sinopsis }}</p>
              </div>
              <div id="myTabContent" class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="home" aria-labelledby="home-tab">
                  <div class="agile-news-table">
                    <table id="table-breakpoint">
                      <thead>
                        <tr>
                        <th>Cast</th>
                        <th>Peran</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse ($perans as $peran)
                        <tr>
                          <td class="w3-list-info">{{ $peran->cast->name ?? ('Cast #' . $peran->cast_id) }}</td>
                          <td class="w3-list-info">{{ $peran->actor ?: '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="2" class="w3-list-info text-center" style="text-align: center; color: #888; padding: 15px;">
                            <em>Data cast belum tersedia</em>
                          </td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"> </div>
          </div>
        </div>
        <div class="song-grid-right">
          <div class="share">
            <h5>Share this</h5>
            <div class="single-agile-shar-buttons">
            <ul>
              <li>
                <div class="fb-like" data-href="https://www.facebook.com/w3layouts" data-layout="button_count" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.7";
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
              </li>
              <li>
                <div class="fb-share-button" data-href="https://www.facebook.com/w3layouts" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.facebook.com%2Fw3layouts&amp;src=sdkpreparse">Share</a></div>
              </li>
              <li class="news-twitter">
                <a href="https://twitter.com/w3layouts" class="twitter-follow-button" data-show-count="false">Follow @w3layouts</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
              </li>
              <li>
                <a href="https://twitter.com/intent/tweet?screen_name=w3layouts" class="twitter-mention-button" data-show-count="false">Tweet to @w3layouts</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
              </li>
              <li>
                <!-- Place this tag where you want the +1 button to render. -->
                <div class="g-plusone" data-size="medium"></div>

                <!-- Place this tag after the last +1 button tag. -->
                <script type="text/javascript">
                  (function() {
                  var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                  po.src = 'https://apis.google.com/js/platform.js';
                  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();
                </script>
              </li>
            </ul>
          </div>
          </div>
        </div>
        <div class="clearfix"> </div>
        <div class="all-comments">
          <div class="all-comments-info">
            <a href="#">Comments ({{ $comments->count() }})</a>

            @auth
              <!-- Form Komentar Hanya untuk User yang Login -->
              <div class="agile-info-wthree-box">
                <p style="font-size: 13px; color: #666; margin-bottom: 5px;">
                  Berkomentar sebagai: <strong>{{ Auth::user()->name }}</strong>
                </p>

                @if ($errors->has('comment'))
                  <div class="alert alert-danger" style="padding: 8px 15px; margin-bottom: 10px; border-radius: 0; font-size: 13px; width: 80%;">
                    <i class="fa fa-exclamation-circle"></i> {{ $errors->first('comment') }}
                  </div>
                @endif

                <form action="{{ route('comments.store', $film->id) }}" method="POST">
                  @csrf
                  <textarea name="comment" placeholder="Tulis komentar Anda di sini... (maks. 500 karakter)" maxlength="500" required>{{ old('comment') }}</textarea>
                  <input type="submit" value="KIRIM KOMENTAR">
                  <div class="clearfix"> </div>
                </form>
              </div>
            @else
              <!-- Pengunjung Belum Login: Pesan & Tombol Login untuk Berkomentar -->
              <div class="agile-info-wthree-box" style="background: #fbfbfb; border: 1px dashed #d9d9d9; padding: 20px; width: 80%; margin: 15px 0;">
                <p style="font-size: 14px; color: #555; margin-bottom: 12px;">
                  <i class="fa fa-lock" style="color: #ff8d1b;"></i> Anda harus login terlebih dahulu untuk dapat mengirim komentar pada film ini.
                </p>
                <a href="{{ route('login', ['redirect' => url()->current()]) }}" data-toggle="modal" data-target="#myModal" class="btn btn-warning" style="background: #ff8d1b; border: none; color: #fff; border-radius: 0; padding: 8px 20px; font-weight: bold; display: inline-block;">
                  <i class="fa fa-sign-in"></i> Login untuk berkomentar
                </a>
              </div>
            @endauth
          </div>

          <div class="media-grids">
            @forelse ($comments as $comment)
              <div class="media">
                <h5>
                  <i class="fa fa-user" style="color: #ff8d1b; font-size: 13px;"></i>
                  {{ $comment->user->name ?? ('User #' . $comment->user_id) }}
                  @if($comment->created_at)
                    <small style="font-size: 11px; color: #999; margin-left: 8px;">
                      {{ $comment->created_at->diffForHumans() }}
                    </small>
                  @endif
                </h5>
                <div class="media-left">
                  <a href="#">
                    <img src="{{ asset('storage/images/user.jpg') }}" title="One movies" alt=" " />
                  </a>
                </div>
                <div class="media-body">
                  <p>{{ $comment->comment }}</p>
                </div>
              </div>
            @empty
              <div style="padding: 20px 0; color: #888;">
                <em>Belum ada komentar untuk film ini. Jadilah yang pertama memberikan komentar!</em>
              </div>
            @endforelse
          </div>
        </div>
      </div>
      <div class="col-md-4 single-right">
        <h3>Similar Film By Genre</h3>
        @foreach ($filmByGenre as $film)
          <div class="single-grid-right">
            <div class="single-right-grids">
              <div class="col-md-4 single-right-grid-left">
                <a href="{{ route('movies.show', $film->id) }}"><img src="{{ $film->poster_url }}" alt="" /></a>
              </div>
              <div class="col-md-8 single-right-grid-right">
                <a href="{{ route('movies.show', $film->id) }}" class="title">{{ $film->title }}</a>
                <p class="author"><a href="#" class="author">{{ $film->year }}</a></p>
                <p class="views" style="text-align: justify; margin-right: 10px">{{ $film->sinopsis }}</p>
              </div>
              <div class="clearfix"> </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="clearfix"> </div>
    </div>
      <!-- //movie-browse-agile -->
      <!--body wrapper start-->
    <div class="w3_agile_banner_bottom_grid">
      <div id="owl-demo" class="owl-carousel owl-theme">
        @foreach ($filmByRelease as $filmRelease)
          <div class="item">
            <div class="w3l-movie-gride-agile w3l-movie-gride-agile1">
              <a href="{{ route('movies.show', $filmRelease->id) }}" class="hvr-shutter-out-horizontal"><img src="{{ $filmRelease->poster_url }}" title="{{ $filmRelease->title }}" class="img-responsive" alt=" " />
                <div class="w3l-action-icon"><i class="fa fa-play-circle" aria-hidden="true"></i></div>
              </a>
              <div class="mid-1 agileits_w3layouts_mid_1_home">
                <div class="w3l-movie-text">
                  <h6><a href="{{ route('movies.show', $filmRelease->id) }}">{{ $filmRelease->title }}</a></h6>							
                </div>
                <div class="mid-2 agile_mid_2_home">
                  <p>{{ $filmRelease->year }}</p>
                  <div class="block-stars">
                    <ul class="w3l-ratings">
                      <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                      <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                      <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                      <li><a href="#"><i class="fa fa-star" aria-hidden="true"></i></a></li>
                      <li><a href="#"><i class="fa fa-star-half-o" aria-hidden="true"></i></a></li>
                    </ul>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </div>
              @if ($filmRelease->year == now()->year)
                <div class="ribben">
                  <p>NEW</p>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  <!--body wrapper end-->
          
             
      </div>
      <!-- //w3l-latest-movies-grids -->
    </div>	
  </div>
@endsection

@push('scripts')
<script src="js/simplePlayer.js"></script>
<script>
	$("document").ready(function() {
		$("#video").simplePlayer();
	});
</script>
@endpush