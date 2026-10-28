<div class="header">
  <div class="container">
    <div class="w3layouts_logo">
      <a href="{{ route('home') }}"><h1>One<span>Movies</span></h1></a>
    </div>
    <div class="w3_search">
      <form action="#" method="post">
        <input type="text" name="Search" placeholder="Search" required="">
        <input type="submit" value="Go">
      </form>
    </div>
    <div class="w3l_sign_in_register">
      <ul>
        @guest
          {{-- Tampilan untuk Pengunjung yang Belum Login --}}
          <li><i class="fa fa-user-circle-o" aria-hidden="true" style="color: #FF8D1B;"></i> Guest</li>
          <li><a href="#" data-toggle="modal" data-target="#myModal"><i class="fa fa-sign-in"></i> Login / Register</a></li>
        @else
          {{-- Tampilan untuk Pengguna yang Sudah Login --}}
          <li style="color: #fff; padding-right: 10px;">
            <i class="fa fa-user" aria-hidden="true" style="color: #FF8D1B;"></i>
            Halo, <strong>{{ Auth::user()->name }}</strong>
            @if(Auth::user()->isAdmin())
              <span class="label label-warning" style="margin-left: 5px; font-size: 11px; padding: 2px 6px;">Admin</span>
            @else
              <span class="label label-info" style="margin-left: 5px; font-size: 11px; padding: 2px 6px;">User</span>
            @endif
          </li>

          {{-- Tombol Dashboard Khusus Admin --}}
          @if(Auth::user()->isAdmin())
            <li>
              <a href="{{ route('admin.dashboard') }}" style="color: #FF8D1B; font-weight: bold;">
                <i class="fa fa-tachometer"></i> Dashboard
              </a>
            </li>
          @endif

          {{-- Tombol Logout Aman dengan Form POST & CSRF --}}
          <li>
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-header-form').submit();"
               style="color: #ff5252;">
              <i class="fa fa-sign-out"></i> Logout
            </a>
            <form id="logout-header-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        @endguest
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>