@extends('layouts.users')

@section('css')
  <link rel='stylesheet' href='/css/foundation.min.css'>
  <link rel="stylesheet" href="/css/jquery.steps.css">
  <link rel="stylesheet" href="/css/linkert-table.css">
  <link rel="stylesheet" href="/adminlte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <style>
    @media (max-width: 767px){
      .box {overflow: auto;}
    }
    
  </style>
@stop

@section('content')

<!-- Main content -->
<section class="content">
  <div class="row">

    @if(!($cursosEstudiante->isEmpty()))
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Mis Cursos</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <table id="cursos-data-table" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($cursosEstudiante as $curso)
                <tr>
                  <td><a href="{{env('CVUCV_GET_SITE_URL').'/course/view.php?id='.$curso->id}}" target="_blank"> {{$curso->cvucv_fullname}} </a></td>
                  <td class="course_summary">{!!$curso->cvucv_summary!!}</td>
                  
                  <td>

                    @if( !empty($curso->categoria)) 
                      @if( !empty($curso->categoria->categoria_raiz)) 
                        @if( !($curso->categoria->categoria_raiz->instrumentos_habilitados)->isEmpty())
                          @if( !($curso->instrumentos_disponibles_usuario(Auth::user()->id, $curso->id))->isEmpty() )
                            @foreach($curso->instrumentos_disponibles_usuario(Auth::user()->id, $curso->id) as $instrumento)
                            <a href="{{ route('evaluacion', ['curso' => $curso, 'instrumento' => $instrumento]) }}" title="Ver" class="btn btn-sm btn-success" style="margin-right: 5px;">
                              <i class="voyager-list"></i> Evaluar {{$instrumento->nombre}}
                            </a>
                            @endforeach
                          @else
                            No hay acciones
                          @endif

                        @else
                          No hay acciones
                        @endif           
                      @endif
                    @endif
                    
                    
                  </td>
                </tr>
              @endforeach
                  
          </table>

        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
    @endif

    @if(!($cursosDocente->isEmpty()))
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Mis Cursos como Docente</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

          <table id="cursos-data-table2" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @foreach($cursosDocente as $curso)
                <tr>
                  <td><a href="{{env('CVUCV_GET_SITE_URL').'/course/view.php?id='.$curso->id}}" target="_blank"> {{$curso->cvucv_fullname}} </a></td>
                  <td class="course_summary">{!!$curso->cvucv_summary!!}</td>
                  
                  <td>
                    <a href="{{ route('curso', ['id' => $curso->id]) }}" title="Ver" class="btn btn-sm btn-primary" style="margin-right: 5px;">
                      <i class="voyager-list"></i> Ver 
                    </a>
                    @if( !empty($curso->categoria)) 
                    @if( !empty($curso->categoria->categoria_raiz)) 
                    @if( !empty($curso->categoria->categoria_raiz->instrumentos_habilitados))
                    @if(!empty ($curso->instrumentos_disponibles_usuario(Auth::user()->id, $curso->id)))
                      @foreach($curso->instrumentos_disponibles_usuario(Auth::user()->id, $curso->id) as $instrumento)
                      <a href="{{ route('evaluacion', ['curso' => $curso, 'instrumento' => $instrumento]) }}" title="Ver" class="btn btn-sm btn-success" style="margin-right: 5px;">
                        <i class="voyager-list"></i> Evaluar {{$instrumento->nombre}}
                      </a>
                      @endforeach
                      @endif
                    @endif
                    @endif
                    @endif
                    
                    
                  </td>
                </tr>
              @endforeach
                  
          </table>

        </div><!-- /.box-body -->
      </div><!-- /.box -->
    </div>
    @endif

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
      

      $('#cursos-data-table, #cursos-data-table2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false
      })

    });
  </script>


@stop
