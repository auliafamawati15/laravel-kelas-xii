<div class="modal video-modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        Sign In & Sign Up
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>						
      </div>
      <section>
        <div class="modal-body">
          <div class="w3_login_module">
            <div class="module form-module">
              <div class="toggle" title="Klik untuk beralih antara Login & Register">
                <i class="fa fa-times fa-pencil"></i>
                <div class="tooltip">Klik Beralih</div>
              </div>

              {{-- ================= FORM LOGIN ================= --}}
              <div class="form" id="loginFormSection">
                <h3>Login ke Akun Anda</h3>

                {{-- Alert Error Khusus Form Login --}}
                @if ($errors->has('login') || (session('auth_form') === 'login' && $errors->any()))
                  <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13px; padding: 10px; border-radius: 4px;">
                    <i class="fa fa-exclamation-circle"></i>
                    {{ $errors->first('login') ?: $errors->first() }}
                  </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                  @csrf
                  <input type="hidden" name="redirect_to" value="{{ old('redirect_to', request()->input('redirect_to', (url()->current() !== route('login') && url()->current() !== route('register')) ? url()->current() : '')) }}">
                  <input type="text" name="login" placeholder="Email atau Username" value="{{ old('login') }}" required autofocus autocomplete="username">
                  <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
                  
                  <div style="margin-bottom: 15px; text-align: left; font-size: 13px; color: #666;">
                    <label style="font-weight: normal; cursor: pointer;">
                      <input type="checkbox" name="remember" value="1" style="width: auto; display: inline-block; margin-right: 5px;"> Ingat saya
                    </label>
                  </div>

                  <input type="submit" value="Masuk (Login)">
                </form>
              </div>

              {{-- ================= FORM REGISTER ================= --}}
              <div class="form" id="registerFormSection">
                <h3>Buat Akun Baru</h3>

                {{-- Alert Error Khusus Form Register --}}
                @if (session('auth_form') === 'register' && $errors->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13px; padding: 10px; border-radius: 4px; text-align: left;">
                    <ul style="margin: 0; padding-left: 15px;">
                      @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                  @csrf
                  <input type="hidden" name="redirect_to" value="{{ old('redirect_to', request()->input('redirect_to', (url()->current() !== route('login') && url()->current() !== route('register')) ? url()->current() : '')) }}">
                  <input type="text" name="name" placeholder="Nama / Username" value="{{ old('name') }}" required autocomplete="name">
                  <input type="email" name="email" placeholder="Alamat Email (cth: user@gmail.com)" value="{{ old('email') }}" required autocomplete="email">
                  <input type="password" name="password" placeholder="Password (Minimal 8 karakter)" required autocomplete="new-password">
                  <input type="password" name="password_confirmation" placeholder="Ulangi Konfirmasi Password" required autocomplete="new-password">
                  
                  <input type="submit" value="Daftar (Register)">
                </form>
              </div>

              <div class="cta" style="padding: 10px 15px; font-size: 12px; background: #f9f9f9; text-align: center;">
                <span id="ctaHint">Belum punya akun? Klik tombol pensil di pojok kanan atas untuk mendaftar.</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script>
  // Fungsi animasi beralih form Login dan Register
  function toggleAuthForms() {
    $('.toggle').children('i').toggleClass('fa-pencil');
    $('.form').animate({
      height: "toggle",
      'padding-top': 'toggle',
      'padding-bottom': 'toggle',
      opacity: "toggle"
    }, "slow");
  }

  $('.toggle').click(function(){
    toggleAuthForms();
  });

  // Otomatis buka modal dan sesuaikan tab jika terjadi error validasi atau ada flash session
  $(document).ready(function(){
    @if ($errors->any() || session('auth_modal'))
      $('#myModal').modal('show');

      // Jika ada error pada register, otomatis alihkan ke form register
      @if (session('auth_form') === 'register' || $errors->has('name') || $errors->has('email') || $errors->has('password_confirmation'))
        // Beralih ke form register jika form login yang sedang aktif
        if ($('#loginFormSection').is(':visible')) {
          $('.toggle').children('i').toggleClass('fa-pencil');
          $('#loginFormSection').hide();
          $('#registerFormSection').show();
        }
      @endif
    @endif
  });
</script>