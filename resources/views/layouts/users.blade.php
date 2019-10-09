<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>{{ config('app.name', 'SISGEVA') }}</title>


  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="/adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/adminlte/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/adminlte/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/adminlte/css/AdminLTE.min.css">

  <link rel="stylesheet" href="/adminlte/css/skins/skin-blue.min.css">
  
  <!-- Google Font -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <!-- Toastr style -->
  <link rel="stylesheet" href="/toastr/css/toastr.min.css">

  @yield('css')
</head>

<body class="hold-transition skin-blue sidebar-mini">

<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="{{ route('home') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>{{ config('app.name', 'SISGEVA') }}</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>{{ config('app.name', 'SISGEVA') }}</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              @if( strpos( $participante['profileimageurlsmall'], env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')) ) !== false )
                <img src="{{env('CVUCV_GET_WEBSERVICE_ENDPOINT2',setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2'))}}/{{strtok($participante['profileimageurlsmall'], env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')))}}/user/icon/f2?token={{env('CVUCV_ADMIN_TOKEN',setting('site.CVUCV_ADMIN_TOKEN'))}}" class="user-image" alt="User Image"> 
              @else
                <img src="{{ Auth::user()->avatar }}" class="user-image" alt="User Image">
              @endif
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">{{ Auth::user()->name }} <i class="fa fa-gears"></i></span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                @if( strpos( $participante['profileimageurlsmall'], env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')) ) !== false )
                  <img src="{{env('CVUCV_GET_WEBSERVICE_ENDPOINT2',setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2'))}}/{{strtok($participante['profileimageurlsmall'], env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')))}}/user/icon/f1?token={{env('CVUCV_ADMIN_TOKEN',setting('site.CVUCV_ADMIN_TOKEN'))}}" class="img-circle" alt="User Image"> 
                @else
                  <img src="{{ Auth::user()->avatar }}" class="img-circle" alt="User Image">
                @endif

                <p>
                  {{ Auth::user()->name }} - Rol
                  <small></small>
                </p>
              </li>

              <!-- Menu Footer-->
              <li class="user-footer">

                <div class="pull-right" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                  <a href="#" class="btn btn-default btn-flat">Cerrar Sesión</a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                  </form>
                </div>
              </li>
            </ul>
          </li>

        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          @if( strpos( Auth::user()->avatar, env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')) ) !== false )
              <img src="{{env('CVUCV_GET_WEBSERVICE_ENDPOINT2',setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT2'))}}/{{strtok(Auth::user()->avatar, env('CVUCV_GET_WEBSERVICE_ENDPOINT1', setting('site.CVUCV_GET_WEBSERVICE_ENDPOINT1')) )}}/user/icon/f1?token={{env('CVUCV_ADMIN_TOKEN',setting('site.CVUCV_ADMIN_TOKEN'))}}" class="img-circle" alt="User Image"> 
          @else
            <img src="{{ Auth::user()->avatar }}" class="img-circle" alt="User Image">
          @endif
        </div>
        <div class="pull-left info">
          <p>{{ Auth::user()->name }}</p>
          <!-- Status -->
          <i class="fa fa-circle text-success"></i> Online
        </div>
      </div>

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">HEADER</li>
        <!-- Optionally, you can add icons to the links -->
        <li class="active"><a href="#"><i class="fa fa-link"></i> <span>Link</span></a></li>
        <li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>
        <li class="treeview">
          <a href="#"><i class="fa fa-link"></i> <span>Multilevel</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
              </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="#">Link in level 2</a></li>
            <li><a href="#">Link in level 2</a></li>
          </ul>
        </li>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Page Header
        <small>Optional description</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
      </ol>
    </section>


    <!-- Main content -->
    <section class="content container-fluid">

        @yield('content')

    </section>
    <!-- /.content -->


  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>©2019 <a href="https://campusvirtual.ucv.ve/">SEDUCV</a>.</strong>
  </footer>

</div>
<!-- ./wrapper -->


<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<script src="/adminlte/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="/adminlte/js/adminlte.min.js"></script>
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


<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>
</html>