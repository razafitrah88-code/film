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
    <div class="w3l_sign_in_register" style="width: auto; text-align: right;">
      <ul>
        @guest
          <li><a href="#" data-toggle="modal" data-target="#myModal">Login</a></li>
        @else
          @if(Auth::user()->isAdmin())
            <li style="padding-right: 8px;">
              <a href="{{ route('admin.dashboard') }}" style="background: #212121; color: #FF8D1B; border: 1px solid #FF8D1B; padding: 6px 15px;">
                <i class="fa fa-dashboard" style="padding-right: 4px;"></i> Admin Panel
              </a>
            </li>
          @endif
          <li style="padding-right: 8px; font-weight: bold; color: #212121;">
            <i class="fa fa-user" style="color: #FF8D1B; padding-right: 4px;"></i> {{ Auth::user()->name }}
          </li>
          <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="background: #e74c3c; padding: 6px 15px;">
              Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        @endguest
      </ul>
    </div>
    <div class="clearfix"> </div>
  </div>
</div>