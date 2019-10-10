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
    .course_acciones{
      display: flex;
      justify-content: center;
      flex-flow: wrap;
    }
    .course_acciones_item{
      flex: 0 1 auto;
      margin-bottom: 10px;
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
                  <td>
                      <a href="{{env('CVUCV_GET_SITE_URL',setting('site.CVUCV_GET_SITE_URL')).'/course/view.php?id='.$curso->id}}" target="_blank"> 
                        {{$curso->cvucv_fullname}} 
                        
                      </a>
                  </td>
                  <td class="course_summary">
                    {!!$curso->cvucv_summary!!}
                  </td>
                  
                  <td class="course_acciones">
                    @if( !empty($curso->categoria)) 
                      @if( !empty($curso->categoria->categoria_raiz)) 
                        @if( !($curso->categoria->categoria_raiz->instrumentos_habilitados)->isEmpty())
                          @if( !($curso->instrumentos_disponibles_usuario(Auth::user()->id, $curso->id))->isEmpty() )
                            @foreach($curso->instrumentos_disponibles_usuario(Auth::user()->id, $curso->id) as $instrumento)
                            <a class="course_acciones_item" href="{{ route('evaluacion', ['curso' => $curso, 'instrumento' => $instrumento]) }}" title="Evaluar" class="" >
                              <button class=" btn-sm btn-success" style="margin-right: 5px;">
                                <i class="voyager-list"></i> Evaluar {{$instrumento->nombre}}
                              </button>
                              
                            </a>
                            @endforeach
                          @else
                            Curso Evaluado
                          @endif

                        @else
                          No hay evaluación disponible
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
                  <td><a href="{{env('CVUCV_GET_SITE_URL',setting('site.CVUCV_GET_SITE_URL')).'/course/view.php?id='.$curso->id}}" target="_blank"> {{$curso->cvucv_fullname}} </a></td>
                  <td class="course_summary">{!!$curso->cvucv_summary!!}</td>
                  
                  <td class="course_acciones">
                    <a class="course_acciones_item" href="{{ route('curso', ['id' => $curso->id]) }}" title="Ver">
                        <button class="btn-sm btn-primary" style="margin-right: 5px;">
                          <i class="voyager-list"></i> Ver 
                        </button>
                    
                    
                    </a>
                    @if( !empty($curso->categoria)) 
                    @if( !empty($curso->categoria->categoria_raiz)) 
                    @if( !empty($curso->categoria->categoria_raiz->instrumentos_habilitados))
                    @if(!empty ($curso->instrumentos_disponibles_usuario(Auth::user()->id, $curso->id)))
                      @foreach($curso->instrumentos_disponibles_usuario(Auth::user()->id, $curso->id) as $instrumento)
                     

                      <a class="course_acciones_item" href="{{ route('evaluacion', ['curso' => $curso, 'instrumento' => $instrumento]) }}" title="Evaluar" >
                        <button class=" btn-sm btn-success" style="margin-right: 5px;">
                          <i class="voyager-list"></i> Evaluar {{$instrumento->nombre}}
                        </button>
                        
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

    @if( $cursosEstudiante->isEmpty() && $cursosDocente->isEmpty())
      <div class="col-md-12">
        <div class="box box-default">
          <div class="box-header with-border">
            <i class="fa fa-bullhorn"></i>

            <h3 class="box-title">Notificaciones</h3>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <div class="callout callout-info">
              <h4>No tienes cursos disponibles</h4>
              <p>Si estas registrado en un curso en el Campus Virutal UCV y no se muestra aquí, comunícate con el docente de tu curso</p>
            </div>
            
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
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
