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

    <!-- Select2 -->
    <link rel="stylesheet" href="/adminlte/bower_components/select2/dist/css/select2.min.css">

    <!-- Toastr style -->
    <link rel="stylesheet" href="/toastr/css/toastr.min.css">
    
    <link rel="stylesheet" href="/adminlte/css/AdminLTE.min.css">
    
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

              @if($alert_type=="success")
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
    <!-- Bootstrap 3.3.7 -->
    <script src="/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="/adminlte/bower_components/select2/dist/js/select2.js"></script>
    <!-- Jquery Validate -->
    <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
    <script type="text/javascript"  src="/js/jquery.steps.js"></script>

    <!-- Toastr style -->
    <script src="/toastr/js/toastr.min.js"></script>
    <script>
        $(function (){
          toastr.options = {
          "closeButton": true,
          "debug": false,
          "newestOnTop": true,
          "progressBar": true,
          "positionClass": "toast-top-right",
          "preventDuplicates": true,
          "onclick": null,
          "showDuration": "300",
          "hideDuration": "1000",
          "timeOut": "5000",
          "extendedTimeOut": "1000",
          "showEasing": "swing",
          "hideEasing": "linear",
          "showMethod": "fadeIn",
          "hideMethod": "fadeOut"
          }
        });
        @if(Session::has('message'))
        var type = "{{ Session::get('alert-type', 'info') }}";
        switch(type){
            case 'info':
                toastr.info("{{ Session::get('message') }}");
                break;

            case 'warning':
                toastr.warning("{{ Session::get('message') }}");
                break;

            case 'success':
                toastr.success("{{ Session::get('message') }}");
                break;

            case 'error':
                toastr.error("{{ Session::get('message') }}");
                break;
        }
        @endif
      
    </script>
    
    @yield('javascript')
    @stack('javascript')

    </body>

</html>