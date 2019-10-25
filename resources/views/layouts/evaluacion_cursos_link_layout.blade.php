<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	  <meta name="author" content="colorlib.com">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Evaluación de @isset($curso) {{$curso->getNombre()}} @else Curso @endif</title>

    <!-- Font Icon -->
    <link rel="stylesheet" href="/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/adminlte/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/adminlte/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Likert CSS -->
    <link rel='stylesheet' href='/css/foundation.min.css'>
    <link rel="stylesheet" href="/css/jquery.steps.css">
    <link rel="stylesheet" href="/css/linkert-table.css">
    <link rel="stylesheet" href="/select2-4.0.11/css/select2.min.css">

    
    <!-- Main css -->
    <link rel="stylesheet" href="/custom-wizard/css/style.css">

    @yield('css')
</head>

<body>
    <div class="main">

        <div class="container">
          
          @if(isset($message) && isset($alert_type))

            <h2 class="message_title">{{ $alert_type }}</h2>

            <div class="center" >

              @if($alert_type=="gracias")
                <i class="fa fa-check message_font message_icon1"></i>
              @else
                <i class="fa fa-close message_font message_icon2"></i>
              @endif


              <div class="descripcion center">
                {{ $message }}
              </div>
            </div>

          @endif

          @yield('content')

        </div>

    </div>

    <!-- JS -->
    <!-- jQuery 3 -->
    <script src="/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
    
    <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
    <script type="text/javascript"  src="/js/jquery.steps.js"></script>
    <script type="text/javascript"  src="/select2-4.0.11/js/select2.min.js"> </script>

    @yield('javascript')
    @stack('javascript')

    </body>

</html>