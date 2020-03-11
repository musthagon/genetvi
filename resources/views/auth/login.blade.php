@php
    $normal_user = isset($normal_user) ? true : false;
@endphp

<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" dir="{{ __('voyager::generic.is_rtl') == 'true' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="none" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="admin login">
    <title>{{ Voyager::setting("admin.title") }}</title>
    <link rel="stylesheet" href="{{ voyager_asset('css/app.css') }}">
    <!-- Favicon -->

    <link rel="shortcut icon" href="{{asset('img/LogoGENETVI_rombo.png')}}" type="image/png">


    <link rel="stylesheet" href="{{asset('css/voyager/login_style.css')}}">

    <style>
        body {            
            background-image:url('{{asset("img/24_VICTOR_VASARELY_HOMMAGE_A_MALEVITCH_HOMENAJE_A_MALEVITCH_1954_F_JUAN_PEREZ_HERNANDEZ.png")}}');
            background-color: {{ Voyager::setting("admin.bg_color", "#FFFFFF" ) }};
        }
        body.login .login-sidebar {
            border-top:5px solid {{ config('voyager.primary_color','#22A7F0') }};
        }
        @media (max-width: 767px) {
            body.login .login-sidebar {
                border-top:0px !important;
                border-left:5px solid {{ config('voyager.primary_color','#22A7F0') }};
            }
        }
        body.login .form-group-default.focused{
            border-color:{{ config('voyager.primary_color','#22A7F0') }};
        }
        .login-button, .bar:before, .bar:after{
            background:{{ config('voyager.primary_color','#22A7F0') }};
        }
        .remember-me-text{
            padding:0 5px;
        }
        #right-image{
            background: #689df6; 
            background-size:cover; 
            background-image: url('{{asset('img/ucv-mural1.jpg')}}'); 
            background-position: center center;
            position:absolute; 
            top:0; 
            left:0; 
            width:100%; 
            height:300px;
        }
        
    </style>
    
    <link href="{{asset('css/fonts.googleapis.com_OpenSans300,400,700.css')}}" rel="stylesheet">

</head>
<body class="login">
<div class="container-fluid">
    <div class="row">
        <div class="faded-bg animated"></div>
        <div class="hidden-xs col-sm-7 col-md-8">
            <div class="clearfix">
                <div class="col-sm-12 col-md-10 col-md-offset-2">
                    <div class="logo-title-container">
                        <img class="img-responsive pull-left logo hidden-xs" src="{{asset('img/LogoGENETVI_rombo.png')}}" alt="Logo Icon">

                        <div class="copy animated fadeIn">
                            <h1>{{ Voyager::setting('admin.title', 'Voyager') }}</h1>
                            <p>{{ Voyager::setting('admin.description', __('voyager::login.welcome')) }}</p>
                        </div>
                    </div> <!-- .logo-title-container -->
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-5 col-md-4 login-sidebar">
            <div class="login-body">

                <div class="side-body padding-top login-container">
                    <div id="right-image"></div>
                    <div style="height:160px; display:block; width:100%"></div>
                    <div style="position:relative; z-index:9; text-align:center; margin-top: -48px;">
                        <img src="{{asset('img/LogoGENETVI.png')}}" class="avatar" style="border-radius:50%; width:200px; height:200px;" alt="GENETVI">
                        <h1 class="text-muted">Aplicación Web para la Gestión de la Evaluación de Entornos Virtuales de Aprendizaje del Campus Virtual de la UCV</h1>
                        <div class="text-muted">
                        ¡Bienvenid@! Esta Aplicación Web es de uso exclusivo de Profesores, Coordinadores de EaD y la Gerencia del SEDUCV, para la gestión de la evaluación de los entornos virtuales de aprendizaje del Campus Virtual de la Universidad Central de Venezuela.
                        </div>
                        <p></p>
                    </div>
                </div>

                <div class="login-container">

                    <p class="login_title">Acceder</p>

                    <form action="{{ $normal_user == true ? route('login') : route('voyager.login') }}" method="POST">
                        
                        {{ csrf_field() }}

                        <div class="form-group form-group-default" id="emailGroup">
                            <label>@if($normal_user == true) Nombre de usuario @else Correo Electrónico @endif</label>
                            <div class="controls">
                            @if($normal_user == true) 
                                <input type="text" name="cvucv_username" id="email" value="{{ old('cvucv_username') }}" placeholder="Nombre de usuario" class="form-control" required>
                            @else  
                                <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="Correo Electrónico" class="form-control" required>
                            @endif
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
                                <a class="instrucciones pull-right" href="#instrucciones">
                                    <span class="voyager-question"></span>
                                    Instrucciones de acceso
                                    
                                </a>
                                <div id="instrucciones" class="modal-window">
                                    <a href="#" title="Close" class="modal-close">  <span class="voyager-x"></span></a>
                                    <div class="modal-body">
                                        <a href="#" title="Close" class="modal-close2">  <span class="voyager-x"></span>Cerrar</a>
                                        <h1>Acceso</h1>
                                        <span class="tooltiptext">
                                            <div class="subcontent">
                                                Para ingresar a la Aplicación debe usar las mismas credenciales del 
                                                <a href="https://campusvirtual.ucv.ve/login/index.php" target="_blank">Campus Virtual UCV.</a>
                                            </div>
                                            <div class="subcontent">
                                                <pre><span style="color:#000080;font-family:arial, helvetica, sans-serif;"><strong>¿Posee una cuenta de correo UCV?</strong></span></pre><p style="margin-left:30px;text-align:left;"><span style="color:#000000;font-family:arial, helvetica, sans-serif;">• En el campo "Nombre de usuario" <span style="background-color:#ffff99;"><strong>escriba su usuario</strong>&nbsp;<span style="color:#000000;"><strong>sin el @ucv.ve</strong></span></span></span></p><p style="margin-left:30px;text-align:left;"><span style="color:#000000;font-family:arial, helvetica, sans-serif;">• En el campo "Contraseña" escriba la clave de su correo UCV.</span></p><p style="margin-left:30px;text-align:left;"><span style="color:#000000;font-family:arial, helvetica, sans-serif;"><span>• En caso de no poseer correo institucional, por favor diríjase a la&nbsp;Dirección&nbsp;de Tecnología de Información y Comunicaciones (DTIC), ubicada en el&nbsp;Edificio El Rectorado, PB. <strong>Debe presentar su C.I y carnet vigente.</strong></span></span></p><pre><span style="color:#000080;font-family:arial, helvetica, sans-serif;"><strong>¿Es ud. un usuario externo?</strong></span></pre><p style="margin-left:30px;text-align:left;"><span style="font-family:arial, helvetica, sans-serif;">• En el campo "Nombre de usuario" escriba su número de cédula, sin puntos ni espacios.</span></p><p style="margin-left:30px;text-align:left;"><span style="font-family:arial, helvetica, sans-serif;">• En el campo&nbsp;"Contraseña" escriba la clave suministrada por el administrador del espacio o profesor del curso.&nbsp;</span></p><pre><span style="color:#000080;font-family:arial, helvetica, sans-serif;"><strong>¿Olvidó su contraseña?</strong></span></pre><p style="text-align:left;margin-left:30px;"><span style="font-family:arial, helvetica, sans-serif;">• Usuario UCV: ingrese en&nbsp;<a target="_blank" rel="noreferrer noopener" href="https://recuperar_password.ucv.ve/">https://recuperar_password.ucv.ve/</a></span></p><p style="text-align:left;margin-left:30px;"><span style="font-family:arial, helvetica, sans-serif;">• Usuario externo: debe dirigirse al&nbsp;administrador del espacio o profesor del curso.&nbsp;</span></p>
                                            </div>
                                        </span>
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-block login-button">
                            <span class="signingin hidden"><span class="voyager-refresh"></span> Cargando...</span>
                            <span class="signin">Acceder</span>
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

                <div class="login-container">
                    <div class="text-muted">Gracias a: </div>
                    
                    <div class="logos-icon">
                        <a href="http://www.ucv.ve" target="_blank">
                            <img class="" src="{{asset('img/LogoUCV.png')}}" alt="Logo Icon">
                        </a>
                        <a href="http://www.ucv.ve/docencia/sistema-de-educacion-a-distancia/seducv.html" target="_blank">
                            <img class="" src="{{asset('img/LogoSEDUCV.png')}}" alt="Logo Icon">
                        </a>
                        <a href="http://www.ciens.ucv.ve/" target="_blank">
                            <img class="" src="{{asset('img/LogoFacultadDeCienciasUCV.png')}}" alt="Logo Icon">
                        </a>
                        <a href="http://www.ciens.ucv.ve/ciens/computacion/" target="_blank">
                            <img class="" src="{{asset('img/LogoEscuelaDeComputacionUCV.png')}}" alt="Logo Icon">
                        </a>
                    </div>

                    <a class="instrucciones pull-right" href="#creditos">
                        <span class="voyager-question"></span>
                        Créditos de la Aplicación
                        
                    </a>
                    <div id="creditos" class="modal-window">
                        <a href="#" title="Close" class="modal-close">  <span class="voyager-x"></span></a>
                        <div class="modal-body">
                            <a href="#" title="Close" class="modal-close2">  <span class="voyager-x"></span>Cerrar</a>
                            <h1>Créditos de la Aplicación</h1>
                            <span class="tooltiptext">
                                <div class="subcontent">
                                    Esta Aplicación Web es de uso exclusivo de Profesores, Coordinadores de EaD y la Gerencia del SEDUCV
                                </div>
                                <div class="subcontent">
                                
                                    <pre><span class="section-title"><strong>Créditos</strong></span></pre>
                                    <p class="section-text">
                                        <span>Esta Aplicación Web para la gestión de la evaluación de los entornos virtuales de aprendizaje del Campus Virtual de la UCV fue desarrollada por Miguel Magdalena como Trabajo Especial de Grado para optar al título de Licenciado en Computación de la Universidad Central de Venezuela, bajo la tutoría de la Profa. Yosly Hernández Bieliukas.</span>
                                    </p>

                                    <pre><span class="section-title"><strong>Licencia</strong></span></pre>
                                    <p class="section-text">
                                        <span>• Este obra está bajo una licencia <a href="https://www.gnu.org/licenses/gpl-3.0.html" target="_blank">GNU General Public License</a></span> 
                                    </p>
                                    <p class="section-text">
                                        <span>• Versión Actual: 1.0v. Disponible en <a href="https://github.com/musthagon/genetvi" target="_blank">Github</a></span>
                                    </p>

                                </div>
                            </span>
                        </div>
                        
                    </div>                                


                </div>

            </div>
        </div> <!-- .login-sidebar -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->
<script>
    var btn = document.querySelector('button[type="submit"]');
    var form = document.forms[0];

    @if($normal_user == true) 
        var email = document.querySelector('[name="cvucv_username"]');
    @else  
        var email = document.querySelector('[name="email"]');
    @endif

    var password = document.querySelector('[name="password"]');
    btn.addEventListener('click', function(ev){
        if(btn.querySelector('.signingin').className == 'signingin' || btn.querySelector('.signin').className == 'signin hidden'){
            ev.preventDefault();
        }
        if (form.checkValidity()) {
            btn.querySelector('.signingin').className = 'signingin';
            btn.querySelector('.signin').className = 'signin hidden';
        } else {
            ev.preventDefault();
        }

        
    });
    email.focus();
    document.getElementById('emailGroup').classList.add("focused");

    // Focus events for email and password fields
    email.addEventListener('focusin', function(e){
        document.getElementById('emailGroup').classList.add("focused");
    });
    email.addEventListener('focusout', function(e){
       document.getElementById('emailGroup').classList.remove("focused");
    });

    password.addEventListener('focusin', function(e){
        document.getElementById('passwordGroup').classList.add("focused");
    });
    password.addEventListener('focusout', function(e){
       document.getElementById('passwordGroup').classList.remove("focused");
    });

</script>
</body>
</html>
