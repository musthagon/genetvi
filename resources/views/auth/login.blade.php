<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SISGEVA') }}</title>

	<!--===============================================================================================-->
		
		<link rel="icon" type="image/png" href="/login_template/images/icons/favicon2.ico"/>
		
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="/login_template/vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="/login_template/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="/login_template/vendor/animate/animate.css">
    <!--===============================================================================================-->	
        <link rel="stylesheet" type="text/css" href="/login_template/vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="/login_template/vendor/select2/select2.min.css">
    <!--===============================================================================================-->
        <link rel="stylesheet" type="text/css" href="/login_template/css/util.css">
        <link rel="stylesheet" type="text/css" href="/login_template/css/main.css">
    <!--===============================================================================================-->
    </head>
<body>
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">

				<span class="login100-form-title">
						Sistema de Gestión de la Evaluación Integral de Entornos Virtuales de Aprendizaje del Campus Virtual de la Universidad Central de Venezuela
				</span>

				<div class="login100-pic js-tilt" data-tilt>
					<img src="/login_template/images/LogoSEDUCV.png" alt="Logo del SEDUCV">
				</div>

				<form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
					{{ csrf_field() }}

					<span class="login100-form-title">
						Acceso a Usuarios
					</span>

					<div class="login100-pic2 js-tilt" data-tilt>
						<img src="/login_template/images/LogoSEDUCV.png" alt="Logo del SEDUCV">
					</div>

					<label for="cvucv_username" class="control-label">Nombre de Usuario:</label>
					<div class="wrap-input100 validate-input {{ $errors->has('cvucv_username') ? 'alert-validate' : '' }}" data-validate = "{{ $errors->has('cvucv_username') ? $errors->first('cvucv_username') : '' }}">
					
						<input class="input100" type="text" name="cvucv_username" placeholder="Nombre de Usuario" value="{{ old('cvucv_username') }}" required autofocus>

						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<label for="cvucv_username" class="control-label">Constraseña:</label>
					<div class="wrap-input100 validate-input {{ $errors->has('password') ? 'alert-validate ' : '' }}" data-validate = "{{ $errors->has('password') ? $errors->first('password') : '' }}">

						<input class="input100" type="password" name="password" placeholder="Constraseña" required>

						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-lock" aria-hidden="true"></i>
						</span>
					</div>

					<div class="form-group">
						<div class="">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Recordar
								</label>
							</div>
						</div>
					</div>
					@if ($errors->has('cvucv_username'))
						<span class="help-block">
							<strong>{{ $errors->first('cvucv_username') }}</strong>
						</span>
					@endif
					@if ($errors->has('password'))
						<span class="help-block">
							<strong>{{ $errors->first('password') }}</strong>
						</span>
					@endif
					
					<div class="container-login100-form-btn">
						<button class="login100-form-btn">
							Acceder
						</button>
					</div>

					
				</form>
			</div>
		</div>
	</div>
	
	

	
<!--===============================================================================================-->	
	<script src="/login_template/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="/login_template/vendor/bootstrap/js/popper.js"></script>
	<script src="/login_template/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="/login_template/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="/login_template/vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	<!--<script src="js/main.js"></script>-->

</body>
</html>