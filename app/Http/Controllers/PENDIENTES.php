<div class="login-container">

                <p>Acceder</p>

                <form action="{{ route('login') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group form-group-default" id="emailGroup">
                        <label>Nombre de usuario</label>
                        <div class="controls">
                            <input type="text" name="cvucv_username" id="email" value="{{ old('email') }}" placeholder="Nombre de usuario" class="form-control" required>
                         </div>
                    </div>

                    <div class="form-group form-group-default" id="passwordGroup">
                        <label>Contraseña</label>
                        <div class="controls">
                            <input type="password" name="password" placeholder="Contraseña" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group" id="rememberMeGroup">
                        <div class="controls">
                        <input type="checkbox" name="remember" id="remember" value="1"><label for="remember" class="remember-me-text">Recordar</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-block login-button">
                        <span class="signingin hidden"><span class="voyager-refresh"></span> {{ __('voyager::login.loggingin') }}...</span>
                        <span class="signin">{{ __('voyager::generic.login') }}</span>
                    </button>

              </form>

              <div style="clear:both"></div>

              @if(!$errors->isEmpty())
              <div class="alert alert-red">
                <ul class="list-unstyled">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
              </div>
              @endif

            </div> <!-- .login-container -->