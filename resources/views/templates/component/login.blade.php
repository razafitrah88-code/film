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
              <div class="toggle"><i class="fa fa-times fa-pencil"></i>
                <div class="tooltip">Click Me</div>
              </div>

              <!-- Form Login -->
              <div class="form">
                <h3>Login to your account</h3>

                @if($errors->login->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13px; padding: 10px 15px; border-radius: 2px;">
                    @foreach($errors->login->all() as $err)
                      <div style="margin: 0;"><i class="fa fa-exclamation-triangle"></i> {{ $err }}</div>
                    @endforeach
                  </div>
                @endif

                <form action="{{ route('login') }}" method="post">
                  @csrf
                  <input type="hidden" name="redirect_to" value="{{ old('redirect_to', request()->fullUrl()) }}">
                  <input type="text" name="login" placeholder="Email or Username" value="{{ old('login') }}" required>
                  <input type="password" name="password" placeholder="Password" required>
                  <input type="submit" value="Login">
                </form>
              </div>

              <!-- Form Register -->
              <div class="form">
                <h3>Create an account</h3>

                @if($errors->register->any())
                  <div class="alert alert-danger" style="margin-bottom: 15px; font-size: 13px; padding: 10px 15px; border-radius: 2px;">
                    <ul style="margin: 0; padding-left: 18px;">
                      @foreach($errors->register->all() as $err)
                        <li>{{ $err }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form action="{{ route('register') }}" method="post">
                  @csrf
                  <input type="text" name="name" placeholder="Name / Username" value="{{ old('name') }}" required>
                  <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                  <input type="password" name="password" placeholder="Password (min. 8 characters)" required>
                  <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                  <input type="submit" value="Register">
                </form>
              </div>

              <div class="cta"><a href="#" onclick="event.preventDefault(); $('.toggle').click();">Switch between Sign In / Sign Up</a></div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    $('.toggle').click(function(){
      // Switches the Icon
      $(this).children('i').toggleClass('fa-pencil');
      // Switches the forms  
      $('.form-module .form').animate({
        height: "toggle",
        'padding-top': 'toggle',
        'padding-bottom': 'toggle',
        opacity: "toggle"
      }, "slow");
    });

    @if(session('show_modal') === 'register' || $errors->register->any())
      $('#myModal').modal('show');
      $('.form-module .form:nth-child(2)').hide();
      $('.form-module .form:nth-child(3)').show();
      $('.toggle').children('i').removeClass('fa-pencil');
    @elseif(session('show_modal') === 'login' || $errors->login->any())
      $('#myModal').modal('show');
      $('.form-module .form:nth-child(2)').show();
      $('.form-module .form:nth-child(3)').hide();
      $('.toggle').children('i').addClass('fa-pencil');
    @endif

    $('#myModal').on('show.bs.modal', function () {
      @if(!($errors->register->any() || session('show_modal') === 'register'))
        $('.form-module .form:nth-child(2)').show();
        $('.form-module .form:nth-child(3)').hide();
        $('.toggle').children('i').addClass('fa-pencil');
      @endif
    });
  });
</script>