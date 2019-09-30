@extends('layouts.users')

@section('css')
  <link rel='stylesheet' href='/css/foundation.min.css'>
  <link rel="stylesheet" href="/css/jquery.steps.css">
  <link rel="stylesheet" href="/css/linkert-table.css">
  <link rel="stylesheet" href="/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
@stop

@section('content')

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <!-- Default box -->
      @if(!empty($instrumento))
      <div class="box">
        <div class="box-header with-border">

          <h3 class="box-title">{{$instrumento->nombre}}</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                    title="Collapse">
              <i class="fa fa-minus"></i></button>
          </div>
        </div>

        <div class="box-body">
          
          @include('user.instrumentos.estructura-instrumento')

        </div>

        <!-- /.box-footer-->
      </div>
      <!-- /.box -->
      @endif

    </div>



    <div class="col-xs-12">
    <div class="box">
            <div class="box-header">
              <h3 class="box-title">Hover Data Table</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">

            @include('user.cursos.tabla-de-cursos')

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
    </div>



  </div>



</section>
@stop

@section('javascript')

  <script type="text/javascript"  src="/js/jquery.steps.js"></script>
  <script type="text/javascript" src="/js/jquery.validate.min.js"></script>
  <!-- DataTables -->
  <script src="/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="/adminlte/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="/adminlte/bower_components/fastclick/lib/fastclick.js"></script>
  <script>
    $(function (){
      $('#cursos-data-table').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      })

      @if(!empty($instrumento))
      var form = $("#wizard");
      form.validate({
            errorPlacement: function errorPlacement(error, element) { element.before(error); },
            rules: {
              
              @foreach($instrumento->categorias as $categoria)
              @foreach($categoria->indicadores as $indicador)
                '{{$indicador->nombre}}' : {required :true},
              @endforeach
              @endforeach
              
            }
        });
        form.steps({
            headerTag: "h2",
            bodyTag: "section",
            transitionEffect: "slideLeft",
            onStepChanging: function (event, currentIndex, newIndex)
            {
                form.validate().settings.ignore = ":disabled,:hidden";
                return form.valid();
            },
            onFinishing: function (event, currentIndex)
            {
                form.validate().settings.ignore = ":disabled";
                return form.valid();
            },
            onFinished: function (event, currentIndex)
            {
                //alert("Submitted!");
                form.submit();
            }
        });
        @endif
    });
  </script>


@stop
