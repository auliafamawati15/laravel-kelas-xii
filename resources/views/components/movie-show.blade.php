@extends('templates.index')

@push('style')
  <link rel="stylesheet" type="text/css" href="{{ asset('template/list-css/table-style.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('template/list-css/basictable.css') }}" />
  <style>
    .all-comments .media {
      margin: 0 !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    .all-comments .media:nth-child(n) {
      padding-left: 0 !important;
    }
    .all-comments .media-grids {
      margin-top: 15px !important;
    }
  </style>
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
              <a href="{{ route('movies.show', $film->id) }}"><img src="/{{ $film->poster }}" alt="" /></a>
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
                    <h4 style="margin-bottom: 15px; color: #fff; font-size: 16px; border-bottom: 2px solid #FF8D1B; padding-bottom: 8px;">
                      <i class="fa fa-users" style="color: #FF8D1B;"></i> Pemeran / Cast
                    </h4>
                    @if($film->perans->count() > 0)
                      <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                        @foreach ($film->perans as $peran)
                          @php
                            $castName = trim($peran->cast->name ?? '');
                            $words = preg_split('/\s+/', $castName);
                            if (count($words) >= 2) {
                                $initials = mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
                            } elseif (!empty($castName)) {
                                $initials = mb_strtoupper(mb_substr($castName, 0, 2));
                            } else {
                                $initials = '?';
                            }
                          @endphp
                          <div style="background: {{ $loop->even ? '#f3f4f6' : '#ffffff' }}; border-radius: 6px; padding: 12px 10px; width: 140px; text-align: center; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.06); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 4px 12px rgba(255,141,27,0.25)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)';">
                            @if($peran->cast && $peran->cast->photo)
                              <img src="{{ asset($peran->cast->photo) }}" alt="{{ e($peran->cast->name ?? '') }}" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 2px solid #FF8D1B; margin-bottom: 8px;">
                            @else
                              <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #FF8D1B, #e07b15); display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; border: 2px solid #FF8D1B; font-size: 22px; color: #fff; font-weight: bold;">
                                {{ $initials }}
                              </div>
                            @endif
                            <p style="color: #1F2937; font-size: 13px; font-weight: 600; margin: 0 0 3px; line-height: 1.3; word-break: break-word;">
                              {{ e($peran->cast->name ?? 'Unknown') }}
                            </p>
                            <p style="color: #4B5563; font-size: 11px; margin: 0; font-weight: normal; word-break: break-word;">
                              {{ e($peran->actor) }}
                            </p>
                          </div>
                        @endforeach
                      </div>
                    @else
                      <p style="color: #aaa; font-style: italic; padding: 15px 0; text-align: center;">
                        <i class="fa fa-info-circle"></i> Data pemeran belum tersedia.
                      </p>
                    @endif
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
        <div class="all-comments" style="margin-top: 30px;">
          <div class="all-comments-info">
            <h4 style="color: #111827; font-size: 18px; font-weight: bold; border-bottom: 2px solid #FF8D1B; padding-bottom: 10px; margin-bottom: 20px;">
              <i class="fa fa-comments" style="color: #FF8D1B; margin-right: 8px;"></i> Ulasan & Komentar ({{ $comments->count() }})
            </h4>

            {{-- 1. KONDISI JIKA USER BELUM LOGIN --}}
            @guest
              <div style="background: rgba(255, 141, 27, 0.08); border: 1px dashed #FF8D1B; border-radius: 8px; padding: 25px 20px; text-align: center; margin: 20px 0;">
                <div style="font-size: 36px; color: #FF8D1B; margin-bottom: 10px;">
                  <i class="fa fa-lock"></i>
                </div>
                <h4 style="color: #111827; font-weight: bold; margin-bottom: 8px; font-size: 16px;">
                  Ingin Memberikan Komentar & Rating?
                </h4>
                <p style="color: #4b5563; font-size: 14px; margin-bottom: 20px;">
                  Silakan <strong>login</strong> terlebih dahulu untuk menulis komentar atau ulasan pada film ini.
                </p>
                <div>
                  <a href="#" data-toggle="modal" data-target="#myModal" class="btn btn-warning" style="background: #FF8D1B; border-color: #FF8D1B; font-weight: bold; padding: 8px 22px; margin-right: 8px; border-radius: 4px;">
                    <i class="fa fa-sign-in"></i> Masuk (Login)
                  </a>
                  <a href="{{ route('register', ['redirect_to' => url()->current()]) }}" class="btn btn-default" style="background: #333; color: #fff; border-color: #555; font-weight: bold; padding: 8px 18px; border-radius: 4px;">
                    <i class="fa fa-user-plus"></i> Daftar Akun
                  </a>
                </div>
              </div>
            @else
              {{-- 2. KONDISI JIKA USER SUDAH LOGIN --}}
              <div class="agile-info-wthree-box" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px;">
                  <div style="color: #4b5563; font-size: 13px;">
                    <i class="fa fa-user-circle" style="color: #FF8D1B; font-size: 16px; margin-right: 5px;"></i>
                    Menulis komentar sebagai: <strong style="color: #111827;">{{ Auth::user()->name }}</strong>
                    @if(Auth::user()->isAdmin())
                      <span class="label label-warning" style="margin-left: 5px; font-size: 10px; background: #FF8D1B;">Admin</span>
                    @endif
                  </div>
                </div>

                {{-- Alert Error Validasi --}}
                @if ($errors->has('comment') || $errors->has('rating'))
                  <div class="alert alert-danger" style="border-radius: 4px; font-size: 13px; margin-bottom: 15px;">
                    <i class="fa fa-exclamation-triangle"></i> {{ $errors->first('comment') ?: $errors->first('rating') }}
                  </div>
                @endif

                <form action="{{ route('comments.store', $film->id) }}" method="POST" style="margin: 0;">
                  @csrf
                  
                  {{-- Pilihan Rating Bintang (1 - 5) --}}
                  <div style="margin-bottom: 15px;">
                    <label style="color: #374151; font-weight: 600; font-size: 13px; margin-right: 10px; display: inline-block;">
                      <i class="fa fa-star" style="color: #FF8D1B;"></i> Beri Rating Film:
                    </label>
                    <select name="rating" class="form-control" style="width: auto; display: inline-block; background: #fff; color: #d97706; border: 1px solid #d1d5db; height: 35px; border-radius: 4px; font-weight: bold; font-size: 13px;">
                      <option value="5" {{ old('rating', 5) == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 - Sangat Bagus)</option>
                      <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (4 - Bagus)</option>
                      <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>⭐⭐⭐ (3 - Cukup)</option>
                      <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>⭐⭐ (2 - Kurang)</option>
                      <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>⭐ (1 - Buruk)</option>
                    </select>
                  </div>

                  {{-- Textarea Isi Komentar --}}
                  <div style="margin-bottom: 15px;">
                    <textarea name="comment" rows="4" maxlength="500" placeholder="Tulis komentar atau ulasan Anda mengenai film ini... (Maksimal 500 karakter)" required style="width: 100%; background: #ffffff; border: 1px solid #d1d5db; border-radius: 4px; padding: 12px; color: #111827; font-size: 14px; resize: vertical; min-height: 90px; outline: none; box-sizing: border-box;">{{ old('comment') }}</textarea>
                    <small style="color: #6b7280; display: block; text-align: right; margin-top: 4px;">Maksimal 500 karakter.</small>
                  </div>

                  {{-- Tombol Kirim Komentar --}}
                  <button type="submit" class="btn btn-warning" style="background: #FF8D1B; border: none; font-weight: bold; padding: 9px 24px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s;">
                    <i class="fa fa-paper-plane"></i> Kirim Komentar
                  </button>
                </form>
              </div>
            @endguest
          </div>

          {{-- 3. DAFTAR KOMENTAR YANG SUDAH ADA (BISA DILIHAT SEMUA PENGUNJUNG) --}}
          <div class="media-grids" style="margin-top: 15px;">
            @forelse ($comments as $comment)
              @php
                // Tentukan nama user dari hasil LEFT JOIN atau relasi; gunakan fallback jika user dihapus/tidak ditemukan
                $commentUserName = !empty($comment->user_name) ? $comment->user_name : ($comment->user->name ?? 'Pengguna tidak dikenal');
                // Format waktu komentar yang mudah dibaca (contoh: "23 Okt 2026, 14:30")
                $commentDate = $comment->created_at ? \Carbon\Carbon::parse($comment->created_at)->locale('id')->translatedFormat('d M Y, H:i') : '-';
              @endphp
              <div class="media comment-item" style="background: transparent; border-bottom: 1px solid #e5e7eb; padding: 18px 0; margin: 0 !important; transition: background 0.2s;">
                <div class="media-left" style="padding-right: 15px;">
                  <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #FF8D1B, #d35400); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff; font-weight: bold; border: 2px solid #FF8D1B; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                    {{ mb_strtoupper(mb_substr($commentUserName, 0, 1)) }}
                  </div>
                </div>
                <div class="media-body">
                  <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;">
                    <div>
                      {{-- Nama Pengguna: Tebal (font-weight 700) dengan warna gelap #111827 --}}
                      <h5 style="margin: 0 0 4px 0; color: #111827; font-size: 15px; font-weight: 700;">
                        {{ $commentUserName }}
                        @if($comment->user && method_exists($comment->user, 'isAdmin') && $comment->user->isAdmin())
                          <span class="label label-warning" style="font-size: 10px; margin-left: 6px; vertical-align: middle; background: #FF8D1B;">Admin</span>
                        @endif
                      </h5>
                      <div style="margin-bottom: 8px;">
                        {{-- Tampilan Bintang Rating: Bintang terisi oranye #FF8D1B, bintang kosong #9ca3af --}}
                        <span style="font-size: 13px; margin-right: 10px;">
                          @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= $comment->rating)
                              <i class="fa fa-star" style="color: #FF8D1B;"></i>
                            @else
                              <i class="fa fa-star-o" style="color: #9ca3af;"></i>
                            @endif
                          @endfor
                        </span>
                        {{-- Waktu Komentar (format: 23 Okt 2026, 14:30) dengan warna #4b5563 dan ikon kalender #6b7280 --}}
                        <small style="color: #4b5563; font-size: 13px; font-weight: 500;">
                          <i class="fa fa-calendar-o" style="color: #6b7280; margin-right: 4px;"></i> {{ $commentDate }}
                        </small>
                      </div>
                    </div>

                    {{-- Tombol Hapus Komentar (Hanya untuk pemilik komentar atau admin) --}}
                    @if(Auth::check() && ((int)Auth::id() === (int)$comment->user_id || Auth::user()->isAdmin()))
                      <div>
                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-xs btn-danger" title="Hapus komentar ini" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; transition: 0.2s;">
                            <i class="fa fa-trash"></i> Hapus
                          </button>
                        </form>
                      </div>
                    @endif
                  </div>

                  {{-- Isi Komentar: Warna gelap #1f2937, ukuran 15px, font-weight 400, line-height 1.6 --}}
                  <p style="color: #1f2937; font-size: 15px; font-weight: 400; line-height: 1.6; margin: 6px 0 0 0; word-break: break-word;">
                    {{ $comment->comment }}
                  </p>
                </div>
              </div>
            @empty
              <div style="background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 6px; padding: 30px; text-align: center; color: #4b5563; margin-top: 15px;">
                <i class="fa fa-comment-o" style="font-size: 28px; color: #9ca3af; margin-bottom: 8px; display: block;"></i>
                Belum ada komentar untuk film ini. Jadilah yang pertama memberikan ulasan!
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
                <a href="{{ route('movies.show', $film->id) }}"><img src="/{{ $film->poster }}" alt="" /></a>
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
              <a href="{{ route('movies.show', $filmRelease->id) }}" class="hvr-shutter-out-horizontal"><img src="/{{ $filmRelease->poster }}" title="{{ $filmRelease->title }}" class="img-responsive" alt=" " />
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